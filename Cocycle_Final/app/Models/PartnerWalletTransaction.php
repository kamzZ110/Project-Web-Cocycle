<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerWalletTransaction extends Model
{
    protected $guarded = [];

    protected $casts = ['amount' => 'decimal:2'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
