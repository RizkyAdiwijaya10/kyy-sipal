@extends('layouts.app')

@section('title', 'Edit Sumber Dana')

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Form Edit Sumber Dana</h4>
                
                <form method="POST" action="{{ route('sumber-dana.update', $sumberDana) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Nama Sumber Dana *</label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $sumberDana->name) }}"
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
                                       value="{{ old('code', $sumberDana->code) }}"
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
                                    <option value="{{ $year }}" {{ old('year', $sumberDana->year) == $year ? 'selected' : '' }}>
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
                                  placeholder="Deskripsi sumber dana (opsional)">{{ old('description', $sumberDana->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('sumber-dana.index') }}" class="btn btn-light">
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