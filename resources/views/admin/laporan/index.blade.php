@extends('layouts.app')

@section('title', 'Laporan Peminjaman')
@section('page-title', 'Laporan Peminjaman')
@section('page-subtitle', 'Filter dan export data peminjaman')

@section('content')
    <div class="container">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- FILTER FORM --}}
        <div class="card shadow-sm border-0 mb-4 no-print">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="mdi mdi-filter-outline me-2"></i>
                    Filter Laporan
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.loans') }}" class="row g-3">

                    {{-- Tanggal Mulai --}}
                    <div class="col-md-2">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>

                    {{-- Tanggal Selesai --}}
                    <div class="col-md-2">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>

                    {{-- Status --}}
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>Semua
                                Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>
                                Disetujui</option>
                            <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Dipinjam
                            </option>
                            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue
                            </option>
                            <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>
                                Dikembalikan</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak
                            </option>
                        </select>
                    </div>

                    {{-- Cari Peminjam / Kode --}}
                    <div class="col-md-3">
                        <label class="form-label">Peminjam / Kode</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="mdi mdi-account-search text-muted"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0"
                                placeholder="Nama peminjam atau kode..." value="{{ request('search') }}">
                        </div>
                    </div>


                    {{-- Tombol --}}
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-magnify"></i> Tampilkan
                        </button>
                        <a href="{{ route('admin.reports.loans') }}" class="btn btn-danger">
                            <i class="mdi mdi-refresh"></i> Reset
                        </a>
                        @if (request()->hasAny(['start_date', 'end_date', 'status', 'search', 'item']) && request('status') !== 'all')
                            <span class="badge bg-info">
                                {{ $loans->count() }} data ditemukan
                            </span>
                        @endif
                    </div>

                </form>
            </div>
        </div>

        {{-- STATISTIK CARD --}}
        <div class="row mb-4 no-print">
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-left-primary shadow h-80 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1 text-center">
                                    Total
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" style="text-align: center;">
                                    {{ $summary['total'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-left-warning shadow h-80 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1 text-center">
                                    Pending
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" style="text-align: center;">
                                    {{ $summary['pending'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-left-info shadow h-80 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1 text-center">
                                    Disetujui
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" style="text-align: center;">
                                    {{ $summary['approved'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-left-primary shadow h-80 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1 text-center">
                                    Dipinjam
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" style="text-align: center;">
                                    {{ $summary['borrowed'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-left-success shadow h-80 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1 text-center">
                                    Dikembalikan
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" style="text-align: center;">
                                    {{ $summary['returned'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-left-danger shadow h-80 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2 justify-content-center">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1 text-center">
                                    Terlambat
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" style="text-align: center;">
                                    {{ $summary['overdue'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="card-title m-0 fw-bold text-primary">
                        <i class="mdi mdi-file-document-outline me-2"></i>
                        Data Peminjaman
                    </h6>
                    <div>
                        <button type="button" class="btn btn-sm btn-success me-2" onclick="exportToExcel()">
                            <i class="mdi mdi-microsoft-excel"></i> Export Excel
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" onclick="printReport()">
                            <i class="mdi mdi-printer"></i> Print
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="reportTable">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Peminjam</th>
                                <th>Tanggal Pinjam</th>
                                <th>Tanggal Kembali</th>
                                <th>Jml Barang</th>
                                <th>Status</th>
                                <th>Tgl Kembali Aktual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loans as $loan)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-semibold">{{ $loan->loan_code }}</td>
                                    <td>{{ $loan->user->name }}</td>
                                    <td>{{ $loan->loan_date->format('d/m/Y') }}</td>
                                    <td>
                                        {{ $loan->return_date->format('d/m/Y') }}
                                        @if ($loan->status == 'borrowed' && $loan->return_date < now())
                                            <br><span class="badge bg-danger">Terlambat</span>
                                        @endif
                                    </td>
                                    <td>{{ $loan->details->count() }} item</td>
                                    <td>
                                        @switch($loan->status)
                                            @case('pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @break

                                            @case('approved')
                                                <span class="badge bg-info">Disetujui</span>
                                            @break

                                            @case('borrowed')
                                                <span class="badge bg-primary">Dipinjam</span>
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
                                    <td>
                                        @if ($loan->actual_return_date)
                                            {{ \Carbon\Carbon::parse($loan->actual_return_date)->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-5">Tidak ada data peminjaman<\
                                                /td>
                                                <\ /tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($loans->count() > 0)
                        <div class="mt-3 no-print">
                            <hr>
                            <p class="text-muted mb-0">
                                <i class="mdi mdi-information-outline me-1"></i>
                                Menampilkan {{ $loans->count() }} data peminjaman
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
            <script>
                function exportToExcel() {
                    var table = document.getElementById('reportTable');
                    var wb = XLSX.utils.book_new();
                    var ws = XLSX.utils.table_to_sheet(table);

                    ws['A1'] = {
                        v: 'No'
                    };
                    ws['B1'] = {
                        v: 'Kode Peminjaman'
                    };
                    ws['C1'] = {
                        v: 'Peminjam'
                    };
                    ws['D1'] = {
                        v: 'Tanggal Pinjam'
                    };
                    ws['E1'] = {
                        v: 'Tanggal Kembali'
                    };
                    ws['F1'] = {
                        v: 'Jml Barang'
                    };
                    ws['G1'] = {
                        v: 'Status'
                    };
                    ws['H1'] = {
                        v: 'Tanggal Kembali Aktual'
                    };

                    var startDate = '{{ request('start_date', 'Semua') }}';
                    var endDate = '{{ request('end_date', 'Semua') }}';
                    var status = '{{ request('status', 'all') }}';

                    XLSX.utils.sheet_add_aoa(ws, [
                        ['Laporan Peminjaman'],
                        ['Tanggal Export', new Date().toLocaleDateString('id-ID')],
                        ['Periode Tanggal', startDate + ' s/d ' + endDate],
                        ['Status', status],
                        []
                    ], {
                        origin: -1
                    });

                    XLSX.utils.book_append_sheet(wb, ws, 'Laporan Peminjaman');
                    XLSX.writeFile(wb, 'laporan_peminjaman_' + new Date().toISOString().slice(0, 10) + '.xlsx');
                }

                function printReport() {
                    var startDate = '{{ request('start_date', 'Semua') }}';
                    var endDate = '{{ request('end_date', 'Semua') }}';
                    var statusFilter = '{{ request('status', 'all') }}';
                    var statusText = '';

                    switch (statusFilter) {
                        case 'pending':
                            statusText = 'Pending';
                            break;
                        case 'approved':
                            statusText = 'Disetujui';
                            break;
                        case 'borrowed':
                            statusText = 'Dipinjam';
                            break;
                        case 'returned':
                            statusText = 'Dikembalikan';
                            break;
                        case 'rejected':
                            statusText = 'Ditolak';
                            break;
                        default:
                            statusText = 'Semua Status';
                    }

                    // Buka jendela baru dengan template print terpisah
                    var printWindow = window.open(
                        '{{ route('admin.reports.print') }}?start_date={{ request('start_date') }}&end_date={{ request('end_date') }}&status={{ request('status') }}',
                        '_blank');
                }
            </script>
        @endpush
    @endsection
