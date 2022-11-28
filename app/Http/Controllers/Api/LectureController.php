<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\Lecture\Index as LectureIndex;
use App\Http\Requests\Lecture\Store as LectureStore;
use App\Http\Requests\Lecture\Show as LectureShow;
use App\Http\Requests\Lecture\Update as LectureUpdate;
use App\Http\Requests\Lecture\Destroy as LectureDestroy;
use App\Http\Requests\Lecture\ToFavorites as LectureToFavorites;
use App\Http\Requests\Lecture\RemoveFromFavorites as LectureRemoveFromFavorites;

use App\Models\Lecture;
use App\Models\Conference;
use App\Models\Category;
use App\Models\User;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Builder;

use App\Services\LectureService;
use App\Services\CategoryService;
use App\Services\ZoomService;
use App\Jobs\Email\SendAdminDeletedLectureEmail;

use Carbon\Carbon;

class LectureController extends Controller
{
    use ApiResponseHelpers;

    protected $lectureService;
    protected $categoryService;
    protected $zoomService;

    public function __construct(LectureService $lectureService, CategoryService $categoryService, ZoomService $zoomService)
    {
        $this->lectureService = $lectureService;
        $this->categoryService = $categoryService;
        $this->zoomService = $zoomService;
    }

    public function index(LectureIndex $request): JsonResponse
    {
        $user = $request->user();

        $lectures = Lecture::query()
            ->when($request->get('conference_id'), function(Builder $query, $conferenceId) {
                $query->where('conference_id', '=', $conferenceId);
            })
            ->when($request->get('from_date'), function(Builder $query, $fromDate) {
                $query->where('lecture_start', '>=', $fromDate);
            })
            ->when($request->get('to_date'), function(Builder $query, $toDate) {
                $query->where('lecture_end', '<=', $toDate);
            })
            ->when($request->get('category_ids'), function(Builder $query, $categoryIds) {
                $query->whereIn('category_id', $categoryIds);
            })
            ->when($request->get('duration'), function(Builder $query, $duration) {
                $query->whereRaw('TIMESTAMPDIFF(MINUTE, lecture_start, lecture_end) = ?', $duration);
            })
            ->paginate(15);

        $lectures->getCollection()->transform(function ($value) use (&$user) {
            $value->in_favorites = $user->inFavoriteLectures($value->id);
            $value->comments_count = count($value->comments);
            unset($value->comments);

            return $value;
        });

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($lectures);
    }

    public function store(LectureStore $request): JsonResponse
    {
        $user = $request->user();

        $hashFileName = null;
        $originalFileName = null;
        $presentation = $request->file('presentation');
        if (isset($presentation)) {
            $temp = $this->lectureService->storePresentation($presentation);

            if (isset($temp)) {
                $hashFileName = $temp;
                $originalFileName = $presentation->getClientOriginalName();
            }
        }

        $zoomMeetingId = null;
        if ($request->is_online) {
            $lectureStartDate = Carbon::createFromFormat('Y-m-d H:i:s', $request->lecture_start);
            $lectureEndDate = Carbon::createFromFormat('Y-m-d H:i:s', $request->lecture_end);
            $lectureDuration = $lectureEndDate->diffInMinutes($lectureStartDate);
            $data = [
                'default_password' => true,
                'topic' => $request->title,
                'start_time' => $lectureStartDate->toIso8601ZuluString(),
                'duration' => $lectureDuration,
                'type' => 2
            ];

            $zoomMeeting = $this->zoomService->createMeeting($user->zoom_id, $data);
            if (isset($zoomMeeting)) {
                $zoomMeetingId = $zoomMeeting['id'];
            } else {
                return $this->respondError();
            }
        }

        Lecture::create([
            'title' => $request->title,
            'description' => $request->description,
            'lecture_start' => $request->lecture_start,
            'lecture_end' => $request->lecture_end,
            'hash_file_name' => $hashFileName,
            'original_file_name' => $originalFileName,
            'conference_id' => $request->conference_id,
            'user_id' => $user->id,
            'category_id' => $request->category_id,
            'is_online' => $request->is_online,
            'zoom_meeting_id' => $zoomMeetingId,
        ]);

        return $this->respondWithSuccess();
    }

    public function show(LectureShow $request, Lecture $lecture): JsonResponse
    {
        $user = $request->user();

        if (isset($lecture->hash_file_name)) {
            $lecture->file_path = $this->lectureService->getPresentationPath($lecture->hash_file_name);
        }

        $lecture->in_favorites = $user->inFavoriteLectures($lecture->id);
        $lecture->conference = $lecture->conference;

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($lecture);
    }

    public function update(LectureUpdate $request, Lecture $lecture): JsonResponse
    {
        $hashFileName = $lecture->hash_file_name;
        $originalFileName = $lecture->original_file_name;
        $presentation = $request->file('presentation');
        if (isset($presentation)) {
            if (isset($hashFileName)) {
                $this->lectureService->deletePresentation($hashFileName);
            }

            $temp = $this->lectureService->storePresentation($presentation);

            if (isset($temp)) {
                $hashFileName = $temp;
                $originalFileName = $presentation->getClientOriginalName();
            }
        }

        if (isset($lecture->zoom_meeting_id)) {
            $lectureStartDate = Carbon::createFromFormat('Y-m-d H:i:s', $request->lecture_start ?? $lecture->lecture_start);
            $lectureEndDate = Carbon::createFromFormat('Y-m-d H:i:s', $request->lecture_end ?? $lecture->lecture_end);
            $lectureDuration = $lectureEndDate->diffInMinutes($lectureStartDate);
            $data = [
                'topic' => $request->title ?? $lecture->title,
                'start_time' => $lectureStartDate->toIso8601ZuluString(),
                'duration' => $lectureDuration,
            ];

            $this->zoomService->updateMeeting($lecture->zoom_meeting_id, $data);
        }

        $lecture->update([
            'title' => $request->title ?? $lecture->title,
            'description' => $request->description ?? $lecture->description,
            'lecture_start' => $request->lecture_start ?? $lecture->lecture_start,
            'lecture_end' => $request->lecture_end ?? $lecture->lecture_end,
            'hash_file_name' => $hashFileName,
            'original_file_name' => $originalFileName,
            'category_id' => $request->category_id ?? $lecture->category_id,
        ]);

        return $this->respondWithSuccess();
    }

    public function destroy(LectureDestroy $request, Lecture $lecture): JsonResponse
    {
        $user = $request->user();

        $hashFileName = $lecture->hash_file_name;
        if (isset($hashFileName)) {
            $this->lectureService->deletePresentation($hashFileName);
        }

        if (isset($lecture->zoom_meeting_id)) {
            $this->zoomService->deleteMeeting($lecture->zoom_meeting_id);
        }

        $lectureUser = $lecture->user;
        $lectureConference = $lecture->conference;

        $lecture->delete();

        if ($user->id !== $lecture->user_id && $user->type === User::ADMIN_TYPE) {
            SendAdminDeletedLectureEmail::dispatch($lectureUser, $lectureConference)->onQueue('emails');
        }

        return $this->respondWithSuccess();
    }

    public function toFavorites(LectureToFavorites $request, Lecture $lecture): JsonResponse
    {
        $user = $request->user();
        $user->favoriteLectures()->attach($lecture->id);

        return $this->respondWithSuccess();
    }

    public function removeFromFavorites(LectureRemoveFromFavorites $request, Lecture $lecture): JsonResponse
    {
        $user = $request->user();
        $user->favoriteLectures()->detach($lecture->id);

        return $this->respondWithSuccess();
    }
}
