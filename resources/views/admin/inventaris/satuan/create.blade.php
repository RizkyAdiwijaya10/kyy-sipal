@extends('layouts.app')

@section('title', 'Tambah Unit Barang Baru')

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Form Unit Barang Baru</h4>
                
                <form method="POST" action="{{ route('item-units.store') }}">
                    @csrf
                    
                    <div class="form-group mb-3">
                        <label for="item_id" class="form-label">Barang *</label>
                        <select class="form-control @error('item_id') is-invalid @enderror" 
                                id="item_id" 
                                name="item_id"
                                required>
                            <option value="">Pilih Barang</option>
                            @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ old('item_id', $selectedItemId) == $item->id ? 'selected' : '' }}>
                                {{ $item->name }} ({{ $item->category->name }})
                            </option>
                            @endforeach
                        </select>
                        @error('item_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="serial_number" class="form-label">Nomor Seri</label>
                                <input type="text" 
                                       class="form-control @error('serial_number') is-invalid @enderror" 
                                       id="serial_number" 
                                       name="serial_number" 
                                       value="{{ old('serial_number') }}"
                                       placeholder="Contoh: SN-2024-001">
                                @error('serial_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inventory_code" class="form-label">Kode Inventaris</label>
                                <input type="text" 
                                       class="form-control @error('inventory_code') is-invalid @enderror" 
                                       id="inventory_code" 
                                       name="inventory_code" 
                                       value="{{ old('inventory_code', $inventoryCode) }}"
                                       placeholder="Contoh: INV-MIK-001">
                                <small class="text-muted">Kode unik untuk identifikasi unit</small>
                                @error('inventory_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="condition" class="form-label">Kondisi *</label>
                                <select class="form-control @error('condition') is-invalid @enderror" 
                                        id="condition" 
                                        name="condition"
                                        required>
                                    <option value="">Pilih Kondisi</option>
                                    @foreach($conditions as $key => $label)
                                    <option value="{{ $key }}" {{ old('condition') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('condition')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-control @error('status') is-invalid @enderror" 
                                        id="status" 
                                        name="status"
                                        required>
                                    <option value="">Pilih Status</option>
                                    @foreach($statuses as $key => $label)
                                    <option value="{{ $key }}" {{ old('status') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('item-units.index') }}" class="btn btn-light">
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
</div>

@push('scripts')
<script>
// Generate inventory code based on selected item
document.getElementById('item_id').addEventListener('change', function() {
    const itemId = this.value;
    if (itemId) {
        // You can implement AJAX to get item details and generate code
        // For now, just clear the inventory code field
        document.getElementById('inventory_code').value = '';
    }
});
</script>
@endpush
@endsection