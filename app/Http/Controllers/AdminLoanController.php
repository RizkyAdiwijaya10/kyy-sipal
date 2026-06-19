<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminLoanController extends Controller
{
    public function index(Request $request)
    {
        $query = Loan::with(['user', 'details.itemUnit.item']);

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'overdue':
                    $query->where('status', 'borrowed')
                        ->whereDate('return_date', '<', now()->startOfDay());
                    break;
                case 'return_pending':
                    $query->where('return_request_status', 'pending');
                    break;
                default:
                    $query->where('status', $request->status);
                    break;
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('loan_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('loan_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('loan_code', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('details.itemUnit.item', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        $loans = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'pending'        => Loan::where('status', 'pending')->count(),
            'approved'       => Loan::where('status', 'approved')->count(),
            'borrowed'       => Loan::where('status', 'borrowed')->count(),
            'returned'       => Loan::where('status', 'returned')->count(),
            'rejected'       => Loan::where('status', 'rejected')->count(),
            'overdue'        => Loan::where('status', 'borrowed')
                                    ->whereDate('return_date', '<', now()->startOfDay())
                                    ->count(),
            'return_pending' => Loan::where('return_request_status', 'pending')->count(),
        ];

        return view('admin.pengajuan.peminjaman', compact('loans', 'stats'));
    }

    public function getDetailJson(Loan $loan)
    {
        $loan->load(['user', 'details.itemUnit.item', 'approver']);

        $suratUrl = null;
        if ($loan->surat_path) {
            $suratUrl = asset('storage/' . $loan->surat_path);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id'                    => $loan->id,
                'loan_code'             => $loan->loan_code,
                'user_name'             => $loan->user->name,
                'user_email'            => $loan->user->email,
                'loan_date'             => $loan->loan_date->format('d F Y'),
                'return_date'           => $loan->return_date->format('d F Y'),
                'actual_return_date'    => $loan->actual_return_date
                                            ? $loan->actual_return_date->format('d F Y')
                                            : null,
                'purpose'               => $loan->purpose,
                'status'                => $loan->status,
                'status_badge'          => ($loan->status),
                'approved_at'           => $loan->approved_at
                                            ? $loan->approved_at->format('d F Y H:i')
                                            : null,
                'approver_name'         => $loan->approver ? $loan->approver->name : null,
                'notes'                 => $loan->notes,
                'created_at'            => $loan->created_at->format('d F Y H:i'),
                'return_requested_at'   => $loan->return_requested_at
                                            ? $loan->return_requested_at->format('d F Y H:i')
                                            : null,
                'return_request_notes'  => $loan->return_request_notes,
                'return_request_status' => $loan->return_request_status,
                'surat_url'             => $suratUrl,
                'surat_filename'        => $loan->surat_path
                                            ? basename($loan->surat_path)
                                            : null,

                'details' => $loan->details->map(function ($detail) {
                    return [
                        'detail_id'            => $detail->id,
                        'inventory_code'       => $detail->itemUnit->inventory_code ?? '-',
                        'item_name'            => $detail->itemUnit->item->name,
                        'condition_before'     => $detail->condition_before,
                        'condition_before_badge' =>($detail->condition_before),
                        'condition_after'      => $detail->condition_after,
                        'condition_after_badge'=> $detail->condition_after
                            ? ($detail->condition_after)
                            : '<span class="badge bg-secondary">Belum dicek</span>',
                    ];
                }),
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // [FIX #4] downloadSurat() — baca dari surat_path, bukan notes
    // ─────────────────────────────────────────────────────────────
    public function downloadSurat(Loan $loan)
    {
        if ($loan->surat_path && Storage::disk('public')->exists($loan->surat_path)) {
            $fileName = 'surat_peminjaman_' . $loan->loan_code . '.pdf';
            $filePath = Storage::disk('public')->path($loan->surat_path);
            return response()->download($filePath, $fileName);
        }

        return redirect()->back()->with('error', 'File surat tidak ditemukan');
    }

    // ─────────────────────────────────────────────────────────────
    // [FIX #4] viewSurat() — baca dari surat_path, bukan notes
    // ─────────────────────────────────────────────────────────────
    public function viewSurat(Loan $loan)
    {
        if ($loan->surat_path && Storage::disk('public')->exists($loan->surat_path)) {
            $file = Storage::disk('public')->get($loan->surat_path);
            $mime = Storage::disk('public')->mimeType($loan->surat_path);

            return response($file, 200)
                ->header('Content-Type', $mime)
                ->header('Content-Disposition', 'inline; filename="surat_' . $loan->loan_code . '.pdf"');
        }

        return redirect()->back()->with('error', 'File surat tidak ditemukan');
    }

    public function approve(Loan $loan)
    {
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Peminjaman sudah diproses');
        }

        DB::transaction(function () use ($loan) {
            $loan->update([
                'status'      => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        });

        return redirect()->route('admin.loans.index')
            ->with('success', 'Peminjaman berhasil disetujui');
    }

    public function reject(Request $request, Loan $loan)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:255',
        ], [
            'reject_reason.required' => 'Alasan penolakan wajib diisi',
        ]);

        if ($loan->status !== 'pending') {
            return back()->with('error', 'Peminjaman sudah diproses');
        }

        // Load relasi sebelum transaksi untuk hindari lazy load di dalam loop
        $loan->load('details.itemUnit');

        DB::transaction(function () use ($loan, $request) {
            $loan->update([
                'status'      => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'notes'       => $request->reject_reason,
            ]);

            foreach ($loan->details as $detail) {
                $detail->itemUnit->update(['status' => 'tersedia']);
            }
        });

        return redirect()->route('admin.loans.index')
            ->with('success', 'Peminjaman berhasil ditolak');
    }

    public function confirmBorrowed(Loan $loan)
    {
        if ($loan->status !== 'approved') {
            return back()->with('error', 'Peminjaman belum disetujui');
        }

        $loan->load('details.itemUnit');

        DB::transaction(function () use ($loan) {
            $loan->update(['status' => 'borrowed']);

            foreach ($loan->details as $detail) {
                $detail->itemUnit->update(['status' => 'dipinjam']);
            }
        });

        return redirect()->route('admin.loans.index')
            ->with('success', 'Barang berhasil dikonfirmasi dipinjam');
    }

    public function returnRequests(Request $request)
    {
        $query = Loan::with(['user', 'details.itemUnit.item'])
            ->whereNotNull('return_requested_at');

        if ($request->filled('status')) {
            $query->where('return_request_status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('return_requested_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('return_requested_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('loan_code', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('details.itemUnit.item', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        $loans = $query->latest('return_requested_at')->paginate(10)->withQueryString();

        $stats = [
            'pending'  => Loan::whereNotNull('return_requested_at')->where('return_request_status', 'pending')->count(),
            'approved' => Loan::whereNotNull('return_requested_at')->where('return_request_status', 'approved')->count(),
            'rejected' => Loan::whereNotNull('return_requested_at')->where('return_request_status', 'rejected')->count(),
            'total'    => Loan::whereNotNull('return_requested_at')->count(),
        ];

        return view('admin.pengajuan.pengembalian', compact('loans', 'stats'));
    }

    // ─────────────────────────────────────────────────────────────
    // approveReturnRequest() — tidak ada perubahan logika utama,
    // kondisi sudah dibaca dengan $detail->id yang sesuai.
    // Pastikan JS juga mengirim condition_after[{detail->id}].
    // ─────────────────────────────────────────────────────────────
    public function approveReturnRequest(Request $request, Loan $loan)
    {
        if ($loan->return_request_status !== 'pending') {
            return redirect()->route('admin.loans.return-requests')
                ->with('error', 'Pengajuan pengembalian sudah diproses');
        }

        $request->validate([
            'condition_after' => 'required|in:baik,rusak,maintenance,hilang',
            'return_notes'    => 'nullable|string|max:500',
        ]);

        $loan->load('details.itemUnit');

        DB::transaction(function () use ($loan, $request) {
            $conditionAfter = $request->condition_after;

            foreach ($loan->details as $detail) {
                // Update kondisi detail
                $detail->update(['condition_after' => $conditionAfter]);

                // Update status unit
                $unitStatus = in_array($conditionAfter, ['rusak', 'hilang']) ? 'nonaktif' : 'tersedia';

                $detail->itemUnit->update([
                    'status'    => $unitStatus,
                    'condition' => $conditionAfter,
                ]);
            }

            // Update loan
            $loan->update([
                'return_request_status' => 'approved',
                'return_approved_by'    => auth()->id(),
                'return_approved_at'    => now(),
                'status'                => 'returned',
                'actual_return_date'    => now(),
                'notes'                 => $request->return_notes ?: ($loan->notes ?? 'Pengembalian disetujui'),
            ]);
        });

        return redirect()->route('admin.loans.return-requests')
            ->with('success', 'Pengajuan pengembalian disetujui');
    }

    public function rejectReturnRequest(Request $request, Loan $loan)
    {
        if ($loan->return_request_status !== 'pending') {
            return redirect()->route('admin.loans.return-requests')
                ->with('error', 'Pengajuan pengembalian sudah diproses');
        }

        $request->validate([
            'reject_reason' => 'required|string|max:255',
        ], [
            'reject_reason.required' => 'Alasan penolakan wajib diisi',
        ]);

        DB::transaction(function () use ($loan, $request) {
            $loan->update([
                'return_request_status' => 'rejected',
                'return_approved_by'    => auth()->id(),
                'return_approved_at'    => now(),
                'notes'                 => $request->reject_reason,
            ]);
        });

        return redirect()->route('admin.loans.return-requests')
            ->with('success', 'Pengajuan pengembalian ditolak');
    }

    public function reports(Request $request)
    {
        $query = Loan::with(['user', 'details.itemUnit.item.category']);

        if ($request->filled('start_date')) {
            $query->whereDate('loan_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('loan_date', '<=', $request->end_date);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'overdue') {
                $query->where('status', 'borrowed')
                    ->whereDate('return_date', '<', now()->startOfDay());
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('loan_code', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('item')) {
            $item = $request->item;
            $query->whereHas('details.itemUnit.item', fn($q) => $q->where('name', 'like', "%{$item}%"));
        }

        $loans = $query->orderBy('loan_date', 'desc')->get();

        $summary = [
            'total'    => $loans->count(),
            'pending'  => $loans->where('status', 'pending')->count(),
            'approved' => $loans->where('status', 'approved')->count(),
            'borrowed' => $loans->where('status', 'borrowed')->count(),
            'returned' => $loans->where('status', 'returned')->count(),
            'rejected' => $loans->where('status', 'rejected')->count(),
            'overdue'  => $loans->filter(
                fn($l) => $l->status === 'borrowed' && $l->return_date < now()->startOfDay()
            )->count(),
        ];

        return view('admin.laporan.index', compact('loans', 'summary'));
    }

    public function printReport(Request $request)
    {
        $query = Loan::with(['user', 'details.itemUnit.item']);

        if ($request->filled('start_date')) {
            $query->whereDate('loan_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('loan_date', '<=', $request->end_date);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $loans     = $query->orderBy('loan_date', 'desc')->get();
        $startDate = $request->start_date ?? 'Semua';
        $endDate   = $request->end_date   ?? 'Semua';

        $statusMap = [
            'pending'  => 'Pending',
            'approved' => 'Disetujui',
            'borrowed' => 'Dipinjam',
            'returned' => 'Dikembalikan',
            'rejected' => 'Ditolak',
        ];
        $statusText = $statusMap[$request->status ?? ''] ?? 'Semua Status';

        return view('admin.laporan.template', compact('loans', 'startDate', 'endDate', 'statusText'));
    }

    // ─────────────────────────────────────────────────────────────
    // [FIX #3] Tambahkan method badge yang sebelumnya tidak ada
    //   tapi sudah dipanggil di getDetailJson()
    // ─────────────────────────────────────────────────────────────
    private function getStatusBadge(string $status): string
    {
        $map = [
            'pending'   => '<span class="badge bg-warning text-dark">Menunggu</span>',
            'approved'  => '<span class="badge bg-primary">Disetujui</span>',
            'borrowed'  => '<span class="badge bg-info">Dipinjam</span>',
            'returned'  => '<span class="badge bg-success">Dikembalikan</span>',
            'cancelled' => '<span class="badge bg-secondary">Dibatalkan</span>',
            'rejected'  => '<span class="badge bg-danger">Ditolak</span>',
        ];

        return $map[$status]
            ?? '<span class="badge bg-secondary">' . htmlspecialchars($status) . '</span>';
    }

    
}