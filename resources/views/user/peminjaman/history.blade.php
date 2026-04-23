@extends('layouts.admin')

@section('title', 'Riwayat Peminjaman')
@section('page-title', 'Riwayat Peminjaman')
@section('page-subtitle', 'Daftar peminjaman yang pernah Anda lakukan')

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
                        
                        {{-- STATUS --}}
                        <td>
                            @if($loan->status == 'pending')
                                <span class="badge bg-warning">Menunggu</span>
                            @elseif($loan->status == 'approved')
                                <span class="badge bg-info">Disetujui</span>
                            @elseif($loan->status == 'rejected')
                                <span class="badge bg-danger">Ditolak</span>
                            @elseif($loan->status == 'borrowed')
                                <span class="badge bg-primary">Dipinjam</span>
                            @elseif($loan->status == 'returned')
                                <span class="badge bg-success">Dikembalikan</span>
                            @elseif($loan->status == 'overdue')
                                <span class="badge bg-dark">Terlambat</span>
                            @else
                                <span class="badge bg-secondary">{{ $loan->status }}</span>
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
                                
                                {{-- Modal Alasan Penolakan --}}
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
                            @elseif($loan->status == 'returned' && $loan->notes)
                                <button type="button" class="btn btn-sm btn-outline-success" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#returnNoteModal{{ $loan->id }}">
                                    <i class="mdi mdi-message-text"></i> Lihat Catatan
                                </button>
                                
                                {{-- Modal Catatan Pengembalian --}}
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
                                                <p><strong>Catatan Admin:</strong></p>
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
                            @elseif($loan->status == 'borrowed' && $loan->return_date < now())
                                <span class="text-danger">
                                    <i class="mdi mdi-alert-circle"></i> Terlambat 
                                    {{ \Carbon\Carbon::parse($loan->return_date)->diffForHumans() }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        {{-- AKSI --}}
                        <td>
                            <a href="{{ route('user.loans.show', $loan) }}" 
                               class="btn btn-sm btn-info">
                                <i class="mdi mdi-eye"></i> Detail
                            </a>
                            
                            @if($loan->status == 'pending')
                                <button type="button" class="btn btn-sm btn-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#cancelModal{{ $loan->id }}">
                                    <i class="mdi mdi-close"></i> Batal
                                </button>
                                
                                {{-- Modal Konfirmasi Batal --}}
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
                        </td>
                    </tr>
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

@push('scripts')
<script>
    // Auto close modal after submit (optional)
    document.addEventListener('DOMContentLoaded', function() {
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
    });
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
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
</style>
@endpush
@endsection