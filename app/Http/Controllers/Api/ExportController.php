<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\Export\ConferencesCSV as ExportConferencesCSV;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

use App\Jobs\Export\ExportConferences;

class ExportController extends Controller
{
    use ApiResponseHelpers;

    public function conferencesCSV(ExportConferencesCSV $request): JsonResponse
    {
        ExportConferences::dispatch()->onQueue('exports');

        return $this->respondWithSuccess();
    }
}
