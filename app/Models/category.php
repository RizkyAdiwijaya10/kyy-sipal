<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Item;

class category extends Model
{
    use HasFactory;

    protected $table = 'categories'; 

    protected $fillable = [
        'name',
        'description'
    ];

    public function items()
    {
        return $this->hasMany(Item::class, 'category_id');
    }
}
