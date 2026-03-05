@extends('layouts.admin')

@section('title', 'Manajemen Peminjaman')

@section('content')
<div class="container py-4">

    <h4 class="fw-semibold mb-4">Manajemen Peminjaman</h4>
    @if($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
    @endif
    {{-- 🔥 STATISTIK --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <small class="text-muted">Pending</small>
                    <h4>{{ $stats['pending'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <small class="text-muted">Disetujui</small>
                    <h4>{{ $stats['approved'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <small class="text-muted">Dipinjam</small>
                    <h4>{{ $stats['borrowed'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-danger text-white">
                <div class="card-body">
                    <small>Overdue</small>
                    <h4>{{ $stats['overdue'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            {{-- FILTER --}}
            <ul class="nav nav-pills mb-4">
                @php
                    $statuses = [
                        null => 'Semua',
                        'pending' => 'Pending',
                        'approved' => 'Disetujui',
                        'borrowed' => 'Dipinjam',
                        'returned' => 'Dikembalikan',
                        'rejected' => 'Ditolak'
                    ];
                @endphp

                @foreach($statuses as $key => $label)
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') == $key ? 'active' : '' }}"
                           href="{{ route('admin.loans.index', ['status' => $key]) }}">
                            {{ $label }}
                        </a>
                    </li>
                @endforeach
            </ul>

            {{-- TABLE --}}
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="border-bottom small text-muted">
                        <tr>
                            <th>Kode</th>
                            <th>Peminjam</th>
                            <th>Tanggal</th>
                            <th>Barang</th>
                            <th>Status</th>
                            <th width="25%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $loan)

                        <tr class="
                            @if($loan->status == 'borrowed' && $loan->return_date < now())
                                table-danger
                            @endif
                        ">

                            <td class="fw-semibold">
                                {{ $loan->loan_code }}
                            </td>

                            <td>{{ $loan->user->name }}</td>

                            <td>
                                {{ $loan->loan_date->format('d M Y') }}
                                <br>
                                <small class="text-muted">
                                    s/d {{ $loan->return_date->format('d M Y') }}
                                </small>
                            </td>

                            <td>
                                <span class="badge bg-secondary">
                                    {{ $loan->details->count() }} barang
                                </span>
                            </td>

                            {{-- STATUS BADGE --}}
                            <td>
                                @switch($loan->status)
                                    @case('pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                        @break
                                    @case('approved')
                                        <span class="badge bg-primary">Disetujui</span>
                                        @break
                                    @case('borrowed')
                                        <span class="badge bg-info text-dark">Dipinjam</span>
                                        @break
                                    @case('returned')
                                        <span class="badge bg-success">Dikembalikan</span>
                                        @break
                                    @case('rejected')
                                        <span class="badge bg-danger">Ditolak</span>
                                        @break
                                @endswitch
                            </td>

                            {{-- AKSI --}}
                            <td class="d-flex flex-wrap gap-1">

                                @if($loan->status == 'pending')
                                    <form method="POST" action="{{ route('admin.loans.approve', $loan) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-success">Approve</button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.loans.reject', $loan) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-danger"
                                                onclick="return confirm('Yakin ingin menolak pengajuan ini?')">
                                            Reject
                                        </button>
                                    
                                @endif

                                @if($loan->status == 'approved')
                                    <form method="POST" action="{{ route('admin.loans.confirm-borrowed', $loan) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-primary">Confirm Borrowed</button>
                                    </form>
                                @endif

                                @if($loan->status == 'borrowed')
                                <form method="POST" action="{{ route('admin.loans.return', $loan) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning">
                                        Confirm Return
                                    </button>
                                </form>
                                @endif

                                <a href="{{ route('admin.loans.show', $loan) }}"
                                   class="btn btn-sm btn-outline-secondary">
                                    Detail
                                </a>

                            </td>

                        </tr>

                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Tidak ada data
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
@endsection