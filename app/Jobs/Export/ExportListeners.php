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

class ExportListeners implements ShouldQueue
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
            trans('export.listeners.firstname'),
            trans('export.listeners.lastname'),
            trans('export.listeners.birthdate'),
            trans('export.listeners.country'),
            trans('export.listeners.phone'),
            trans('export.listeners.email'),
        ];
        $data = [$headers];
        Conference::query()
            ->when($this->conferenceId, function(Builder $query, $conferenceId) {
                $query->where('id', '=', $conferenceId);
            })
            ->get()
            ->each(function ($conference) use (&$data) {
                $conference->users()
                    ->where('type', '=', User::LISTENER_TYPE)
                    ->get()
                    ->each(function ($user) use (&$data) {
                        $country = $user->country;
                        $fields = [
                            $user->firstname,
                            $user->lastname,
                            Carbon::parse($user->birthdate)->format('Y-m-d'),
                            $country->name,
                            $user->phone,
                            $user->email,
                        ];

                        array_push($data, $fields);
                    });
            });

        $exportService->saveToCSV($data);
    }
}
