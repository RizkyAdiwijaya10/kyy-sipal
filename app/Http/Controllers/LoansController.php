<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Item;
use App\Models\LoanDetail;
use App\Models\ItemUnit;
use App\Models\User;
use App\Helpers\WhatsAppHelper;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class LoansController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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
        $request->validate([
            'loan_date' => 'required|date|after_or_equal:today',
            'return_date' => 'required|date|after:loan_date',
            'purpose' => 'required|string|max:500',
            'surat' => 'required|file|mimes:pdf|max:2048',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {

            $suratPath = $request->file('surat')->store('surat_peminjaman', 'public');

            $loan = Loan::create([
                'loan_code' => Loan::generateLoanCode(),
                'user_id' => auth()->id(),
                'loan_date' => $request->loan_date,
                'return_date' => $request->return_date,
                'purpose' => $request->purpose,
                'status' => 'pending',
                'surat_path' => $suratPath,
            ]);

            foreach ($request->items as $itemData) {
                $itemId = $itemData['item_id'];
                $quantity = $itemData['quantity'];

                $availableUnits = ItemUnit::where('item_id', $itemId)
                    ->where('status', 'tersedia')
                    ->where('condition', 'baik')
                    ->lockForUpdate()
                    ->limit($quantity)
                    ->get();

                if ($availableUnits->count() < $quantity) {
                    throw new \Exception("Stok barang tidak mencukupi. Tersedia: {$availableUnits->count()}, Diminta: {$quantity}");
                }

                foreach ($availableUnits as $unit) {
                    LoanDetail::create([
                        'loan_id' => $loan->id,
                        'item_unit_id' => $unit->id,
                        'quantity' => 1,
                        'condition_before' => $unit->condition,
                    ]);

                    $unit->update(['status' => 'dipinjam']);
                }
            }

            $loan->load('details.itemUnit.item');
            DB::commit();
            $this->sendWhatsAppNotification($loan, 'peminjaman');

            return redirect()->route('user.loans.history')
                ->with('success', 'Pengajuan peminjaman berhasil dikirim. Kode: ' . $loan->loan_code);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function loanHistory(Request $request)
    {
        $query = Loan::with('details.itemUnit.item')
            ->where('user_id', auth()->id());

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'terlambat':
                    $query->where('status', 'borrowed')
                        ->where('return_date', '<', now());
                    break;
                case 'return_pending':
                    $query->where('return_request_status', 'pending');
                    break;
                case 'return_rejected':
                    $query->where('return_request_status', 'rejected');
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

        // Filter by search kode / nama item
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('loan_code', 'like', "%{$search}%")
                    ->orWhereHas('details.itemUnit.item', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $loans = $query->latest()->paginate(10)->withQueryString();

        return view('user.peminjaman.history', compact('loans'));
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
            foreach ($loan->details as $detail) {
                $detail->itemUnit->update(['status' => 'tersedia']);
            }

            $loan->update(['status' => 'cancelled']);
        });

        return redirect()
            ->route('user.loans.history')
            ->with('success', 'Peminjaman berhasil dibatalkan');
    }
 
    public function returnStore(Request $request, Loan $loan)
    {
        if ($loan->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$this->canRequestReturn($loan)) {
            return redirect()->route('user.returns.index')
                ->with('error', 'Tidak dapat mengajukan pengembalian untuk peminjaman ini.');
        }

        $request->validate([
            'return_notes' => 'nullable|string|max:500',
        ]);

        // Update data dalam transaksi
        DB::transaction(function () use ($loan, $request) {
            $loan->update([
                'return_requested_at'  => now(),
                'return_request_notes' => $request->return_notes,
                'return_request_status' => 'pending',
            ]);
        });

        $loan->refresh();
        $loan->load('details.itemUnit.item');

        $this->sendWhatsAppNotification($loan, 'pengembalian');

        return redirect()->route('user.loans.history')
            ->with('success', 'Pengajuan pengembalian berhasil dikirim. Silakan tunggu konfirmasi admin.');
    }

    public function returnCancel(Loan $loan)
    {
        if ($loan->user_id !== auth()->id()) {
            abort(403);
        }

        if ($loan->return_request_status !== 'pending') {
            return back()->with('error', 'Pengajuan pengembalian tidak dapat dibatalkan.');
        }

        DB::transaction(function () use ($loan) {
            $loan->update([
                'return_requested_at' => null,
                'return_request_notes' => null,
                'return_request_status' => null,
            ]);
        });

        return redirect()->route('user.loans.history')
            ->with('success', 'Pengajuan pengembalian berhasil dibatalkan.');
    }

    private function canRequestReturn($loan)
    {
        return $loan->status === 'borrowed' &&
            is_null($loan->return_requested_at) &&
            is_null($loan->actual_return_date);
    }

    protected function sendWhatsAppNotification($loan, $type)
    {
        // Ambil semua admin yang memiliki nomor telepon
        $admins = User::where('role', 'admin')
            ->whereNotNull('phone')
            ->get();

        if ($admins->isEmpty()) {
            return;
        }

        // Siapkan pesan
        $title = $type == 'peminjaman' ? 'PENGAJUAN PEMINJAMAN BARU' : 'PENGAJUAN PENGEMBALIAN BARU';

        $message = "━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "      {$title}\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        $message .= "Peminjam: " . auth()->user()->name . "\n";
        $message .= "Kode: " . $loan->loan_code . "\n";
        $message .= "Tgl Pinjam: " . $loan->loan_date->format('d/m/Y') . "\n";
        $message .= "Rencana Kembali: " . $loan->return_date->format('d/m/Y') . "\n";
        $message .= "Jumlah Item: " . $loan->details->count() . " barang\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "Segera proses di aplikasi!";

        foreach ($admins as $admin) {

            \Log::info('Mengirim WA ke admin', [
                'admin' => $admin->name,
                'phone' => $admin->phone
            ]);

            $result = WhatsAppHelper::sendMessage(
                $admin->phone,
                $message
            );

            \Log::info('Hasil pengiriman WA', [
                'admin' => $admin->name,
                'result' => $result
            ]);
        }
    }

    public function getDetailJson(Loan $loan)
    {
        if ($loan->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $loan->load(['user', 'details.itemUnit.item', 'approver']);

        $suratUrl = null;
        if ($loan->surat_path) {
            $suratUrl = asset('storage/' . $loan->surat_path);
        }

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
                'status_badge' => ($loan->status),
                'approved_at' => $loan->approved_at ? $loan->approved_at->format('d F Y H:i') : null,
                'approver_name' => $loan->approver ? $loan->approver->name : null,
                'created_at' => $loan->created_at->format('d F Y H:i'),
                'notes' => $loan->notes,
                'return_requested_at' => $loan->return_requested_at ? $loan->return_requested_at->format('d F Y H:i') : null,
                'return_request_notes' => $loan->return_request_notes,
                'return_request_status' => $loan->return_request_status,
                'surat_path' => $loan->surat_path,
                'surat_url' => $suratUrl,
                'details' => $loan->details->map(function ($detail) {
                    return [
                        'inventory_code' => $detail->itemUnit->inventory_code ?? '-',
                        'item_name' => $detail->itemUnit->item->name,
                        'condition_before' => $detail->condition_before,
                        'condition_before_badge' => ($detail->condition_before),
                        'condition_after' => $detail->condition_after,
                        'condition_after_badge' => $detail->condition_after ? ($detail->condition_after) : '<span class="badge bg-secondary">Belum dicek</span>',
                    ];
                })
            ]
        ]);
    }

    public function downloadTemplate()
    {
        $templatePath = storage_path('app/template/Template_Surat_Peminjaman.docx');

        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'Template surat peminjaman tidak ditemukan.');
        }

        return response()->download($templatePath, 'Template_Surat_Peminjaman.docx');
    }

    public function viewSurat(Loan $loan)
    {
        if ($loan->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        if (!$loan->surat_path) {
            return redirect()->back()->with('error', 'Surat tidak ditemukan.');
        }

        $suratPath = storage_path('app/public/' . $loan->surat_path);

        if (!file_exists($suratPath)) {
            return redirect()->back()->with('error', 'File surat tidak ditemukan.');
        }

        return response()->file($suratPath);
    }

    public function downloadSurat(Loan $loan)
    {
        if ($loan->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        if (!$loan->surat_path) {
            return redirect()->back()->with('error', 'Surat tidak ditemukan.');
        }

        $suratPath = storage_path('app/public/' . $loan->surat_path);

        if (!file_exists($suratPath)) {
            return redirect()->back()->with('error', 'File surat tidak ditemukan.');
        }

        $fileName = 'Surat_Peminjaman_' . $loan->loan_code . '.pdf';
        return response()->download($suratPath, $fileName);
    }

    public function showLoan(Loan $loan)
    {
        if ($loan->user_id !== auth()->id()) {
            abort(403);
        }

        $loan->load('details.itemUnit.item');
        return view('user.peminjaman.show', compact('loan'));
    }

    public function showItem($item)
    {
        return view('user.item.show', compact('item'));
    }

    public function returnIndex()
    {
        $loans = Loan::where('user_id', auth()->id())
            ->where('status', 'borrowed')
            ->with('details.itemUnit.item')
            ->latest()
            ->paginate(10);

        return view('user.returns.index', compact('loans'));
    }

    public function returnCreate(Loan $loan)
    {
        if ($loan->user_id !== auth()->id()) {
            abort(403);
        }

        if ($loan->status !== 'borrowed') {
            return back()->with('error', 'Peminjaman tidak dapat dikembalikan.');
        }

        $loan->load('details.itemUnit.item');
        return view('user.returns.create', compact('loan'));
    }

    public function returnShow(Loan $loan)
    {
        if ($loan->user_id !== auth()->id()) {
            abort(403);
        }

        $loan->load('details.itemUnit.item');
        return view('user.returns.show', compact('loan'));
    }

}