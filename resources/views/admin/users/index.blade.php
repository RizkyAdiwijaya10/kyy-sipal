@extends('layouts.admin')

@section('title', 'Data User')

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">Data User Sistem</h4>
                    <div class="d-flex gap-2 flex-nowrap">
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
                            <i class="mdi mdi-plus"></i> Tambah User
                        </button>
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

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>No. Telepon</th>
                                <th>Role</th>
                                {{-- <th>Email Verifikasi</th> --}}
                                <th>Tanggal Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $user->name }}</strong>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone ?? '-' }}</td>
                                <td>
                                    @if($user->role === 'admin')
                                        <span class="badge bg-danger">Admin</span>
                                    @else
                                        <span class="badge bg-info">User</span>
                                    @endif
                                </td>
                                {{-- <td>
                                    @if($user->email_verified_at)
                                        <span class="badge bg-success">Terverifikasi</span>
                                        <br>
                                        <small>{{ $user->email_verified_at->format('d/m/Y H:i') }}</small>
                                    @else
                                        <span class="badge bg-warning">Belum Verifikasi</span>
                                    @endif
                                </td> --}}
                                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-info btn-sm btn-icon"
                                                data-bs-toggle="modal"
                                                data-bs-target="#showUserModal{{ $user->id }}"
                                                title="Lihat Detail">
                                            <i class="mdi mdi-eye"></i>
                                        </button>
                                        
                                        <button class="btn btn-warning btn-sm btn-icon"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editUserModal{{ $user->id }}"
                                                title="Edit User">
                                            <i class="mdi mdi-pencil"></i>
                                        </button>

                                        @if(auth()->id() !== $user->id)
                                        <form id="delete-user-{{ $user->id }}"
                                            action="{{ route('admin.users.destroy', $user) }}"
                                            method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')

                                            <button type="button"
                                                    class="btn btn-danger btn-sm btn-icon"
                                                    onclick="confirmDelete(
                                                        'delete-user-{{ $user->id }}',
                                                        'User {{ $user->name }} akan dihapus secara permanen'
                                                    )"
                                                    title="Hapus User">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                        @else
                                        <span class="btn btn-secondary btn-sm btn-icon disabled"
                                              title="Tidak dapat menghapus akun sendiri">
                                            <i class="mdi mdi-delete"></i>
                                        </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data user</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-5">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Create User -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah User Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-control" required>
                            <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required>
                        <small class="text-muted">Minimal 8 karakter</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Show & Edit untuk setiap user -->
@foreach($users as $user)
<!-- Modal Show User -->
<div class="modal fade" id="showUserModal{{ $user->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="35%">Nama Lengkap</th>
                        <td width="5%">:</td>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>:</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>No. Telepon</th>
                        <td>:</td>
                        <td>{{ $user->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td>:</td>
                        <td>
                            @if($user->role === 'admin')
                                <span class="badge bg-danger">Admin</span>
                            @else
                                <span class="badge bg-info">User</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Status Verifikasi</th>
                        <td>:</td>
                        <td>
                            @if($user->email_verified_at)
                                <span class="badge bg-success">Terverifikasi</span>
                                <br><small>{{ $user->email_verified_at->format('d/m/Y H:i') }}</small>
                            @else
                                <span class="badge bg-warning">Belum Verifikasi</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal Dibuat</th>
                        <td>:</td>
                        <td>{{ $user->created_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Terakhir Diupdate</th>
                        <td>:</td>
                        <td>{{ $user->updated_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit User -->
<div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit User: {{ $user->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="phone" class="form-control" value="{{ $user->phone }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-control" required>
                            <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password (Kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" class="form-control">
                        <small class="text-muted">Minimal 8 karakter</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection

@push('scripts')
<script>
function confirmDelete(formId, message) {
    if (confirm(message + '\n\nApakah Anda yakin ingin menghapus?')) {
        document.getElementById(formId).submit();
    }
}

// Auto close modal if there are errors and show alert
@if($errors->any())
    @if(old('password') !== null || old('name') !== null)
        var myModal = new bootstrap.Modal(document.getElementById('createUserModal'));
        myModal.show();
    @endif
@endif
</script>
@endpush