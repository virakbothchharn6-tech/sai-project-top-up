# Session 7: Admin Dashboard & Order Management

## Overview
Build a secured administrative panel to manage store inventory, game listings, package pricing, and track incoming orders.

## Objectives
- Create admin authentication middleware.
- Build a dashboard interface for managing games and packages.
- Display customer transaction logs and payment verification statuses.

---

## Step 1 — Add authentication with Laravel Sanctum

```bash
docker-compose exec backend composer require laravel/sanctum
docker-compose exec backend php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
docker-compose exec backend php artisan migrate
```

## Step 2 — Create an `is_admin` flag on users

```bash
docker-compose exec backend php artisan make:migration add_is_admin_to_users_table
```
```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->boolean('is_admin')->default(false);
    });
}
```

## Step 3 — Build the admin middleware

```bash
docker-compose exec backend php artisan make:middleware IsAdmin
```
`app/Http/Middleware/IsAdmin.php`:
```php
public function handle(Request $request, Closure $next)
{
    if (! $request->user() || ! $request->user()->is_admin) {
        return response()->json(['message' => 'Forbidden'], 403);
    }
    return $next($request);
}
```
Register it in `bootstrap/app.php` (or `Kernel.php` for older Laravel):
```php
$middleware->alias(['admin' => \App\Http\Middleware\IsAdmin::class]);
```

## Step 4 — Build admin API routes

`routes/api.php`:
```php
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::apiResource('games', Admin\GameManageController::class);
    Route::apiResource('packages', Admin\PackageManageController::class);
    Route::get('transactions', [Admin\TransactionController::class, 'index']);
});
```

## Step 5 — Build the transaction log endpoint

`app/Http/Controllers/Admin/TransactionController.php`:
```php
class TransactionController extends Controller
{
    public function index()
    {
        return Transaction::with(['game', 'package'])
            ->latest()
            ->paginate(20);
    }
}
```

## Step 6 — Build a simple admin dashboard page (Vue)

`frontend/src/pages/admin/Dashboard.vue`:
```vue
<script setup>
import { ref, onMounted } from "vue";
import api from "../../axios";

const transactions = ref([]);

onMounted(async () => {
  const res = await api.get("/admin/transactions");
  transactions.value = res.data.data;
});
</script>

<template>
  <table>
    <thead>
      <tr><th>Reference</th><th>Game</th><th>Package</th><th>Status</th></tr>
    </thead>
    <tbody>
      <tr v-for="t in transactions" :key="t.id">
        <td>{{ t.transaction_reference }}</td>
        <td>{{ t.game.name }}</td>
        <td>{{ t.package.name }}</td>
        <td>{{ t.status }}</td>
      </tr>
    </tbody>
  </table>
</template>
```

## ✅ Result
Admins can now log in and view/manage games, packages, and all customer transactions — including KHQR payment statuses — from a protected dashboard.
