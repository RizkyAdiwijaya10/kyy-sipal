@extends('layouts.app')

@section('title', 'Manajemen Peminjaman')
@section('page-title', 'Manajemen Peminjaman')
@section('page-subtitle', 'Kelola pengajuan peminjaman alat')

@section('content')
<div class="row">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- STATISTIK CARD --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="mdi mdi-clock-outline fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Disetujui</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['approved'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="mdi mdi-check-circle-outline fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Dipinjam</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['borrowed'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="mdi mdi-bookmark-outline fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Ditolak</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['rejected'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="mdi mdi-alert-circle-outline fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            {{-- FILTER --}}
            <form method="GET" action="{{ route('admin.loans.index') }}" id="filterForm">
                <div class="row g-2 mb-4">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="mdi mdi-magnify text-muted"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0"
                                placeholder="Cari kode / peminjam / barang..."
                                value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Semua Status</option>
                            <option value="pending"        {{ request('status') == 'pending'        ? 'selected' : '' }}>Pending</option>
                            <option value="approved"       {{ request('status') == 'approved'       ? 'selected' : '' }}>Disetujui</option>
                            <option value="borrowed"       {{ request('status') == 'borrowed'       ? 'selected' : '' }}>Dipinjam</option>
                            <option value="overdue"        {{ request('status') == 'overdue'        ? 'selected' : '' }}>Overdue / Terlambat</option>
                            <option value="return_pending" {{ request('status') == 'return_pending' ? 'selected' : '' }}>Pengajuan Pengembalian</option>
                            <option value="returned"       {{ request('status') == 'returned'       ? 'selected' : '' }}>Dikembalikan</option>
                            <option value="rejected"       {{ request('status') == 'rejected'       ? 'selected' : '' }}>Ditolak</option>
                            <option value="cancelled"      {{ request('status') == 'cancelled'      ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3 d-flex gap-2 align-items-center">
                        <button type="submit" class="btn btn-success">
                            <i class="mdi mdi-filter"></i> Filter
                        </button>
                        @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                            <a href="{{ route('admin.loans.index') }}" class="btn btn-outline-secondary">
                                <i class="mdi mdi-close"></i> Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            {{-- TABLE --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Peminjam</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th>
                            <th>Barang</th>
                            <th>Status</th>
                            <th width="28%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $loan)
                        <tr class="{{ in_array($loan->status, ['borrowed','overdue']) && $loan->return_date < now() ? 'table-danger' : '' }}">
                            <td class="fw-semibold">{{ $loan->loan_code }}</td>
                            <td>{{ $loan->user->name }}</td>
                            <td>{{ $loan->loan_date->format('d/m/Y') }}</td>
                            <td>
                                {{ $loan->return_date->format('d/m/Y') }}
                                @if(in_array($loan->status, ['borrowed', 'overdue']) && $loan->return_date < now())
                                    <br><small class="text-danger"><i class="mdi mdi-alert-circle"></i> Terlambat</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $loan->details->count() }} barang</span>
                            </td>

                            {{-- STATUS BADGE --}}
                            <td>
                                @switch($loan->status)
                                    @case('pending')
                                        <span class="badge bg-warning text-dark"><i class="mdi mdi-clock-outline me-1"></i>Pending</span>
                                        @break
                                    @case('approved')
                                        <span class="badge bg-primary"><i class="mdi mdi-check-circle-outline me-1"></i>Disetujui</span>
                                        @break
                                    @case('borrowed')
                                        @if($loan->return_request_status == 'pending')
                                            <span class="badge text-white" style="background-color:#6f42c1"><i class="mdi mdi-keyboard-return me-1"></i>Pengajuan Kembali</span>
                                        @else
                                            <span class="badge bg-info"><i class="mdi mdi-bookmark-outline me-1"></i>Dipinjam</span>
                                        @endif
                                        @break
                                    @case('overdue')
                                        <span class="badge bg-danger"><i class="mdi mdi-alert-circle-outline me-1"></i>Overdue</span>
                                        @break
                                    @case('returned')
                                        <span class="badge bg-success"><i class="mdi mdi-check-all me-1"></i>Dikembalikan</span>
                                        @break
                                    @case('rejected')
                                        <span class="badge bg-danger"><i class="mdi mdi-close-circle-outline me-1"></i>Ditolak</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-secondary"><i class="mdi mdi-cancel me-1"></i>Dibatalkan</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $loan->status }}</span>
                                @endswitch
                            </td>

                            {{-- AKSI — semua tombol memanggil fungsi JS, tidak ada data-bs-target ke modal di dalam tabel --}}
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    @if($loan->status == 'pending')
                                        <button type="button" class="btn btn-success btn-sm"
                                                onclick="openApproveModal({{ $loan->id }}, '{{ $loan->loan_code }}', '{{ $loan->user->name }}', {{ $loan->details->count() }}, '{{ $loan->loan_date->format('d/m/Y') }}', '{{ $loan->return_date->format('d/m/Y') }}')">
                                            <i class="mdi mdi-check"></i> Setujui
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm"
                                                onclick="openRejectModal({{ $loan->id }}, '{{ $loan->loan_code }}', '{{ $loan->user->name }}')">
                                            <i class="mdi mdi-close"></i> Tolak
                                        </button>
                                    @endif

                                    @if($loan->status == 'approved')
                                        <button type="button" class="btn btn-primary btn-sm"
                                                onclick="openConfirmBorrowedModal({{ $loan->id }}, '{{ $loan->loan_code }}', '{{ $loan->user->name }}', {{ $loan->details->count() }})">
                                            <i class="mdi mdi-bookmark"></i> Konfirmasi
                                        </button>
                                    @endif

                                    <button type="button" class="btn btn-info btn-sm"
                                            onclick="showDetailModal({{ $loan->id }})">
                                        <i class="mdi mdi-eye"></i> Detail
                                    </button>
                                </div>
                            </td>
                        </tr>

                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="mdi mdi-inbox-outline display-3 d-block mb-3"></i>
                                <p class="mb-0">Tidak ada data peminjaman</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $loans->withQueryString()->links() }}
            </div>

        </div>
    </div>
</div>

{{-- MODAL APPROVE --}}
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="approveForm">
                @csrf
                <div class="modal-header text-dark">
                    <h5 class="modal-title"><i class="mdi mdi-check-circle-outline me-2"></i>Setujui Peminjaman</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Kode:</strong> <span id="approve-loan-code"></span><br>
                        <strong>Peminjam:</strong> <span id="approve-user-name"></span><br>
                        <strong>Jumlah Barang:</strong> <span id="approve-item-count"></span> item<br>
                        <strong>Tanggal Pinjam:</strong> <span id="approve-loan-date"></span><br>
                        <strong>Rencana Kembali:</strong> <span id="approve-return-date"></span>
                    </div>
                    <p>Apakah Anda yakin ingin menyetujui peminjaman ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL REJECT --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="rejectForm">
                @csrf
                <div class="modal-header text-dark">
                    <h5 class="modal-title"><i class="mdi mdi-close-circle-outline me-2"></i>Tolak Peminjaman</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <strong>Kode:</strong> <span id="reject-loan-code"></span><br>
                        <strong>Peminjam:</strong> <span id="reject-user-name"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger"></span></label>
                        <textarea name="reject_reason" class="form-control" rows="3"
                            placeholder="Masukkan alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL CONFIRM BORROWED --}}
<div class="modal fade" id="confirmBorrowedModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="confirmBorrowedForm">
                @csrf
                <div class="modal-header text-dark">
                    <h5 class="modal-title"><i class="mdi mdi-bookmark-check-outline me-2"></i>Konfirmasi Pengambilan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Kode:</strong> <span id="confirm-loan-code"></span><br>
                        <strong>Peminjam:</strong> <span id="confirm-user-name"></span><br>
                        <strong>Jumlah Barang:</strong> <span id="confirm-item-count"></span> item
                    </div>
                    <p>Konfirmasi bahwa barang sudah diambil oleh peminjam?</p>
                    <div class="alert alert-warning">
                        <i class="mdi mdi-alert-circle-outline me-2"></i>
                        Status akan berubah menjadi <strong>Dipinjam</strong>.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL APPROVE RETURN REQUEST --}}
<div class="modal fade" id="approveReturnModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="approveReturnForm">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="mdi mdi-keyboard-return me-2"></i>Terima Pengembalian</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Kode:</strong> <span id="approve-return-loan-code"></span><br>
                        <strong>Peminjam:</strong> <span id="approve-return-user-name"></span><br>
                        <strong>Diajukan:</strong> <span id="approve-return-requested-at"></span><br>
                        <span id="approve-return-notes-wrap" class="d-none">
                            <strong>Catatan Peminjam:</strong> <span id="approve-return-request-notes"></span>
                        </span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kondisi Barang per Unit <span class="text-danger"></span></label>
                        <div id="approve-return-units-table">
                            <div class="text-center py-3">
                                <div class="spinner-border spinner-border-sm text-success"></div>
                                <span class="ms-2">Memuat data unit...</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Admin (Opsional)</label>
                        <textarea name="return_notes" class="form-control" rows="2"
                            placeholder="Catatan kondisi, kerusakan, atau keterangan lain..."></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="mdi mdi-alert-circle-outline me-2"></i>
                        Status akan berubah menjadi <strong>Dikembalikan</strong>.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="mdi mdi-check"></i> Terima Pengembalian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL REJECT RETURN REQUEST --}}
<div class="modal fade" id="rejectReturnModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="rejectReturnForm">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="mdi mdi-close-circle-outline me-2"></i>Tolak Pengajuan Pengembalian</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <strong>Kode:</strong> <span id="reject-return-loan-code"></span><br>
                        <strong>Peminjam:</strong> <span id="reject-return-user-name"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger"></span></label>
                        <textarea name="reject_reason" class="form-control" rows="3"
                            placeholder="Masukkan alasan penolakan pengajuan pengembalian..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL DETAIL (AJAX) --}}
<div class="modal fade" id="detailModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Peminjaman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat data...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="mdi mdi-close"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

// ============================================================
// URL BASE
// ============================================================
const baseUrl = '{{ url('admin/loans') }}';

// ============================================================
// Helper: build kondisi unit table HTML
// ============================================================
function buildUnitsTable(details, namePrefix) {
    if (!details || details.length === 0) {
        return '<p class="text-muted">Tidak ada data unit.</p>';
    }

    const conditionBadge = (c) => {
        const map = {
            'baik':        '<span class="badge bg-success">Baik</span>',
            'rusak ringan': '<span class="badge bg-warning text-dark">Rusak Ringan</span>',
            'rusak':       '<span class="badge bg-danger">Rusak</span>',
        };
        return map[c] ?? `<span class="badge bg-secondary">${c}</span>`;
    };

    let rows = details.map((d, i) => `
        <tr>
            <td>${i + 1}</td>
            <td>${d.inventory_code ?? '-'}</td>
            <td>${d.item_name ?? '-'}</td>
            <td>${conditionBadge(d.condition_before)}</td>
            <td>
                <select name="${namePrefix}[${d.id}]" class="form-select form-select-sm" required>
                    <option value="baik">Baik</option>
                    <option value="rusak ringan">Rusak Ringan</option>
                    <option value="rusak">Rusak</option>
                </select>
            </td>
        </tr>
    `).join('');

    return `
        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Kode Inventaris</th>
                        <th>Nama Barang</th>
                        <th>Kondisi Awal</th>
                        <th>Kondisi Kembali <span class="text-danger"></span></th>
                    </tr>
                </thead>
                <tbody>${rows}</tbody>
            </table>
        </div>
    `;
}

// ============================================================
// APPROVE MODAL
// ============================================================
function openApproveModal(id, code, name, count, loanDate, returnDate) {
    document.getElementById('approveForm').action = `${baseUrl}/${id}/approve`;
    document.getElementById('approve-loan-code').textContent  = code;
    document.getElementById('approve-user-name').textContent  = name;
    document.getElementById('approve-item-count').textContent = count;
    document.getElementById('approve-loan-date').textContent  = loanDate;
    document.getElementById('approve-return-date').textContent = returnDate;
    new bootstrap.Modal(document.getElementById('approveModal')).show();
}

// ============================================================
// REJECT MODAL
// ============================================================
function openRejectModal(id, code, name) {
    document.getElementById('rejectForm').action = `${baseUrl}/${id}/reject`;
    document.getElementById('rejectForm').querySelector('textarea').value = '';
    document.getElementById('reject-loan-code').textContent = code;
    document.getElementById('reject-user-name').textContent = name;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

// ============================================================
// CONFIRM BORROWED MODAL
// ============================================================
function openConfirmBorrowedModal(id, code, name, count) {
    document.getElementById('confirmBorrowedForm').action = `${baseUrl}/${id}/confirm-borrowed`;
    document.getElementById('confirm-loan-code').textContent  = code;
    document.getElementById('confirm-user-name').textContent  = name;
    document.getElementById('confirm-item-count').textContent = count;
    new bootstrap.Modal(document.getElementById('confirmBorrowedModal')).show();
}

// ============================================================
// APPROVE RETURN MODAL — fetch detail unit via AJAX
// ============================================================
function openApproveReturnModal(id) {
    document.getElementById('approveReturnForm').action = `${baseUrl}/${id}/approve-return`;
    document.getElementById('approveReturnForm').querySelector('textarea').value = '';
    document.getElementById('approve-return-units-table').innerHTML = `
        <div class="text-center py-3">
            <div class="spinner-border spinner-border-sm text-success"></div>
            <span class="ms-2">Memuat data unit...</span>
        </div>`;

    new bootstrap.Modal(document.getElementById('approveReturnModal')).show();

    fetch(`${baseUrl}/${id}/json`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const d = data.data;
                document.getElementById('approve-return-loan-code').textContent    = d.loan_code;
                document.getElementById('approve-return-user-name').textContent    = d.user_name;
                document.getElementById('approve-return-requested-at').textContent = d.return_requested_at ?? '-';

                const notesWrap = document.getElementById('approve-return-notes-wrap');
                if (d.return_request_notes) {
                    document.getElementById('approve-return-request-notes').textContent = d.return_request_notes;
                    notesWrap.classList.remove('d-none');
                } else {
                    notesWrap.classList.add('d-none');
                }

                document.getElementById('approve-return-units-table').innerHTML =
                    buildUnitsTable(d.details, 'condition_after');
            }
        })
        .catch(() => {
            document.getElementById('approve-return-units-table').innerHTML =
                '<div class="alert alert-danger">Gagal memuat data unit.</div>';
        });
}

// ============================================================
// REJECT RETURN MODAL
// ============================================================
function openRejectReturnModal(id, code, name) {
    document.getElementById('rejectReturnForm').action = `${baseUrl}/${id}/reject-return`;
    document.getElementById('rejectReturnForm').querySelector('textarea').value = '';
    document.getElementById('reject-return-loan-code').textContent = code;
    document.getElementById('reject-return-user-name').textContent = name;
    new bootstrap.Modal(document.getElementById('rejectReturnModal')).show();
}

// ============================================================
// DETAIL MODAL (read-only, AJAX)
// ============================================================
function showDetailModal(loanId) {
    const modalBody = document.getElementById('detailModalBody');

    modalBody.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2">Memuat detail peminjaman...</p>
        </div>`;

    new bootstrap.Modal(document.getElementById('detailModal')).show();

    fetch(`${baseUrl}/${loanId}/json`)
        .then(r => r.json())
        .then(data => {
            if (!data.success) throw new Error();
            const d = data.data;

            const conditionBadge = (c) => {
                if (!c) return '<span class="badge bg-secondary">-</span>';
                const map = {
                    'baik':        '<span class="badge bg-success">Baik</span>',
                    'rusak ringan': '<span class="badge bg-warning text-dark">Rusak Ringan</span>',
                    'rusak':       '<span class="badge bg-danger">Rusak</span>',
                };
                return map[c] ?? `<span class="badge bg-secondary">${c}</span>`;
            };

            let detailsHtml = (d.details ?? []).map((detail, i) => `
                <tr>
                    <td>${i + 1}</td>
                    <td>${detail.inventory_code ?? '-'}</td>
                    <td>${detail.item_name ?? '-'}</td>
                    <td>${conditionBadge(detail.condition_before)}</td>
                    <td>${conditionBadge(detail.condition_after)}</td>
                </tr>
            `).join('') || '<tr><td colspan="5" class="text-center py-3 text-muted">Tidak ada data barang</td></tr>';

            let suratHtml = '';
            if (d.surat_url) {
                suratHtml = `
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="mdi mdi-file-pdf text-danger me-2"></i>Surat Peminjaman</h6>
                            <div>
                                <a href="${d.surat_url}" class="btn btn-sm btn-primary me-2" target="_blank">
                                    <i class="mdi mdi-open-in-new"></i> Buka Baru
                                </a>
                                <a href="${d.surat_url}" download="Surat_${d.loan_code}.pdf" class="btn btn-sm btn-success">
                                    <i class="mdi mdi-download"></i> Unduh
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div style="height: 400px; overflow: auto; border: 1px solid #dee2e6; border-radius: 4px;">
                                <div id="pdf-viewer-admin-${loanId}" style="width: 100%; padding: 20px; text-align: center;">
                                    
                                </div>
                            </div>
                            <div class="mt-2 text-center">
                                <small class="text-muted">PDF Viewer - Scroll untuk melihat halaman berikutnya</small>
                            </div>
                        </div>
                    </div>`;
            }

            let returnRequestHtml = '';
            if (d.return_request_status === 'pending') {
                returnRequestHtml = `
                    <div class="alert" style="background-color:#f3eeff; border-left:4px solid #6f42c1;">
                        <i class="mdi mdi-keyboard-return me-2" style="color:#6f42c1"></i>
                        <strong>Pengajuan Pengembalian Masuk</strong><br>
                        Diajukan: ${d.return_requested_at ?? '-'}<br>
                        ${d.return_request_notes ? 'Catatan: ' + d.return_request_notes : ''}
                    </div>`;
            }

            modalBody.innerHTML = `
                ${returnRequestHtml}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><th>Kode</th><td>: ${d.loan_code}</td></tr>
                                    <tr><th>Peminjam</th><td>: ${d.user_name}</td></tr>
                                    <tr><th>Email</th><td>: ${d.user_email}</td></tr>
                                    <tr><th>Tanggal Pinjam</th><td>: ${d.loan_date}</td></tr>
                                    <tr><th>Rencana Kembali</th><td>: ${d.return_date}</td></tr>
                                    <tr><th>Tanggal Kembali</th><td>: ${d.actual_return_date || '-'}</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><th>Status</th><td>: ${d.status_badge}</td></tr>
                                    <tr><th>Tujuan</th><td>: ${d.purpose || '-'}</td></tr>
                                    <tr><th>Disetujui</th><td>: ${d.approved_at || '-'}</td></tr>
                                    <tr><th>Oleh</th><td>: ${d.approver_name || '-'}</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                ${suratHtml}
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Daftar Barang yang Dipinjam</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Inventaris</th>
                                        <th>Nama Barang</th>
                                        <th>Kondisi Awal</th>
                                        <th>Kondisi Akhir</th>
                                    </tr>
                                </thead>
                                <tbody>${detailsHtml}</tbody>
                            </table>
                        </div>
                    </div>
                </div>`;
            
            // Load PDF after HTML is rendered
            if (d.surat_url) {
                setTimeout(() => {
                    loadPdfViewerAdmin(`pdf-viewer-admin-${loanId}`, d.surat_url);
                }, 100);
            }
        })
        .catch(() => {
            modalBody.innerHTML = `
                <div class="alert alert-danger">
                    <i class="mdi mdi-alert-circle me-2"></i>Terjadi kesalahan saat memuat data.
                </div>`;
        });
}

function loadPdfViewerAdmin(containerId, pdfUrl) {
    const container = document.getElementById(containerId);
    if (!container) {
        console.error('Container not found:', containerId);
        return;
    }

    try {
        const loadingTask = pdfjsLib.getDocument({
            url: pdfUrl,
            withCredentials: true
        });
                    
        loadingTask.promise.then(pdf => {
            const renderPages = [];
            for (let i = 1; i <= Math.min(pdf.numPages, 3); i++) {
                renderPages.push(
                    pdf.getPage(i).then(page => {
                        const viewport = page.getViewport({ scale: 1.5 });
                        const canvas = document.createElement('canvas');
                        const context = canvas.getContext('2d');
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;
                        canvas.style.marginBottom = '10px';
                        canvas.style.border = '1px solid #ddd';
                        canvas.style.maxWidth = '100%';
                        canvas.style.height = 'auto';
                        canvas.style.display = 'block';

                        const renderContext = {
                            canvasContext: context,
                            viewport: viewport
                        };
                        return page.render(renderContext).promise.then(() => {
                            container.appendChild(canvas);
                        });
                    })
                );
            }
            return Promise.all(renderPages);
        }).catch(error => {
            console.error('Error loading PDF:', error);
            container.innerHTML = '<div class="alert alert-warning m-2"><i class="mdi mdi-alert"></i> Gagal memuat PDF. URL: ' + pdfUrl + '</div>';
        });
    } catch (error) {
        console.error('Error initializing PDF.js:', error);
        container.innerHTML = '<div class="alert alert-warning m-2"><i class="mdi mdi-alert"></i> Error: ' + error.message + '</div>';
    }
}
</script>
@endpush

@endsection