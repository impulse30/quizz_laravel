<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\LeaderboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Publicly accessible authentication routes
// These routes allow new users to register and existing users to log in.
Route::post('/register', [AuthController::class, 'register'])->name('register'); // User registration endpoint
Route::post('/login',    [AuthController::class, 'login'])->name('login');       // User login endpoint

// Routes requiring user authentication (using Sanctum for token-based auth)
Route::middleware('auth:sanctum')->group(function () {
    // Endpoint to get the currently authenticated user's details
    Route::get('/user', function (Request $request) {
        return $request->user(); // Returns the authenticated user model
    })->name('user.me'); // Named route for easy URL generation

    // Routes for quiz functionality (accessible by any authenticated user, e.g., 'player' or 'creator')
    Route::get('quiz/start', [QuizController::class, 'start'])->name('quiz.start');       // Fetches questions to start a new quiz
    Route::post('quiz/submit', [QuizController::class, 'submit'])->name('quiz.submit');     // Submits quiz answers and calculates the score
    Route::get('leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index'); // Displays the game leaderboard

    // User logout endpoint
    // This invalidates the user's current API token.
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Routes restricted to users with the 'creator' role
    // This group uses an additional 'creator' middleware to ensure only authorized users can manage content.
    Route::middleware('creator')->group(function () {
        // CRUD operations for categories (e.g., create, read, update, delete categories)
        // `apiResource` automatically maps to standard RESTful controller methods.
        Route::apiResource('categories', CategoryController::class);

        // CRUD operations for questions
        Route::apiResource('questions', QuestionController::class);
    });
});
