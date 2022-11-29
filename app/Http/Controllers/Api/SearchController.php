<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\Search\Index as SearchIndex;

use App\Models\Conference;
use App\Models\Lecture;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

use Carbon\Carbon;

class SearchController extends Controller
{
    use ApiResponseHelpers;

    public function index(SearchIndex $request): JsonResponse
    {
        $result = collect([]);
        $now = Carbon::now()->format('Y-m-d H:i:s');

        if (!$request->has('type') || $request->type === Conference::TYPE) {
            $conferences = Conference::where('conferences.title', 'LIKE', '%'. $request->search_term .'%')
                ->whereRaw('ADDTIME(`conferences`.`date`, `conferences`.`day_end`) >= ?', $now)
                ->get();
            $conferences->transform(function ($value) {
                $value->type = Conference::TYPE;
                return $value;
            });

            $result = $result->merge($conferences);
        }

        if (!$request->has('type') || $request->type === Lecture::TYPE) {
            $lectures = Lecture::select('lectures.*')    
                ->join('conferences', 'lectures.conference_id', '=', 'conferences.id')
                ->where('lectures.title', 'LIKE', '%'. $request->search_term .'%')
                ->whereRaw('ADDTIME(`conferences`.`date`, `conferences`.`day_end`) >= ?', $now)
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
