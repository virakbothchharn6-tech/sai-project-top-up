<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;

class TopUpPackageController extends Controller
{
    public function index(Game $game)
    {
        return $game->packages;
    }
}