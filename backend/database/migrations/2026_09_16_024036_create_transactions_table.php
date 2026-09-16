<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
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
};