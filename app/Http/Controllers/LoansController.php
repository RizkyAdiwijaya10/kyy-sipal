<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Item;
use App\Models\LoanDetail;
use App\Models\ItemUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoansController extends Controller
{
    public function create()
    {
        $items = Item::withCount([
            'itemUnits as available_units_count' => function ($q) {
                $q->where('status', 'tersedia');
            }
        ])
        ->having('available_units_count', '>', 0)
        ->get();

        return view('user.peminjaman.create', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'loan_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:loan_date',
            'purpose' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*' => 'exists:items,id',
            'surat' => 'required|file|mimes:pdf|max:2048'
        ]);
    
        DB::transaction(function () use ($request) {
    
            $suratPath = $request->file('surat')->store('surat_peminjaman', 'public');
    
            $loan = Loan::create([
                'loan_code' => Loan::generateLoanCode(),
                'user_id' => auth()->id(),
                'loan_date' => $request->loan_date,
                'return_date' => $request->return_date,
                'purpose' => $request->purpose,
                'status' => 'pending',
                'notes' => $suratPath,
            ]);
    
            foreach ($request->items as $itemId) {
    
                $unit = \App\Models\ItemUnit::where('item_id', $itemId)
                    ->where('status', 'tersedia')
                    ->lockForUpdate()
                    ->first();
    
                if (!$unit) {
                    throw new \Exception('Stok barang tidak tersedia.');
                }
    
                LoanDetail::create([
                    'loan_id' => $loan->id,
                    'item_unit_id' => $unit->id,
                    'condition_before' => $unit->condition,
                ]);
    
                $unit->update([
                    'status' => 'dipesan'
                ]);
            }
        });
    
        return redirect()->route('user.loans.create')
            ->with('success', 'Pengajuan peminjaman berhasil dikirim.');
    }
}