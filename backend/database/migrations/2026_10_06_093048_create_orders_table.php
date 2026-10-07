<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->string('game_id');
            $table->string('player_id');
            $table->string('server_id')->nullable();
            $table->string('username')->nullable();
            $table->string('package_name');
            $table->decimal('price', 10, 2);
            // pending | paid | completed | expired | cancelled
            $table->string('status')->default('pending');

            // Bakong KHQR data
            $table->text('khqr')->nullable();
            $table->string('khqr_md5', 32)->nullable()->index();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('bakong_hash')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};