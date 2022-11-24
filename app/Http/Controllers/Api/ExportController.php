<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\Export\ConferencesCSV as ExportConferencesCSV;
use App\Http\Requests\Export\LecturesCSV as ExportLecturesCSV;
use App\Http\Requests\Export\ListenersCSV as ExportListenersCSV;
use App\Http\Requests\Export\CommentsCSV as ExportCommentsCSV;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

use App\Jobs\Export\ExportConferences;
use App\Jobs\Export\ExportLectures;
use App\Jobs\Export\ExportListeners;
use App\Jobs\Export\ExportComments;

class ExportController extends Controller
{
    use ApiResponseHelpers;

    public function conferencesCSV(ExportConferencesCSV $request): JsonResponse
    {
        ExportConferences::dispatch()->onQueue('exports');

        return $this->respondWithSuccess();
    }

    public function lecturesCSV(ExportLecturesCSV $request): JsonResponse
    {
        ExportLectures::dispatch($request->get('conference_id'))->onQueue('exports');

        return $this->respondWithSuccess();
    }

    public function listenersCSV(ExportListenersCSV $request): JsonResponse
    {
        ExportListeners::dispatch($request->get('conference_id'))->onQueue('exports');

        return $this->respondWithSuccess();
    }

    public function commentsCSV(ExportCommentsCSV $request): JsonResponse
    {
        ExportComments::dispatch($request->get('lecture_id'))->onQueue('exports');

        return $this->respondWithSuccess();
    }
}
