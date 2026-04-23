@extends('layouts.app')

@section('title', 'Tambah Sumber Dana Baru')

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Form Sumber Dana Baru</h4>
                
                <form method="POST" action="{{ route('sumber-dana.store') }}">
                    @csrf
                    
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Nama Sumber Dana *</label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               placeholder="Contoh: Dana BOS, APBD, Hibah"
                               required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="code" class="form-label">Kode Sumber Dana</label>
                                <input type="text" 
                                       class="form-control @error('code') is-invalid @enderror" 
                                       id="code" 
                                       name="code" 
                                       value="{{ old('code') }}"
                                       placeholder="Contoh: BOS-2024">
                                @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="year" class="form-label">Tahun Anggaran</label>
                                <select class="form-control @error('year') is-invalid @enderror" 
                                        id="year" 
                                        name="year">
                                    <option value="">Pilih Tahun</option>
                                    @foreach($years as $year)
                                    <option value="{{ $year }}" {{ old('year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('year')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3"
                                  placeholder="Deskripsi sumber dana (opsional)">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('sumber-dana.index') }}" class="btn btn-light">
                            <i class="mdi mdi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    {{-- <div class="col-lg-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Informasi</h4>
                <p class="text-muted">
                    <small>
                        <i class="mdi mdi-information-outline text-primary me-2"></i>
                        Sumber dana digunakan untuk mencatat asal dana pembelian barang.
                    </small>
                </p>
                <p class="text-muted">
                    <small>
                        <i class="mdi mdi-alert-circle-outline text-warning me-2"></i>
                        Nama dan kode sumber dana harus unik.
                    </small>
                </p>
                <p class="text-muted">
                    <small>
                        <i class="mdi mdi-check-circle-outline text-success me-2"></i>
                        Sumber dana dapat dihapus jika tidak memiliki barang.
                    </small>
                </p>
            </div>
        </div>
    </div> --}}
</div>
@endsection