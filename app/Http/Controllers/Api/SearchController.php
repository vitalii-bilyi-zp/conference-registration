<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\Search\Index as SearchIndex;

use App\Models\Conference;
use App\Models\Lecture;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    use ApiResponseHelpers;
    
    public function index(SearchIndex $request): JsonResponse
    {
        $result = collect([]);

        if (!$request->has('type') || $request->type === Conference::TYPE) {
            $conferences = Conference::query()
                ->where('title', 'LIKE', '%'. $request->search_term .'%')
                ->get();
            $conferences->transform(function ($value) {
                $value->type = Conference::TYPE;
                return $value;
            });

            $result = $result->merge($conferences);
        }

        if (!$request->has('type') || $request->type === Lecture::TYPE) {
            $lectures = Lecture::query()
                ->where('title', 'LIKE', '%'. $request->search_term .'%')
                ->get();
            $lectures->transform(function ($value) {
                $value->type = Lecture::TYPE;
                return $value;
            });

            $result = $result->merge($lectures);
        }

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($result);
    }
}
