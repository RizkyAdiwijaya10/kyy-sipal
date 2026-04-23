<?php
// app/Http/Controllers/LoansController.php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Item;
use App\Models\LoanDetail;
use App\Models\ItemUnit;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LoansController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('user');
    }

    public function availableItems()
    {
        $items = Item::with([
            'category',
            'itemUnits' => function ($q) {
                $q->where('status', 'tersedia')
                  ->where('condition', 'baik');
            }
        ])
        ->whereHas('itemUnits', function ($q) {
            $q->where('status', 'tersedia')
              ->where('condition', 'baik');
        })
        ->get();

        return view('user.item.index', compact('items'));
    }
    
    public function createLoan()
    {
        // Ambil barang yang memiliki unit tersedia
        $items = Item::withCount([
            'itemUnits as available_units_count' => function ($q) {
                $q->where('status', 'tersedia')
                  ->where('condition', 'baik');
            }
        ])
        ->having('available_units_count', '>', 0)
        ->orderBy('name')
        ->get();

        return view('user.peminjaman.create', compact('items'));
    }

    public function storeLoan(Request $request)
    {
        // Validasi dasar
        $validator = Validator::make($request->all(), [
            'loan_date' => 'required|date|after_or_equal:today',
            'return_date' => 'required|date|after:loan_date',
            'purpose' => 'required|string|max:500',
            'surat' => 'required|file|mimes:pdf|max:2048',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            'loan_date.required' => 'Tanggal pinjam wajib diisi',
            'loan_date.after_or_equal' => 'Tanggal pinjam minimal hari ini',
            'return_date.required' => 'Tanggal kembali wajib diisi',
            'return_date.after' => 'Tanggal kembali harus setelah tanggal pinjam',
            'purpose.required' => 'Tujuan peminjaman wajib diisi',
            // 'purpose.min' => 'Tujuan peminjaman minimal 10 karakter',
            'surat.required' => 'File surat peminjaman wajib diupload',
            'surat.mimes' => 'File surat harus berformat PDF',
            'surat.max' => 'Ukuran file surat maksimal 2MB',
            'items.required' => 'Minimal pilih 1 barang',
            'items.*.quantity.min' => 'Quantity minimal 1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Upload surat
            $suratPath = $request->file('surat')->store('surat_peminjaman', 'public');

            // Buat peminjaman
            $loan = Loan::create([
                'loan_code' => Loan::generateLoanCode(),
                'user_id' => auth()->id(),
                'loan_date' => $request->loan_date,
                'return_date' => $request->return_date,
                'purpose' => $request->purpose,
                'status' => 'pending',
                'notes' => $suratPath,
            ]);

            // Proses setiap item
            foreach ($request->items as $itemData) {
                $itemId = $itemData['item_id'];
                $quantity = $itemData['quantity'];

                // Cari unit yang tersedia untuk item ini
                $availableUnits = ItemUnit::where('item_id', $itemId)
                    ->where('status', 'tersedia')
                    ->where('condition', 'baik')
                    ->lockForUpdate()
                    ->limit($quantity)
                    ->get();

                if ($availableUnits->count() < $quantity) {
                    throw new \Exception("Stok barang tidak mencukupi. Tersedia: {$availableUnits->count()}, Diminta: {$quantity}");
                }

                // Buat detail untuk setiap unit
                foreach ($availableUnits as $unit) {
                    LoanDetail::create([
                        'loan_id' => $loan->id,
                        'item_unit_id' => $unit->id,
                        'quantity' => 1,
                        'condition_before' => $unit->condition,
                    ]);

                    // Update status unit menjadi dipesan
                    $unit->update(['status' => 'dipesan']);
                }
            }

            DB::commit();

            return redirect()->route('user.loans.history')
                ->with('success', 'Pengajuan peminjaman berhasil dikirim. Kode: ' . $loan->loan_code);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function loanHistory()
    {
        $loans = Loan::with('details.itemUnit.item')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('user.peminjaman.history', compact('loans'));
    }

    public function showLoan(Loan $loan)
    {
        if ($loan->user_id !== auth()->id()) {
            abort(403);
        }

        $loan->load('details.itemUnit.item');

        return view('user.peminjaman.show', compact('loan'));
    }

    public function cancelLoan(Loan $loan)
    {
        if ($loan->user_id !== auth()->id()) {
            abort(403);
        }

        if ($loan->status !== 'pending') {
            return back()->with('error', 'Peminjaman tidak dapat dibatalkan karena sudah diproses');
        }

        DB::transaction(function () use ($loan) {
            // Kembalikan status unit
            foreach ($loan->details as $detail) {
                $detail->itemUnit->update(['status' => 'tersedia']);
            }

            $loan->update(['status' => 'cancelled']);
        });

        return redirect()
            ->route('user.loans.history')
            ->with('success', 'Peminjaman berhasil dibatalkan');
    }

}