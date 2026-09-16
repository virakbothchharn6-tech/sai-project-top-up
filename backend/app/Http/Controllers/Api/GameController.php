<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index()
    {
        return Game::where('status', true)->get();
    }

    public function show(string $slug)
    {
        return Game::where('slug', $slug)
            ->with('packages')
            ->firstOrFail();
    }
}