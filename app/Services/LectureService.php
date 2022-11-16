<?php

namespace App\Services;

class LectureService
{
    public function savePresentation($file)
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
}
