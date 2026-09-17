<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\TopUpPackageController;
use App\Http\Controllers\Api\AccountCheckController;
use App\Models\Game;
use App\Models\TopUpPackage;

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

// --- Routes request data game from controller
Route::get('/games', [GameController::class, 'index']);
Route::get('/games/{slug}', [GameController::class, 'show']);
Route::get('/games/{game}/packages', [TopUpPackageController::class, 'index']);

// --- Route for check account (អនុញ្ញាតទាំង /check-account និង /check-player ទៅ Controller តែមួយ)
Route::post('/check-account', [AccountCheckController::class, 'check']);
Route::post('/check-player', [AccountCheckController::class, 'check']);

// --- Inline Closure Routes test data
Route::get('/v1/games', function () {
    try {
        return response()->json([
            'success' => true,
            'data' => Game::with('packages')->get()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
});

Route::get('/v1/top-up-packages', function () {
    return response()->json([
        'success' => true,
        'data' => TopUpPackage::with('game')->get()
    ]);
});