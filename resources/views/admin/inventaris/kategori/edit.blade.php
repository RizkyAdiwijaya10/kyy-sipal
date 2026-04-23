@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Form Edit Kategori</h4>
                
                <form method="POST" action="{{ route('categories.update', $category) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Nama Kategori *</label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $category->name) }}"
                               placeholder="Contoh: Alat Laboratorium"
                               required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3"
                                  placeholder="Deskripsi kategori (opsional)">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('categories.index') }}" class="btn btn-light">
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
@endsection