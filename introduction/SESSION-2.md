# Session 2: Database Design & Models (Games & Top-Up Packages)

## Overview
Design and migrate the relational database schema required to support multi-game top-ups, account verification fields, and pricing packages.

## Objectives
- Create database migrations for `games`, `top_up_packages`, and `transactions`.
- Establish Eloquent relationships between games and their respective packages.
- Implement database seeders with sample data (Mobile Legends, PUBG Mobile, Free Fire, etc.).

---

## Step 1 — Install Laravel inside the backend container

```bash
docker-compose exec backend composer create-project laravel/laravel . 
cp backend/.env.example backend/.env
docker-compose exec backend php artisan key:generate
```

Update `backend/.env` database connection to match Docker Compose:
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=sai_top_up
DB_USERNAME=root
DB_PASSWORD=root
```

## Step 2 — Create the migrations

```bash
docker-compose exec backend php artisan make:migration create_games_table
docker-compose exec backend php artisan make:migration create_top_up_packages_table
docker-compose exec backend php artisan make:migration create_transactions_table
```

`database/migrations/xxxx_create_games_table.php`:
```php
public function up(): void
{
    Schema::create('games', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('slug')->unique();
        $table->string('thumbnail')->nullable();
        $table->boolean('requires_zone_id')->default(false);
        $table->boolean('status')->default(true);
        $table->timestamps();
    });
}
```

`database/migrations/xxxx_create_top_up_packages_table.php`:
```php
public function up(): void
{
    Schema::create('top_up_packages', function (Blueprint $table) {
        $table->id();
        $table->foreignId('game_id')->constrained()->onDelete('cascade');
        $table->string('name');
        $table->integer('diamonds_amount')->nullable();
        $table->integer('bonus_amount')->default(0);
        $table->decimal('price_usd', 8, 2);
        $table->decimal('price_khr', 10, 0);
        $table->timestamps();
    });
}
```

`database/migrations/xxxx_create_transactions_table.php`:
```php
public function up(): void
{
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->string('transaction_reference')->unique();
        $table->foreignId('game_id')->constrained();
        $table->string('user_game_id');
        $table->string('user_zone_id')->nullable();
        $table->string('account_username')->nullable();
        $table->foreignId('package_id')->constrained('top_up_packages');
        $table->string('payment_method')->default('khqr');
        $table->string('status')->default('pending'); // pending, success, failed
        $table->timestamps();
    });
}
```

## Step 3 — Create Eloquent models with relationships

```bash
docker-compose exec backend php artisan make:model Game
docker-compose exec backend php artisan make:model TopUpPackage
docker-compose exec backend php artisan make:model Transaction
```

`app/Models/Game.php`:
```php
class Game extends Model
{
    protected $fillable = ['name', 'slug', 'thumbnail', 'requires_zone_id', 'status'];

    public function packages()
    {
        return $this->hasMany(TopUpPackage::class);
    }
}
```

`app/Models/TopUpPackage.php`:
```php
class TopUpPackage extends Model
{
    protected $fillable = ['game_id', 'name', 'diamonds_amount', 'bonus_amount', 'price_usd', 'price_khr'];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
```

## Step 4 — Create seeders with sample data

```bash
docker-compose exec backend php artisan make:seeder GameSeeder
docker-compose exec backend php artisan make:seeder TopUpPackageSeeder
```

`database/seeders/GameSeeder.php`:
```php
public function run(): void
{
    Game::insert([
        ['name' => 'Mobile Legends', 'slug' => 'mobile-legends', 'requires_zone_id' => true, 'status' => true],
        ['name' => 'PUBG Mobile', 'slug' => 'pubg-mobile', 'requires_zone_id' => false, 'status' => true],
        ['name' => 'Free Fire', 'slug' => 'free-fire', 'requires_zone_id' => false, 'status' => true],
    ]);
}
```

`database/seeders/TopUpPackageSeeder.php`:
```php
public function run(): void
{
    $ml = Game::where('slug', 'mobile-legends')->first();

    TopUpPackage::insert([
        ['game_id' => $ml->id, 'name' => '86 Diamonds', 'diamonds_amount' => 86, 'price_usd' => 1.99, 'price_khr' => 8000],
        ['game_id' => $ml->id, 'name' => '172 Diamonds', 'diamonds_amount' => 172, 'price_usd' => 3.99, 'price_khr' => 16000],
    ]);
}
```

## Step 5 — Run migrations and seeders

```bash
docker-compose exec backend php artisan migrate --seed
```

## ✅ Result
The database now supports multiple games, each with their own top-up packages, and a `transactions` table ready to store Game ID / Zone ID verification and payment status for the next sessions.
