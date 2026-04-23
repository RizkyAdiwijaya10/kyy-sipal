@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Form Edit Barang</h4>
                
                <form method="POST" action="{{ route('items.update', $item) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category_id" class="form-label">Kategori *</label>
                                <select class="form-control @error('category_id') is-invalid @enderror" 
                                        id="category_id" 
                                        name="category_id"
                                        required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="funding_source_id" class="form-label">Sumber Dana</label>
                                <select class="form-control @error('funding_source_id') is-invalid @enderror" 
                                        id="funding_source_id" 
                                        name="funding_source_id">
                                    <option value="">Pilih Sumber Dana (opsional)</option>
                                    @foreach($sumberDanas as $sumberDana)
                                    <option value="{{ $sumberDana->id }}" {{ old('funding_source_id', $item->funding_source_id) == $sumberDana->id ? 'selected' : '' }}>
                                        {{ $sumberDana->name }} ({{ $sumberDana->year ?? '-' }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('funding_source_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Nama Barang *</label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $item->name) }}"
                               placeholder="Contoh: Mikroskop, Laptop, Meja"
                               required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="brand" class="form-label">Merek</label>
                                <input type="text" 
                                       class="form-control @error('brand') is-invalid @enderror" 
                                       id="brand" 
                                       name="brand" 
                                       value="{{ old('brand', $item->brand) }}"
                                       placeholder="Contoh: Olympus, Dell">
                                @error('brand')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="model" class="form-label">Model/Tipe</label>
                                <input type="text" 
                                       class="form-control @error('model') is-invalid @enderror" 
                                       id="model" 
                                       name="model" 
                                       value="{{ old('model', $item->model) }}"
                                       placeholder="Contoh: CX23, Latitude 5430">
                                @error('model')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="specification" class="form-label">Spesifikasi</label>
                        <textarea class="form-control @error('specification') is-invalid @enderror" 
                                  id="specification" 
                                  name="specification" 
                                  rows="3"
                                  placeholder="Spesifikasi teknis barang (opsional)">{{ old('specification', $item->specification) }}</textarea>
                        @error('specification')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    {{-- <div class="form-group mb-3">
                        <label for="total_stock" class="form-label">Jumlah Stok *</label>
                        <input type="number" 
                               class="form-control @error('total_stock') is-invalid @enderror" 
                               id="total_stock" 
                               name="total_stock" 
                               value="{{ old('total_stock', $item->total_stock) }}"
                               min="0"
                               required>
                        @error('total_stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Jumlah unit barang yang tersedia</small>
                    </div> --}}
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('items.index') }}" class="btn btn-light">
                            <i class="mdi mdi-arrow-left"></i> Kembali
                        </a>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save"></i> Update
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editModal{{ $item->id }}">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form action="{{ route('items.update', $item) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5>Edit Barang</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Nama</label>
                        <input type="text"
                               name="name"
                               value="{{ $item->name }}"
                               class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">
                        Update
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection