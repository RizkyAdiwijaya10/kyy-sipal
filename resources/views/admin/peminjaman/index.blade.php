@extends('layouts.app')

@section('title', 'Manajemen Peminjaman')

@section('content')
<div class="container">
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- STATISTIK CARD --}}
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="card-title text-white">{{ $stats['pending'] }}</h3>
                            <p class="card-text">Pending</p>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-clock-outline mdi-36px"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="card-title text-white">{{ $stats['approved'] }}</h3>
                            <p class="card-text">Disetujui</p>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-check-circle-outline mdi-36px"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="card-title text-white">{{ $stats['borrowed'] }}</h3>
                            <p class="card-text">Dipinjam</p>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-bookmark-outline mdi-36px"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="card-title text-white">{{ $stats['overdue'] }}</h3>
                            <p class="card-text">Overdue</p>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-alert-circle-outline mdi-36px"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            {{-- FILTER DROPDOWN --}}
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle w-20" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            @php
                                $statusLabels = [
                                    null => 'Semua Status',
                                    'pending' => 'Pending',
                                    'approved' => 'Disetujui',
                                    'borrowed' => 'Dipinjam',
                                    'returned' => 'Dikembalikan',
                                    'rejected' => 'Ditolak'
                                ];
                                $currentStatus = request('status');
                                $buttonLabel = $statusLabels[$currentStatus] ?? 'Semua Status';
                            @endphp
                            {{ $buttonLabel }}
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item {{ !request('status') ? 'active' : '' }}" 
                                   href="{{ route('admin.loans.index') }}">
                                    <i class="mdi mdi-view-dashboard"></i> Semua Status
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item {{ request('status') == 'pending' ? 'active' : '' }}" 
                                   href="{{ route('admin.loans.index', ['status' => 'pending']) }}">
                                    <i class="mdi mdi-clock-outline text-warning me-2"></i> Pending
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request('status') == 'approved' ? 'active' : '' }}" 
                                   href="{{ route('admin.loans.index', ['status' => 'approved']) }}">
                                    <i class="mdi mdi-check-circle-outline text-primary me-2"></i> Disetujui
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request('status') == 'borrowed' ? 'active' : '' }}" 
                                   href="{{ route('admin.loans.index', ['status' => 'borrowed']) }}">
                                    <i class="mdi mdi-bookmark-outline text-info me-2"></i> Dipinjam
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request('status') == 'returned' ? 'active' : '' }}" 
                                   href="{{ route('admin.loans.index', ['status' => 'returned']) }}">
                                    <i class="mdi mdi-check-all text-success me-2"></i> Dikembalikan
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request('status') == 'rejected' ? 'active' : '' }}" 
                                   href="{{ route('admin.loans.index', ['status' => 'rejected']) }}">
                                    <i class="mdi mdi-close-circle-outline text-danger me-2"></i> Ditolak
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                
                {{-- <div class="col-md-8">
                    <div class="text-end">
                        <small class="text-muted">
                            Total data: {{ $loans->total() }} peminjaman
                        </small>
                    </div>
                </div> --}}
            </div>

            {{-- TABLE --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Peminjam</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th>
                            <th>Barang</th>
                            <th>Status</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $loan)
                        <tr class="@if($loan->status == 'borrowed' && $loan->return_date < now()) table-danger @endif">
                            <td class="fw-semibold">{{ $loan->loan_code }}</td>
                            <td>{{ $loan->user->name }}</td>
                            <td>{{ $loan->loan_date->format('d/m/Y') }}</td>
                            <td>
                                {{ $loan->return_date->format('d/m/Y') }}
                                @if($loan->status == 'borrowed' && $loan->return_date < now())
                                    <br>
                                    <small class="text-danger">
                                        <i class="mdi mdi-alert-circle"></i> Terlambat
                                    </small>
                                @endif
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
                                    @default
                                        <span class="badge bg-secondary">{{ $loan->status }}</span>
                                @endswitch
                            </td>

                            {{-- AKSI --}}
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    @if($loan->status == 'pending')
                                        <button type="button" class="btn btn-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#approveModal{{ $loan->id }}">
                                            <i class="mdi mdi-check"></i> Setujui
                                        </button>

                                        <button type="button" class="btn btn-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#rejectModal{{ $loan->id }}">
                                            <i class="mdi mdi-close"></i> Tolak
                                        </button>
                                    @endif

                                    @if($loan->status == 'approved')
                                        <button type="button" class="btn btn-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#confirmBorrowedModal{{ $loan->id }}">
                                            <i class="mdi mdi-bookmark"></i> Konfirmasi
                                        </button>
                                    @endif

                                    @if($loan->status == 'borrowed')
                                        <button type="button" class="btn btn-warning" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#returnModal{{ $loan->id }}">
                                            <i class="mdi mdi-undo"></i> Kembali
                                        </button>
                                    @endif

                                    <a href="{{ route('admin.loans.show', $loan) }}"
                                       class="btn btn-outline-secondary">
                                        <i class="mdi mdi-eye"></i> Detail
                                    </a>
                                </div>
                            </td>
                        </tr>

                        {{-- MODAL APPROVE --}}
                        <div class="modal fade" id="approveModal{{ $loan->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('admin.loans.approve', $loan) }}">
                                        @csrf
                                        <div class="modal-header bg-success text-white">
                                            <h5 class="modal-title">Setujui Peminjaman</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-info">
                                                <i class="mdi mdi-information-outline me-2"></i>
                                                <strong>Kode:</strong> {{ $loan->loan_code }}<br>
                                                <strong>Peminjam:</strong> {{ $loan->user->name }}<br>
                                                <strong>Jumlah Barang:</strong> {{ $loan->details->count() }} item<br>
                                                <strong>Tanggal Pinjam:</strong> {{ $loan->loan_date->format('d/m/Y') }}<br>
                                                <strong>Rencana Kembali:</strong> {{ $loan->return_date->format('d/m/Y') }}
                                            </div>
                                            <p>Apakah Anda yakin ingin menyetujui peminjaman ini?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-success">Ya, Setujui</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- MODAL REJECT --}}
                        <div class="modal fade" id="rejectModal{{ $loan->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('admin.loans.reject', $loan) }}">
                                        @csrf
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Tolak Peminjaman</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-warning">
                                                <i class="mdi mdi-alert-circle-outline me-2"></i>
                                                <strong>Kode:</strong> {{ $loan->loan_code }}<br>
                                                <strong>Peminjam:</strong> {{ $loan->user->name }}
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                                <textarea name="reject_reason" class="form-control" rows="3" placeholder="Masukkan alasan penolakan..." required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger">Ya, Tolak</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- MODAL CONFIRM BORROWED --}}
                        <div class="modal fade" id="confirmBorrowedModal{{ $loan->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('admin.loans.confirm-borrowed', $loan) }}">
                                        @csrf
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title">Konfirmasi Pengambilan Barang</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-info">
                                                <i class="mdi mdi-information-outline me-2"></i>
                                                <strong>Kode:</strong> {{ $loan->loan_code }}<br>
                                                <strong>Peminjam:</strong> {{ $loan->user->name }}<br>
                                                <strong>Jumlah Barang:</strong> {{ $loan->details->count() }} item
                                            </div>
                                            <p>Konfirmasi bahwa barang sudah diambil oleh peminjam?</p>
                                            <div class="alert alert-warning">
                                                <i class="mdi mdi-alert-circle-outline me-2"></i>
                                                Setelah dikonfirmasi, status akan berubah menjadi <strong>Dipinjam</strong>.
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Ya, Konfirmasi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- MODAL RETURN --}}
                        <div class="modal fade" id="returnModal{{ $loan->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('admin.loans.return', $loan) }}">
                                        @csrf
                                        <div class="modal-header bg-warning text-dark">
                                            <h5 class="modal-title">Konfirmasi Pengembalian Barang</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-info">
                                                <i class="mdi mdi-information-outline me-2"></i>
                                                <strong>Kode:</strong> {{ $loan->loan_code }}<br>
                                                <strong>Peminjam:</strong> {{ $loan->user->name }}<br>
                                                <strong>Rencana Kembali:</strong> {{ $loan->return_date->format('d/m/Y') }}
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Kondisi Barang <span class="text-danger">*</span></label>
                                                <select name="condition_after" class="form-control" required>
                                                    <option value="baik">Baik</option>
                                                    <option value="maintenance">Maintenance</option>
                                                    <option value="rusak">Rusak</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Catatan (Opsional)</label>
                                                <textarea name="return_notes" class="form-control" rows="2" placeholder="Masukkan catatan jika ada kerusakan atau keterangan lain"></textarea>
                                            </div>
                                            <div class="alert alert-warning">
                                                <i class="mdi mdi-alert-circle-outline me-2"></i>
                                                Setelah dikonfirmasi, status akan berubah menjadi <strong>Dikembalikan</strong>.
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-warning">Ya, Konfirmasi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="mdi mdi-inbox-outline display-3 d-block mb-3"></i>
                                <p class="mb-0">Tidak ada data peminjaman</p>
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