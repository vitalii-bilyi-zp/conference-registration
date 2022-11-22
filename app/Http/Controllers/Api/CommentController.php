<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\Comment\Index as CommentIndex;
use App\Http\Requests\Comment\Store as CommentStore;
use App\Http\Requests\Comment\Update as CommentUpdate;

use App\Models\Comment;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Builder;

class CommentController extends Controller
{
    use ApiResponseHelpers;

    public function index(CommentIndex $request): JsonResponse
    {
        $comments = Comment::query()
            ->when($request->get('lecture_id'), function(Builder $query, $lectureId) {
                $query->where('lecture_id', '=', $lectureId);
            })
            ->paginate(15);

        $comments->getCollection()->transform(function ($value) {
            $commentAuthor = $value->user;
            $value->user_firstname = $commentAuthor->firstname;
            $value->user_lastname = $commentAuthor->lastname;
            unset($value->user);

            return $value;
        });

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($comments);
    }

    public function store(CommentStore $request): JsonResponse
    {
        $user = $request->user();
        Comment::create([
            'content' => $request->content,
            'lecture_id' => $request->lecture_id,
            'user_id' => $user->id,
        ]);

        return $this->respondWithSuccess();
    }

    public function update(CommentUpdate $request, Comment $comment): JsonResponse
    {
        $comment->update([
            'content' => $request->content,
        ]);

        return $this->respondWithSuccess();
    }
}
