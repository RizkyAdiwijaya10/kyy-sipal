@extends('layouts.app')

@section('title', 'Data Kategori')

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="card-title m-0 fw-bold text-primary">
                            <i class="mdi mdi-clipboard-list me-2"></i> Data Kategori
                        </h6>
                        <div class="d-flex gap-2 flex-nowrap">
                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
                                <i class="mdi mdi-filter"></i> Filter
                            </button>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#createCategoryModal">
                                <i class="mdi mdi-plus"></i> Tambah Kategori
                            </button>
                            <form id="importForm" action="{{ route('categories.import') }}" method="POST"
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
                    @if (request()->hasAny(['search', 'has_items']))
                        <div class="alert alert-info mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="mdi mdi-filter me-2"></i>
                                    <strong>Filter Aktif:</strong>
                                    @if (request('search'))
                                        <span class="badge bg-primary ms-2">Pencarian: "{{ request('search') }}"</span>
                                    @endif
                                    @if (request('has_items') !== null && request('has_items') !== '')
                                        <span class="badge bg-info ms-2">
                                            {{ request('has_items') == '1' ? 'Memiliki Barang' : 'Tidak Memiliki Barang' }}
                                        </span>
                                    @endif
                                </div>
                                <a href="{{ route('categories.index') }}" class="btn btn-sm btn-danger">
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
                                    <th>Nama</th>
                                    <th>Deskripsi</th>
                                    <th>Jumlah Barang</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td>{{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $category->name }}</strong>
                                        </td>
                                        <td>{{ $category->description ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $category->items_count }} barang</span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button class="btn btn-info btn-sm btn-icon" data-bs-toggle="modal"
                                                    data-bs-target="#showCategoryModal{{ $category->id }}"
                                                    title="Lihat Detail">
                                                    <i class="mdi mdi-eye"></i> Lihat
                                                </button>
                                                <button class="btn btn-warning btn-sm btn-icon" data-bs-toggle="modal"
                                                    data-bs-target="#editCategoryModal{{ $category->id }}"
                                                    title="Edit">
                                                    <i class="mdi mdi-pencil"></i> Edit
                                                </button>
                                                @if ($category->items_count == 0)
                                                    <form id="delete-category-{{ $category->id }}"
                                                        action="{{ route('categories.destroy', $category) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-danger btn-sm btn-icon"
                                                            onclick="confirmDelete(
                                                                'delete-category-{{ $category->id }}',
                                                                'Kategori ini akan dihapus secara permanen'
                                                            )">
                                                            <i class="mdi mdi-delete"></i> Hapus
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="btn btn-secondary btn-sm btn-icon disabled"
                                                        title="Tidak dapat dihapus (memiliki {{ $category->items_count }} barang)">
                                                        <i class="mdi mdi-delete"></i> Hapus
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data kategori</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="d-flex justify-content-center mt-5">
                            {{ $categories->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SEMUA MODAL DI LUAR TABEL --}}

    <!-- Modal Create -->
    <div class="modal fade" id="createCategoryModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Kategori</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Nama <span class="text-danger"></span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
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

    <!-- Modal Show & Edit per baris -->
    @foreach ($categories as $category)

        <!-- Modal Show -->
        <div class="modal fade" id="showCategoryModal{{ $category->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Kategori</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="35%">Nama Kategori</th>
                                <td width="5%">:</td>
                                <td>{{ $category->name }}</td>
                            </tr>
                            <tr>
                                <th>Deskripsi</th>
                                <td>:</td>
                                <td>{{ $category->description ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Jumlah Barang</th>
                                <td>:</td>
                                <td>
                                    <span class="badge bg-info">{{ $category->items_count }} barang</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Tanggal Dibuat</th>
                                <td>:</td>
                                <td>{{ $category->created_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>Terakhir Diupdate</th>
                                <td>:</td>
                                <td>{{ $category->updated_at->format('d/m/Y H:i:s') }}</td>
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
        <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('categories.update', $category) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Kategori</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Nama <span class="text-danger"></span></label>
                                <input type="text" name="name" value="{{ $category->name }}"
                                    class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Deskripsi</label>
                                <textarea name="description" class="form-control" rows="3">{{ $category->description }}</textarea>
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
                <div class="modal-header text-dark">
                    <h5 class="modal-title" id="filterModalLabel">
                        <i class="mdi mdi-filter me-2"></i> Filter Kategori
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form method="GET" action="{{ route('categories.index') }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="search" class="form-label">Cari Nama Kategori</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="mdi mdi-magnify text-muted"></i>
                                </span>
                                <input type="text" name="search" id="search"
                                    class="form-control border-start-0"
                                    placeholder="Nama kategori..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="has_items" class="form-label">Filter Berdasarkan Barang</label>
                            <select class="form-select" id="has_items" name="has_items">
                                <option value="">Semua Kategori</option>
                                <option value="1" {{ request('has_items') === '1' ? 'selected' : '' }}>
                                    Memiliki Barang
                                </option>
                                <option value="0" {{ request('has_items') === '0' ? 'selected' : '' }}>
                                    Tidak Memiliki Barang
                                </option>
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