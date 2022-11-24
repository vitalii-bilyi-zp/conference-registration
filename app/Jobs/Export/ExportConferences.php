<?php

namespace App\Jobs\Export;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Conference;
use App\Models\User;

use App\Services\ExportService;

use Carbon\Carbon;

class ExportConferences implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ExportService $exportService)
    {
        $headers = [
            trans('export.conferences.title'),
            trans('export.conferences.date'),
            trans('export.conferences.address'),
            trans('export.conferences.country'),
            trans('export.conferences.lectures_count'),
            trans('export.conferences.listeners_count'),
        ];
        $data = [$headers];
        $conferences = Conference::all()
            ->each(function ($conference) use (&$data) {
                $country = $conference->country;
                $lectures = $conference->lectures;
                $listeners = $conference->users()
                    ->where('type', '=', User::LISTENER_TYPE)
                    ->get();
                $fields = [
                    $conference->title,
                    Carbon::parse($conference->date)->format('Y-m-d'),
                    $conference->latitude . ' - ' . $conference->longitude,
                    $country->name,
                    count($lectures),
                    count($listeners),
                ];

                array_push($data, $fields);
            });

        $exportService->saveToCSV($data);
    }
}
