<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\category;
use App\Models\SumberDana;
use App\Models\itemUnits;
use PhpOffice\PhpSpreadsheet\Calculation\Category as CalculationCategory;

class Item extends Model
{
    protected $fillable = [
        'category_id',
        'funding_source_id',
        'name',
        'brand',
        'model',
        'specification',
        // 'total_stock',
    ];

    public function category()
    {
        return $this->belongsTo(category::class);
    }

    public function fundingSource()
    {
        return $this->belongsTo(SumberDana::class);
    }

    public function itemUnits()
    {
        return $this->hasMany(ItemUnit::class, 'item_id');
    }

    public function getAvailableUnitsAttribute()
    {
        return $this->itemUnits()->where('status', 'tersedia')->count();
    }

    public function getBorrowedUnitsAttribute()
    {
        return $this->itemUnits()->where('status', 'dipinjam')->count();
    }
}
