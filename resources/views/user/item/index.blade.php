@extends('layouts.app')

@section('title', 'Daftar Alat')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="card-title m-0 fw-bold text-primary">
                    <i class="mdi mdi-clipboard-list me-2"></i> Data Barang Inventaris
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Brand / Model</th>
                            <th>Spesifikasi</th>
                            <th class="text-center">Unit Tersedia</th>
                            {{-- <th class="text-center" width="15%">Aksi</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>
                                    <strong>{{ $item->name }}</strong>
                                </td>

                                <td>
                                    <span class="badge bg-info">
                                        {{ $item->category->name ?? '-' }}
                                    </span>
                                </td>

                                <td>
                                    {{ $item->brand ?? '-' }}
                                    {{ $item->model ?? '' }}
                                </td>

                                <td class="small text-muted">
                                    {{ Str::limit($item->specification, 80) ?? '-' }}
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-success">
                                        {{ $item->itemUnits->count() }}
                                    </span>
                                </td>

                                {{-- <td class="text-center">
                            <a href="{{ route('user.items.show', $item) }}"
                               class="btn btn-sm btn-outline-secondary">
                                <i class="mdi mdi-eye"></i>
                            </a>

                            <a href="{{ route('user.loans.create', ['item' => $item->id]) }}"
                               class="btn btn-sm btn-primary">
                                <i class="mdi mdi-plus"></i>
                            </a>
                        </td> --}}
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="mdi mdi-information-outline me-2"></i>
                                    Tidak ada alat yang tersedia saat ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
@endsection
