<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminLoanController extends Controller
{

    /**
     * List semua peminjaman
     */
    public function index(Request $request)
    {
        $status = $request->status;
        
        $loans = Loan::with(['user', 'details.itemUnit.item'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10);

        $stats = [
            'pending'   => Loan::where('status', 'pending')->count(),
            'approved'  => Loan::where('status', 'approved')->count(),
            'borrowed'  => Loan::where('status', 'borrowed')->count(),
            'overdue'   => Loan::where('status', 'borrowed')
                            ->whereDate('return_date', '<', now())
                            ->count(),
        ];

        return view('admin.peminjaman.index', compact('loans', 'stats', 'status'));
    }

    /**
     * Detail peminjaman
     */
    public function show(Loan $loan)
    {
        $loan->load(['user', 'details.itemUnit.item', 'approver']);

        return view('admin.peminjaman.show', compact('loan'));
    }

    /**
     * Download surat peminjaman
     */
    public function downloadSurat(Loan $loan)
    {
        // Cek apakah file surat ada
        if ($loan->notes && Storage::disk('public')->exists($loan->notes)) {
            $fileName = 'surat_peminjaman_' . $loan->loan_code . '.pdf';
            $filePath = Storage::disk('public')->path($loan->notes);
            return response()->download($filePath, $fileName);
        }
        
        return redirect()->back()->with('error', 'File surat tidak ditemukan');
    }

    /**
     * View surat peminjaman (inline PDF)
     */
    public function viewSurat(Loan $loan)
    {
        if ($loan->notes && Storage::disk('public')->exists($loan->notes)) {
            $file = Storage::disk('public')->get($loan->notes);
            $mime = Storage::disk('public')->mimeType($loan->notes);
            
            return response($file, 200)
                ->header('Content-Type', $mime)
                ->header('Content-Disposition', 'inline; filename="surat_' . $loan->loan_code . '.pdf"');
        }
        
        return redirect()->back()->with('error', 'File surat tidak ditemukan');
    }

    /**
     * Approve peminjaman
     */
    public function approve(Loan $loan)
    {
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Peminjaman sudah diproses');
        }

        DB::transaction(function () use ($loan) {

            $loan->update([
                'status'       => 'approved',
                'approved_by'  => auth()->id(),
                'approved_at'  => now(),
            ]);

            // Unit tetap dipesan sampai confirmBorrowed
        });

        return redirect()->route('admin.loans.index')
            ->with('success', 'Peminjaman berhasil disetujui');
    }

    /**
     * Reject peminjaman
     */
    public function reject(Request $request, Loan $loan)
    {
        $validator = Validator::make($request->all(), [
            'reject_reason' => 'required|string|max:255',
        ], [
            'reject_reason.required' => 'Alasan penolakan wajib diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($loan->status !== 'pending') {
            return back()->with('error', 'Peminjaman sudah diproses');
        }

        DB::transaction(function () use ($loan, $request) {

            $loan->update([
                'status'       => 'rejected',
                'approved_by'  => auth()->id(),
                'approved_at'  => now(),
                'notes'        => $request->reject_reason, // Simpan alasan penolakan
            ]);

            // Kembalikan unit jadi tersedia
            foreach ($loan->details as $detail) {
                $detail->itemUnit->update([
                    'status' => 'tersedia'
                ]);
            }
        });

        return redirect()->route('admin.loans.index')
            ->with('success', 'Peminjaman berhasil ditolak');
    }

    /**
     * Konfirmasi barang diambil
     */
    public function confirmBorrowed(Loan $loan)
    {
        if ($loan->status !== 'approved') {
            return back()->with('error', 'Peminjaman belum disetujui');
        }

        $loan->load('details.itemUnit');

        DB::transaction(function () use ($loan) {

            $loan->update([
                'status' => 'borrowed',
            ]);

            foreach ($loan->details as $detail) {
                $detail->itemUnit->update([
                    'status' => 'dipinjam'
                ]);
            }
        });

        return redirect()->route('admin.loans.index')
            ->with('success', 'Barang berhasil dikonfirmasi dipinjam');
    }

    /**
     * Pengembalian barang
     */
    public function returnItems(Request $request, Loan $loan)
    {
        if ($loan->status !== 'borrowed') {
            return back()->with('error', 'Barang belum dipinjam');
        }

        $validator = Validator::make($request->all(), [
            'return_notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::transaction(function () use ($loan, $request) {

            foreach ($loan->details as $detail) {
                // Simpan kondisi setelah pengembalian (bisa diisi admin)
                $detail->update([
                    'condition_after' => $request->condition_after ?? 'baik',
                ]);

                $detail->itemUnit->update([
                    'status'    => 'tersedia',
                    'condition' => $request->condition_after ?? 'baik',
                ]);
            }

            $loan->update([
                'status'             => 'returned',
                'actual_return_date' => now(),
                'notes'              => $request->return_notes ?: $loan->notes,
            ]);
        });

        return redirect()->route('admin.loans.index')
            ->with('success', 'Barang berhasil dikembalikan');
    }

    /**
     * Laporan
     */
    public function reports(Request $request)
    {
        $query = Loan::with(['user', 'details.itemUnit.item']);

        if ($request->start_date) {
            $query->whereDate('loan_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('loan_date', '<=', $request->end_date);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $loans = $query->orderBy('loan_date', 'desc')->get();

        $summary = [
            'total'     => $loans->count(),
            'pending'   => $loans->where('status', 'pending')->count(),
            'approved'  => $loans->where('status', 'approved')->count(),
            'borrowed'  => $loans->where('status', 'borrowed')->count(),
            'returned'  => $loans->where('status', 'returned')->count(),
            'overdue'   => $loans->filter(function ($loan) {
                return $loan->status === 'borrowed'
                    && $loan->return_date < now();
            })->count(),
        ];

        return view('admin.peminjaman.reports', compact('loans', 'summary'));
    }
}