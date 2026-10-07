<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_code', 'game_id', 'player_id', 'server_id',
        'username', 'package_name', 'price', 'status',
        'khqr', 'khqr_md5', 'expires_at', 'paid_at', 'bakong_hash',
    ];

    protected $casts = [
        'price'      => 'decimal:2',
        'expires_at' => 'datetime',
        'paid_at'    => 'datetime',
    ];

    // កុំបញ្ជូន md5/hash ទៅ frontend
    protected $hidden = ['khqr_md5', 'bakong_hash'];
}