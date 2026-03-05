@extends('layouts.admin')

@section('title', 'Detail Peminjaman')

@section('content')
<div class="card">
    <div class="card-body">

        <h4>Kode: {{ $loan->loan_code }}</h4>
        <p>Status: {{ ucfirst($loan->status) }}</p>
        <p>Tanggal Pinjam: {{ $loan->loan_date->format('d/m/Y') }}</p>
        <p>Tanggal Kembali: {{ $loan->return_date->format('d/m/Y') }}</p>

        <hr>

        <h5>Detail Item:</h5>
        <table class="table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Kode Unit</th>
                    <th>Kondisi Awal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($loan->details as $detail)
                <tr>
                    <td>{{ $detail->itemUnit->item->name }}</td>
                    <td>{{ $detail->itemUnit->inventory_code }}</td>
                    <td>{{ $detail->condition_before }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <a href="{{ route('user.loans.history') }}" class="btn btn-light">
            <i class="mdi mdi-arrow-left"></i> Kembali
        </a>

    </div>
</div>
@endsection