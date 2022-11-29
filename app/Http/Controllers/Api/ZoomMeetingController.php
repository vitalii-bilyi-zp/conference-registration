<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\ZoomMeeting\Index as ZoomMeetingIndex;

use App\Models\Lecture;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

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
        $lectureMeetings = Cache::rememberForever(config('cache.keys.zoom_meetings'), function () {
            $meetings = $this->zoomService->getAllMeetings();
            $lecturesMap = Lecture::whereNotNull('zoom_meeting_id')
                ->get()
                ->reduce(function ($carry, $item) {
                    $carry[$item->zoom_meeting_id] = $item->toArray();
                    return $carry;
                }, []);

            return array_reduce($meetings, function ($carry, $item) use (&$lecturesMap) {
                $lecture = $lecturesMap[$item['id']] ?? null;
                if (isset($lecture)) {
                    $item['lecture_title'] = $lecture['title'];
                    array_push($carry, $item);
                }
                return $carry;
            }, []);
        });

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($lectureMeetings);
    }
}
