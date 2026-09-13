<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Game;
use App\Models\TopUpPackage;

Route::get('/games', function () {
    try {
        return response()->json([
            'success' => true,
            'data' => Game::with('topUpPackages')->get()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}); // close the route definition with a semicolon

Route::get('/top-up-packages', function () {
    return response()->json([
        'success' => true,
        'data' => TopUpPackage::with('game')->get()
    ]);
});