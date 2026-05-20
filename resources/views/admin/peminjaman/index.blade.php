@extends('layouts.app')

@section('title', 'Manajemen Peminjaman')
@section('page-title', 'Manajemen Peminjaman')
@section('page-subtitle', 'Kelola pengajuan peminjaman alat')

@section('content')
<div class="container">
    
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending
                            </div>
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
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Disetujui
                            </div>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Dipinjam
                            </div>
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
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Overdue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['overdue'] }}</div>
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

            {{-- FILTER DROPDOWN --}}
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            @php
                                $statusLabels = [
                                    null => 'Semua Status',
                                    'pending' => 'Pending',
                                    'approved' => 'Disetujui',
                                    'borrowed' => 'Dipinjam',
                                    'returned' => 'Dikembalikan',
                                    'rejected' => 'Ditolak'
                                ];
                                $currentStatus = request('status');
                                $buttonLabel = $statusLabels[$currentStatus] ?? 'Semua Status';
                            @endphp
                            {{ $buttonLabel }}
                        </button>
                        <ul class="dropdown-menu w-100">
                            <li>
                                <a class="dropdown-item {{ !request('status') ? 'active' : '' }}" 
                                   href="{{ route('admin.loans.index') }}">
                                    <i class="mdi mdi-view-dashboard me-2"></i> Semua Status
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item {{ request('status') == 'pending' ? 'active' : '' }}" 
                                   href="{{ route('admin.loans.index', ['status' => 'pending']) }}">
                                    <i class="mdi mdi-clock-outline text-warning me-2"></i> Pending
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request('status') == 'approved' ? 'active' : '' }}" 
                                   href="{{ route('admin.loans.index', ['status' => 'approved']) }}">
                                    <i class="mdi mdi-check-circle-outline text-primary me-2"></i> Disetujui
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request('status') == 'borrowed' ? 'active' : '' }}" 
                                   href="{{ route('admin.loans.index', ['status' => 'borrowed']) }}">
                                    <i class="mdi mdi-bookmark-outline text-info me-2"></i> Dipinjam
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request('status') == 'returned' ? 'active' : '' }}" 
                                   href="{{ route('admin.loans.index', ['status' => 'returned']) }}">
                                    <i class="mdi mdi-check-all text-success me-2"></i> Dikembalikan
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request('status') == 'rejected' ? 'active' : '' }}" 
                                   href="{{ route('admin.loans.index', ['status' => 'rejected']) }}">
                                    <i class="mdi mdi-close-circle-outline text-danger me-2"></i> Ditolak
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

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
                            <th width="25%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $loan)
                        <tr class="@if($loan->status == 'borrowed' && $loan->return_date < now()) table-danger @endif">
                            <td class="fw-semibold">{{ $loan->loan_code }}</td>
                            <td>{{ $loan->user->name }}</td>
                            <td>{{ $loan->loan_date->format('d/m/Y') }}</td>
                            <td>
                                {{ $loan->return_date->format('d/m/Y') }}
                                @if($loan->status == 'borrowed' && $loan->return_date < now())
                                    <br>
                                    <small class="text-danger">
                                        <i class="mdi mdi-alert-circle"></i> Terlambat
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span>
                                    {{ $loan->details->count() }} barang
                                </span>
                            </td>

                            {{-- STATUS BADGE --}}
                            <td>
                                @switch($loan->status)
                                    @case('pending')
                                        <span>Pending</span>
                                        @break
                                    @case('approved')
                                        <span>Disetujui</span>
                                        @break
                                    @case('borrowed')
                                        <span>Dipinjam</span>
                                        @break
                                    @case('returned')
                                        <span>Dikembalikan</span>
                                        @break
                                    @case('rejected')
                                        <span>Ditolak</span>
                                        @break
                                    @default
                                        <span>{{ $loan->status }}</span>
                                @endswitch
                            </td>

                            {{-- AKSI --}}
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    @if($loan->status == 'pending')
                                        <button type="button" class="btn btn-success btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#approveModal{{ $loan->id }}">
                                            <i class="mdi mdi-check"></i> Setujui
                                        </button>

                                        <button type="button" class="btn btn-danger btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#rejectModal{{ $loan->id }}">
                                            <i class="mdi mdi-close"></i> Tolak
                                        </button>
                                    @endif

                                    @if($loan->status == 'approved')
                                        <button type="button" class="btn btn-primary btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#confirmBorrowedModal{{ $loan->id }}">
                                            <i class="mdi mdi-bookmark"></i> Konfirmasi
                                        </button>
                                    @endif

                                    {{-- TOMBOL DETAIL - BUKA MODAL --}}
                                    <button type="button" class="btn btn-info btn-sm" 
                                            onclick="showDetailModal({{ $loan->id }})">
                                        <i class="mdi mdi-eye"></i> Detail
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- MODAL APPROVE --}}
                        <div class="modal fade" id="approveModal{{ $loan->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('admin.loans.approve', $loan) }}">
                                        @csrf
                                        <div class="modal-header bg-gradient-cyan text-white">
                                            <h5 class="modal-title">Setujui Peminjaman</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-info">
                                                <strong>Kode:</strong> {{ $loan->loan_code }}<br>
                                                <strong>Peminjam:</strong> {{ $loan->user->name }}<br>
                                                <strong>Jumlah Barang:</strong> {{ $loan->details->count() }} item<br>
                                                <strong>Tanggal Pinjam:</strong> {{ $loan->loan_date->format('d/m/Y') }}<br>
                                                <strong>Rencana Kembali:</strong> {{ $loan->return_date->format('d/m/Y') }}
                                            </div>
                                            <p>Apakah Anda yakin ingin menyetujui peminjaman ini?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-success">Ya, Setujui</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- MODAL REJECT --}}
                        <div class="modal fade" id="rejectModal{{ $loan->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('admin.loans.reject', $loan) }}">
                                        @csrf
                                        <div class="modal-header bg-gradient-orange text-white">
                                            <h5 class="modal-title">Tolak Peminjaman</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-warning">
                                                <i class="mdi mdi-alert-circle-outline me-2"></i>
                                                <strong>Kode:</strong> {{ $loan->loan_code }}<br>
                                                <strong>Peminjam:</strong> {{ $loan->user->name }}
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Alasan Penolakan <span class="text-danger"></span></label>
                                                <textarea name="reject_reason" class="form-control" rows="3" placeholder="Masukkan alasan penolakan..." required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger">Ya, Tolak</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- MODAL CONFIRM BORROWED --}}
                        <div class="modal fade" id="confirmBorrowedModal{{ $loan->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('admin.loans.confirm-borrowed', $loan) }}">
                                        @csrf
                                        <div class="modal-header bg-gradient-cyan text-white">
                                            <h5 class="modal-title">Konfirmasi Pengambilan Barang</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-info">
                                                <i class="mdi mdi-information-outline me-2"></i>
                                                <strong>Kode:</strong> {{ $loan->loan_code }}<br>
                                                <strong>Peminjam:</strong> {{ $loan->user->name }}<br>
                                                <strong>Jumlah Barang:</strong> {{ $loan->details->count() }} item
                                            </div>
                                            <p>Konfirmasi bahwa barang sudah diambil oleh peminjam?</p>
                                            <div class="alert alert-warning">
                                                <i class="mdi mdi-alert-circle-outline me-2"></i>
                                                Setelah dikonfirmasi, status akan berubah menjadi <strong>Dipinjam</strong>.
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Ya, Konfirmasi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- MODAL RETURN --}}
                        <div class="modal fade" id="returnModal{{ $loan->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('admin.loans.return', $loan) }}">
                                        @csrf
                                        <div class="modal-header bg-gradient-warning text-dark">
                                            <h5 class="modal-title">Konfirmasi Pengembalian Barang</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-info">
                                                <i class="mdi mdi-information-outline me-2"></i>
                                                <strong>Kode:</strong> {{ $loan->loan_code }}<br>
                                                <strong>Peminjam:</strong> {{ $loan->user->name }}<br>
                                                <strong>Rencana Kembali:</strong> {{ $loan->return_date->format('d/m/Y') }}
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Kondisi Barang <span class="text-danger"></span></label>
                                                <select name="condition_after" class="form-control" required>
                                                    <option value="baik">Baik</option>
                                                    <option value="maintenance">Maintenance</option>
                                                    <option value="rusak">Rusak</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Catatan (Opsional)</label>
                                                <textarea name="return_notes" class="form-control" rows="2" placeholder="Masukkan catatan jika ada kerusakan atau keterangan lain"></textarea>
                                            </div>
                                            <div class="alert alert-warning">
                                                <i class="mdi mdi-alert-circle-outline me-2"></i>
                                                Setelah dikonfirmasi, status akan berubah menjadi <strong>Dikembalikan</strong>.
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-warning">Ya, Konfirmasi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

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

{{-- MODAL DETAIL PEMINJAMAN --}}
<div class="modal fade" id="detailModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" id="detailModalHeader">
                <h5 class="modal-title">
                    <i class="mdi mdi-information-outline me-2"></i>
                    Detail Peminjaman
                </h5>
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
<script>
function showDetailModal(loanId) {
    // Tampilkan modal dengan loading
    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
    const modalBody = document.getElementById('detailModalBody');
    
    // Set loading state
    modalBody.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Memuat detail peminjaman...</p>
        </div>
    `;
    
    modal.show();
    
    // Fetch data via AJAX
    fetch(`{{ url('admin/loans') }}/${loanId}/json`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const d = data.data;
                
                // Build detail HTML
                let detailsHtml = '';
                d.details.forEach((detail, index) => {
                    detailsHtml += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${detail.inventory_code}</td>
                            <td>${detail.item_name}</td>
                            <td>${detail.condition_before_badge}</td>
                            <td>${detail.condition_after_badge}</td>
                        </tr>
                    `;
                });
                
                // Surat HTML
                let suratHtml = '';
                if (d.notes && d.notes !== 'null') {
                    suratHtml = `
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Surat Peminjaman</h5>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="d-flex align-items-center">
                                            <i class="mdi mdi-file-pdf text-danger" style="font-size: 48px;"></i>
                                            <div class="ms-3">
                                                <strong>${d.notes.split('/').pop()}</strong>
                                                <br>
                                                <small class="text-muted">Diupload: ${d.created_at}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <a href="{{ url('admin/loans') }}/${loanId}/view-surat" class="btn btn-primary btn-sm" target="_blank">
                                            <i class="mdi mdi-eye"></i> Lihat
                                        </a>
                                        <a href="{{ url('admin/loans') }}/${loanId}/download-surat" class="btn btn-success btn-sm">
                                            <i class="mdi mdi-download"></i> Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }
                
                modalBody.innerHTML = `
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
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
                                    <table class="table table-sm table-borderless">
                                        <tr><th>Status</th><td>: ${d.status_badge}</td></tr>
                                        <tr><th>Tujuan</th><td>: ${d.purpose}</td></tr>
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
                            <h5 class="mb-0">Daftar Barang yang Dipinjam</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Inventaris</th>
                                            <th>Nama Barang</th>
                                            <th>Kondisi Awal</th>
                                            <th>Kondisi Akhir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${detailsHtml || '<tr><td colspan="5" class="text-center">Tidak ada data barang</td></tr>'}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="mdi mdi-alert-circle me-2"></i>
                        Gagal memuat data detail peminjaman.
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            modalBody.innerHTML = `
                <div class="alert alert-danger">
                    <i class="mdi mdi-alert-circle me-2"></i>
                    Terjadi kesalahan saat memuat data.
                </div>
            `;
        });
}
</script>
@endpush

@push('styles')
{{-- <style>
.modal-lg {
    max-width: 900px;
}
.table-borderless td, .table-borderless th {
    padding: 0.3rem 0;
}
.card-header {
    background-color: #f8f9fc;
} --}}
</style>
@endpush
@endsection