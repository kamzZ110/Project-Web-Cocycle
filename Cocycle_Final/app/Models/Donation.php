<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $guarded = [];
    // public $timestamps = false; 

    public function community()
    {
        return $this->belongsTo(Community::class);
    }
}
