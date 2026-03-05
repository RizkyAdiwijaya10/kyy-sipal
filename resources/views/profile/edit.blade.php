{{-- <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
 --}}


 @extends('layouts.admin')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Kelola informasi akun Anda')

@section('content')
<div class="container-fluid">

    {{-- SUCCESS MESSAGE --}}
    @if(session('status') == 'profile-updated')
        <div class="alert alert-success">
            Profil berhasil diperbarui
        </div>
    @endif

    @if(session('status') == 'password-updated')
        <div class="alert alert-success">
            Password berhasil diperbarui
        </div>
    @endif

    <div class="row">

        {{-- UPDATE PROFILE --}}
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Profil</h5>
                </div>

                <div class="card-body">

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   value="{{ old('name', auth()->user()->name) }}"
                                   required>

                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   value="{{ old('email', auth()->user()->email) }}"
                                   required>

                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <button class="btn btn-primary">
                            Update Profil
                        </button>

                    </form>

                </div>
            </div>
        </div>


        {{-- UPDATE PASSWORD --}}
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Ubah Password</h5>
                </div>

                <div class="card-body">

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Password Lama</label>
                            <input type="password"
                                   name="current_password"
                                   class="form-control"
                                   required>

                            @error('current_password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password"
                                   name="password"
                                   class="form-control"
                                   required>

                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Konfirmasi Password Baru
                            </label>
                            <input type="password"
                                   name="password_confirmation"
                                   class="form-control"
                                   required>
                        </div>

                        <button class="btn btn-warning">
                            Update Password
                        </button>

                    </form>

                </div>
            </div>
        </div>

    </div>

</div>
@endsection