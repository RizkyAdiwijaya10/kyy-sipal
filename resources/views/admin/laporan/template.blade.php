<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Peminjaman</title>
    <style>
        @page {
            margin: 0;
            size: A4;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            /* margin: 20px; */
            /* padding: 20px; */
            font-size: 11pt;
            margin: 1.5cm;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18pt;
            margin: 0;
        }
        .header p {
            margin: 3px 0;
            color: #666;
            font-size: 10pt;
        }
        .info {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f5f5f5;
            font-size: 10pt;
        }
        .info table {
            width: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10pt;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            color: black;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9pt;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9pt;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        

    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PEMINJAMAN BARANG</h1>
        <p>Sistem Manajemen Inventory Dan Peminjaman Alat Laboratorium AI</p>
        <p>Dicetak: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="info mb-3">
        <table style="border: none;">
            <tr>
                <td style="border: none; width: 200px;"><strong>Periode</strong></td>
                <td style="border: none; ">: {{ $startDate }} / {{ $endDate }}</td>             
            </tr>
            <tr>
                <td style="border: none;"><strong>Jumlah Peminjaman</strong></td>
                <td style="border: none;" colspan="3">: {{ $loans->count() }} peminjaman</td> 
            </tr>
            <tr>
                <td style="border: none;"><strong>Total Data</strong></td>
                <td style="border: none;" colspan="3">: {{ $loans->count() }} peminjaman</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kode</th>
                <th width="20%">Peminjam</th>
                <th width="12%">Tgl Pinjam</th>
                <th width="12%">Rencana Kembali</th>
                <th width="10%">Jml Barang</th>
                <th width="13%">Status</th>
                <th width="13%">Tgl Kembali</th>
            </tr>
        </thead>
        <tbody>
            @foreach($loans as $index => $loan)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $loan->loan_code }}</td>
                <td>{{ $loan->user->name }}</td>
                <td class="text-center">{{ $loan->loan_date->format('d/m/Y') }}</td>
                <td class="text-center">{{ $loan->return_date->format('d/m/Y') }}</td>
                <td class="text-center">{{ $loan->details->count() }}</td>
                <td>
                    @switch($loan->status)
                        @case('pending')
                            <span>Pending</span>
                            @break
                        @case('approved')
                            <span>Disetujui</span>
                            @break
                        @case('borrowed')
                            <span>Dipinjam</span>
                            @break
                        @case('returned')
                            <span>Dikembalikan</span>
                            @break
                        @case('rejected')
                            <span>Ditolak</span>
                            @break
                        @default
                            <span>{{ $loan->status }}</span>
                    @endswitch
                </td>
                <td class="text-center">
                    @if($loan->actual_return_date)
                        {{ $loan->actual_return_date->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    
</body>
</html>