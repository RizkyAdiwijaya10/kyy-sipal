@extends('layouts.app')

@section('title', 'Dashboard User')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, {{ Auth::user()->name }}')

@section('content')
{{-- STATISTIK CARD --}}
<div class="row">
    {{-- Total Peminjaman --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Peminjaman
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="mdi mdi-clipboard-list fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Menunggu Persetujuan --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Menunggu Persetujuan
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

    {{-- Sedang Dipinjam --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Sedang Dipinjam
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

    {{-- Sudah Dikembalikan --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Sudah Dikembalikan
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['returned'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="mdi mdi-check-circle-outline fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- PEMINJAMAN TERBARU --}}
<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-primary">
                    <i class="mdi mdi-history me-2"></i>
                    Peminjaman Terbaru
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Kode Peminjaman</th>
                                <th>Tanggal Pinjam</th>
                                <th>Jumlah Item</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLoans as $loan)
                            <tr>
                                <td>
                                    <strong>{{ $loan->loan_code }}</strong>  {{-- Pastikan ini ada --}}
                                </td>
                                <td>
                                    {{ $loan->loan_date->format('d/m/Y') }}
                                    <br>
                                    <small class="text-muted">
                                        {{ $loan->loan_date->diffForHumans() }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $loan->details->count() }} barang
                                    </span>
                                </td>
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
                                
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="mdi mdi-inbox-outline display-4 d-block mb-3"></i>
                                    <p>Belum ada peminjaman</p>
                                    <a href="{{ route('user.items.index') }}" class="btn btn-primary btn-sm">
                                        <i class="mdi mdi-plus"></i> Pinjam Alat
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($recentLoans->count() > 0)
                <div class="text-end mt-3">
                    <a href="{{ route('user.loans.history') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua <i class="mdi mdi-arrow-right"></i>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection