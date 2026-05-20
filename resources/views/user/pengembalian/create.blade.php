@extends('layouts.app')

@section('title', 'Ajukan Pengembalian')
@section('page-title', 'Ajukan Pengembalian')
@section('page-subtitle', 'Form pengajuan pengembalian barang')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="mdi mdi-information-outline me-2"></i>
                    <strong>Informasi Peminjaman:</strong><br>
                    Kode: {{ $loan->loan_code }}<br>
                    Tanggal Pinjam: {{ $loan->loan_date->format('d/m/Y') }}<br>
                    Rencana Kembali: {{ $loan->return_date->format('d/m/Y') }}
                </div>

                <form method="POST" action="{{ route('user.returns.store', $loan) }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Catatan Pengembalian (Opsional)</label>
                        <textarea name="return_notes" 
                                  class="form-control @error('return_notes') is-invalid @enderror" 
                                  rows="4"
                                  placeholder="Contoh: Barang dalam kondisi baik, siap dikembalikan...">{{ old('return_notes') }}</textarea>
                        @error('return_notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Tambahkan catatan jika perlu (misal: kondisi barang, keterangan lain)</small>
                    </div>

                    <div class="alert alert-warning">
                        <i class="mdi mdi-alert-circle-outline me-2"></i>
                        <strong>Perhatian:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Pastikan barang dalam kondisi baik saat dikembalikan</li>
                            <li>Admin akan mengecek kondisi barang sebelum menyetujui</li>
                            <li>Denda akan dikenakan jika terlambat mengembalikan</li>
                            <li>Pengajuan dapat dibatalkan selama masih menunggu persetujuan</li>
                        </ul>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('user.loans.history') }}" class="btn btn-light">
                            <i class="mdi mdi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="mdi mdi-send"></i> Ajukan Pengembalian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="mdi mdi-package-variant"></i> Daftar Barang Dipinjam
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($loan->details as $detail)
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ $detail->itemUnit->item->name }}</strong>
                                <br>
                                <small class="text-muted">
                                    Kode: {{ $detail->itemUnit->inventory_code ?? '-' }}
                                </small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-secondary">1 unit</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection