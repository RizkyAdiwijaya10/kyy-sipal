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
                        <td>
                            <a href="{{ route('user.loans.show', $loan) }}" 
                               class="btn btn-sm btn-info">
                                <i class="mdi mdi-eye"></i>
                            </a>
                            
                            @if($loan->status == 'pending')
                                <form action="{{ route('user.loans.cancel', $loan) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Batalkan peminjaman?')">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="mdi mdi-close"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">
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
@endsection