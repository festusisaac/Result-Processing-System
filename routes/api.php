<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\ScratchCardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Student routes
    Route::apiResource('students', StudentController::class);

    // Score routes
    Route::get('/scores/students', [ScoreController::class, 'getStudentsWithScores']);
    Route::post('/scores/store-bulk', [ScoreController::class, 'storeBulk']);
    Route::post('/scores/import', [ScoreController::class, 'import']);
    Route::get('/scores/export', [ScoreController::class, 'export']);
    Route::get('/results', [ScoreController::class, 'generateResults']);
    Route::apiResource('scores', ScoreController::class);

    // Scratch card routes
    Route::prefix('scratch-cards')->group(function () {
        Route::get('/', [ScratchCardController::class, 'index']);
        Route::post('/generate', [ScratchCardController::class, 'generate']);
        Route::post('/redeem', [ScratchCardController::class, 'redeem']);
        Route::put('/{card}/sell', [ScratchCardController::class, 'sell']);
    });
});