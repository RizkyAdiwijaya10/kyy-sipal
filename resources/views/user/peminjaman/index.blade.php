@extends('layouts.admin')

@section('title', 'Data Kategori')

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">Data Kategori</h4>
                    <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm">
                        <i class="mdi mdi-plus"></i> Tambah Kategori
                    </a>
                </div>
                
                <!-- Alert Message -->
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
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th>Jumlah Barang</th>
                                {{-- <th>Tanggal Dibuat</th> --}}
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->description ?? '-' }}</td>
                                <td>
                                    <span>{{ $category->items_count }} barang</span>
                                </td>
                                {{-- <td>{{ $category->created_at->format('d/m/Y') }}</td> --}}
                                <td>
                                    <div class="d-flex gap-1">
                                        {{-- <!-- View Button -->
                                        <a href="{{ route('categories.show', $category) }}" 
                                           class="btn btn-info btn-sm btn-icon"
                                           title="Detail"
                                           data-bs-toggle="tooltip">
                                            <i class="mdi mdi-eye"></i>
                                        </a> --}}
                                        
                                        <!-- Edit Button -->
                                        <a href="{{ route('categories.edit', $category) }}" 
                                           class="btn btn-warning btn-sm btn-icon"
                                           title="Edit"
                                           data-bs-toggle="tooltip">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        
                                        <!-- Delete Button -->
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
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data kategori</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection