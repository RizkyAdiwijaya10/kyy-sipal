@extends('layouts.app')

@section('title', 'Data Sumber Dana')

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">Data Sumber Dana</h4>
                    <div class="d-flex gap-2 flex-nowrap">
                        <!-- Tambah Sumber Dana (MODAL) -->
                        <button class="btn btn-primary btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#createSumberDanaModal">
                            <i class="mdi mdi-plus"></i> Tambah Sumber Dana
                        </button>
                        
                        <!-- Import -->
                        <form id="importForm" 
                              action="{{ route('sumber-dana.import') }}" 
                              method="POST" 
                              enctype="multipart/form-data" 
                              class="d-inline-flex align-items-center m-0">
                            @csrf
                            <label class="btn btn-success btn-sm mb-0">
                                <i class="mdi mdi-upload"></i> Import File
                                <input type="file" name="file" accept=".csv,.xlsx,.xls" hidden
                                       onchange="document.getElementById('importForm').submit()">
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
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Sumber Dana</th>
                                <th>Kode</th>
                                <th>Tahun</th>
                                <th>Deskripsi</th>
                                {{-- <th>Jumlah Barang</th> --}}
                                {{-- <th>Tanggal Dibuat</th> --}}
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sumberDanas as $sumberDana)
                            <tr>
                                <td>{{ ($sumberDanas->currentPage() - 1) * $sumberDanas->perPage() + $loop->iteration }}</td>
                                <td>{{ $sumberDana->name }}</td>
                                <td>{{ $sumberDana->code ?? '-' }}</td>
                                <td>{{ $sumberDana->year ?? '-' }}</td>
                                <td>{{ $sumberDana->description ?? '-' }}</td>
                                {{-- <td>
                                    <span class="badge badge-info">{{ $sumberDana->items_count }} barang</span>
                                </td> --}}
                                {{-- <td>{{ $sumberDana->created_at->format('d/m/Y') }}</td> --}}
                                <td>
                                    <div class="d-flex gap-1">
                                        <!-- EDIT BUTTON (MODAL) -->
                                        <button class="btn btn-warning btn-sm btn-icon"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editSumberDanaModal{{ $sumberDana->id }}"
                                                title="Edit">
                                            <i class="mdi mdi-pencil"></i>
                                        </button>
                                        
                                        <!-- DELETE -->
                                        @if($sumberDana->items_count == 0)
                                            <form id="delete-sumber-dana-{{ $sumberDana->id }}"
                                                  action="{{ route('sumber-dana.destroy', $sumberDana) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" 
                                                        class="btn btn-danger btn-sm btn-icon"
                                                        title="Hapus"
                                                        onclick="confirmDelete(
                                                            'delete-sumber-dana-{{ $sumberDana->id }}',
                                                            'Sumber dana {{ $sumberDana->name }} akan dihapus secara permanen'
                                                        )">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="btn btn-secondary btn-sm btn-icon disabled"
                                                  title="Tidak dapat dihapus (memiliki {{ $sumberDana->items_count }} barang)">
                                                <i class="mdi mdi-delete"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- EDIT MODAL - DILETAKKAN DI DALAM LOOP -->
                            <div class="modal fade" id="editSumberDanaModal{{ $sumberDana->id }}" tabindex="-1">
                                <div class="modal-dialog modal-l modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="{{ route('sumber-dana.update', $sumberDana) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Sumber Dana</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label>Nama Sumber Dana <span class="text-danger">*</span></label>
                                                    <input type="text"
                                                        name="name"
                                                        value="{{ $sumberDana->name }}"
                                                        class="form-control"
                                                        required>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label>Kode</label>
                                                    <input type="text"
                                                        name="code"
                                                        value="{{ $sumberDana->code }}"
                                                        class="form-control">
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label>Tahun</label>
                                                    <input type="number"
                                                        name="year"
                                                        value="{{ $sumberDana->year }}"
                                                        class="form-control"
                                                        min="1900"
                                                        max="{{ date('Y') + 5 }}">
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label>Deskripsi</label>
                                                    <textarea name="description"
                                                            class="form-control"
                                                            rows="3">{{ $sumberDana->description }}</textarea>
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
                                <td colspan="7" class="text-center">Tidak ada data sumber dana</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    <!-- PAGINATION -->
                    @if($sumberDanas->hasPages())
                    <div class="d-flex justify-content-center mt-5">
                        {{ $sumberDanas->links() }}
                    </div>
                    @endif
                </div>
                
                <!-- CREATE MODAL - DI LUAR LOOP (HANYA SATU) -->
                <div class="modal fade" id="createSumberDanaModal" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <form action="{{ route('sumber-dana.store') }}" method="POST">
                                @csrf
                                
                                <div class="modal-header">
                                    <h5 class="modal-title">Tambah Sumber Dana</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label>Nama Sumber Dana <span class="text-danger">*</span></label>
                                        <input type="text"
                                            name="name"
                                            class="form-control"
                                            required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label>Kode</label>
                                        <input type="text"
                                            name="code"
                                            class="form-control"
                                            placeholder="Contoh: APBN-2024">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label>Tahun</label>
                                        <input type="number"
                                            name="year"
                                            class="form-control"
                                            value="{{ date('Y') }}"
                                            min="1900"
                                            max="{{ date('Y') + 5 }}">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label>Deskripsi</label>
                                        <textarea name="description"
                                                class="form-control"
                                                rows="3"
                                                placeholder="Masukkan deskripsi sumber dana"></textarea>
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
