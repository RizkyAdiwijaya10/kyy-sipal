<?php
// app/Models/Loan.php

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
        'status',
        'notes',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'loan_date' => 'date',
        'return_date' => 'date',
        'actual_return_date' => 'date',
        'approved_at' => 'datetime',
    ];

    // Relasi ke user peminjam
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke user yang menyetujui
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Relasi ke detail peminjaman
    public function details()
    {
        return $this->hasMany(LoanDetail::class);
    }

    // Relasi ke unit barang melalui detail
    public function itemUnits()
    {
        return $this->belongsToMany(ItemUnit::class, 'loan_details')
                    ->withPivot('condition_before', 'condition_after', 'notes')
                    ->withTimestamps();
    }

    // Generate kode peminjaman
    public static function generateLoanCode()
    {
        $year = date('Y');
        $month = date('m');
        $lastLoan = self::whereYear('created_at', $year)
                        ->whereMonth('created_at', $month)
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

    // Cek apakah sudah melewati tanggal kembali
    public function isOverdue()
    {
        return $this->status == 'borrowed' && now()->startOfDay() > $this->return_date;
    }

    // Scope untuk status tertentu
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeBorrowed($query)
    {
        return $query->where('status', 'borrowed');
    }

    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'borrowed')
                     ->whereDate('return_date', '<', now()->startOfDay());
    }

    // Scope untuk user tertentu
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}