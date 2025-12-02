<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImpactTracking extends Model
{
    protected $guarded = ['id'];
    
    public function donation() {
        return $this->belongsTo(Donation::class);
    }
}