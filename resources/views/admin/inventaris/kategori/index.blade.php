@extends('layouts.admin')

@section('title', 'Data Kategori')

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Data Kategori</h4>
                    <div class="d-flex gap-2">
                        <!-- Tambah -->
                        <button class="btn btn-primary btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#createCategoryModal">
                            <i class="mdi mdi-plus"></i> Tambah Kategori
                        </button>
                        
                        <!-- Import -->
                        <form action="{{ route('categories.import') }}"
                            method="POST"
                            enctype="multipart/form-data"
                            class="m-0"
                            id="importForm">
                            @csrf
                            <label class="btn btn-success btn-sm mb-0">
                                <i class="mdi mdi-upload"></i> Import CSV
                                <input type="file"
                                    name="file"
                                    accept=".csv,.xlsx,.xls"
                                    hidden
                                    onchange="document.getElementById('importForm').submit()">
                            </label>                
                        </form>              
                    </div>              
                </div>
                
                {{-- @if(session('success'))
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
                @endif --}}
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Deskripsi</th>
                                <th>Jumlah Barang</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                            <tr>
                                <td>{{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $category->name }}</strong>
                                </td>
                                <td>
                                    {{ $category->description ?? '-' }}
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $category->items_count }} barang
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <!-- EDIT BUTTON -->
                                        <button class="btn btn-warning btn-sm btn-icon"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editCategoryModal{{ $category->id }}">
                                            <i class="mdi mdi-pencil"></i>
                                        </button>
                                        
                                        <!-- DELETE -->
                                        @if($category->items_count == 0)
                                        <form id="delete-category-{{ $category->id }}"
                                            action="{{ route('categories.destroy', $category) }}"
                                            method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                    class="btn btn-danger btn-sm btn-icon"
                                                    onclick="confirmDelete(
                                                        'delete-category-{{ $category->id }}',
                                                        'Kategori ini akan dihapus secara permanen'
                                                    )">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                        @else
                                        <span class="btn btn-secondary btn-sm btn-icon disabled"
                                            title="Tidak dapat dihapus (memiliki {{ $category->items_count }} barang)">
                                            <i class="mdi mdi-delete"></i>
                                        </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- EDIT MODAL - DILETAKKAN DI DALAM LOOP (SAMA SEPERTI POLA ITEMS) -->
                            <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
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
                                                    <label>Nama</label>
                                                    <input type="text"
                                                        name="name"
                                                        value="{{ $category->name }}"
                                                        class="form-control"
                                                        required>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label>Deskripsi</label>
                                                    <textarea name="description"
                                                            class="form-control">{{ $category->description }}</textarea>
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
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    Tidak ada data kategori
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    <!-- PAGINATION -->
                    <div class="d-flex justify-content-center mt-5">
                        {{ $categories->links() }}
                    </div>
                </div>
                
                <!-- CREATE MODAL - DI LUAR LOOP (HANYA SATU) -->
                <div class="modal fade" id="createCategoryModal" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <form action="{{ route('categories.store') }}" method="POST">
                                @csrf
                                
                                <div class="modal-header">
                                    <h5 class="modal-title">Tambah Kategori</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label>Nama</label>
                                        <input type="text"
                                            name="name"
                                            class="form-control"
                                            required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label>Deskripsi</label>
                                        <textarea name="description" class="form-control"></textarea>
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
                
            </div>
        </div>
    </div>
</div>
@endsection