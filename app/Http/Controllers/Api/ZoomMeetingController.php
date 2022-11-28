<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\ZoomMeeting\Index as ZoomMeetingIndex;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

use App\Services\ZoomService;

class ZoomMeetingController extends Controller
{
    use ApiResponseHelpers;

    protected $zoomService;

    public function __construct(ZoomService $zoomService)
    {
        $this->zoomService = $zoomService;
    }

    public function index(ZoomMeetingIndex $request): JsonResponse
    {
        $meetings = $this->zoomService->getAllMeetings();

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($meetings);
    }
}
