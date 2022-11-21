<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\Conference\Index as ConferenceIndex;
use App\Http\Requests\Conference\Store as ConferenceStore;
use App\Http\Requests\Conference\Show as ConferenceShow;
use App\Http\Requests\Conference\Update as ConferenceUpdate;
use App\Http\Requests\Conference\Destroy as ConferenceDestroy;
use App\Http\Requests\Conference\Participate as ConferenceParticipate;
use App\Http\Requests\Conference\CancelParticipation as ConferenceCancelParticipation;

use App\Models\Conference;
use App\Models\Country;
use App\Models\Lecture;
use App\Models\Category;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Builder;

use App\Services\LectureService;
use App\Services\CategoryService;

class ConferenceController extends Controller
{
    use ApiResponseHelpers;

    protected $lectureService;
    protected $categoryService;

    public function __construct(LectureService $lectureService, CategoryService $categoryService)
    {
        $this->lectureService = $lectureService;
        $this->categoryService = $categoryService;
    }

    public function index(ConferenceIndex $request): JsonResponse
    {
        $conferences = Conference::with('country')
            ->when($request->get('category_id'), function(Builder $query) use (&$request) {
                // $category = Category::find($request->get('category_id'));
                // $allowedCategoryIds = $this->categoryService->getSubcategoryIdsRecursively($category);
                // $query->whereIn('category_id', $allowedCategoryIds);

                $query->where('category_id', '=', $request->get('category_id'));
            })
            ->paginate(15);
        $conferences = $conferences->getCollection()->transform(function ($value) {
            $value->has_free_time = $this->lectureService->checkConferenceFreeTime($value, Lecture::MIN_DURATION);
            return $value;
        });

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($conferences);
    }

    public function store(ConferenceStore $request): JsonResponse
    {
        Conference::create([
            'title' => $request->title,
            'date' => $request->date,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'country_id' => $request->country_id,
            'category_id' => $request->category_id,
        ]);

        return $this->respondWithSuccess();
    }

    public function show(ConferenceShow $request, Conference $conference): JsonResponse
    {
        if (isset($conference->country_id)) {
            $conference->country = Country::find($conference->country_id);
        }

        $conference->has_free_time = $this->lectureService->checkConferenceFreeTime($conference, Lecture::MIN_DURATION);

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($conference);
    }

    public function update(ConferenceUpdate $request, Conference $conference): JsonResponse
    {
        $originalCategoryId = $conference->category_id;

        $conference->update([
            'title' => $request->title ?? $conference->title,
            'date' => $request->date ?? $conference->date,
            'latitude' => $request->latitude ?? $conference->latitude,
            'longitude' => $request->longitude ?? $conference->longitude,
            'country_id' => $request->country_id ?? $conference->country_id,
            'category_id' => $request->category_id ?? $conference->category_id,
        ]);

        if (isset($request->category_id) && $request->category_id !== $originalCategoryId) {
            $conference->lectures()->update([
                'category_id' => null
            ]);
        }

        return $this->respondWithSuccess();
    }

    public function destroy(ConferenceDestroy $request, Conference $conference): JsonResponse
    {
        $conference->delete();

        return $this->respondWithSuccess();
    }

    public function participate(ConferenceParticipate $request, Conference $conference): JsonResponse
    {
        $user = $request->user();
        $conference->users()->attach($user->id);

        return $this->respondWithSuccess();
    }

    public function cancelParticipation(ConferenceCancelParticipation $request, Conference $conference): JsonResponse
    {
        $user = $request->user();
        $conference->users()->detach($user->id);

        if (!$user->isAnnouncer()) {
            return $this->respondWithSuccess();
        }

        $existingLecture = Lecture::where([
            ['user_id', '=', $user->id],
            ['conference_id', '=', $conference->id],
        ])->first();

        if (isset($existingLecture)) {
            $hashFileName = $existingLecture->hash_file_name;

            if (isset($hashFileName)) {
                $this->lectureService->deletePresentation($hashFileName);
            }

            $existingLecture->delete();
        }

        return $this->respondWithSuccess();
    }
}
