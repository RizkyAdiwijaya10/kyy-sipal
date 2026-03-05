@extends('layouts.admin')

@section('title', 'Ajukan Peminjaman')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-body">
            {{-- Alert Success --}}
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Alert Error --}}
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
                  enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Pinjam</label>
                        <input type="date" 
                               name="loan_date" 
                               class="form-control"
                               value="{{ old('loan_date') }}"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Kembali</label>
                        <input type="date" 
                               name="return_date" 
                               class="form-control"
                               value="{{ old('return_date') }}"
                               required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tujuan Peminjaman</label>
                    <textarea name="purpose" 
                              class="form-control" 
                              placeholder="Skripsi/Praktikum/dll"
                              rows="3"
                              required>{{ old('purpose') }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label">Upload Surat Peminjaman (PDF)</label>
                    <input type="file" 
                        name="surat" 
                        class="form-control"
                        accept="application/pdf"
                        required>
                    <small class="text-muted">Maksimal 2MB</small>
                </div>

                <hr>

                <h6 class="mb-3">Pilih Barang</h6>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="5%">Pilih</th>
                                <th>Nama Barang</th>
                                <th>Merek</th>
                                <th>Model</th>
                                <th>Stok Tersedia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox"
                                               name="items[]"
                                               value="{{ $item->id }}"
                                               class="form-check-input item-checkbox">
                                    </td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->brand }}</td>
                                    <td>{{ $item->model }}</td>
                                    <td>
                                        <span class="badge bg-success">
                                            {{ $item->available_units_count }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        Tidak ada barang tersedia
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        Ajukan Peminjaman
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                        Batal
                    </a>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
    
        const maxItems = 3;
        const checkboxes = document.querySelectorAll('.item-checkbox');
    
        function updateCheckboxState() {
    
            const checked = document.querySelectorAll('.item-checkbox:checked');
    
            if (checked.length >= maxItems) {
    
                checkboxes.forEach(cb => {
                    if (!cb.checked) {
                        cb.disabled = true;
                    }
                });
    
            } else {
    
                checkboxes.forEach(cb => {
                    cb.disabled = false;
                });
    
            }
        }
    
        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateCheckboxState);
        });
    
    });
    </script>