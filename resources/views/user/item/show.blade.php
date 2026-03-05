@extends('layouts.admin')

@section('title', 'Detail Alat')
@section('page-title', 'Detail Alat')
@section('page-subtitle', 'Informasi lengkap alat')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title">{{ $item->name }}</h4>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <p><strong>Kategori:</strong> {{ $item->category->name ?? '-' }}</p>
                        <p><strong>Merek:</strong> {{ $item->brand ?? '-' }}</p>
                        <p><strong>Model:</strong> {{ $item->model ?? '-' }}</p>
                        <p><strong>Sumber Dana:</strong> {{ $item->fundingSource->name ?? '-' }}</p>
                        {{-- <p><strong>Total Stok:</strong> {{ $item->total_stock }} unit</p> --}}
                        <p><strong>Tersedia:</strong> 
                            <span class="badge bg-success">{{ $availableUnits->count() }} unit</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Informasi Peminjaman</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="mdi mdi-clock-outline text-primary me-2"></i>
                        Maksimal 7 hari
                    </li>
                    <li class="mb-2">
                        <i class="mdi mdi-check-circle-outline text-success me-2"></i>
                        Kondisi baik saat pinjam
                    </li>
                    <li class="mb-2">
                        <i class="mdi mdi-alert-circle-outline text-warning me-2"></i>
                        Denda jika terlambat
                    </li>
                </ul>
                
                @if($availableUnits->count() > 0)
                <a href="{{ route('user.loans.create', ['item_id' => $item->id]) }}" 
                   class="btn btn-primary w-100">
                    <i class="mdi mdi-plus-box-outline me-2"></i>
                    Ajukan Peminjaman
                </a>
                @else
                <button class="btn btn-secondary w-100" disabled>
                    Stok Tidak Tersedia
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        @if($availableUnits->count() > 0)
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Unit Tersedia</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Inventaris</th>
                                <th>Nomor Seri</th>
                                {{-- <th>Aksi</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($availableUnits as $unit)
                            <tr>
                                <td>{{ ($availableUnits->currentPage() - 1) * $availableUnits->perPage() + $loop->iteration }}</td>
                                <td>{{ $unit->inventory_code ?? '-' }}</td>
                                <td>{{ $unit->serial_number ?? '-' }}</td>
                                {{-- <td>
                                    <a href="{{ route('user.loans.create', ['item_id' => $item->id]) }}" 
                                       class="btn btn-sm btn-primary">
                                        Pinjam
                                    </a>
                                </td> --}}
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $availableUnits->links() }}
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

</div>
@endsection