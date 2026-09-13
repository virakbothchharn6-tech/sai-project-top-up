<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Game;
use App\Models\TopUpPackage;

class GameSeeder extends Seeder
{
    public function run(): void
    {
        // create a game
        $mlbb = Game::create([
            'name' => 'Mobile Legends',
            'slug' => 'mobile-legends',
            'image' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/react/react-original.svg' // put the actual image URL for Mobile Legends here
        ]);

        // create top-up packages for Mobile Legends
        TopUpPackage::create(['game_id' => $mlbb->id, 'title' => '100 Diamonds', 'price' => 0.99, 'diamonds' => 100]);
        TopUpPackage::create(['game_id' => $mlbb->id, 'title' => '500 Diamonds', 'price' => 4.99, 'diamonds' => 500]);

        // create another game and its top-up packages
        $pubg = Game::create([
            'name' => 'PUBG Mobile',
            'slug' => 'pubg-mobile',
            'image' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vuejs/vuejs-original.svg'
        ]);

        // create top-up packages for PUBG Mobile
        TopUpPackage::create(['game_id' => $pubg->id, 'title' => '60 UC', 'price' => 0.99, 'diamonds' => 60]);
        TopUpPackage::create(['game_id' => $pubg->id, 'title' => '300 UC', 'price' => 4.99, 'diamonds' => 300]);
    }
}
