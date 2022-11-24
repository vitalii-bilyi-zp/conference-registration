<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

use App\Models\Conference;

class ExportService
{
    public function saveToCSV(array $data)
    {
        $path = config('filesystems.disks.exports.root');

        if(!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $fileName = uniqid() . '.csv';
        $file = fopen($path . '/' . $fileName, 'w');

        foreach ($data as $fields) {
            fputcsv($file, $fields);
        }

        fclose($file);
    }
}
