@extends('layouts.app')

@section('title', 'Ajukan Peminjaman')

@section('content')
    <div class="loan-page">

        {{-- Main Form --}}
        <div class="loan-main">
            <div class="card">
                <div class="card-header py-3 d-flex justify-content-between align-items-center mb-4">
                    <h6 class="card-title m-0 fw-bold text-primary">
                        <i class="mdi mdi-clipboard-list me-2"></i> Ajukan Peminjaman
                    </h6>
                </div>
                {{-- <div class="card-header-custom">
                    <div class="icon-wrap">
                        <i class="mdi mdi-clipboard-list"></i>
                    </div>
                    <h2>Ajukan Peminjaman</h2>
                </div> --}}
                <div class="card-body">

                    @if (session('success'))
                        <div class="alert-custom alert-success-custom">
                            <i class="mdi mdi-check-circle"></i>
                            <span>{{ session('success') }}</span>
                            <button type="button" class="alert-close" data-bs-dismiss="alert">
                                <i class="mdi mdi-close"></i>
                            </button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert-custom alert-danger-custom">
                            <i class="mdi mdi-alert-circle"></i>
                            <span>{{ session('error') }}</span>
                            <button type="button" class="alert-close" data-bs-dismiss="alert">
                                <i class="mdi mdi-close"></i>
                            </button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert-custom alert-danger-custom">
                            <i class="mdi mdi-alert-circle"></i>
                            <div>
                                <strong>Terjadi kesalahan:</strong>
                                <ul class="mb-0 mt-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('user.loans.store') }}" method="POST" enctype="multipart/form-data"
                        id="loanForm">
                        @csrf

                        {{-- Tanggal --}}
                        <div class="form-row-2">
                            <div class="field">
                                <label class="field-label">
                                    Tanggal Pinjam <span class="req"></span>
                                </label>
                                <input type="date" name="loan_date" class="form-control"
                                    value="{{ old('loan_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="field">
                                <label class="field-label">
                                    Rencana Kembali <span class="req"></span>
                                </label>
                                <input type="date" name="return_date" class="form-control"
                                    value="{{ old('return_date', date('Y-m-d', strtotime('+7 days'))) }}"
                                    min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                            </div>
                        </div>

                        {{-- Tujuan --}}
                        <div class="field">
                            <label class="field-label">
                                Tujuan Peminjaman <span class="req"></span>
                            </label>
                            <textarea name="purpose" class="form-control" rows="3"
                                placeholder="Contoh: Praktikum Laboratorium, Penelitian Skripsi, Kegiatan Organisasi..." required>{{ old('purpose') }}</textarea>
                            <small class="field-hint">Minimal 10 karakter</small>
                        </div>

                        {{-- Upload Surat --}}
                        <div class="field">
                            <label class="field-label">
                                Surat Peminjaman (PDF) <span class="req"></span>
                                &nbsp;
                                <a href="{{ route('user.loans.download-template') }}" class="link-download">
                                    <i class="mdi mdi-download"></i> Unduh Template
                                </a>
                            </label>
                            <div class="file-upload-area" id="fileUploadArea">
                                <i class="mdi mdi-file-upload-outline"></i>
                                <p class="file-upload-text">Klik untuk memilih file PDF</p>
                                <p class="file-upload-hint">Maksimal 2 MB</p>
                                <input type="file" name="surat" id="suratInput" accept="application/pdf" required>
                            </div>
                            <div id="fileSelected" class="file-selected" style="display:none;">
                                <i class="mdi mdi-file-pdf-box"></i>
                                <span id="fileName"></span>
                                <button type="button" id="removeFile">
                                    <i class="mdi mdi-close"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Section Divider --}}
                        <div class="section-divider">
                            <span>Daftar Barang</span>
                        </div>

                        {{-- Items --}}
                        <div id="items-container">
                            <div class="item-card">
                                {{-- <div class="item-card-header">
                                    <span class="item-label">Barang #1</span>
                                </div> --}}
                                <div class="form-row-3">
                                    <div class="field mb-0">
                                        <label class="field-label">Pilih Barang</label>
                                        <select name="items[0][item_id]" class="form-control item-select" data-index="0"
                                            required>
                                            <option value="">— Pilih barang —</option>
                                            @foreach ($items as $item)
                                                <option value="{{ $item->id }}"
                                                    data-stok="{{ $item->available_units_count }}"
                                                    {{ $item->available_units_count == 0 ? 'disabled' : '' }}>
                                                    {{ $item->name }}
                                                    @if ($item->brand || $item->model)
                                                        ({{ $item->brand }} {{ $item->model }})
                                                    @endif
                                                    @if ($item->available_units_count == 0)
                                                        — Habis
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="field mb-0">
                                        <label class="field-label">Jumlah</label>
                                        <input type="number" name="items[0][quantity]" class="form-control quantity-input"
                                            data-index="0" value="1" min="1" required>
                                    </div>
                                    <div class="field mb-0 field-action">
                                        <label class="field-label invisible">x</label>
                                        <button type="button" class="btn-remove-item" style="display:none;">
                                            <i class="mdi mdi-trash-can-outline"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                                <div class="stock-info" id="info-stok-0"></div>
                            </div>
                        </div>

                        <button type="button" class="btn-add-item" id="addItemBtn">
                            <i class="mdi mdi-plus"></i> Tambah barang lain
                        </button>

                        {{-- Actions --}}
                        <div class="form-actions">
                            <a href="{{ route('user.items.index') }}" class="btn-back">
                                <i class="mdi mdi-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn-submit" id="submitBtn">
                                <i class="mdi mdi-send"></i> Ajukan Peminjaman
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="loan-sidebar">
            <div class="card">
                <div class="card-header-custom">
                    <div class="icon-wrap icon-wrap-warning">
                        <i class="mdi mdi-information-outline"></i>
                    </div>
                    <h2>Informasi</h2>
                </div>
                <div class="card-body">
                    <ul class="info-list">
                        <li>
                            <i class="mdi mdi-clock-outline info-icon-info"></i>
                            <span>Maksimal peminjaman <strong>14 hari</strong></span>
                        </li>
                        <li>
                            <i class="mdi mdi-file-pdf-box info-icon-danger"></i>
                            <span>Surat peminjaman wajib diupload</span>
                        </li>
                        <li>
                            <i class="mdi mdi-check-all info-icon-success"></i>
                            <span>Maksimal <strong>5 jenis</strong> barang</span>
                        </li>
                        <li>
                            <i class="mdi mdi-alert-circle-outline info-icon-warning"></i>
                            <span>Maksimal <strong>10 unit</strong> per barang</span>
                        </li>
                    </ul>

                    <hr class="sidebar-divider">

                    <p class="summary-label">Ringkasan Peminjaman</p>
                    <div id="summary-content">
                        <div class="summary-empty">
                            <i class="mdi mdi-cart-off"></i>
                            <span>Belum ada barang dipilih</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <style>
        /* ── Layout ─────────────────────────────────────────── */
        .loan-page {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 20px;
            align-items: start;
        }

        @media (max-width: 768px) {
            .loan-page {
                grid-template-columns: 1fr;
            }

            .loan-sidebar {
                display: none;
            }
        }

        /* ── Card ────────────────────────────────────────────── */
        .card {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: none;
        }

        .card-header-custom {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fff;
        }

        .card-header-custom h2 {
            font-size: 15px;
            font-weight: 600;
            margin: 0;
            color: #1a1a2e;
        }

        .icon-wrap {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #e8f0fe;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-wrap i {
            font-size: 16px;
            color: #1967d2;
        }

        .icon-wrap-warning {
            background: #fff3e0;
        }

        .icon-wrap-warning i {
            color: #e65100;
        }

        .card-body {
            padding: 1.25rem;
            background: #fff;
        }

        /* ── Alerts ──────────────────────────────────────────── */
        .alert-custom {
            padding: .75rem 1rem;
            border-radius: 8px;
            font-size: 13px;
            display: flex;
            gap: 8px;
            align-items: flex-start;
            margin-bottom: 1rem;
            position: relative;
        }

        .alert-custom i {
            font-size: 16px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .alert-success-custom {
            background: #e6f4ea;
            color: #137333;
        }

        .alert-danger-custom {
            background: #fce8e6;
            color: #c5221f;
        }

        .alert-close {
            position: absolute;
            right: 10px;
            top: 10px;
            background: none;
            border: none;
            cursor: pointer;
            color: inherit;
            opacity: .7;
            padding: 0;
        }

        /* ── Form rows ───────────────────────────────────────── */
        .form-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 12px;
        }

        .form-row-3 {
            display: grid;
            grid-template-columns: 1fr 130px 110px;
            gap: 12px;
        }

        @media (max-width: 576px) {

            .form-row-2,
            .form-row-3 {
                grid-template-columns: 1fr;
            }
        }

        /* ── Fields ──────────────────────────────────────────── */
        .field {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-bottom: 12px;
        }

        .field.mb-0 {
            margin-bottom: 0;
        }

        .field-label {
            font-size: 13px;
            color: #5f6368;
            font-weight: 500;
        }

        .req {
            color: #c5221f;
            margin-left: 2px;
        }

        .field-hint {
            font-size: 12px;
            color: #9aa0a6;
        }

        .invisible {
            visibility: hidden;
        }

        .field-action {
            justify-content: flex-end;
        }

        /* ── Form controls ───────────────────────────────────── */
        .form-control {
            width: 100%;
            font-size: 14px;
            padding: 8px 10px;
            border: 1px solid #dadce0;
            border-radius: 8px;
            background: #fff;
            color: #202124;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            font-family: inherit;
        }

        .form-control:focus {
            border-color: #1967d2;
            box-shadow: 0 0 0 3px rgba(25, 103, 210, .12);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 85px;
            line-height: 1.5;
        }

        /* ── File upload ─────────────────────────────────────── */
        .link-download {
            font-size: 12px;
            color: #1967d2;
            text-decoration: none;
        }

        .link-download:hover {
            text-decoration: underline;
        }

        .file-upload-area {
            border: 1px dashed #dadce0;
            border-radius: 8px;
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            background: #f8f9fa;
            transition: border-color .15s, background .15s;
        }

        .file-upload-area:hover {
            border-color: #1967d2;
            background: #e8f0fe;
        }

        .file-upload-area i {
            font-size: 28px;
            color: #9aa0a6;
        }

        .file-upload-area input[type=file] {
            display: none;
        }

        .file-upload-text {
            font-size: 13px;
            color: #5f6368;
            margin: 0;
        }

        .file-upload-hint {
            font-size: 11px;
            color: #9aa0a6;
            margin: 0;
        }

        .file-selected {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: #e6f4ea;
            border-radius: 8px;
            font-size: 13px;
            color: #137333;
        }

        .file-selected i {
            font-size: 18px;
        }

        .file-selected span {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .file-selected button {
            background: none;
            border: none;
            cursor: pointer;
            color: #137333;
            padding: 0;
            display: flex;
            align-items: center;
        }

        /* ── Section divider ─────────────────────────────────── */
        .section-divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 1.5rem 0 1rem;
        }

        .section-divider span {
            font-size: 12px;
            font-weight: 600;
            color: #9aa0a6;
            white-space: nowrap;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .section-divider::before,
        .section-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e9ecef;
        }

        /* ── Item card ───────────────────────────────────────── */
        .item-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 10px;
        }

        .item-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .item-label {
            font-size: 12px;
            font-weight: 600;
            color: #9aa0a6;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .stock-info {
            font-size: 12px;
            color: #5f6368;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .stock-info .ok {
            color: #137333;
        }

        .stock-info .empty {
            color: #c5221f;
        }

        /* ── Buttons ─────────────────────────────────────────── */
        .btn-remove-item {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 13px;
            padding: 7px 12px;
            border: 1px solid #fce8e6;
            border-radius: 8px;
            background: #fff;
            color: #c5221f;
            cursor: pointer;
            font-family: inherit;
            transition: background .15s;
        }

        .btn-remove-item:hover {
            background: #fce8e6;
        }

        .btn-add-item {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            font-size: 13px;
            padding: 9px;
            border: 1px dashed #dadce0;
            border-radius: 8px;
            background: none;
            color: #5f6368;
            cursor: pointer;
            font-family: inherit;
            transition: all .15s;
            margin-top: 4px;
        }

        .btn-add-item:hover {
            border-color: #1967d2;
            color: #1967d2;
            background: #e8f0fe;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1.25rem;
            border-top: 1px solid #e9ecef;
            margin-top: 1.25rem;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            padding: 9px 18px;
            border: 1px solid #dadce0;
            border-radius: 8px;
            background: #fff;
            color: #5f6368;
            cursor: pointer;
            text-decoration: none;
            transition: background .15s;
        }

        .btn-back:hover {
            background: #f8f9fa;
            color: #202124;
        }

        .btn-submit {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            padding: 9px 20px;
            border: none;
            border-radius: 8px;
            background: #1967d2;
            color: #fff;
            cursor: pointer;
            font-family: inherit;
            font-weight: 500;
            transition: background .15s;
        }

        .btn-submit:hover {
            background: #1557b0;
        }

        /* ── Sidebar ─────────────────────────────────────────── */
        .info-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .info-list li {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: 13px;
            color: #5f6368;
        }

        .info-list li i {
            font-size: 16px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .info-icon-info {
            color: #1967d2;
        }

        .info-icon-danger {
            color: #c5221f;
        }

        .info-icon-success {
            color: #137333;
        }

        .info-icon-warning {
            color: #e65100;
        }

        .sidebar-divider {
            border: none;
            border-top: 1px solid #e9ecef;
            margin: 1rem 0;
        }

        .summary-label {
            font-size: 11px;
            font-weight: 600;
            color: #9aa0a6;
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-bottom: 10px;
        }

        .summary-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            padding: 1rem 0;
            color: #9aa0a6;
            font-size: 13px;
        }

        .summary-empty i {
            font-size: 24px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            padding: 5px 0;
            border-bottom: 1px solid #f1f3f4;
            color: #5f6368;
        }

        .summary-row:last-of-type {
            border-bottom: none;
        }

        .summary-row strong {
            color: #202124;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            font-weight: 600;
            padding-top: 8px;
            margin-top: 6px;
            border-top: 1px solid #dadce0;
            color: #202124;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            
            const stockData = {};
            @foreach ($items as $item)
                stockData[{{ $item->id }}] = {{ $item->available_units_count }};
            @endforeach

            let itemCount = 1;
            const maxItems = 5;

            // ── File upload handler ────────────────────────────────────
            const fileUploadArea = document.getElementById('fileUploadArea');
            const suratInput = document.getElementById('suratInput');
            const fileSelected = document.getElementById('fileSelected');
            const fileName = document.getElementById('fileName');
            const removeFile = document.getElementById('removeFile');

            if (fileUploadArea) {
                fileUploadArea.addEventListener('click', () => suratInput.click());

                suratInput.addEventListener('change', function() {
                    if (this.files.length) {
                        fileName.textContent = this.files[0].name;
                        fileUploadArea.style.display = 'none';
                        fileSelected.style.display = 'flex';
                    }
                });

                removeFile.addEventListener('click', function() {
                    suratInput.value = '';
                    fileUploadArea.style.display = 'flex';
                    fileSelected.style.display = 'none';
                });
            }

            // ── Helpers ────────────────────────────────────────────────
            function getSelectedIds(excludeEl = null) {
                return Array.from(document.querySelectorAll('.item-select'))
                    .filter(s => s !== excludeEl && s.value)
                    .map(s => s.value);
            }

            function updateAllDropdowns(changedSelect = null) {
                const selected = getSelectedIds(changedSelect);

                document.querySelectorAll('.item-select').forEach(sel => {
                    Array.from(sel.options).forEach(opt => {
                        if (!opt.value) {
                            opt.disabled = false;
                            return;
                        }

                        const outOfStock = (stockData[opt.value] ?? 0) === 0;
                        const takenElsewhere = selected.includes(opt.value) && opt.value !== sel
                            .value;
                        opt.disabled = outOfStock || takenElsewhere;
                    });

                    // Reset jika nilai saat ini menjadi disabled
                    if (sel.options[sel.selectedIndex]?.disabled) {
                        sel.value = '';
                        updateStockInfo(sel.closest('.item-card'));
                    }
                });
            }

            function updateStockInfo(row) {
                if (!row) return;
                const sel = row.querySelector('.item-select');
                const qty = row.querySelector('.quantity-input');
                const info = row.querySelector('.stock-info');
                const idx = sel?.getAttribute('data-index');

                if (!sel?.value) {
                    if (info) info.innerHTML = '';
                    return;
                }

                const stock = stockData[sel.value] ?? 0;
                if (qty) {
                    qty.max = Math.min(stock, 10);
                    if (+qty.value > stock) qty.value = stock;
                    qty.disabled = stock === 0;
                    if (stock === 0) qty.value = 0;
                }
                if (info) {
                    info.innerHTML = stock === 0 ?
                        `<i class="mdi mdi-alert-circle empty"></i><span class="empty">Stok habis, tidak dapat dipinjam</span>` :
                        `<i class="mdi mdi-check-circle ok"></i><span class="ok">${stock} unit tersedia</span>`;
                }
            }

            function updateSummary() {
                const content = document.getElementById('summary-content');
                const rows = document.querySelectorAll('.item-card');
                const items = [];
                let totalQty = 0;

                rows.forEach(row => {
                    const sel = row.querySelector('.item-select');
                    const qty = row.querySelector('.quantity-input');
                    const opt = sel?.options[sel.selectedIndex];
                    if (sel?.value && qty && +qty.value > 0) {
                        const name = opt?.text?.split(' —')[0]?.split(' (')[0] ?? 'Barang';
                        items.push({
                            name,
                            qty: +qty.value
                        });
                        totalQty += +qty.value;
                    }
                });

                if (!items.length) {
                    content.innerHTML =
                        `<div class="summary-empty"><i class="mdi mdi-cart-off"></i><span>Belum ada barang dipilih</span></div>`;
                    return;
                }

                const rows_html = items.map(it =>
                    `<div class="summary-row"><span>${it.name}</span><strong>${it.qty} unit</strong></div>`
                ).join('');

                content.innerHTML = rows_html +
                    `<div class="summary-total"><span>${items.length} jenis barang</span><span>${totalQty} total unit</span></div>`;
            }

            function bindRowEvents(row, idx) {
                const sel = row.querySelector('.item-select');
                const qty = row.querySelector('.quantity-input');
                const rm = row.querySelector('.btn-remove-item');
                const label = row.querySelector('.item-label');

                if (label) label.textContent = `Barang #${idx + 1}`;
                if (sel) {
                    sel.name = `items[${idx}][item_id]`;
                    sel.setAttribute('data-index', idx);
                    sel.onchange = function() {
                        updateStockInfo(row);
                        updateAllDropdowns(this);
                        updateSummary();
                    };
                }
                if (qty) {
                    qty.name = `items[${idx}][quantity]`;
                    qty.setAttribute('data-index', idx);
                    qty.oninput = () => updateSummary();
                }
                if (rm) rm.style.display = idx === 0 ? 'none' : 'inline-flex';

                const infoDiv = row.querySelector('.stock-info');
                if (infoDiv) infoDiv.id = `info-stok-${idx}`;
            }

            function reindex() {
                document.querySelectorAll('.item-card').forEach((row, i) => bindRowEvents(row, i));
                itemCount = document.querySelectorAll('.item-card').length;
                updateAllDropdowns();
            }

            // ── Tambah item ────────────────────────────────────────────
            document.getElementById('addItemBtn')?.addEventListener('click', function() {
                if (itemCount >= maxItems) {
                    alert('Maksimal 5 jenis barang!');
                    return;
                }

                // Build options dari item pertama
                const firstSelect = document.querySelector('.item-select');
                const optionsHtml = Array.from(firstSelect.options).map(o => o.outerHTML).join('');

                const div = document.createElement('div');
                div.className = 'item-card';
                div.innerHTML = `
                    <div class="item-card-header">
                        <span class="item-label">Barang #${itemCount + 1}</span>
                    </div>
                    <div class="form-row-3">
                        <div class="field mb-0">
                            <label class="field-label">Pilih Barang</label>
                            <select class="form-control item-select" required>${optionsHtml}</select>
                        </div>
                        <div class="field mb-0">
                            <label class="field-label">Jumlah</label>
                            <input type="number" class="form-control quantity-input" value="1" min="1" max="10" required>
                        </div>
                        <div class="field mb-0 field-action">
                            <label class="field-label invisible">x</label>
                            <button type="button" class="btn-remove-item">
                                <i class="mdi mdi-trash-can-outline"></i> Hapus
                            </button>
                        </div>
                    </div>
                    <div class="stock-info"></div>`;

                div.querySelector('.item-select').value = '';
                document.getElementById('items-container').appendChild(div);
                reindex();
                updateSummary();
            });

            // ── Hapus item ─────────────────────────────────────────────
            document.getElementById('items-container').addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-remove-item');
                if (btn && document.querySelectorAll('.item-card').length > 1) {
                    btn.closest('.item-card').remove();
                    reindex();
                    updateSummary();
                }
            });

            // ── Validasi submit ────────────────────────────────────────
            document.getElementById('loanForm')?.addEventListener('submit', function(e) {
                const selects = document.querySelectorAll('.item-select');
                const selectedIds = [];
                let hasSelected = false;

                for (const sel of selects) {
                    if (!sel.value) continue;

                    if (selectedIds.includes(sel.value)) {
                        e.preventDefault();
                        alert('Tidak dapat memilih barang yang sama lebih dari satu kali!');
                        return;
                    }

                    selectedIds.push(sel.value);
                    const idx = sel.getAttribute('data-index');
                    const qty = document.querySelector(`input[name="items[${idx}][quantity]"]`);
                    const maxStock = stockData[sel.value];

                    if (qty && +qty.value > maxStock) {
                        e.preventDefault();
                        alert('Jumlah yang diminta melebihi stok tersedia!');
                        return;
                    }

                    hasSelected = true;
                }

                if (!hasSelected) {
                    e.preventDefault();
                    alert('Silakan pilih minimal 1 barang!');
                }
            });

            // ── Init ───────────────────────────────────────────────────
            reindex();
            updateSummary();
        });
    </script>
@endsection
