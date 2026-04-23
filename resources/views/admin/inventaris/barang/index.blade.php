@extends('layouts.app')

@section('title', 'Data Barang')

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">Data Barang Inventaris</h4>
                    <div class="d-flex gap-2 flex-nowrap">
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createItemModal">
                            <i class="mdi mdi-plus"></i> Tambah Barang
                        </button>
                        <form id="importForm" action="{{ route('items.import') }}" method="POST" enctype="multipart/form-data" class="d-inline-flex align-items-center m-0">
                            @csrf
                        
                            <label class="btn btn-success btn-sm mb-0">
                                <i class="mdi mdi-upload"></i> Import File
                                <input type="file" name="file" accept=".csv,.xlsx,.xls" hidden
                                       onchange="document.getElementById('importForm').submit()">
                            </label>
                        </form>
                    </div>
                    
                </div>
                
                {{-- @if(session('success'))
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
                @endif --}}
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Sumber Dana</th>
                                <th>Merek/Model</th>
                                {{-- <th>Stok Total</th> --}}
                                <th>Status</th>
                                {{-- <th>Tanggal Dibuat</th> --}}
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                            <tr>
                                <td>{{ ($items->currentPage() - 1) * $items->perPage() + $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $item->name }}</strong>
                                    @if($item->specification)
                                    <br><small class="text-muted">{{ Str::limit($item->specification, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span>{{ $item->category->name }}</span>
                                </td>
                                <td>{{ $item->fundingSource->name ?? '-' }}</td>
                                <td>
                                    @if($item->brand || $item->model)
                                        {{ $item->brand }} {{ $item->model }}
                                    @else
                                        -
                                    @endif
                                </td>
                                {{-- <td>
                                    <span>{{ $item->item_units_count }} unit</span>
                                </td> --}}
                                <td>
                                    @php
                                        $available = $item->itemUnits->where('status', 'tersedia')->count();
                                        $borrowed = $item->itemUnits->where('status', 'dipinjam')->count();
                                        $damaged = $item->itemUnits->where('condition', '!=', 'baik')->count();
                                    @endphp
                                    <div class="d-flex flex-column gap-1">
                                        <span class="badge badge-success">{{ $available }} tersedia</span>
                                        <span class="badge badge-info">{{ $borrowed }} dipinjam</span>
                                        @if($damaged > 0)
                                        <span class="badge badge-warning">{{ $damaged }} bermasalah</span>
                                        @endif
                                    </div>
                                </td>
                                {{-- <td>{{ $item->created_at->format('d/m/Y') }}</td> --}}
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-warning btn-sm btn-icon"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editModal{{ $item->id }}">
                                            <i class="mdi mdi-pencil"></i>
                                        </button>

                                        @if($item->item_unit_count == 0)
                                        <form id="delete-items-{{ $item->id }}"
                                            action="{{ route('items.destroy', $item) }}"
                                            method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')

                                            <button type="button"
                                                    class="btn btn-danger btn-sm btn-icon"
                                                    onclick="confirmDelete(
                                                        'delete-items-{{ $item->id }}',
                                                        'Kategori ini akan dihapus secara permanen'
                                                    )">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                        @else
                                        <span class="btn btn-secondary btn-sm btn-icon disabled"
                                            title="Tidak dapat dihapus (memiliki {{ $item->item_units_count }} barang)">
                                            <i class="mdi mdi-delete"></i>
                                        </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada data barang</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-5">
                        {{ $items->links() }}
                    </div>
                </div>
                <div class="modal fade" id="createItemModal" tabindex="-1">
                    <div class="modal-dialog modal-l modal-dialog-centered">
                        <div class="modal-content">
                
                            <form action="{{ route('items.store') }}" method="POST">
                                @csrf
                
                                <div class="modal-header">
                                    <h5 class="modal-title">Tambah Barang</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                
                                <div class="modal-body">
                
                                    <div class="mb-3">
                                        <label>Nama Barang</label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>
                
                                    <div class="mb-3">
                                        <label>Kategori</label>
                                        <select name="category_id" class="form-control">
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label>Sumber Dana</label>
                                        <select name="funding_source_id" class="form-control">
                                            <option value="">-- Pilih Sumber Dana --</option>
                                            @foreach($sumberDanas as $sd)
                                                <option value="{{ $sd->id }}">
                                                    {{ $sd->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                
                                    <div class="mb-3">
                                        <label>Merek</label>
                                        <input type="text" name="brand" class="form-control">
                                    </div>
                
                                    <div class="mb-3">
                                        <label>Model</label>
                                        <input type="text" name="model" class="form-control">
                                    </div>
                
                                    <div class="mb-3">
                                        <label>Spesifikasi</label>
                                        <textarea name="specification" class="form-control"></textarea>
                                    </div>
                
                                </div>
                
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">
                                        Simpan
                                    </button>
                
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        Batal
                                    </button>
                                </div>
                
                            </form>
                
                        </div>
                    </div>
                </div>

                @foreach($items as $item)
                <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1">
                    <div class="modal-dialog modal-l modal-dialog-centered">
                        <div class="modal-content">

                            <form action="{{ route('items.update', $item) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Barang</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">

                                    <div class="mb-3">
                                        <label>Nama Barang</label>
                                        <input type="text"
                                            name="name"
                                            value="{{ $item->name }}"
                                            class="form-control"
                                            required>
                                    </div>

                                    <div class="mb-3">
                                        <label>Kategori</label>
                                        <select name="category_id" class="form-control">
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ $item->category_id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label>Sumber Dana</label>
                                        <select name="funding_source_id" class="form-control">
                                            <option value="">-- Pilih Sumber Dana --</option>
                                            @foreach($sumberDanas as $sd)
                                                <option value="{{ $sd->id }}"
                                                    {{ $item->funding_source_id == $sd->id ? 'selected' : '' }}>
                                                    {{ $sd->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label>Merek</label>
                                        <input type="text"
                                            name="brand"
                                            value="{{ $item->brand }}"
                                            class="form-control">
                                    </div>

                                    <div class="mb-3">
                                        <label>Model</label>
                                        <input type="text"
                                            name="model"
                                            value="{{ $item->model }}"
                                            class="form-control">
                                    </div>

                                    <div class="mb-3">
                                        <label>Spesifikasi</label>
                                        <textarea name="specification"
                                                class="form-control">{{ $item->specification }}</textarea>
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

@endsection

