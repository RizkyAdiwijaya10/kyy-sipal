<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'brand',
        'model',
        'specification',
        // 'total_stock',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function itemUnits()
    {
        return $this->hasMany(ItemUnit::class, 'item_id');
    }

    
}
