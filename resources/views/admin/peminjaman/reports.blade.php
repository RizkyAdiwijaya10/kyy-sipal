@extends('layouts.app')

@section('title', 'Laporan Peminjaman')
@section('page-title', 'Laporan Peminjaman')
@section('page-subtitle', 'Filter dan export data peminjaman')

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
                <div class="col-md-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" 
                           name="start_date" 
                           class="form-control"
                           value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" 
                           name="end_date" 
                           class="form-control"
                           value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status Peminjaman</label>
                    <select name="status" class="form-select">
                        <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Dikembalikan</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="d-grid gap-2 w-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-magnify"></i> Tampilkan
                        </button>
                        <a href="{{ route('admin.reports.loans') }}" class="btn btn-secondary">
                            <i class="mdi mdi-refresh"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- SUMMARY STATISTIK --}}
    <div class="row mb-4 no-print">
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4 class="mb-0 text-white">{{ $summary['total'] }}</h4>
                    <small>Total</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center">
                    <h4 class="mb-0">{{ $summary['pending'] }}</h4>
                    <small>Pending</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4 class="mb-0 text-white">{{ $summary['approved'] }}</h4>
                    <small>Disetujui</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4 class="mb-0 text-white">{{ $summary['borrowed'] }}</h4>
                    <small>Dipinjam</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4 class="mb-0 text-white">{{ $summary['returned'] }}</h4>
                    <small>Dikembalikan</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h4 class="mb-0 text-white">{{ $summary['overdue'] }}</h4>
                    <small>Terlambat</small>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLE LAPORAN --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3 no-print">
                <h5 class="mb-0">
                    <i class="mdi mdi-file-document-outline me-2"></i>
                    Data Peminjaman
                </h5>
                <button type="button" class="btn btn-sm btn-success" onclick="exportToExcel()">
                    <i class="mdi mdi-microsoft-excel"></i> Export Excel
                </button>
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
                                @if($loan->status == 'borrowed' && $loan->return_date < now())
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
                                @if($loan->actual_return_date)
                                    {{ \Carbon\Carbon::parse($loan->actual_return_date)->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="mdi mdi-inbox-outline display-3 d-block mb-3"></i>
                                <p class="mb-0">Tidak ada data peminjaman dengan filter yang dipilih</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($loans->count() > 0)
            <div class="mt-3 no-print">
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <p class="text-muted mb-0">
                            <i class="mdi mdi-information-outline me-1"></i>
                            Menampilkan {{ $loans->count() }} data peminjaman
                        </p>
                    </div>
                    <div class="col-md-6 text-end">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="printReport()">
                            <i class="mdi mdi-printer"></i> Print
                        </button>
                    </div>
                </div>
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
    
    ws['A1'] = { v: 'No' };
    ws['B1'] = { v: 'Kode Peminjaman' };
    ws['C1'] = { v: 'Peminjam' };
    ws['D1'] = { v: 'Tanggal Pinjam' };
    ws['E1'] = { v: 'Tanggal Kembali' };
    ws['F1'] = { v: 'Jml Barang' };
    ws['G1'] = { v: 'Status' };
    ws['H1'] = { v: 'Tanggal Kembali Aktual' };
    
    var startDate = '{{ request('start_date', 'Semua') }}';
    var endDate = '{{ request('end_date', 'Semua') }}';
    var status = '{{ request('status', 'all') }}';
    
    XLSX.utils.sheet_add_aoa(ws, [['Laporan Peminjaman'], 
        ['Tanggal Export', new Date().toLocaleDateString('id-ID')],
        ['Periode Tanggal', startDate + ' s/d ' + endDate],
        ['Status', status],
        []], { origin: -1 });
    
    XLSX.utils.book_append_sheet(wb, ws, 'Laporan Peminjaman');
    XLSX.writeFile(wb, 'laporan_peminjaman_' + new Date().toISOString().slice(0,10) + '.xlsx');
}

function printReport() {
    var printWindow = window.open('', '_blank');
    
    var table = document.getElementById('reportTable');
    var tableClone = table.cloneNode(true);
    
    var startDate = '{{ request('start_date', 'Semua') }}';
    var endDate = '{{ request('end_date', 'Semua') }}';
    var statusFilter = '{{ request('status', 'all') }}';
    var statusText = '';
    
    switch(statusFilter) {
        case 'pending': statusText = 'Pending'; break;
        case 'approved': statusText = 'Disetujui'; break;
        case 'borrowed': statusText = 'Dipinjam'; break;
        case 'returned': statusText = 'Dikembalikan'; break;
        case 'rejected': statusText = 'Ditolak'; break;
        default: statusText = 'Semua Status';
    }
    
    var printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Laporan Peminjaman</title>
            <meta charset="utf-8">
            <style>
                body {
                    font-family: Arial, Helvetica, sans-serif;
                    margin: 20px;
                    padding: 20px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .header h1 {
                    margin: 0;
                    font-size: 20px;
                }
                .header p {
                    margin: 3px 0;
                    color: #666;
                    font-size: 11px;
                }
                .info {
                    margin-bottom: 15px;
                    padding: 8px;
                    background-color: #f5f5f5;
                    font-size: 11px;
                }
                .info table {
                    width: 100%;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 10px;
                    font-size: 11px;
                }
                th, td {
                    border: 1px solid #ddd;
                    padding: 6px;
                    text-align: left;
                }
                th {
                    background-color: #4e73df;
                    color: white;
                    font-weight: bold;
                }
                tr:nth-child(even) {
                    background-color: #f9f9f9;
                }
                .badge {
                    padding: 2px 5px;
                    border-radius: 3px;
                    font-size: 9px;
                    font-weight: bold;
                }
                .badge-warning { background-color: #ffc107; color: #000; }
                .badge-info { background-color: #17a2b8; color: #fff; }
                .badge-primary { background-color: #007bff; color: #fff; }
                .badge-success { background-color: #28a745; color: #fff; }
                .badge-danger { background-color: #dc3545; color: #fff; }
                .footer {
                    margin-top: 20px;
                    text-align: center;
                    font-size: 9px;
                    color: #999;
                    border-top: 1px solid #ddd;
                    padding-top: 8px;
                }
                @media print {
                    body {
                        margin: 0;
                        padding: 10px;
                    }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>LAPORAN PEMINJAMAN</h1>
                <p>Sistem Informasi Peminjaman Alat Lab</p>
                <p>Tanggal Cetak: ${new Date().toLocaleDateString('id-ID')}</p>
            </div>
            
            <div class="info">
                <table style="border: none;">
                    <tr><td style="border: none; width: 120px;"><strong>Periode:</strong></td><td style="border: none;">${startDate} s/d ${endDate}</td></tr>
                    <tr><td style="border: none;"><strong>Status:</strong></td><td style="border: none;">${statusText}</td></tr>
                    <tr><td style="border: none;"><strong>Total Data:</strong></td><td style="border: none;">{{ $loans->count() }} peminjaman</td></tr>
                </table>
            </div>
            
            ${tableClone.outerHTML}
            
            <div class="footer">
                Dicetak oleh: {{ Auth::user()->name }} | {{ now()->format('d/m/Y H:i:s') }}
            </div>
        </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.print();
}
</script>
@endpush

@push('styles')
<style>
@media print {
    .navbar, .sidebar, .footer, .no-print {
        display: none !important;
    }
    .main-panel {
        margin: 0 !important;
        padding: 0 !important;
    }
    .card {
        box-shadow: none !important;
        border: none !important;
    }
    body {
        background: white !important;
    }
}
</style>
@endpush
@endsection