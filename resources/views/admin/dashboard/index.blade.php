@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
{{-- STATISTIK UNIT & KATEGORI --}}
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Unit
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUnits }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="mdi mdi-barcode-scan fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Unit Tersedia
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $availableUnits }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="mdi mdi-check-circle fa-2x text-success"></i>
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
                            Unit Dipinjam
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $borrowedUnits }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="mdi mdi-bookmark fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Total Kategori
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCategories }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="mdi mdi-tag fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- PEMINJAMAN TERBARU --}}
<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="mdi mdi-clipboard-list me-2"></i> Peminjaman Terbaru
                </h6>
                <a href="{{ route('admin.loans.index') }}" class="btn btn-sm btn-primary">
                    Lihat Semua <i class="mdi mdi-arrow-right"></i>
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Peminjam</th>
                                <th>Tgl Pinjam</th>
                                <th>Rencana Kembali</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLoans as $loan)
                            <tr>
                                <td>{{ $loan->loan_code }}</td>
                                <td>{{ $loan->user->name }}</td>
                                <td>{{ $loan->loan_date->format('d/m/Y') }}</td>
                                <td>{{ $loan->return_date->format('d/m/Y') }}</td>
                                <td>
                                    @if($loan->status == 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($loan->status == 'approved')
                                        <span class="badge bg-primary">Disetujui</span>
                                    @elseif($loan->status == 'borrowed')
                                        <span class="badge bg-info">Dipinjam</span>
                                    @elseif($loan->status == 'returned')
                                        <span class="badge bg-success">Dikembalikan</span>
                                    @elseif($loan->status == 'rejected')
                                        <span class="badge bg-danger">Ditolak</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $loan->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.loans.show', $loan) }}" class="btn btn-sm btn-info">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center">Tidak ada data peminjaman terbaru</td><\/tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- BARANG STOK RENDAH & BARANG TERBARU --}}
<div class="row">
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning">
                    <i class="mdi mdi-alert me-2"></i> Barang Stok Rendah
                    <span class="badge bg-warning ms-2">{{ $lowStockItems->count() }}</span>
                </h6>
            </div>
            <div class="card-body">
                @if($lowStockItems->count() > 0)
                    <div class="list-group">
                        @foreach($lowStockItems as $item)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $item->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $item->category->name ?? '-' }}</small>
                                </div>
                                <span class="badge bg-{{ $item->item_units_count == 0 ? 'danger' : 'warning' }}">
                                    {{ $item->item_units_count }} unit tersisa
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="mdi mdi-check-circle text-success" style="font-size: 48px;"></i>
                        <p class="mt-2 mb-0 text-muted">Semua stok aman</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="mdi mdi-new-box me-2"></i> Barang Terbaru
                </h6>
            </div>
            <div class="card-body">
                @if($recentItems->count() > 0)
                    <div class="list-group">
                        @foreach($recentItems as $item)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $item->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $item->category->name ?? '-' }}</small>
                                </div>
                                <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center py-4">Belum ada barang baru</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- STATISTIK RINGKAS
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="mdi mdi-chart-line me-2"></i> Ringkasan Sistem
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h3 class="mb-0">{{ $totalUsers }}</h3>
                                <small class="text-muted">Total Pengguna</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h3 class="mb-0">{{ $totalLoans }}</h3>
                                <small class="text-muted">Total Peminjaman</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h3 class="mb-0">{{ $totalUnits }}</h3>
                                <small class="text-muted">Total Unit</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h3 class="mb-0">{{ $totalCategories }}</h3>
                                <small class="text-muted">Kategori</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}

@push('styles')
<style>
.border-left-primary { border-left: 0.25rem solid #4e73df !important; }
.border-left-success { border-left: 0.25rem solid #1cc88a !important; }
.border-left-info { border-left: 0.25rem solid #36b9cc !important; }
.border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
.border-left-danger { border-left: 0.25rem solid #e74a3b !important; }
.border-left-dark { border-left: 0.25rem solid #5a5c69 !important; }
.text-xs { font-size: 0.7rem; }
.shadow { box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important; }
.list-group-item { border-left: none; border-right: none; }
.list-group-item:first-child { border-top: none; }
</style>
@endpush
@endsection