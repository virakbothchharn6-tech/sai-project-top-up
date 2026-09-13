<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    protected $guarded = [];

    // add a relationship to the TopUpPackage model
    public function topUpPackages(): HasMany
    {
        return $this->hasMany(TopUpPackage::class);
    }
}
