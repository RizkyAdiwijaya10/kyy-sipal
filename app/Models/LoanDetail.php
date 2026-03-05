<?php
// app/Models/LoanDetail.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'item_unit_id',
        'condition_before',
        'condition_after',
        'notes',
    ];

    protected $casts = [
        'condition_before' => 'string',
        'condition_after' => 'string',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function itemUnit()
    {
        return $this->belongsTo(ItemUnit::class);
    }
}