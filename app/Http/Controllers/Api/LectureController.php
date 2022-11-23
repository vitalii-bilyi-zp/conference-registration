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

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Builder;

use App\Services\LectureService;
use App\Services\CategoryService;

class LectureController extends Controller
{
    use ApiResponseHelpers;

    protected $lectureService;
    protected $categoryService;

    public function __construct(LectureService $lectureService, CategoryService $categoryService)
    {
        $this->lectureService = $lectureService;
        $this->categoryService = $categoryService;
    }

    public function index(LectureIndex $request): JsonResponse
    {
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

        $user = $request->user();
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

        $user = $request->user();

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
        ]);

        return $this->respondWithSuccess();
    }

    public function show(LectureShow $request, Lecture $lecture): JsonResponse
    {
        if (isset($lecture->hash_file_name)) {
            $lecture->file_path = $this->lectureService->getPresentationPath($lecture->hash_file_name);
        }

        $user = $request->user();
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
        $hashFileName = $lecture->hash_file_name;

        if (isset($hashFileName)) {
            $this->lectureService->deletePresentation($hashFileName);
        }

        $lecture->delete();

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
