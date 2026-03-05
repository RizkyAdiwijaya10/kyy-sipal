<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Item;

class SumberDana extends Model
{
    use HasFactory;
    protected $table = 'funding_sources';

    protected $fillable = [
        'name',
        'code',
        'year',
        'description',
    ];

    public function items()
    {
        return $this->hasMany(Item::class, 'funding_source_id');
    }
}
