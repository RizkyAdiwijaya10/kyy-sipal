@extends('layouts.admin')

@section('title', 'Ajukan Peminjaman')
@section('page-title', 'Ajukan Peminjaman')
@section('page-subtitle', 'Form pengajuan peminjaman barang')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-alert-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Terjadi Kesalahan:</strong>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('user.loans.store') }}" 
                      method="POST" 
                      enctype="multipart/form-data"
                      id="loanForm">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Pinjam *</label>
                            <input type="date" 
                                   name="loan_date" 
                                   class="form-control"
                                   value="{{ old('loan_date', date('Y-m-d')) }}"
                                   min="{{ date('Y-m-d') }}"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Rencana Kembali *</label>
                            <input type="date" 
                                   name="return_date" 
                                   class="form-control"
                                   value="{{ old('return_date', date('Y-m-d', strtotime('+7 days'))) }}"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tujuan Peminjaman *</label>
                        <textarea name="purpose" 
                                  class="form-control" 
                                  rows="3"
                                  placeholder="Contoh: Praktikum Laboratorium, Penelitian Skripsi, Kegiatan Organisasi, dll"
                                  required>{{ old('purpose') }}</textarea>
                        <small class="text-muted">Minimal 10 karakter</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload Surat Peminjaman (PDF) *</label>
                        <input type="file" 
                               name="surat" 
                               class="form-control"
                               accept="application/pdf"
                               required>
                        <small class="text-muted">Maksimal 2MB, format PDF</small>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3">Daftar Barang yang Dipinjam</h5>
                    
                    <div id="items-container">
                        <div class="item-row card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Pilih Barang *</label>
                                        <select name="items[0][item_id]" 
                                                class="form-control item-select" 
                                                data-index="0"
                                                required>
                                            <option value="">-- Pilih Barang --</option>
                                            @foreach($items as $item)
                                            <option value="{{ $item->id }}" 
                                                    data-stok="{{ $item->available_units_count }}"
                                                    data-nama="{{ $item->name }}">
                                                {{ $item->name }} 
                                                @if($item->brand || $item->model)
                                                    ({{ $item->brand }} {{ $item->model }})
                                                @endif
                                                - Stok: {{ $item->available_units_count }} unit
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Jumlah *</label>
                                        <input type="number" 
                                               name="items[0][quantity]" 
                                               class="form-control quantity-input" 
                                               data-index="0"
                                               value="1"
                                               min="1"
                                               required>
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger remove-item" style="display: none;">
                                            <i class="mdi mdi-delete"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <small class="text-muted info-stok" id="info-stok-0"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-secondary btn-sm" id="addItemBtn">
                            <i class="mdi mdi-plus"></i> Tambah Barang Lain
                        </button>
                        <small class="text-muted ms-2">Maksimal 5 jenis barang</small>
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('user.items.index') }}" class="btn btn-light">
                            <i class="mdi mdi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="mdi mdi-send"></i> Ajukan Peminjaman
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="mdi mdi-information-outline text-primary me-2"></i>
                    Informasi Peminjaman
                </h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="mdi mdi-clock-outline text-info me-2"></i>
                        Maksimal peminjaman 14 hari
                    </li>
                    <li class="mb-2">
                        <i class="mdi mdi-file-pdf text-danger me-2"></i>
                        Surat peminjaman wajib diupload
                    </li>
                    <li class="mb-2">
                        <i class="mdi mdi-check-circle text-success me-2"></i>
                        Maksimal 5 jenis barang
                    </li>
                    <li class="mb-2">
                        <i class="mdi mdi-alert-circle text-warning me-2"></i>
                        Maksimal 10 unit per barang
                    </li>
                </ul>
                
                <hr>
                
                <div id="summary">
                    <h6>Ringkasan Peminjaman:</h6>
                    <div id="summary-content">
                        <p class="text-muted">Belum ada barang dipilih</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPT LANGSUNG TANPA PUSH (jika push tidak berfungsi) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script loaded'); // Debug
    
    let itemCount = 1;
    const maxItems = 5;

    // Data stok barang dari PHP
    const stockData = {};
    @foreach($items as $item)
        stockData[{{ $item->id }}] = {{ $item->available_units_count }};
    @endforeach

    // Function untuk update info stok
    function updateStockInfo(select, index) {
        const itemId = select.value;
        const maxStock = stockData[itemId] || 0;
        const quantityInput = document.querySelector(`input[name="items[${index}][quantity]"]`);
        
        if (quantityInput) {
            quantityInput.max = maxStock;
            
            if (parseInt(quantityInput.value) > maxStock) {
                quantityInput.value = maxStock;
            }
            if (maxStock === 0) {
                quantityInput.value = 0;
                quantityInput.disabled = true;
            } else {
                quantityInput.disabled = false;
            }
            
            const infoDiv = document.getElementById(`info-stok-${index}`);
            if (infoDiv) {
                if (maxStock === 0) {
                    infoDiv.innerHTML = '<span class="text-danger">Stok habis! Tidak dapat dipinjam.</span>';
                } else {
                    infoDiv.innerHTML = `Stok tersedia: ${maxStock} unit. Maksimal pinjam: ${maxStock} unit.`;
                }
            }
        }
    }

    // Function untuk update ringkasan
    function updateSummary() {
        const summaryDiv = document.getElementById('summary-content');
        let summaryHtml = '';
        let totalItems = 0;
        let totalQuantity = 0;
        
        document.querySelectorAll('.item-row').forEach((row, idx) => {
            const select = row.querySelector('.item-select');
            const quantityInput = row.querySelector('.quantity-input');
            const selectedOption = select.options[select.selectedIndex];
            
            if (select.value && quantityInput && parseInt(quantityInput.value) > 0) {
                const itemName = selectedOption ? selectedOption.text.split(' - ')[0] : 'Item';
                const quantity = parseInt(quantityInput.value);
                totalItems++;
                totalQuantity += quantity;
                summaryHtml += `<div class="mb-1"><small>• ${itemName}: <strong>${quantity}</strong> unit</small></div>`;
            }
        });
        
        if (totalItems > 0) {
            summaryHtml = `<div class="alert alert-info py-2 mb-2">
                <strong>${totalItems}</strong> jenis barang<br>
                <strong>${totalQuantity}</strong> total unit
            </div>` + summaryHtml;
        } else {
            summaryHtml = '<p class="text-muted">Belum ada barang dipilih</p>';
        }
        
        summaryDiv.innerHTML = summaryHtml;
    }

    // Function untuk update nomor index semua row
    function updateRowIndices() {
        const rows = document.querySelectorAll('.item-row');
        itemCount = rows.length;
        
        rows.forEach((row, idx) => {
            const select = row.querySelector('.item-select');
            const quantityInput = row.querySelector('.quantity-input');
            const removeBtn = row.querySelector('.remove-item');
            
            if (select) {
                select.name = `items[${idx}][item_id]`;
                select.setAttribute('data-index', idx);
            }
            if (quantityInput) {
                quantityInput.name = `items[${idx}][quantity]`;
                quantityInput.setAttribute('data-index', idx);
            }
            if (removeBtn) {
                if (idx === 0) {
                    removeBtn.style.display = 'none';
                } else {
                    removeBtn.style.display = 'inline-block';
                }
            }
            
            // Update info stok ID
            const infoDiv = document.getElementById(`info-stok-${idx}`);
            if (infoDiv) {
                infoDiv.id = `info-stok-${idx}`;
            }
            
            // Update event listener
            if (select) {
                select.onchange = function() { 
                    updateStockInfo(this, idx); 
                    updateSummary();
                };
                // Trigger update for existing
                if (select.value) {
                    updateStockInfo(select, idx);
                }
            }
            if (quantityInput) {
                quantityInput.onchange = function() { 
                    const selectRow = row.querySelector('.item-select');
                    updateStockInfo(selectRow, idx); 
                    updateSummary();
                };
                quantityInput.onkeyup = function() { updateSummary(); };
            }
        });
    }

    // Tambah item baru
    const addBtn = document.getElementById('addItemBtn');
    if (addBtn) {
        addBtn.addEventListener('click', function() {
            console.log('Add button clicked'); // Debug
            if (itemCount >= maxItems) {
                alert('Maksimal 5 jenis barang!');
                return;
            }
            
            const container = document.getElementById('items-container');
            const newRow = document.createElement('div');
            newRow.className = 'item-row card mb-3';
            
            // Build options HTML
            let optionsHtml = '<option value="">-- Pilih Barang --</option>';
            @foreach($items as $item)
            optionsHtml += `<option value="{{ $item->id }}" data-stok="{{ $item->available_units_count }}" data-nama="{{ $item->name }}">
                {{ $item->name }} 
                @if($item->brand || $item->model)
                    ({{ $item->brand }} {{ $item->model }})
                @endif
                - Stok: {{ $item->available_units_count }} unit
            </option>`;
            @endforeach
            
            newRow.innerHTML = `
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Pilih Barang</label>
                            <select name="items[${itemCount}][item_id]" 
                                    class="form-control item-select" 
                                    data-index="${itemCount}"
                                    required>
                                ${optionsHtml}
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Jumlah</label>
                            <input type="number" 
                                   name="items[${itemCount}][quantity]" 
                                   class="form-control quantity-input" 
                                   data-index="${itemCount}"
                                   value="1"
                                   min="1"
                                   required>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-danger remove-item">
                                <i class="mdi mdi-delete"></i> Hapus
                            </button>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <small class="text-muted info-stok" id="info-stok-${itemCount}"></small>
                        </div>
                    </div>
                </div>
            `;
            
            container.appendChild(newRow);
            itemCount++;
            updateRowIndices();
            updateSummary();
        });
    }

    // Hapus item (delegation)
    document.getElementById('items-container').addEventListener('click', function(e) {
        const removeBtn = e.target.closest('.remove-item');
        if (removeBtn) {
            const row = removeBtn.closest('.item-row');
            if (row && document.querySelectorAll('.item-row').length > 1) {
                row.remove();
                updateRowIndices();
                updateSummary();
            }
        }
    });

    // Event listener untuk perubahan
    document.getElementById('items-container').addEventListener('change', function(e) {
        if (e.target.classList.contains('item-select') || e.target.classList.contains('quantity-input')) {
            updateSummary();
        }
    });
    
    // Initial update
    updateRowIndices();
    updateSummary();
    
    // Validasi sebelum submit
    const loanForm = document.getElementById('loanForm');
    if (loanForm) {
        loanForm.addEventListener('submit', function(e) {
            const selectedItems = document.querySelectorAll('.item-select');
            let hasSelected = false;
            
            for (let select of selectedItems) {
                if (select.value) {
                    const idx = select.getAttribute('data-index');
                    const quantityInput = document.querySelector(`input[name="items[${idx}][quantity]"]`);
                    const maxStock = stockData[select.value];
                    
                    if (quantityInput && parseInt(quantityInput.value) > maxStock) {
                        e.preventDefault();
                        alert(`Jumlah yang diminta melebihi stok tersedia untuk item tersebut!`);
                        return false;
                    }
                    hasSelected = true;
                }
            }
            
            if (!hasSelected) {
                e.preventDefault();
                alert('Silakan pilih minimal 1 barang!');
                return false;
            }
        });
    }
});
</script>
@endsection