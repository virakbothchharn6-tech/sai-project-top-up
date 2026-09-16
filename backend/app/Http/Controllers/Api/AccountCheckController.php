<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountCheckController extends Controller
{
    public function check(Request $request)
    {
        $request->validate([
            'game_id'    => 'required|string',
            'user_id'    => 'required|string',
            'zone_id'    => 'nullable|string',
        ]);

        // NOTE: In production this calls the real game provider API.
        // For development we mock the response.
        $mockUsername = 'Player_' . substr($request->user_id, -4);

        return response()->json([
            'valid'    => true,
            'username' => $mockUsername,
        ]);
    }
}