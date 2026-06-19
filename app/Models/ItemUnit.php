<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'serial_number',
        'inventory_code',
        'condition',
        'status',
    ];

    protected $casts = [
        'condition' => 'string',
        'status' => 'string',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

   
}
