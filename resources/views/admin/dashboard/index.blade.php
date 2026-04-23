@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="card-title text-white">{{ $totalItems }}</h3>
                        <p class="card-text">Total Barang</p>
                    </div>
                    <div class="align-self-center">
                        <i class="mdi mdi-cube-outline mdi-36px"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="card-title text-white">{{ $availableUnits }}</h3>
                        <p class="card-text">Unit Tersedia</p>
                    </div>
                    <div class="align-self-center">
                        <i class="mdi mdi-check-circle mdi-36px"></i>
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
                        <h3 class="card-title text-white">{{ $borrowedUnits }}</h3>
                        <p class="card-text">Unit Dipinjam</p>
                    </div>
                    <div class="align-self-center">
                        <i class="mdi mdi-bookmark mdi-36px"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="card-title text-white">{{ $totalCategories }}</h3>
                        <p class="card-text">Kategori</p>
                    </div>
                    <div class="align-self-center">
                        <i class="mdi mdi-tag mdi-36px"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Barang Stok Rendah</h5>
            </div>
           <div class="card-body">
                @if($lowStockItems->count() > 0)
                <div class="list-group">
                    @foreach($lowStockItems as $item)
                    <a href="{{ route('items.show', $item) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">{{ $item->name }}</h6>
                            <span class="badge bg-{{ $item->item_units_count == 0 ? 'danger' : 'warning' }}">
                                {{ $item->item_units_count }} unit
                            </span>
                        </div>
                        <small class="text-muted">{{ $item->category->name ?? '-' }}</small>
                    </a>
                    @endforeach
                </div> 
                @else
                <p class="text-muted text-center">Tidak ada barang dengan stok rendah</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Barang Terbaru</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    @foreach($recentItems as $item)
                    <a href="{{ route('items.show', $item) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">{{ $item->name }}</h6>
                        </div>
                        <small class="text-muted">
                            <span>{{ $item->category->name ?? '-' }}</span>
                        </small>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection