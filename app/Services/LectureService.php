<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class LectureService
{
    public function storePresentation($file)
    {
        $hashFileName = null;

        if (isset($file)) {
            $filePath = $file->store('/', 'presentations');

            if (isset($filePath)) {
                $hashFileName = basename($filePath);
            }
        }

        return $hashFileName;
    }

    public function getPresentationPath($fileName)
    {
        if (!isset($fileName)) {
            return null;
        }

        return Storage::disk('presentations')->url($fileName);
    }

    public function deletePresentation($fileName)
    {
        if (!isset($fileName)) {
            return;
        }

        Storage::disk('presentations')->delete($fileName);
    }
}
