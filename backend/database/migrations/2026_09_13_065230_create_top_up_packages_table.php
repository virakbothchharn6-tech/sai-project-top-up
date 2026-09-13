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
    Schema::create('top_up_packages', function (Blueprint $table) {
        $table->id();
        $table->foreignId('game_id')->constrained()->onDelete('cascade'); // foreign key to the games table
        $table->string('title');       // name of the package
        $table->decimal('price', 8, 2); // price of the package
        $table->integer('diamonds');   // number of diamonds
        $table->timestamps();
    });
}
};
