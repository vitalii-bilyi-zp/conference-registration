<?php

namespace App\Jobs\Export;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Builder;

use App\Models\Comment;

use App\Services\ExportService;
use App\Jobs\Export\DeleteFile;
use App\Events\ExportFinished;

use Carbon\Carbon;

class ExportComments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    protected $lectureId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($lectureId = null)
    {
        $this->lectureId = $lectureId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ExportService $exportService)
    {
        $headers = [
            trans('export.comments.author'),
            trans('export.comments.date'),
            trans('export.comments.content'),
        ];
        $data = [$headers];
        Comment::query()
            ->when($this->lectureId, function(Builder $query, $lectureId) {
                $query->where('lecture_id', '=', $lectureId);
            })
            ->get()
            ->each(function ($comment) use (&$data) {
                $user = $comment->user;
                $fields = [
                    $user->firstname . ' ' . $user->lastname,
                    Carbon::parse($comment->created_at)->format('Y-m-d H:i:s'),
                    $comment->content,
                ];

                array_push($data, $fields);
            });

        $fileName = $exportService->saveToCSV($data);
        $filePath = $exportService->getFilePath($fileName);
        ExportFinished::dispatch('comments', $filePath);
        DeleteFile::dispatch($fileName)
            ->onQueue('exports')
            ->delay(now()->addSeconds(config('export.file_lifetime')));
    }
}
