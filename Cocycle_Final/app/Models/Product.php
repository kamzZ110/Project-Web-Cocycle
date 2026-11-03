<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getImageAttribute($value)
    {
        return \App\Support\AssetPath::local($value, 'assets/default_product.png');
    }


    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
