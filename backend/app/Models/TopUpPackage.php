<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- please add this line to import the BelongsTo class

class TopUpPackage extends Model
{
    protected $guarded = [];

    // add a relationship to the Game model
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
