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
        $query = Loan::with(['user', 'details.itemUnit.item']);

        // Filter status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'overdue':
                    $query->where('status', 'borrowed')
                        ->whereDate('return_date', '<', now());
                    break;
                case 'return_pending':
                    $query->where('return_request_status', 'pending');
                    break;
                default:
                    $query->where('status', $request->status);
                    break;
            }
        }

        // Filter tanggal pinjam
        if ($request->filled('date_from')) {
            $query->whereDate('loan_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('loan_date', '<=', $request->date_to);
        }

        // Filter search (kode / nama peminjam / nama barang)
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
                                ->whereDate('return_date', '<', now())
                                ->count(),
            'return_pending' => Loan::where('return_request_status', 'pending')->count(),
        ];

        return view('admin.peminjaman.index', compact('loans', 'stats'));
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
     * Get loan detail as JSON for modal (AJAX)
     */
    public function getDetailJson(Loan $loan)
    {
        $loan->load(['user', 'details.itemUnit.item', 'approver']);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $loan->id,
                'loan_code' => $loan->loan_code,
                'user_name' => $loan->user->name,
                'user_email' => $loan->user->email,
                'loan_date' => $loan->loan_date->format('d F Y'),
                'return_date' => $loan->return_date->format('d F Y'),
                'actual_return_date' => $loan->actual_return_date ? $loan->actual_return_date->format('d F Y') : null,
                'purpose' => $loan->purpose,
                'status' => $loan->status,
                'status_badge' => $this->getStatusBadge($loan->status),
                'approved_at' => $loan->approved_at ? $loan->approved_at->format('d F Y H:i') : null,
                'approver_name' => $loan->approver ? $loan->approver->name : null,
                'notes' => $loan->notes,
                'created_at' => $loan->created_at->format('d F Y H:i'),
                
                // ========== TAMBAHKAN INI ==========
                'return_requested_at' => $loan->return_requested_at ? $loan->return_requested_at->format('d F Y H:i') : null,
                'return_request_notes' => $loan->return_request_notes,
                'return_request_status' => $loan->return_request_status,
                
                'details' => $loan->details->map(function($detail) {
                    return [
                        'inventory_code' => $detail->itemUnit->inventory_code ?? '-',
                        'item_name' => $detail->itemUnit->item->name,
                        'condition_before' => $detail->condition_before,
                        'condition_before_badge' => $this->getConditionBadge($detail->condition_before),
                        'condition_after' => $detail->condition_after,
                        'condition_after_badge' => $detail->condition_after ? $this->getConditionBadge($detail->condition_after) : '<span class="badge bg-secondary">Belum dicek</span>',
                    ];
                })
            ]
        ]);
    }

    /**
     * Get status badge HTML
     */
    private function getStatusBadge($status)
    {
        switch($status) {
            case 'pending': return '<span class="badge bg-warning text-dark">Pending</span>';
            case 'approved': return '<span class="badge bg-primary">Disetujui</span>';
            case 'borrowed': return '<span class="badge bg-info text-dark">Dipinjam</span>';
            case 'returned': return '<span class="badge bg-success">Dikembalikan</span>';
            case 'rejected': return '<span class="badge bg-danger">Ditolak</span>';
            default: return '<span class="badge bg-secondary">' . $status . '</span>';
        }
    }

    /**
     * Get condition badge HTML
     */
    private function getConditionBadge($condition)
    {
        switch($condition) {
            case 'baik': return '<span class="badge bg-success">Baik</span>';
            case 'rusak': return '<span class="badge bg-danger">Rusak</span>';
            case 'maintenance': return '<span class="badge bg-warning">Maintenance</span>';
            default: return '<span class="badge bg-secondary">' . $condition . '</span>';
        }
    }

    /**
     * Download surat peminjaman
     */
    public function downloadSurat(Loan $loan)
    {
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
                'notes'        => $request->reject_reason,
            ]);

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
     * Pengembalian barang (LANGSUNG tanpa pengajuan - untuk admin)
     */
    public function returnItems(Request $request, Loan $loan)
    {
        if ($loan->status !== 'borrowed') {
            return back()->with('error', 'Barang belum dipinjam');
        }

        $validator = Validator::make($request->all(), [
            'condition_after' => 'required|in:baik,rusak,maintenance',
            'return_notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::transaction(function () use ($loan, $request) {
            foreach ($loan->details as $detail) {
                $detail->update([
                    'condition_after' => $request->condition_after,
                ]);

                $detail->itemUnit->update([
                    'status'    => 'tersedia',
                    'condition' => $request->condition_after,
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

    // ==================== PENGAJUAN PENGEMBALIAN ====================

    /**
     * List pengajuan pengembalian dari user
     */
    public function returnRequests(Request $request)
    {
        $query = Loan::with(['user', 'details.itemUnit.item'])
            ->whereNotNull('return_requested_at');

        // Filter status pengembalian
        if ($request->filled('status')) {
            $query->where('return_request_status', $request->status);
        }

        // Filter tanggal pengajuan pengembalian
        if ($request->filled('date_from')) {
            $query->whereDate('return_requested_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('return_requested_at', '<=', $request->date_to);
        }

        // Filter search (kode / nama peminjam / nama barang)
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

        return view('admin.peminjaman.return-requests', compact('loans', 'stats'));
    }

    /**
     * Detail pengajuan pengembalian
     */
    public function showReturnRequest(Loan $loan)
    {
        $loan->load(['user', 'details.itemUnit.item', 'approver']);
        
        return view('admin.peminjaman.return-request-detail', compact('loan'));
    }

    /**
     * Approve pengajuan pengembalian (dari user)
     */
     public function approveReturnRequest(Request $request, Loan $loan)
    {
        // Debug untuk melihat apakah method dipanggil
        // dd('Method approveReturnRequest dipanggil', $loan->id);
        
        if ($loan->return_request_status !== 'pending') {
            return redirect()->route('admin.loans.return-requests')
                ->with('error', 'Pengajuan pengembalian sudah diproses');
        }

        $validator = Validator::make($request->all(), [
            'condition_after' => 'required|in:baik,rusak,maintenance,hilang',
            'return_notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::transaction(function () use ($loan, $request) {
            // Update kondisi di loan_details
            foreach ($loan->details as $detail) {
                $detail->update([
                    'condition_after' => $request->condition_after,
                ]);

                // Update status unit
                $status = ($request->condition_after == 'rusak' || $request->condition_after == 'hilang') 
                    ? 'nonaktif' : 'tersedia';
                
                $detail->itemUnit->update([
                    'status' => $status,
                    'condition' => $request->condition_after,
                ]);
            }

            // Update loan
            $loan->update([
                'return_request_status' => 'approved',
                'return_approved_by' => auth()->id(),
                'return_approved_at' => now(),
                'status' => 'returned',
                'actual_return_date' => now(),
                'notes' => $request->return_notes ?: ($loan->notes ?? 'Pengembalian disetujui'),
            ]);
        });

        return redirect()->route('admin.loans.return-requests')
            ->with('success', 'Pengajuan pengembalian disetujui');
    }

    /**
     * Reject pengajuan pengembalian
     */
    public function rejectReturnRequest(Request $request, Loan $loan)
    {
        if ($loan->return_request_status !== 'pending') {
            return redirect()->route('admin.loans.return-requests')
                ->with('error', 'Pengajuan pengembalian sudah diproses');
        }

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

        DB::transaction(function () use ($loan, $request) {
            $loan->update([
                'return_request_status' => 'rejected',
                'return_approved_by' => auth()->id(),
                'return_approved_at' => now(),
                'notes' => $request->reject_reason,
            ]);
        });

        return redirect()->route('admin.loans.return-requests')
            ->with('success', 'Pengajuan pengembalian ditolak');
    }

    public function reports(Request $request)
    {
        $query = Loan::with(['user', 'details.itemUnit.item.category']);

        // Filter tanggal pinjam
        if ($request->filled('start_date')) {
            $query->whereDate('loan_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('loan_date', '<=', $request->end_date);
        }

        // Filter status
        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'overdue') {
                $query->where('status', 'borrowed')
                    ->whereDate('return_date', '<', now());
            } else {
                $query->where('status', $request->status);
            }
        }

        // Filter nama peminjam
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('loan_code', 'like', "%{$search}%")
                ->orWhereHas('user', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        // Filter nama/kategori barang
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
            'overdue'  => $loans->filter(fn($loan) =>
                $loan->status === 'borrowed' && $loan->return_date < now()
            )->count(),
        ];

        return view('admin.laporan.index', compact('loans', 'summary'));
    }
public function printReport(Request $request)
{
    $query = Loan::with(['user', 'details.itemUnit.item']);

    if ($request->filled('start_date')) {
        $query->whereDate('loan_date', '>=', $request->start_date);
        $startDate = $request->start_date;
    } else {
        $startDate = 'Semua';
    }

    // TANGGAL SELESAI
    if ($request->filled('end_date')) {
        $query->whereDate('loan_date', '<=', $request->end_date);
        $endDate = $request->end_date;
    } else {
        $endDate = 'Semua';
    }

    // STATUS
    if ($request->filled('status') && $request->status != 'all') {
        $query->where('status', $request->status);
    }


    $loans = $query->orderBy('loan_date', 'desc')->get();
    
    $startDate = $request->start_date ?? 'Semua';
    $endDate = $request->end_date ?? 'Semua';
    
    $statusFilter = $request->status ?? 'all';
    switch($statusFilter) {
        case 'pending': $statusText = 'Pending'; break;
        case 'approved': $statusText = 'Disetujui'; break;
        case 'borrowed': $statusText = 'Dipinjam'; break;
        case 'returned': $statusText = 'Dikembalikan'; break;
        case 'rejected': $statusText = 'Ditolak'; break;
        default: $statusText = 'Semua Status';
    }

    return view('admin.laporan.template', compact('loans', 'startDate', 'endDate', 'statusText'));
}
}