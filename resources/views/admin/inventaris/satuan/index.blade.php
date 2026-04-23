@extends('layouts.app')

@section('title', 'Data Unit Barang')

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">Data Unit Barang</h4>
                    <div>
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
                            <i class="mdi mdi-filter"></i> Filter
                        </button>
                        <button class="btn btn-primary btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#createUnitModal">
                            <i class="mdi mdi-plus"></i>Tambah Unit
                        </button>
                        <form id="importUnitForm"
                            action="{{ route('item-units.import') }}"
                            method="POST"
                            enctype="multipart/form-data"
                            class="d-inline-flex align-items-center m-0">
                            @csrf

                            <label class="btn btn-success btn-sm mb-0">
                                <i class="mdi mdi-upload"></i> Import File
                                <input type="file" name="file" accept=".csv,.xlsx,.xls" hidden
                                    onchange="document.getElementById('importUnitForm').submit()">
                            </label>
                        </form>
                    </div>
                </div>
                
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                
                <!-- Filter Summary -->
                @if(request()->hasAny(['item_id', 'status', 'condition']))
                <div class="alert alert-info mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="mdi mdi-filter me-2"></i>
                            <strong>Filter Aktif:</strong>
                            @if(request('item_id'))
                                <span class="badge bg-primary ms-2">Barang: {{ \App\Models\Item::find(request('item_id'))->name ?? 'Unknown' }}</span>
                            @endif
                            @if(request('status'))
                                <span class="badge bg-info ms-2">Status: {{ request('status') }}</span>
                            @endif
                            @if(request('condition'))
                                <span class="badge bg-warning ms-2">Kondisi: {{ request('condition') }}</span>
                            @endif
                        </div>
                        <a href="{{ route('item-units.index') }}" class="btn btn-sm btn-light">
                            <i class="mdi mdi-close"></i> Hapus Filter
                        </a>
                    </div>
                </div>
                @endif
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Inventaris</th>
                                <th>Nama Barang</th>
                                <th>No. Seri</th>
                                <th>Kondisi</th>
                                <th>Status</th>
                                {{-- <th>Tanggal Dibuat</th> --}}
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($itemUnits as $unit)
                            <tr>
                                <td>
                                    {{ ($itemUnits->currentPage() - 1) * $itemUnits->perPage() + $loop->iteration }}
                                </td>                                
                                <td>
                                    <strong>{{ $unit->inventory_code ?? '-' }}</strong>
                                </td>
                                <td>
                                    {{ $unit->item->name }}
                                </td>
                                <td>{{ $unit->serial_number ?? '-' }}</td>
                                <td>
                                    @if($unit->condition == 'baik')
                                        <span class="badge badge-success">Baik</span>
                                    @elseif($unit->condition == 'rusak')
                                        <span class="badge badge-danger">Rusak</span>
                                    @elseif($unit->condition == 'maintenance')
                                        <span class="badge badge-warning">Maintenance</span>
                                    @else
                                        <span class="badge badge-dark">Hilang</span>
                                    @endif
                                </td>
                                <td>
                                    @if($unit->status == 'tersedia')
                                        <span class="badge badge-success">Tersedia</span>
                                    @elseif($unit->status == 'dipinjam')
                                        <span class="badge badge-info">Dipinjam</span>
                                    @elseif($unit->status == 'dipesan')
                                        <span class="badge badge-warning">Dipesan</span>
                                    @else
                                        <span class="badge badge-danger">Nonaktif</span>
                                    @endif
                                </td>
                                {{-- <td>{{ $unit->created_at->format('d/m/Y') }}</td> --}}
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-warning btn-sm btn-icon"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editUnitModal{{ $unit->id }}">
                                            <i class="mdi mdi-pencil"></i>
                                        </button>
                                        
                                        @if($unit->status != 'dipinjam')
                                        <form id="delete-unit-{{ $unit->id }}"
                                              action="{{ route('item-units.destroy', $unit) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                        
                                            <button type="button"
                                                    class="btn btn-danger btn-sm btn-icon"
                                                    title="Hapus"
                                                    onclick="confirmDelete(
                                                        'delete-unit-{{ $unit->id }}',
                                                        'Unit barang {{ $unit->inventory_code ?? '' }} akan dihapus secara permanen'
                                                    )">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                        @else
                                        <span class="btn btn-secondary btn-sm btn-icon disabled"
                                              title="Tidak dapat dihapus (sedang dipinjam)">
                                            <i class="mdi mdi-delete"></i>
                                        </span>
                                        @endif
                                        
                                    </div>
                                </td>
                            </tr>

                            @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data unit barang</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-5">
                        {{ $itemUnits->links() }}
                    </div>
                </div>
                <!-- Modal Create -->
                <div class="modal fade" id="createUnitModal" tabindex="-1">
                    <div class="modal-dialog modal-l modal-dialog-centered">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('item-units.store') }}">
                                @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Tambah Unit Barang</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">

                                        <div class="mb-3">
                                            <label class="form-label">Barang</label>
                                            <select name="item_id" class="form-control" required>
                                                <option value="">Pilih Barang</option>
                                                @foreach($items as $item)
                                                    <option value="{{ $item->id }}">
                                                        {{ $item->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Kode Inventaris</label>
                                            <input type="text" name="inventory_code" class="form-control">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">No Seri</label>
                                            <input type="text" name="serial_number" class="form-control">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Kondisi</label>
                                            <select name="condition" class="form-control">
                                                <option value="baik">Baik</option>
                                                <option value="rusak">Rusak</option>
                                                <option value="maintenance">Maintenance</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-control">
                                                <option value="tersedia">Tersedia</option>
                                                <option value="dipinjam">Dipinjam</option>
                                                <option value="dipesan">Dipesan</option>
                                                <option value="nonaktif">Nonaktif</option>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">
                                            Simpan
                                        </button>
                                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                                            Batal
                                        </button>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
                @foreach($itemUnits as $unit)
                <div class="modal fade" id="editUnitModal{{ $unit->id }}" tabindex="-1">
                    <div class="modal-dialog modal-l modal-dialog-centered">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('item-units.update', $unit->id) }}">
                                @csrf
                                @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            Edit Unit Barang
                                        </h5>

                                        <button type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal">
                                        </button>
                                    </div>
                                    <div class="modal-body">

                                        <div class="mb-3">
                                            <label class="form-label">Barang</label>

                                            <select name="item_id"
                                                    class="form-control"
                                                    required>

                                                @foreach($items as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ $unit->item_id == $item->id ? 'selected' : '' }}>
                                                        {{ $item->name }}
                                                    </option>
                                                @endforeach

                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label>Kode Inventaris</label>
                                            <input type="text"
                                                name="inventory_code"
                                                value="{{ $unit->inventory_code }}"
                                                class="form-control">
                                        </div>

                                        <div class="mb-3">
                                            <label>No Seri</label>
                                            <input type="text"
                                                name="serial_number"
                                                value="{{ $unit->serial_number }}"
                                                class="form-control">
                                        </div>

                                        <div class="mb-3">
                                            <label>Kondisi</label>

                                            <select name="condition"
                                                    class="form-control">

                                                <option value="baik"
                                                    {{ $unit->condition == 'baik' ? 'selected' : '' }}>
                                                    Baik
                                                </option>

                                                <option value="rusak"
                                                    {{ $unit->condition == 'rusak' ? 'selected' : '' }}>
                                                    Rusak
                                                </option>

                                                <option value="maintenance"
                                                    {{ $unit->condition == 'maintenance' ? 'selected' : '' }}>
                                                    Maintenance
                                                </option>

                                            </select>

                                        </div>

                                        <div class="mb-3">
                                            <label>Status</label>

                                            <select name="status"
                                                    class="form-control">

                                                <option value="tersedia"
                                                    {{ $unit->status == 'tersedia' ? 'selected' : '' }}>
                                                    Tersedia
                                                </option>

                                                <option value="dipinjam"
                                                    {{ $unit->status == 'dipinjam' ? 'selected' : '' }}>
                                                    Dipinjam
                                                </option>

                                                <option value="dipesan"
                                                    {{ $unit->status == 'dipesan' ? 'selected' : '' }}>
                                                    Dipesan
                                                </option>

                                                <option value="nonaktif"
                                                    {{ $unit->status == 'nonaktif' ? 'selected' : '' }}>
                                                    Nonaktif
                                                </option>

                                            </select>

                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">
                                            Update
                                        </button>
                                        <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">
                                            Batal
                                        </button>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>                         
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter Unit Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="GET" action="{{ route('item-units.index') }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="item_id" class="form-label">Filter Berdasarkan Barang</label>
                        <select class="form-control" id="item_id" name="item_id">
                            <option value="">Semua Barang</option>
                            @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="">Semua Status</option>
                                    @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="condition" class="form-label">Kondisi</label>
                                <select class="form-control" id="condition" name="condition">
                                    <option value="">Semua Kondisi</option>
                                    @foreach($conditions as $condition)
                                    <option value="{{ $condition }}" {{ request('condition') == $condition ? 'selected' : '' }}>
                                        {{ ucfirst($condition) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
