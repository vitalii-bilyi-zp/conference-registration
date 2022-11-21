<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConferenceController;
use App\Http\Controllers\Api\LectureController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// auth
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::put('/auth/user', [AuthController::class, 'updateUser']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
});

// conferences
Route::get('/conferences', [ConferenceController::class, 'index']);
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/conferences', [ConferenceController::class, 'store']);
    Route::get('/conferences/{conference}', [ConferenceController::class, 'show']);
    Route::put('/conferences/{conference}', [ConferenceController::class, 'update']);
    Route::delete('/conferences/{conference}', [ConferenceController::class, 'destroy']);
    Route::post('/conferences/{conference}/participate', [ConferenceController::class, 'participate']);
    Route::post('/conferences/{conference}/cancel-participation', [ConferenceController::class, 'cancelParticipation']);
});

// lectures
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/lectures', [LectureController::class, 'index']);
    Route::post('/lectures', [LectureController::class, 'store']);
    Route::get('/lectures/{lecture}', [LectureController::class, 'show']);
    Route::put('/lectures/{lecture}', [LectureController::class, 'update']);
    Route::delete('/lectures/{lecture}', [LectureController::class, 'destroy']);
    Route::post('/lectures/{lecture}/to-favorites', [LectureController::class, 'toFavorites']);
    Route::post('/lectures/{lecture}/remove-from-favorites', [LectureController::class, 'removeFromFavorites']);
});

// comments
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/comments', [CommentController::class, 'index']);
    Route::post('/comments', [CommentController::class, 'store']);
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
});

// categories
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
});
