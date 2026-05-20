@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Kelola informasi akun Anda')

@section('content')
<div class="container-fluid">

    {{-- SUCCESS MESSAGE --}}
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

    {{-- INFORMASI AKUN --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="mdi mdi-information-outline me-2"></i>
                        Informasi Akun
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="mdi mdi-account-circle" style="font-size: 112px; color: #4e73df;"></i>
                                <h5 class="mt-0">{{ auth()->user()->name }}</h5>
                                
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="border rounded p-3 mb-2">
                                        <small class="text-muted">Email</small>
                                        <p class="mb-0"><strong>{{ auth()->user()->email }}</strong></p>
                                        @if(auth()->user()->email_verified_at)
                                            <span class="badge bg-success mt-1">Terverifikasi</span>
                                        @else
                                            <span class="badge bg-warning mt-1">Belum Verifikasi</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="border rounded p-3 mb-2">
                                        <small class="text-muted">Nomor Telepon</small>
                                        <p class="mb-0">
                                            <strong>{{ auth()->user()->phone ?? '-' }}</strong>
                                        </p>
                                        @if(auth()->user()->phone)
                                            <span class="badge bg-info mt-1">WhatsApp Terdaftar</span>
                                        @else
                                            <span class="badge bg-secondary mt-1">Belum diisi</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="border rounded p-3 mb-2">
                                        <small class="text-muted">Bergabung</small>
                                        <p class="mb-0">
                                            <strong>{{ auth()->user()->created_at->format('d F Y') }}</strong>
                                        </p>
                                        <small class="text-muted">{{ auth()->user()->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="border rounded p-3 mb-2">
                                        <small class="text-muted">Terakhir Update</small>
                                        <p class="mb-0">
                                            <strong>{{ auth()->user()->updated_at->format('d F Y') }}</strong>
                                        </p>
                                        <small class="text-muted">{{ auth()->user()->updated_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- UPDATE PROFILE --}}
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="mdi mdi-account-circle me-2"></i>
                        Informasi Profil
                    </h5>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        {{-- Nama --}}
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', auth()->user()->name) }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email"
                                   name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', auth()->user()->email) }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nomor Telepon --}}
                        <div class="mb-3">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text"
                                   name="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', auth()->user()->phone) }}"
                                   placeholder="Contoh: 6281234567890">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Role (Read Only) --}}
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <input type="text"
                                   class="form-control"
                                   value="{{ auth()->user()->role == 'admin' ? 'Administrator' : 'User Biasa' }}"
                                   readonly
                                   disabled>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save me-1"></i> Update Profil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- UPDATE PASSWORD --}}
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="mdi mdi-lock-reset me-2"></i>
                        Ubah Password
                    </h5>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('PUT')

                        {{-- Password Lama --}}
                        <div class="mb-3">
                            <label class="form-label">Password Lama</label>
                            <input type="password"
                                   name="current_password"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password Baru --}}
                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password"
                                   name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Minimal 8 karakter</small>
                        </div>

                        {{-- Konfirmasi Password Baru --}}
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password"
                                   name="password_confirmation"
                                   class="form-control"
                                   required>
                        </div>

                        <div class="alert alert-info small">
                            <i class="mdi mdi-information-outline me-1"></i>
                            Password yang kuat terdiri dari minimal 8 karakter, kombinasi huruf, angka, dan simbol.
                        </div>

                        <button type="submit" class="btn btn-warning">
                            <i class="mdi mdi-lock-reset me-1"></i> Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- TOMBOL LOGOUT --}}
    <div class="row mt-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="mdi mdi-power text-danger me-2"></i>
                            <span>Keluar dari sistem</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger" id="logoutBtnProfile">
                            <i class="mdi  mdi-power me-1"></i>
                            Logout
                        </button>
                        
                        <form method="POST" action="{{ route('logout') }}" id="logout-form-profile" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>
    // Auto close alert after 3 seconds
    setTimeout(function() {
        let alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            let bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 3000);

    // ========== LOGOUT CONFIRMATION ==========
    const logoutBtn = document.getElementById('logoutBtnProfile');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Anda akan keluar dari sistem!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Keluar!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Sedang keluar...',
                        text: 'Mohon tunggu',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    document.getElementById('logout-form-profile').submit();
                }
            });
        });
    }
</script>
@endpush

@push('styles')
<style>
    .card-header {
        font-weight: 600;
    }
    .form-label {
        font-weight: 500;
    }
    .border {
        transition: all 0.2s;
    }
    .border:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .btn-danger {
        transition: all 0.3s;

    }
</style>
@endpush
@endsection