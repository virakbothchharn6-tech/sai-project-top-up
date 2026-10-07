<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\TopUpPackageController;
use App\Http\Controllers\Api\AccountCheckController;
use App\Http\Controllers\Api\OrderController;
use App\Models\Game;
use App\Models\TopUpPackage;

// --- Game data
Route::get('/games', [GameController::class, 'index']);
Route::get('/games/{slug}', [GameController::class, 'show']);
Route::get('/games/{game}/packages', [TopUpPackageController::class, 'index']);

// --- Check player account (both URLs use the same controller)
Route::post('/check-account', [AccountCheckController::class, 'check']);
Route::post('/check-player', [AccountCheckController::class, 'check']);

// --- Orders + Bakong KHQR payment
Route::middleware('throttle:60,1')->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{orderCode}/status', [OrderController::class, 'status']);
});

// --- Inline closure routes (test data)
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