@extends('layouts.admin')

@section('title', 'Detail Peminjaman')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <h4 class="mb-0">Detail Peminjaman: {{ $loan->loan_code }}</h4>
        </div>
        <div class="card-body">
            <!-- Informasi Peminjaman -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="150">Kode</th>
                            <td>: {{ $loan->loan_code }}</td>
                        </tr>
                        <tr>
                            <th>Peminjam</th>
                            <td>: {{ $loan->user->name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>: {{ $loan->user->email }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Pinjam</th>
                            <td>: {{ $loan->loan_date->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Rencana Kembali</th>
                            <td>: {{ $loan->return_date->format('d F Y') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="150">Status</th>
                            <td>: 
                                @switch($loan->status)
                                    @case('pending') <span class="badge bg-warning">Pending</span> @break
                                    @case('approved') <span class="badge bg-primary">Disetujui</span> @break
                                    @case('borrowed') <span class="badge bg-info">Dipinjam</span> @break
                                    @case('returned') <span class="badge bg-success">Dikembalikan</span> @break
                                    @case('rejected') <span class="badge bg-danger">Ditolak</span> @break
                                @endswitch
                            </td>
                        </tr>
                        <tr>
                            <th>Tujuan</th>
                            <td>: {{ $loan->purpose }}</td>
                        </tr>
                        @if($loan->actual_return_date)
                        <tr>
                            <th>Tgl Kembali</th>
                            <td>: {{ $loan->actual_return_date->format('d F Y') }}</td>
                        </tr>
                        @endif
                        @if($loan->approved_at)
                        <tr>
                            <th>Disetujui</th>
                            <td>: {{ $loan->approved_at->format('d F Y H:i') }} oleh {{ $loan->approver->name ?? '-' }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Surat Peminjaman -->
            @if($loan->notes)
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
                                    <strong>{{ basename($loan->notes) }}</strong>
                                    <br>
                                    <small class="text-muted">Diupload: {{ $loan->created_at->format('d F Y H:i') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('admin.loans.view-surat', $loan) }}" class="btn btn-primary" target="_blank">
                                <i class="mdi mdi-eye"></i> Lihat Surat
                            </a>
                            <a href="{{ route('admin.loans.download-surat', $loan) }}" class="btn btn-success">
                                <i class="mdi mdi-download"></i> Download
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Daftar Barang -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Daftar Barang yang Dipinjam</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Inventaris</th>
                                    <th>Nama Barang</th>
                                    <th>Kondisi Awal</th>
                                    <th>Kondisi Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loan->details as $detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $detail->itemUnit->inventory_code ?? '-' }}</td>
                                    <td>{{ $detail->itemUnit->item->name }}</td>
                                    <td>
                                        @if($detail->condition_before == 'baik')
                                            <span class="badge bg-success">Baik</span>
                                        @elseif($detail->condition_before == 'rusak')
                                            <span class="badge bg-danger">Rusak</span>
                                        @else
                                            <span class="badge bg-warning">Maintenance</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($detail->condition_after)
                                            @if($detail->condition_after == 'baik')
                                                <span class="badge bg-success">Baik</span>
                                            @elseif($detail->condition_after == 'rusak')
                                                <span class="badge bg-danger">Rusak</span>
                                            @else
                                                <span class="badge bg-warning">Maintenance</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Belum dicek</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('admin.loans.index') }}" class="btn btn-light">
                    <i class="mdi mdi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection