<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
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

    public function scopeAvailable($query)
    {
        return $query->where('status', 'tersedia');
    }

    public function scopeBorrowed($query)
    {
        return $query->where('status', 'dipinjam');
    }

    public function scopeGoodCondition($query)
    {
        return $query->where('condition', 'baik');
    }

    public function getConditionBadgeAttribute()
    {
        $badges = [
            'baik' => 'success',
            'rusak' => 'danger',
            'maintenance' => 'warning',
            'hilang' => 'dark',
        ];

        return $badges[$this->condition] ?? 'secondary';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'tersedia' => 'success',
            'dipinjam' => 'info',
            'dipesan' => 'warning',
            'nonaktif' => 'danger',
        ];

        return $badges[$this->status] ?? 'secondary';
    }
}
