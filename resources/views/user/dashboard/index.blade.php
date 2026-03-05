@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan aktivitas peminjaman Anda')

@section('content')

<div class="container-fluid">
    {{-- STATISTIK --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white" style="background-color: #0d6efd !important;">
                <div class="card-body">
                    <small>Dipinjam</small>
                    <h3>{{ $stats['borrowed'] }}</h3>
                </div>
            </div>
        </div>
    
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-dark" style="background-color: #ffc107 !important;">
                <div class="card-body">
                    <small>Pending</small>
                    <h3>{{ $stats['pending'] }}</h3>
                </div>
            </div>
        </div>
    
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white" style="background-color: #dc3545 !important;">
                <div class="card-body">
                    <small>Terlambat</small>
                    <h3>{{ $stats['overdue'] }}</h3>
                </div>
            </div>
        </div>
    
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white" style="background-color: #198754 !important;">
                <div class="card-body">
                    <small>Total Riwayat</small>
                    <h3>{{ $stats['total'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- PEMINJAMAN TERBARU --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white">
            <h6 class="mb-0">Peminjaman Terbaru</h6>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover">

                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Tanggal</th>
                            <th>Jumlah Item</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($recentLoans as $loan)

                        <tr>

                            <td>
                                <strong>{{ $loan->loan_code }}</strong>
                            </td>

                            <td>
                                {{ $loan->loan_date->format('d M Y') }}
                            </td>

                            <td>
                                {{ $loan->details->count() }}
                            </td>

                            <td>

                                @if($loan->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>

                                @elseif($loan->status == 'approved')
                                    <span class="badge bg-info">Disetujui</span>

                                @elseif($loan->status == 'borrowed')
                                    <span class="badge bg-primary">Dipinjam</span>

                                @elseif($loan->status == 'returned')
                                    <span class="badge bg-success">Dikembalikan</span>

                                @elseif($loan->status == 'rejected')
                                    <span class="badge bg-danger">Ditolak</span>

                                @endif

                            </td>

                        </tr>

                        @empty

                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                Belum ada peminjaman
                            </td>
                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection