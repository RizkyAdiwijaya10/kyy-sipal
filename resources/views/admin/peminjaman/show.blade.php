@extends('layouts.admin')

@section('title', 'Detail Peminjaman')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-semibold">Detail Peminjaman</h4>
        <a href="{{ route('admin.loans.index') }}" class="btn btn-secondary btn-sm">
            Kembali
        </a>
    </div>

    <div class="row">

        {{-- INFORMASI UTAMA --}}
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">

                    <h5 class="mb-3">Informasi Peminjaman</h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Kode:</strong> {{ $loan->loan_code }}</p>
                            <p><strong>Peminjam:</strong> {{ $loan->user->name }}</p>
                            <p><strong>Tanggal Pinjam:</strong> 
                                {{ $loan->loan_date->format('d M Y') }}
                            </p>
                            <p><strong>Tanggal Kembali:</strong> 
                                {{ $loan->return_date->format('d M Y') }}
                            </p>
                        </div>

                        <div class="col-md-6">
                            <p><strong>Status:</strong>
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
                            </p>

                            @if($loan->approved_at)
                                <p><strong>Diproses Pada:</strong> 
                                    {{ \Carbon\Carbon::parse($loan->approved_at)->format('d M Y H:i') }}
                                </p>
                            @endif

                            @if($loan->approver)
                                <p><strong>Diproses Oleh:</strong> 
                                    {{ $loan->approver->name }}
                                </p>
                            @endif

                            @if($loan->actual_return_date)
                                <p><strong>Dikembalikan Pada:</strong> 
                                    {{ \Carbon\Carbon::parse($loan->actual_return_date)->format('d M Y H:i') }}
                                </p>
                            @endif
                        </div>
                    </div>

                </div>
            </div> 
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <h6 class="fw-semibold mb-3">Timeline Status</h6>

                    <ul class="list-unstyled">
                        <li class="mb-2">
                            ✔ Pengajuan dibuat
                        </li>

                        <li class="mb-2">
                            {{ $loan->status != 'pending' ? '✔' : '○' }}
                            Disetujui / Ditolak
                        </li>

                        <li class="mb-2">
                            {{ in_array($loan->status, ['borrowed','returned']) ? '✔' : '○' }}
                            Barang dipinjam
                        </li>

                        <li class="mb-2">
                            {{ $loan->status == 'returned' ? '✔' : '○' }}
                            Barang dikembalikan
                        </li>
                    </ul>

                    @if($loan->status == 'borrowed' && $loan->return_date < now())
                        <div class="alert alert-danger mt-3">
                            ⚠ Terlambat Mengembalikan
                        </div>
                    @endif

                </div>
            </div>
        </div>
        <div class="col-md-12">
            {{-- DAFTAR UNIT --}}
            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <h5 class="mb-3">Daftar Unit Dipinjam</h5>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="small text-muted border-bottom">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Barang</th>
                                    <th>Kode Inventaris</th>
                                    <th>Kondisi Awal</th>
                                    <th>Kondisi Kembali</th>
                                    <th>Status Unit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loan->details as $detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $detail->itemUnit->item->name ?? '-' }}</td>
                                    <td>{{ $detail->itemUnit->inventory_code ?? '-' }}</td>
                                    <td>{{ $detail->condition_before ?? '-' }}</td>
                                    <td>{{ $detail->condition_after ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $detail->itemUnit->status }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection