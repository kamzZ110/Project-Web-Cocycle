<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodItem extends Model
{
    use HasFactory;
    
    // Agar kolom 'tags' yang isinya JSON bisa langsung dibaca sebagai Array
    protected $casts = [
        'tags' => 'array',
        'quantity_available' => 'float',
        'handling_fee' => 'float',
    ];

    protected $guarded = ['id'];
}