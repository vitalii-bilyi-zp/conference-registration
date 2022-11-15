<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\Conference\Index as ConferenceIndex;
use App\Http\Requests\Conference\Store as ConferenceStore;
use App\Http\Requests\Conference\Show as ConferenceShow;
use App\Http\Requests\Conference\Update as ConferenceUpdate;
use App\Http\Requests\Conference\Destroy as ConferenceDestroy;

use App\Models\Conference;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

class ConferenceController extends Controller
{
    use ApiResponseHelpers;

    public function index(ConferenceIndex $request): JsonResponse
    {
        $conferences = Conference::paginate(15);

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
        ]);

        return $this->respondWithSuccess();
    }

    public function show(ConferenceShow $request, Conference $conference): JsonResponse
    {
        return $this->setDefaultSuccessResponse([])->respondWithSuccess($conference);
    }

    public function update(ConferenceUpdate $request, Conference $conference): JsonResponse
    {
        $conference->update([
            'title' => $request->title ?? $conference->title,
            'date' => $request->date ?? $conference->date,
            'latitude' => $request->latitude ?? $conference->latitude,
            'longitude' => $request->longitude ?? $conference->longitude,
            'country_id' => $request->country_id ?? $conference->country_id,
        ]);

        return $this->respondWithSuccess();
    }

    public function destroy(ConferenceDestroy $request, Conference $conference): JsonResponse
    {
        $conference->delete();

        return $this->respondWithSuccess();
    }
}
