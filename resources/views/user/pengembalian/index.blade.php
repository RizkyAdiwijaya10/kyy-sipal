@extends('layouts.app')

@section('title', 'Pengajuan Pengembalian')
@section('page-title', 'Pengajuan Pengembalian')
@section('page-subtitle', 'Ajukan pengembalian barang yang sedang dipinjam')

@section('content')
<div class="row">
    <div class="col-12">
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
    </div>
</div>

<div class="row">
    {{-- Peminjaman yang Bisa Diajukan Pengembalian --}}
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="mdi mdi-check-circle me-2"></i>
                    Dapat Diajukan Pengembalian
                </h5>
            </div>
            <div class="card-body">
                @if($loans->count() > 0)
                    <div class="list-group">
                        @foreach($loans as $loan)
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-md-9">
                                    <h6 class="mb-1">
                                        <strong>{{ $loan->loan_code }}</strong>
                                        <span class="badge bg-info ms-2">Dipinjam</span>
                                    </h6>
                                    <small class="text-muted d-block">
                                        <i class="mdi mdi-calendar"></i> Pinjam: {{ $loan->loan_date->format('d/m/Y') }}
                                    </small>
                                    <small class="text-muted d-block">
                                        <i class="mdi mdi-calendar-return"></i> Rencana kembali: {{ $loan->return_date->format('d/m/Y') }}
                                    </small>
                                    <small class="text-muted">
                                        <i class="mdi mdi-package-variant"></i> {{ $loan->details->count() }} barang
                                    </small>
                                </div>
                                
                            </div>
                            <div class="col-md-3 text-end">
                                    <a href="{{ route('user.returns.create', $loan) }}" 
                                       class="btn btn-warning btn-sm w-100">
                                        <i class="mdi mdi-backup-restore"></i> Ajukan Pengembalian
                                    </a>
                                </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-3">
                        {{ $loans->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="mdi mdi-check-circle text-success" style="font-size: 48px;"></i>
                        <p class="text-muted mt-2 mb-0">Tidak ada peminjaman yang dapat diajukan pengembalian</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function cancelReturnRequest(loanId) {
    Swal.fire({
        title: 'Batalkan Pengajuan?',
        text: "Apakah Anda yakin ingin membatalkan pengajuan pengembalian ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Batalkan!',
        cancelButtonText: 'Tidak'
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit form delete
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('user/loans') }}/${loanId}/return-request`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
@endsection