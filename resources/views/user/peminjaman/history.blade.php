@extends('layouts.app')

@section('title', 'Riwayat Peminjaman')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Jumlah Item</th>
                        <th>Status</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $loan->loan_code }}</strong></td>
                        <td>{{ $loan->loan_date->format('d/m/Y') }}</td>
                        <td>{{ $loan->return_date->format('d/m/Y') }}</td>
                        <td>{{ $loan->details->count() }} item</td>
                        
                        {{-- STATUS GABUNGAN --}}
                        <td>
                            @php
                                if ($loan->status == 'returned') {
                                    $statusBadge = 'success';
                                    $statusText = 'Dikembalikan';
                                }
                                elseif ($loan->status == 'rejected') {
                                    $statusBadge = 'danger';
                                    $statusText = 'Ditolak';
                                }
                                elseif ($loan->status == 'cancelled') {
                                    $statusBadge = 'secondary';
                                    $statusText = 'Dibatalkan';
                                }
                                elseif ($loan->return_request_status == 'pending') {
                                    $statusBadge = 'warning';
                                    $statusText = 'Pengajuan Pengembalian';
                                }
                                elseif ($loan->return_request_status == 'approved') {
                                    $statusBadge = 'success';
                                    $statusText = 'Pengembalian Disetujui';
                                }
                                elseif ($loan->return_request_status == 'rejected') {
                                    $statusBadge = 'danger';
                                    $statusText = 'Pengembalian Ditolak';
                                }
                                elseif ($loan->status == 'approved') {
                                    $statusBadge = 'info';
                                    $statusText = 'Disetujui';
                                }
                                elseif ($loan->status == 'borrowed') {
                                    if ($loan->return_date < now()) {
                                        $statusBadge = 'dark';
                                        $statusText = 'Terlambat';
                                    } else {
                                        $statusBadge = 'primary';
                                        $statusText = 'Dipinjam';
                                    }
                                }
                                elseif ($loan->status == 'pending') {
                                    $statusBadge = 'warning';
                                    $statusText = 'Menunggu Persetujuan';
                                }
                                else {
                                    $statusBadge = 'secondary';
                                    $statusText = ucfirst($loan->status);
                                }
                            @endphp
                            <span class="badge bg-{{ $statusBadge }}">{{ $statusText }}</span>
                            
                            @if($loan->status == 'borrowed' && $loan->return_date < now())
                                <br>
                                <small class="text-danger">
                                    {{ \Carbon\Carbon::parse($loan->return_date)->diffForHumans(now(), true) }} terlambat
                                </small>
                            @endif
                        </td>
                    
                        {{-- CATATAN --}}
                        <td>
                            @if($loan->status == 'rejected' && $loan->notes)
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#rejectNoteModal{{ $loan->id }}">
                                    <i class="mdi mdi-message-text"></i> Lihat Alasan
                                </button>
                            @elseif($loan->status == 'returned' && $loan->notes)
                                <button type="button" class="btn btn-sm btn-outline-success" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#returnNoteModal{{ $loan->id }}">
                                    <i class="mdi mdi-message-text"></i> Lihat Catatan
                                </button>
                            @elseif($loan->return_request_status == 'rejected' && $loan->notes)
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#returnRejectNoteModal{{ $loan->id }}">
                                    <i class="mdi mdi-message-text"></i> Lihat Alasan
                                </button>
                            @elseif($loan->return_request_status == 'pending' && $loan->return_request_notes)
                                <button type="button" class="btn btn-sm btn-outline-info" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#returnPendingNoteModal{{ $loan->id }}">
                                    <i class="mdi mdi-message-text"></i> Lihat Catatan
                                </button>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        {{-- AKSI --}}
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                {{-- TOMBOL DETAIL - BUKA MODAL --}}
                                <button type="button" class="btn btn-sm btn-info" 
                                        onclick="showDetailModal({{ $loan->id }})">
                                    <i class="mdi mdi-eye"></i> Detail
                                </button>
                                
                                {{-- TOMBOL AJUKAN PENGEMBALIAN - BUKA MODAL --}}
                                @if($loan->status == 'borrowed' && !$loan->return_requested_at)
                                    <button type="button" class="btn btn-sm btn-warning" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#returnRequestModal{{ $loan->id }}">
                                        <i class="mdi mdi-backup-restore"></i> Ajukan Kembali
                                    </button>
                                @endif
                                
                                @if($loan->return_request_status == 'pending')
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="cancelReturnRequest({{ $loan->id }})">
                                        <i class="mdi mdi-close"></i> Batal
                                    </button>
                                    
                                    <form id="cancel-return-form-{{ $loan->id }}" 
                                        action="{{ route('user.returns.cancel', $loan) }}" 
                                        method="POST" 
                                        style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @endif
                                
                                {{-- Tombol Batalkan Peminjaman (hanya pending) --}}
                                @if($loan->status == 'pending')
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#cancelModal{{ $loan->id }}">
                                        <i class="mdi mdi-close"></i> Batal
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>

                    {{-- MODAL AJUKAN PENGEMBALIAN --}}
                    @if($loan->status == 'borrowed' && !$loan->return_requested_at)
                    <div class="modal fade" id="returnRequestModal{{ $loan->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('user.returns.store', $loan) }}">
                                    @csrf
                                    <div class="modal-header bg-warning text-dark">
                                        <h5 class="modal-title">
                                            <i class="mdi mdi-backup-restore me-2"></i>
                                            Ajukan Pengembalian Barang
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-info">
                                            <i class="mdi mdi-information-outline me-2"></i>
                                            <strong>Informasi Peminjaman:</strong><br>
                                            Kode: {{ $loan->loan_code }}<br>
                                            Tanggal Pinjam: {{ $loan->loan_date->format('d/m/Y') }}<br>
                                            Rencana Kembali: {{ $loan->return_date->format('d/m/Y') }}
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card bg-light">
                                                    <div class="card-header">
                                                        <strong>Daftar Barang Dipinjam</strong>
                                                    </div>
                                                    <div class="card-body p-0">
                                                        <div class="list-group list-group-flush">
                                                            @foreach($loan->details as $detail)
                                                            <div class="list-group-item">
                                                                <div class="d-flex justify-content-between">
                                                                    <div>
                                                                        <strong>{{ $detail->itemUnit->item->name }}</strong>
                                                                        <br>
                                                                        <small class="text-muted">
                                                                            Kode: {{ $detail->itemUnit->inventory_code ?? '-' }}
                                                                        </small>
                                                                    </div>
                                                                    <div>
                                                                        <span class="badge bg-secondary">1 unit</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="alert alert-warning">
                                                    <i class="mdi mdi-alert-circle-outline me-2"></i>
                                                    <strong>Perhatian:</strong>
                                                    <ul class="mb-0 mt-2 small">
                                                        <li>Pastikan barang dalam kondisi baik</li>
                                                        <li>Admin akan mengecek kondisi barang</li>
                                                        <li>Denda dikenakan jika terlambat</li>
                                                        <li>Pengajuan dapat dibatalkan</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            <i class="mdi mdi-close"></i> Batal
                                        </button>
                                        <button type="submit" class="btn btn-warning">
                                            <i class="mdi mdi-send"></i> Ajukan Pengembalian
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- MODAL-MODAL LAINNYA (Reject Note, Return Note, dll) --}}
                    @if($loan->status == 'rejected' && $loan->notes)
                    <div class="modal fade" id="rejectNoteModal{{ $loan->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">Alasan Penolakan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Kode Peminjaman:</strong> {{ $loan->loan_code }}</p>
                                    <p><strong>Tanggal Ditolak:</strong> {{ $loan->approved_at ? \Carbon\Carbon::parse($loan->approved_at)->format('d/m/Y H:i') : '-' }}</p>
                                    <hr>
                                    <p><strong>Alasan:</strong></p>
                                    <div class="alert alert-danger">
                                        {{ $loan->notes }}
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($loan->status == 'returned' && $loan->notes)
                    <div class="modal fade" id="returnNoteModal{{ $loan->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title">Catatan Pengembalian</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Kode Peminjaman:</strong> {{ $loan->loan_code }}</p>
                                    <p><strong>Tanggal Dikembalikan:</strong> {{ $loan->actual_return_date ? \Carbon\Carbon::parse($loan->actual_return_date)->format('d/m/Y H:i') : '-' }}</p>
                                    <hr>
                                    <p><strong>Catatan:</strong></p>
                                    <div class="alert alert-success">
                                        {{ $loan->notes }}
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($loan->return_request_status == 'rejected' && $loan->notes)
                    <div class="modal fade" id="returnRejectNoteModal{{ $loan->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">Alasan Penolakan Pengembalian</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Kode Peminjaman:</strong> {{ $loan->loan_code }}</p>
                                    <p><strong>Tanggal Ditolak:</strong> {{ $loan->return_approved_at ? \Carbon\Carbon::parse($loan->return_approved_at)->format('d/m/Y H:i') : '-' }}</p>
                                    <hr>
                                    <p><strong>Alasan:</strong></p>
                                    <div class="alert alert-danger">
                                        {{ $loan->notes }}
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($loan->return_request_status == 'pending' && $loan->return_request_notes)
                    <div class="modal fade" id="returnPendingNoteModal{{ $loan->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-info text-white">
                                    <h5 class="modal-title">Catatan Pengajuan Pengembalian</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Kode Peminjaman:</strong> {{ $loan->loan_code }}</p>
                                    <p><strong>Tanggal Diajukan:</strong> {{ $loan->return_requested_at ? \Carbon\Carbon::parse($loan->return_requested_at)->format('d/m/Y H:i') : '-' }}</p>
                                    <hr>
                                    <p><strong>Catatan:</strong></p>
                                    <div class="alert alert-info">
                                        {{ $loan->return_request_notes ?? '-' }}
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($loan->status == 'pending')
                    <div class="modal fade" id="cancelModal{{ $loan->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('user.loans.cancel', $loan) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Batalkan Peminjaman</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-warning">
                                            <i class="mdi mdi-alert-circle-outline me-2"></i>
                                            Apakah Anda yakin ingin membatalkan peminjaman ini?
                                        </div>
                                        <p><strong>Kode:</strong> {{ $loan->loan_code }}</p>
                                        <p><strong>Tanggal Pinjam:</strong> {{ $loan->loan_date->format('d/m/Y') }}</p>
                                        <p><strong>Jumlah Item:</strong> {{ $loan->details->count() }} barang</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                                        <button type="submit" class="btn btn-danger">Ya, Batalkan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    @empty
                    <tr>
                        <td colspan="8" class="text-center">
                            <div class="py-4">
                                <i class="mdi mdi-history display-4 d-block mb-3 text-muted"></i>
                                <p class="text-muted mb-0">Belum ada riwayat peminjaman</p>
                                <a href="{{ route('user.items.index') }}" class="btn btn-primary mt-3">
                                    Pinjam Alat
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $loans->links() }}
        </div>
    </div>
</div>

{{-- MODAL DETAIL PEMINJAMAN --}}
<div class="modal fade" id="detailModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="mdi mdi-information-outline me-2"></i>
                    Detail Peminjaman
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat detail peminjaman...</p>
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
    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
    const modalBody = document.getElementById('detailModalBody');
    
    modalBody.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Memuat detail peminjaman...</p>
        </div>
    `;
    
    modal.show();
    
    fetch(`{{ url('user/loans') }}/${loanId}/json`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const d = data.data;
                
                let detailsHtml = '';
                if (d.details && d.details.length > 0) {
                    d.details.forEach((detail, index) => {
                        let kondisiAkhir = detail.condition_after_badge || '<span class="badge bg-secondary">Belum dicek</span>';
                        
                        detailsHtml += `
                            <tr>
                                <td class="text-center">${index + 1}<\/td>
                                <td>${detail.inventory_code || '-'}<\/td>
                                <td>${detail.item_name}<\/td>
                                <td>${detail.condition_before_badge}<\/td>
                                <td>${kondisiAkhir}<\/td>
                            </tr>
                        `;
                    });
                } else {
                    detailsHtml = '<tr><td colspan="5" class="text-center">Tidak ada data barang<\/td><\/tr>';
                }
                
                let suratHtml = '';
                if (d.generated_pdf_path) {
                    suratHtml = `
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Surat Peminjaman</h5>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="d-flex align-items-center">
                                            <i class="mdi mdi-file-pdf text-danger" style="font-size: 48px;"></i>
                                            <div class="ms-3">
                                                <strong>${d.surat_number || '-'}</strong>
                                                <br>
                                                <small class="text-muted">Dibuat: ${d.created_at}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <a href="{{ url('storage') }}/${d.generated_pdf_path}" class="btn btn-primary btn-sm" target="_blank">
                                            <i class="mdi mdi-eye"></i> Lihat Surat
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
                                        <tr><th width="120">Kode</th><td>: <strong>${d.loan_code}</strong><\/td><\/tr>
                                        <tr><th>Nomor Surat</th><td>: ${d.surat_number || '-'}<\/td><\/tr>
                                        <tr><th>Status</th><td>: ${d.status_badge}<\/td><\/tr>
                                        <tr><th>Tujuan</th><td>: ${d.purpose}<\/td><\/tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <tr><th width="120">Peminjam</th><td>: ${d.user_name}<\/td><\/tr>
                                        <tr><th>Tanggal Pinjam</th><td>: ${d.loan_date}<\/td><\/tr>
                                        <tr><th>Rencana Kembali</th><td>: ${d.return_date}<\/td><\/tr>
                                        <tr><th>Tanggal Kembali</th><td>: ${d.actual_return_date || '-'}<\/td><\/tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                    ${suratHtml}
                    
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Daftar Barang yang Dipinjam</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50">No</th>
                                            <th>Kode Inventaris</th>
                                            <th>Nama Barang</th>
                                            <th>Kondisi Awal</th>
                                            <th>Kondisi Akhir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${detailsHtml}
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

document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session("success") }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '{{ session("error") }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif
});

function cancelReturnRequest(loanId) {
    Swal.fire({
        title: 'Batalkan Pengajuan?',
        text: 'Apakah Anda yakin ingin membatalkan pengajuan pengembalian ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Batalkan!',
        cancelButtonText: 'Tidak'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('cancel-return-form-' + loanId).submit();
        }
    });
}

function cancelLoan(loanId) {
    Swal.fire({
        title: 'Batalkan Peminjaman?',
        text: 'Apakah Anda yakin ingin membatalkan peminjaman ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Batalkan!',
        cancelButtonText: 'Tidak'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('cancel-loan-form-' + loanId).submit();
        }
    });
}

// Session messages
@if(session('success'))
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '{{ session('success') }}',
    timer: 3000,
    showConfirmButton: false
});
@endif

@if(session('error'))
Swal.fire({
    icon: 'error',
    title: 'Gagal!',
    text: '{{ session('error') }}',
    timer: 3000,
    showConfirmButton: false
});
@endif
</script>
@endpush

@push('styles')
<style>
    .table td {
        vertical-align: middle;
    }
    .badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
        border-radius: 6px;
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 4px;
    }
    .table {
        min-width: 800px;
    }
    .modal-lg {
        max-width: 900px;
    }
    .modal-body .card {
        margin-bottom: 0;
    }
    .list-group-item {
        border-left: none;
        border-right: none;
    }
    .list-group-item:first-child {
        border-top: none;
    }
</style>
@endpush
@endsection