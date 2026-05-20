@extends('layouts.app')

@section('title', 'Detail Pengajuan Pengembalian')
@section('page-title', 'Detail Pengajuan Pengembalian')
@section('page-subtitle', 'Informasi lengkap pengajuan pengembalian')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                {{-- Status Badge --}}
                <div class="row mb-4">
                    <div class="col-12">
                        @if($loan->return_request_status == 'pending')
                            <div class="alert alert-warning">
                                <i class="mdi mdi-clock-outline me-2"></i>
                                <strong>Status: Menunggu Persetujuan Admin</strong>
                                <p class="mb-0 mt-1">Pengajuan pengembalian Anda sedang diproses oleh admin.</p>
                            </div>
                        @elseif($loan->return_request_status == 'approved')
                            <div class="alert alert-success">
                                <i class="mdi mdi-check-circle me-2"></i>
                                <strong>Status: Disetujui</strong>
                                <p class="mb-0 mt-1">Barang sudah diverifikasi dan dikembalikan.</p>
                            </div>
                        @elseif($loan->return_request_status == 'rejected')
                            <div class="alert alert-danger">
                                <i class="mdi mdi-close-circle me-2"></i>
                                <strong>Status: Ditolak</strong>
                                <p class="mb-0 mt-1">Pengajuan pengembalian ditolak. Silakan hubungi admin.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Informasi Peminjaman --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Informasi Peminjaman</h6>
                                <table class="table table-sm table-borderless">
                                    <tr><td width="120">Kode</td><td>: <strong>{{ $loan->loan_code }}</strong></td></tr>
                                    <tr><td>Tanggal Pinjam</td><td>: {{ $loan->loan_date->format('d/m/Y') }}</td></tr>
                                    <tr><td>Rencana Kembali</td><td>: {{ $loan->return_date->format('d/m/Y') }}</td></tr>
                                    <tr><td>Status Pinjam</td>
                                        <td>: @if($loan->status == 'borrowed') <span class="badge bg-info">Dipinjam</span> @elseif($loan->status == 'returned') <span class="badge bg-success">Dikembalikan</span> @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Informasi Pengajuan</h6>
                                <table class="table table-sm table-borderless">
                                    <tr><td width="120">Diajukan</td><td>: {{ \Carbon\Carbon::parse($loan->return_requested_at)->format('d/m/Y H:i') }}</td></tr>
                                    <tr><td>Catatan</td><td>: {{ $loan->return_request_notes ?? '-' }}</td></tr>
                                    @if($loan->return_approved_at)
                                    <tr><td>Diproses</td><td>: {{ \Carbon\Carbon::parse($loan->return_approved_at)->format('d/m/Y H:i') }}</td></tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Daftar Barang --}}
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Daftar Barang yang Dipinjam</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Barang</th>
                                        <th>Kode Inventaris</th>
                                        <th>Kondisi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($loan->details as $detail)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $detail->itemUnit->item->name }}</td>
                                        <td>{{ $detail->itemUnit->inventory_code ?? '-' }}</td>
                                        <td>
                                            @if($detail->condition_after)
                                                <span class="badge bg-success">Sudah dicek</span>
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

                @if($loan->return_request_status == 'rejected' && $loan->notes)
                <div class="alert alert-danger mt-3">
                    <i class="mdi mdi-message-alert me-2"></i>
                    <strong>Alasan Penolakan:</strong><br>
                    {{ $loan->notes }}
                </div>
                @endif

                <div class="mt-4">
                    <a href="{{ route('user.returns.index') }}" class="btn btn-light">
                        <i class="mdi mdi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="mdi mdi-information-outline"></i> Informasi
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="mdi mdi-clock-outline me-2"></i>
                    <strong>Status Pengajuan:</strong><br>
                    @if($loan->return_request_status == 'pending')
                        <span class="badge bg-warning mt-1">Menunggu Konfirmasi Admin</span>
                        <p class="mt-2 small">Admin akan segera memproses pengajuan Anda.</p>
                    @elseif($loan->return_request_status == 'approved')
                        <span class="badge bg-success mt-1">Disetujui</span>
                        <p class="mt-2 small">Pengembalian barang telah dikonfirmasi.</p>
                    @elseif($loan->return_request_status == 'rejected')
                        <span class="badge bg-danger mt-1">Ditolak</span>
                        <p class="mt-2 small">Silakan hubungi admin untuk informasi lebih lanjut.</p>
                    @endif
                </div>

                <div class="alert alert-warning mt-3">
                    <i class="mdi mdi-alert-circle-outline me-2"></i>
                    <strong>Catatan:</strong>
                    <ul class="small mt-2 mb-0">
                        <li>Pastikan barang dalam kondisi baik</li>
                        <li>Denda keterlambatan Rp 5.000/hari</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection