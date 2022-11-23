<?php

namespace App\Jobs\Export;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

use App\Models\Conference;

class ExportConferences implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
    public function handle()
    {
        $path = config('filesystems.disks.exports.root');

        if(!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $fileName = uniqid() . '.csv';
        $file = fopen($path . '/' . $fileName, 'w');

        $conferences = Conference::all();
        $conferences->each(function ($conference) use ($file) {
            fputcsv($file, $conference->toArray());
        });

        fclose($file);
    }
}
