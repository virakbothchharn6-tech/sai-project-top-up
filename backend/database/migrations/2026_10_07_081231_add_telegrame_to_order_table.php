<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('claimed_at')->nullable();           // when the customer pressed "I have paid"
            $table->string('telegram_message_id')->nullable();     // the message sent to the admin
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['claimed_at', 'telegram_message_id']);
        });
    }
};