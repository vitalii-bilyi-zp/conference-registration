<?php

namespace App\Jobs\Export;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Lecture;

use App\Services\ExportService;

use Carbon\Carbon;

class ExportLectures implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    protected $conferenceId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($conferenceId = null)
    {
        $this->conferenceId = $conferenceId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ExportService $exportService)
    {
        $headers = [
            trans('export.lectures.title'),
            trans('export.lectures.lecture_time'),
            trans('export.lectures.description'),
            trans('export.lectures.comments_count'),
        ];
        $data = [$headers];
        Lecture::query()
            ->when($this->conferenceId, function(Builder $query, $conferenceId) {
                $query->where('conference_id', '=', $conferenceId);
            })
            ->get()
            ->each(function ($lecture) use (&$data) {
                $comments = $lecture->comments;
                $fields = [
                    $lecture->title,
                    Carbon::parse($lecture->lecture_start)->format('Y-m-d H:i:s') . ' - ' . Carbon::parse($lecture->lecture_end)->format('Y-m-d H:i:s'),
                    $lecture->description,
                    count($comments),
                ];

                array_push($data, $fields);
            });

        $exportService->saveToCSV($data);
    }
}
