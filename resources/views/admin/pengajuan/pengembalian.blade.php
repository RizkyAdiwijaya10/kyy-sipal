@extends('layouts.app')

@section('title', 'Pengajuan Pengembalian')
@section('page-title', 'Pengajuan Pengembalian')
@section('page-subtitle', 'Kelola pengajuan pengembalian barang dari user')

@section('content')
    <div class="row">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Menunggu
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
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Ditolak
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['rejected'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="mdi mdi-close-circle-outline fa-2x text-danger"></i>
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
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Total Pengajuan
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="mdi mdi-clipboard-account-outline fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body mb-0">
                {{-- FILTER --}}

                <form method="GET" action="{{ route('admin.loans.return-requests') }}" id="filterForm">
                    <div class="row g-2 mb-4">

                        {{-- Search --}}
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="mdi mdi-magnify text-muted"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0"
                                    placeholder="Cari kode / peminjam / barang..." value="{{ request('search') }}">
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-2">
                            <select name="status" class="form-select"
                                onchange="document.getElementById('filterForm').submit()">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu
                                </option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui
                                </option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak
                                </option>
                            </select>
                        </div>

                        {{-- Tanggal Pengajuan Dari --}}
                        <div class="col-md-2">
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}"
                                title="Tanggal pengajuan dari">
                        </div>

                        {{-- Tanggal Pengajuan Sampai --}}
                        <div class="col-md-2">
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}"
                                title="Tanggal pengajuan sampai">
                        </div>

                        {{-- Tombol --}}
                        <div class="col-md-3 d-flex gap-2 align-items-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-filter"></i> Filter
                            </button>
                            @if (request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                                <a href="{{ route('admin.loans.return-requests') }}" class="btn btn-outline-secondary">
                                    <i class="mdi mdi-close"></i> Reset
                                </a>
                                <span class="badge bg-info align-self-center">
                                    {{ $loans->total() }} hasil
                                </span>
                            @endif
                        </div>

                    </div>
                </form>
                {{-- END FILTER --}}

                {{-- TABLE --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Peminjam</th>
                                <th>Tgl Pinjam</th>
                                <th>Rencana Kembali</th>
                                <th>Barang</th>
                                <th>Kondisi Akhir</th>
                                <th>Status</th>
                                <th width="25%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loans as $loan)
                                <tr class="@if ($loan->status == 'borrowed' && $loan->return_date < now()) table-danger @endif">
                                    <td class="fw-semibold">{{ $loan->loan_code }}</td>
                                    <td>{{ $loan->user->name }}</td>
                                    <td>{{ $loan->loan_date->format('d/m/Y') }}</td>
                                    <td>{{ $loan->return_date->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $loan->details->count() }} barang
                                        </span>
                                    </td>

                                    {{-- KOLOM KONDISI AKHIR --}}
                                    <td>
                                        @php
                                            $allChecked = true;
                                            $conditions = [];
                                            foreach ($loan->details as $detail) {
                                                if (!$detail->condition_after) {
                                                    $allChecked = false;
                                                } else {
                                                    $conditions[] = $detail->condition_after;
                                                }
                                            }
                                        @endphp
                                        @if ($allChecked && count($conditions) > 0)
                                            <span>
                                                {{ implode(', ', array_unique($conditions)) }}
                                            </span>
                                        @elseif($loan->return_request_status == 'approved')
                                            <span class="badge bg-success">Sudah dicek</span>
                                        @else
                                            <span class="badge bg-secondary">Belum dicek</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($loan->return_request_status == 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif($loan->return_request_status == 'approved')
                                            <span class="badge bg-success">Disetujui</span>
                                        @else
                                            <span class="badge bg-danger">Ditolak</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <button type="button" class="btn btn-info btn-sm"
                                                onclick="showReturnDetailModal({{ $loan->id }})">
                                                <i class="mdi mdi-eye"></i> Detail
                                            </button>

                                            @if ($loan->return_request_status == 'pending')
                                                <button type="button" class="btn btn-success btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#approveReturnModal{{ $loan->id }}">
                                                    <i class="mdi mdi-check"></i> Setujui
                                                </button>

                                                <button type="button" class="btn btn-danger btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#rejectReturnModal{{ $loan->id }}">
                                                    <i class="mdi mdi-close"></i> Tolak
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                {{-- MODAL APPROVE RETURN --}}
                                <div class="modal fade" id="approveReturnModal{{ $loan->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST"
                                                action="{{ route('admin.loans.return-request.approve', $loan) }}">
                                                @csrf
                                                <div class="modal-header bg-gradient-success text-white">
                                                    <h5 class="modal-title">Setujui Pengembalian</h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-info">
                                                        <strong>Kode:</strong> {{ $loan->loan_code }}<br>
                                                        <strong>Peminjam:</strong> {{ $loan->user->name }}<br>
                                                        <strong>Jumlah Barang:</strong> {{ $loan->details->count() }} item
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Kondisi Barang <span
                                                                class="text-danger"></span></label>
                                                        <select name="condition_after" class="form-control" required>
                                                            <option value="baik">Baik</option>
                                                            <option value="maintenance">Maintenance</option>
                                                            <option value="rusak">Rusak</option>
                                                            <option value="hilang">Hilang</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Catatan (Opsional)</label>
                                                        <textarea name="return_notes" class="form-control" rows="2" placeholder="Masukkan catatan jika ada"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-success">Ya, Setujui</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- MODAL REJECT RETURN --}}
                                <div class="modal fade" id="rejectReturnModal{{ $loan->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST"
                                                action="{{ route('admin.loans.return-request.reject', $loan) }}">
                                                @csrf
                                                <div class="modal-header bg-gradient-danger text-white">
                                                    <h5 class="modal-title">Tolak Pengembalian</h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-warning">
                                                        <i class="mdi mdi-alert-circle-outline me-2"></i>
                                                        <strong>Kode:</strong> {{ $loan->loan_code }}<br>
                                                        <strong>Peminjam:</strong> {{ $loan->user->name }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Alasan Penolakan <span
                                                                class="text-danger"></span></label>
                                                        <textarea name="reject_reason" class="form-control" rows="3" placeholder="Masukkan alasan penolakan..."
                                                            required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger">Ya, Tolak</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-5">
                                        <i class="mdi mdi-inbox-outline display-3 d-block mb-3"></i>
                                        <p class="mb-0">Tidak ada pengajuan pengembalian</p>
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

    {{-- MODAL DETAIL PENGAJUAN --}}
    <div class="modal fade" id="returnDetailModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header text-black">
                    <h5 class="modal-title">
                        <i class="mdi mdi-information-outline me-2"></i>
                        Detail Pengajuan Pengembalian
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="returnDetailModalBody">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat detail pengajuan...</p>
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

    @push('styles')
        <style>
            .bg-gradient-warning {
                background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
            }

            .bg-gradient-success {
                background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
            }

            .bg-gradient-danger {
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            }

            .bg-gradient-info {
                background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%);
            }

            .modal-lg {
                max-width: 900px;
            }

            .table-borderless td,
            .table-borderless th {
                padding: 0.3rem 0;
            }

            .card-header {
                background-color: #f8f9fc;
            }

            .badge {
                font-weight: 500;
                padding: 0.35rem 0.65rem;
                border-radius: 6px;
            }

            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
                border-radius: 4px;
            }

            .table td {
                vertical-align: middle;
            }

            .nav-tabs .nav-link {
                color: #4e73df;
                font-weight: 500;
            }

            .nav-tabs .nav-link.active {
                background-color: #4e73df;
                color: white;
                border-color: #4e73df;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function showReturnDetailModal(loanId) {
                const modal = new bootstrap.Modal(document.getElementById('returnDetailModal'));
                const modalBody = document.getElementById('returnDetailModalBody');

                modalBody.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Memuat detail pengajuan...</p>
        </div>
    `;

                modal.show();

                fetch(`{{ url('admin/loans') }}/${loanId}/json`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const d = data.data;
                            let detailsHtml = '';

                            if (d.details && d.details.length > 0) {
                                d.details.forEach((detail, index) => {
                                    let kondisiAkhir = detail.condition_after_badge ||
                                        '<span class="badge bg-secondary">Belum dicek</span>';

                                    detailsHtml += `
                            <tr>
                                <td class="text-center">${index + 1}</td>
                                <td>${detail.inventory_code || '-'}</td>
                                <td>${detail.item_name}</td>
                                <td>${detail.condition_before_badge}</td>
                                <td>${kondisiAkhir}</td>
                            </tr>
                        `;
                                });
                            } else {
                                detailsHtml = '<tr><td colspan="5" class="text-center">Tidak ada data barang</td></tr>';
                            }

                            // Informasi pengajuan pengembalian
                            let returnRequestInfo = '';
                            if (d.return_requested_at) {
                                let statusColor = '';
                                let statusText = '';
                                if (d.return_request_status == 'pending') {
                                    statusColor = 'warning';
                                    statusText = 'Menunggu Persetujuan';
                                } else if (d.return_request_status == 'approved') {
                                    statusColor = 'success';
                                    statusText = 'Disetujui';
                                } else if (d.return_request_status == 'rejected') {
                                    statusColor = 'danger';
                                    statusText = 'Ditolak';
                                }

                                returnRequestInfo = `
                        <div class="alert alert-${statusColor} mt-3">
                            <i class="mdi mdi-information-outline me-2"></i>
                            <strong>Status Pengajuan Pengembalian:</strong> ${statusText}<br>
                            <strong>Tanggal Pengajuan:</strong> ${d.return_requested_at}<br>
                            <strong>Catatan Pengajuan:</strong> ${d.return_request_notes || 'Tidak ada catatan'}
                        </div>
                    `;
                            }

                            modalBody.innerHTML = `
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <tr><th width="120">Kode</th><td>: <strong>${d.loan_code}</strong></td></tr>
                                        <tr><th>Peminjam</th><td>: ${d.user_name}</td></tr>
                                        <tr><th>Tanggal Pinjam</th><td>: ${d.loan_date}</td></tr>
                                        <tr><th>Rencana Kembali</th><td>: ${d.return_date}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <tr><th width="120">Tujuan</th><td>: ${d.purpose}</td></tr>
                                        <tr><th>Status</th><td>: ${d.status_badge}</td></tr>
                                        <tr><th>Disetujui</th><td>: ${d.approved_at || '-'}</td></tr>
                                        <tr><th>Oleh</th><td>: ${d.approver_name || '-'}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                    
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
        </script>
    @endpush
@endsection
