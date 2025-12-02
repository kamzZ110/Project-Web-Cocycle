<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $guarded = ['id'];

    public function details() {
        return $this->hasMany(DonationDetail::class);
    }
    
    public function community() {
        return $this->belongsTo(Community::class);
    }
    
    // Relasi ke Impact Tracking
    public function impact() {
        return $this->hasOne(ImpactTracking::class);
    }
}