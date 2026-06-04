@extends('layouts.app')

@section('title', 'Data Barang')

@section('content')
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="card-title m-0 fw-bold text-primary">
                            <i class="mdi mdi-clipboard-list me-2"></i> Data Barang Inventaris
                        </h6>
                        <div class="d-flex gap-2 flex-nowrap">
                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
                                <i class="mdi mdi-filter"></i> Filter
                            </button>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#createItemModal">
                                <i class="mdi mdi-plus"></i> Tambah Barang
                            </button>
                            <form id="importForm" action="{{ route('items.import') }}" method="POST"
                                enctype="multipart/form-data" class="d-inline-flex align-items-center m-0">
                                @csrf
                                <label class="btn btn-success btn-sm mb-0">
                                    <i class="mdi mdi-upload"></i> Import File
                                    <input type="file" name="file" accept=".csv,.xlsx,.xls" hidden
                                        onchange="document.getElementById('importForm').submit()">
                                </label>
                            </form>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Filter Summary --}}
                    @if (request()->hasAny(['search', 'category_id', 'funding_source_id']))
                        <div class="alert alert-info mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="mdi mdi-filter me-2"></i>
                                    <strong>Filter Aktif:</strong>
                                    @if (request('search'))
                                        <span class="badge bg-primary ms-2">Pencarian: "{{ request('search') }}"</span>
                                    @endif
                                    @if (request('category_id'))
                                        <span class="badge bg-info ms-2">
                                            Kategori: {{ $categories->find(request('category_id'))->name ?? 'Unknown' }}
                                        </span>
                                    @endif
                                    
                                </div>
                                <a href="{{ route('items.index') }}" class="btn btn-sm btn-danger">
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
                                    <th>Nama Barang</th>
                                    <th>Kategori</th>
                                    <th>Merek</th>
                                    <th>Model</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                    <tr>
                                        <td>{{ ($items->currentPage() - 1) * $items->perPage() + $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $item->name }}</strong>
                                            @if ($item->specification)
                                                <br><small class="text-muted">{{ Str::limit($item->specification, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $item->category->name }}</td>
                                        <td>
                                            {{ $item->brand }}
                                        </td>
                                        <td>{{ $item->model}}</td>
                                        <td>
                                            @php
                                                $available = $item->itemUnits->where('status', 'tersedia')->count();
                                                $borrowed  = $item->itemUnits->where('status', 'dipinjam')->count();
                                                $damaged   = $item->itemUnits->where('condition', '!=', 'baik')->count();
                                            @endphp
                                            <div class="d-flex flex-column gap-1">
                                                <span class="badge badge-success">{{ $available }} tersedia</span>
                                                <span class="badge badge-info">{{ $borrowed }} dipinjam</span>
                                                @if ($damaged > 0)
                                                    <span class="badge badge-warning">{{ $damaged }} bermasalah</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button class="btn btn-info btn-sm btn-icon" data-bs-toggle="modal"
                                                    data-bs-target="#showItemModal{{ $item->id }}"
                                                    title="Lihat Detail">
                                                    <i class="mdi mdi-eye"></i> Lihat
                                                </button>
                                                <button class="btn btn-warning btn-sm btn-icon" data-bs-toggle="modal"
                                                    data-bs-target="#editModal{{ $item->id }}"
                                                    title="Edit">
                                                    <i class="mdi mdi-pencil"></i> Edit
                                                </button>
                                                @if ($item->item_unit_count == 0)
                                                    <form id="delete-items-{{ $item->id }}"
                                                        action="{{ route('items.destroy', $item) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-danger btn-sm btn-icon"
                                                            onclick="confirmDelete(
                                                                'delete-items-{{ $item->id }}',
                                                                'Barang {{ $item->name }} akan dihapus secara permanen'
                                                            )">
                                                            <i class="mdi mdi-delete"></i> Hapus
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="btn btn-secondary btn-sm btn-icon disabled"
                                                        title="Tidak dapat dihapus (memiliki {{ $item->item_units_count }} unit)">
                                                        <i class="mdi mdi-delete"></i> Hapus
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data barang</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center mt-5">
                            {{ $items->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SEMUA MODAL DI LUAR TABEL --}}

    <!-- Modal Create -->
    <div class="modal fade" id="createItemModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('items.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Barang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Kategori</label>
                            <select name="category_id" class="form-control">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
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
                            <textarea name="specification" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Show, Edit per baris -->
    @foreach ($items as $item)
        @php
            $available = $item->itemUnits->where('status', 'tersedia')->count();
            $borrowed  = $item->itemUnits->where('status', 'dipinjam')->count();
            $damaged   = $item->itemUnits->where('condition', '!=', 'baik')->count();
        @endphp

        <!-- Modal Show -->
        <div class="modal fade" id="showItemModal{{ $item->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Barang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="35%">Nama Barang</th>
                                <td width="5%">:</td>
                                <td>{{ $item->name }}</td>
                            </tr>
                            <tr>
                                <th>Kategori</th>
                                <td>:</td>
                                <td>{{ $item->category->name }}</td>
                            </tr>
                    
                            <tr>
                                <th>Merek</th>
                                <td>:</td>
                                <td>{{ $item->brand ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Model</th>
                                <td>:</td>
                                <td>{{ $item->model ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Spesifikasi</th>
                                <td>:</td>
                                <td>{{ $item->specification ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Unit Tersedia</th>
                                <td>:</td>
                                <td><span class="badge badge-success">{{ $available }} unit</span></td>
                            </tr>
                            <tr>
                                <th>Unit Dipinjam</th>
                                <td>:</td>
                                <td><span class="badge badge-info">{{ $borrowed }} unit</span></td>
                            </tr>
                            <tr>
                                <th>Unit Bermasalah</th>
                                <td>:</td>
                                <td><span class="badge badge-warning">{{ $damaged }} unit</span></td>
                            </tr>
                            <tr>
                                <th>Tanggal Dibuat</th>
                                <td>:</td>
                                <td>{{ $item->created_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>Terakhir Diupdate</th>
                                <td>:</td>
                                <td>{{ $item->updated_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Edit -->
        <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
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
                                <label>Nama Barang <span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ $item->name }}"
                                    class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Kategori</label>
                                <select name="category_id" class="form-control">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $item->category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label>Merek</label>
                                <input type="text" name="brand" value="{{ $item->brand }}"
                                    class="form-control">
                            </div>
                            <div class="mb-3">
                                <label>Model</label>
                                <input type="text" name="model" value="{{ $item->model }}"
                                    class="form-control">
                            </div>
                            <div class="mb-3">
                                <label>Spesifikasi</label>
                                <textarea name="specification" class="form-control" rows="3">{{ $item->specification }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @endforeach

    <!-- Modal Filter -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="filterModalLabel">
                        <i class="mdi mdi-filter me-2"></i> Filter Barang
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form method="GET" action="{{ route('items.index') }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="search" class="form-label">Cari Nama Barang</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="mdi mdi-magnify text-muted"></i>
                                </span>
                                <input type="text" name="search" id="search"
                                    class="form-control border-start-0"
                                    placeholder="Nama barang..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Filter Berdasarkan Kategori</label>
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-filter"></i> Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection