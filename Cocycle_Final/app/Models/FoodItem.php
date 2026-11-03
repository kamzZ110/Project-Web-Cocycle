<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodItem extends Model
{
    protected $table = 'food_items';
    protected $guarded = [];
    public $timestamps = false; // Legacy table likely has no updated_at

    // Accessors to map legacy column names to View expectations
    public function getPriceAttribute()
    {
        return $this->attributes['handling_fee'];
    }

    public function getImageAttribute()
    {
        return \App\Support\AssetPath::local($this->attributes['image_url'] ?? null, 'assets/default_product.png');
    }
}
