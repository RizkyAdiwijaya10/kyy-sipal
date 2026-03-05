<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Loan;
use App\Models\ItemUnit;
use App\Models\LoanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserLoanController extends Controller
{
    /**
     * Daftar barang tersedia
     */
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

    /**
     * Detail barang
     */
    public function showItem(Item $item)
    {
        $availableUnits = $item->itemUnits()
            ->where('status', 'tersedia')
            ->where('condition', 'baik')
            ->paginate(10);

        return view('user.item.show', compact('item', 'availableUnits'));
    }

    /**
     * Form pengajuan peminjaman
     */
    public function createLoan(Request $request)
    {
        $items = Item::with([
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

        // Ambil item dari tombol "Pinjam Sekarang"
        $selectedItemId = $request->query('item');

        return view('user.peminjaman.create', compact(
            'items',
            'selectedItemId'
        ));
    }

    /**
     * Simpan pengajuan
     */
    public function storeLoan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'loan_date' => 'required|date|after_or_equal:today',
            'return_date' => 'required|date|after:loan_date',
            'purpose' => 'nullable|string|max:255',
            'items' => 'required|array|min:1|max:3',
            'items.*.item_unit_id' => 'required|exists:item_units,id',
            'items.*.condition_before' => 'required|in:baik,rusak,maintenance',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {

            $loan = Loan::create([
                'loan_code' => Loan::generateLoanCode(),
                'user_id' => auth()->id(),
                'loan_date' => $request->loan_date,
                'return_date' => $request->return_date,
                'purpose' => $request->purpose,
                'status' => 'pending',
            ]);

            foreach ($request->items as $item) {

                $itemUnit = ItemUnit::lockForUpdate()
                    ->findOrFail($item['item_unit_id']);

                // Validasi ulang di database
                if ($itemUnit->status !== 'tersedia' || 
                    $itemUnit->condition !== 'baik') {
                    throw new \Exception(
                        "Unit {$itemUnit->inventory_code} tidak tersedia"
                    );
                }

                LoanDetail::create([
                    'loan_id' => $loan->id,
                    'item_unit_id' => $item['item_unit_id'],
                    'condition_before' => $item['condition_before'],
                ]);

                $itemUnit->update([
                    'status' => 'dipesan'
                ]);
            }

            DB::commit();

            return redirect()
                ->route('user.loans.history')
                ->with('success', 'Peminjaman berhasil diajukan');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Riwayat peminjaman
     */
    public function loanHistory()
    {
        $loans = Loan::with('details.itemUnit.item')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('user.peminjaman.history', compact('loans'));
    }

    /**
     * Detail peminjaman
     */
    public function showLoan(Loan $loan)
    {
        if ($loan->user_id !== auth()->id()) {
            abort(403);
        }

        $loan->load('details.itemUnit.item');

        return view('user.peminjaman.show', compact('loan'));
    }

    /**
     * Batalkan peminjaman
     */
    public function cancelLoan(Loan $loan)
    {
        if ($loan->user_id !== auth()->id()) {
            abort(403);
        }

        if ($loan->status !== 'pending') {
            return back()->with('error', 'Tidak dapat dibatalkan');
        }

        DB::transaction(function () use ($loan) {

            foreach ($loan->details as $detail) {
                $detail->itemUnit->update([
                    'status' => 'tersedia'
                ]);
            }

            $loan->update([
                'status' => 'cancelled'
            ]);
        });

        return redirect()
            ->route('user.loans.history')
            ->with('success', 'Peminjaman dibatalkan');
    }
}