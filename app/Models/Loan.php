<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_code',
        'user_id',
        'loan_date',
        'return_date',
        'actual_return_date',
        'purpose',
        'surat_path',
        'status',
        'notes',
        'approved_by',
        'approved_at',
        'return_requested_at',
        'return_request_notes',
        'return_request_status',
        'return_approved_by',
        'return_approved_at',
    ];

    protected $casts = [
        'loan_date' => 'date',
        'return_date' => 'date',
        'actual_return_date' => 'date',
        'approved_at' => 'datetime',
        'return_requested_at' => 'datetime',
        'return_approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function details()
    {
        return $this->hasMany(LoanDetail::class);
    }

    public function itemUnits()
    {
        return $this->belongsToMany(ItemUnit::class, 'loan_details')
                    ->withPivot('condition_before', 'condition_after', 'notes')
                    ->withTimestamps();
    }

    public static function generateLoanCode()
    {
        $year = date('Y');
        $month = date('m');
        $lastLoan = self::whereYear('created_at', $year)
                        ->whereMonth('created_at', $month)
                        ->lockForUpdate()
                        ->orderBy('id', 'desc')
                        ->first();

        if ($lastLoan) {
            $lastNumber = intval(substr($lastLoan->loan_code, -3));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return 'PJM/' . $year . '/' . $month . '/' . $newNumber;
    }

    public function isOverdue()
    {
        return $this->status == 'borrowed' && now()->startOfDay() > $this->return_date;
    }

    public function canRequestReturn()
    {
        return $this->status === 'borrowed' && 
               is_null($this->return_requested_at) && 
               is_null($this->actual_return_date);
    }

    public function isReturnRequestPending()
    {
        return $this->return_request_status === 'pending';
    }
}