# Session 3: RESTful API Development & Game ID Verification

## Overview
Build the backend API controllers and routes in Laravel to serve game lists, packages, and handle account ID validation logic.

## Objectives
- Create API routes in `routes/api.php`.
- Build `GameController` and `TopUpPackageController`.
- Implement a mock verification endpoint to check account usernames based on Game ID and Zone ID.

---

## Step 1 — Generate the controllers

```bash
docker-compose exec backend php artisan make:controller Api/GameController
docker-compose exec backend php artisan make:controller Api/TopUpPackageController
docker-compose exec backend php artisan make:controller Api/AccountCheckController
```

## Step 2 — Build `GameController`

`app/Http/Controllers/Api/GameController.php`:
```php
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
```

## Step 3 — Build `TopUpPackageController`

`app/Http/Controllers/Api/TopUpPackageController.php`:
```php
class TopUpPackageController extends Controller
{
    public function index(Game $game)
    {
        return $game->packages;
    }
}
```

## Step 4 — Build the account verification endpoint

This endpoint receives the **Game ID** (and **Zone ID** if required by the game) and returns the matched in-game username — this powers the "Check Name" feature on the frontend.

`app/Http/Controllers/Api/AccountCheckController.php`:
```php
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
```

## Step 5 — Register the API routes

`routes/api.php`:
```php
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\TopUpPackageController;
use App\Http\Controllers\Api\AccountCheckController;

Route::get('/games', [GameController::class, 'index']);
Route::get('/games/{slug}', [GameController::class, 'show']);
Route::get('/games/{game}/packages', [TopUpPackageController::class, 'index']);
Route::post('/check-account', [AccountCheckController::class, 'check']);
```

## Step 6 — Test the API

```bash
curl http://localhost:8000/api/games
curl -X POST http://localhost:8000/api/check-account \
  -H "Content-Type: application/json" \
  -d '{"game_id":"mobile-legends","user_id":"123456789","zone_id":"2001"}'
```

## ✅ Result
The backend now exposes a working REST API for listing games, packages, and verifying a player's Game ID / Zone ID — ready to be consumed by the Vue.js frontend in the next session.
