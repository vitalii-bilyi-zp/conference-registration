<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConferenceController;
use App\Http\Controllers\Api\LectureController;

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

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
});

Route::get('/conferences', [ConferenceController::class, 'index']);
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/conferences', [ConferenceController::class, 'store']);
    Route::get('/conferences/{conference}', [ConferenceController::class, 'show']);
    Route::put('/conferences/{conference}', [ConferenceController::class, 'update']);
    Route::delete('/conferences/{conference}', [ConferenceController::class, 'destroy']);
    Route::post('/conferences/{conference}/participate', [ConferenceController::class, 'participate']);
    Route::post('/conferences/{conference}/cancel-participation', [ConferenceController::class, 'cancelParticipation']);
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/lectures', [LectureController::class, 'index']);
    Route::post('/lectures', [LectureController::class, 'store']);
    Route::get('/lectures/{lecture}', [LectureController::class, 'show']);
    Route::put('/lectures/{lecture}', [LectureController::class, 'update']);
    Route::delete('/lectures/{lecture}', [LectureController::class, 'destroy']);
});
