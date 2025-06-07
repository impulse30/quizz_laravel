<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'creator'])->group(function () {
    Route::apiResource('categories', \App\Http\Controllers\CategoryController::class);
    Route::apiResource('questions', \App\Http\Controllers\QuestionController::class);
});
Route::middleware('auth:sanctum')->group(function (){

    Route::get('quiz/start', [\App\Http\Controllers\QuizController::class, 'start']);
    Route::post('quiz/submit', [\App\Http\Controllers\QuizController::class, 'submit']);
    Route::get('leaderboard', [\App\Http\Controllers\LeaderboardController::class, 'index']);



});




Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);
Route::post('/login',    [\App\Http\Controllers\AuthController::class, 'login']);
