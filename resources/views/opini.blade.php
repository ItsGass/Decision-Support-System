<x-app-layout>

    {{-- ============================================================
     OPINI — DATA UPLOAD PAGE  |  Bootstrap 5  |  Refactored UI
     ============================================================ --}}

    <style>
        /* ── Custom tokens (Bootstrap‑safe, no extra libs) ── */

        @media (max-width: 640px) {
            .tbl-opini-col {
                display: none !important;
            }

            .tbl-expand-row {
                display: table-row !important;
            }
        }

        @media (min-width: 641px) {
            .tbl-expand-row {
                display: none !important;
            }
        }

        .tbl-expand-inner {
            background: var(--bs-light, #f8f9fa);
            padding: 8px 12px 12px;
            border-top: 1px solid var(--border-muted, #dee2e6);
        }

        .tbl-expand-label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #6c757d;
            margin-top: 8px;
            margin-bottom: 3px;
        }


        :root {
            --accent: #0d6efd;
            --accent-soft: #e8f0fe;
            --success-soft: #e6f9f0;
            --warning-soft: #fff8e6;
            --danger-soft: #fff0f0;
            --card-radius: 1rem;
            --border-muted: #e9ecef;
            --text-muted2: #8c97a8;
        }

        /* Page wrapper */
        .opini-page {
            background: #f4f6fb;
            min-height: 100vh;
            padding: 2.5rem 1.5rem;
        }

        /* Section card */
        .section-card {
            border: 1px solid var(--border-muted);
            border-radius: var(--card-radius);
            background: #fff;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .05);
            overflow: hidden;
        }

        /* Coloured top bar */
        .section-card .card-topbar {
            height: 4px;
            width: 100%;
        }

        /* File input override */
        .form-control-file-custom {
            cursor: pointer;
        }

        .form-control-file-custom::file-selector-button {
            background: var(--accent-soft);
            color: var(--accent);
            border: none;
            padding: .4rem .9rem;
            border-radius: .4rem;
            font-size: .82rem;
            font-weight: 500;
            margin-right: .75rem;
            transition: background .2s;
        }

        .form-control-file-custom::file-selector-button:hover {
            background: #cfe2ff;
        }

        /* Preview table */
        .preview-table thead th {
            background: #f8f9ff;
            font-size: .78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--text-muted2);
            border-bottom: 2px solid var(--border-muted);
            padding: .75rem 1rem;
            white-space: nowrap;
        }

        .preview-table tbody td {
            font-size: .875rem;
            padding: .7rem 1rem;
            color: #3d4451;
            vertical-align: middle;
        }

        .preview-table tbody tr:hover td {
            background: #f8f9ff;
        }

        /* Empty state */
        .empty-state-icon {
            width: 52px;
            height: 52px;
            background: var(--accent-soft);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        /* Loading popup */
        .popup-overlay {
            background: rgba(10, 14, 26, .55);
            backdrop-filter: blur(3px);
        }

        .popup-box {
            border-radius: 1.25rem;
            border: 1px solid var(--border-muted);
            box-shadow: 0 20px 60px rgba(0, 0, 0, .18);
        }

        .spinner-ring {
            width: 44px;
            height: 44px;
            border: 3px solid #e9ecef;
            border-top-color: var(--accent);
            border-radius: 50%;
            animation: spin .8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Section label badge */
        .section-label {
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: .25rem .65rem;
            border-radius: 2rem;
        }

        <style>@media (max-width: 640px) {
            .tbl-opini-col {
                display: none !important;
            }

            .tbl-expand-row {
                display: table-row !important;
            }
        }

        @media (min-width: 641px) {
            .tbl-expand-row {
                display: none !important;
            }
        }

        .tbl-expand-inner {
            background: var(--bs-light, #f8f9fa);
            padding: 8px 12px 12px;
            border-top: 1px solid var(--border-muted, #dee2e6);
        }

        .tbl-expand-label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #6c757d;
            margin-top: 8px;
            margin-bottom: 3px;
        }
    </style>

    <div class="opini-page">
        <div class="container-xl px-0">

            {{-- ── PAGE HEADER ── --}}
            <div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
                <div>
                    <h1 class="fw-semibold text-dark mb-1" style="font-size:1.6rem;letter-spacing:-.02em">
                        Data Opini
                    </h1>
                    <p class="text-muted mb-0" style="font-size:.9rem">
                        Upload dan kelola opini pelanggan
                    </p>
                </div>

            </div>

            {{-- ══════════════════════════════════
             🔵  SECTION: UPLOAD
        ══════════════════════════════════ --}}
            <div class="section-card mb-4">
                <div class="card-topbar bg-primary"></div>

                <div class="p-4">

                    {{-- Header row --}}
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <span class="section-label bg-primary bg-opacity-10 text-primary">
                            Upload
                        </span>
                        <h2 class="mb-0 fw-semibold" style="font-size:1rem;color:#1e2433">
                            Data Opini
                        </h2>
                    </div>

                    <form action="{{ route('opini.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3 align-items-end">

                            {{-- Nama Dataset --}}
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-medium" style="font-size:.85rem">
                                    Nama Dataset
                                </label>
                                <input type="text" name="dataset_name" class="form-control"
                                    value="{{ old('dataset_name', session('dataset_name')) }}"
                                    placeholder="Contoh: P1 Opini" style="border-radius:.6rem;font-size:.875rem">
                            </div>

                            {{-- File --}}
                            <div class="col-12 col-md-5">
                                <label class="form-label fw-medium" style="font-size:.85rem">
                                    File Excel (.xlsx)
                                </label>
                                <input type="file" name="file" class="form-control form-control-file-custom"
                                    accept=".xlsx,.xls,.csv" style="border-radius:.6rem;font-size:.875rem">
                            </div>

                            {{-- Actions --}}
                            <div class="col-12 col-md-3">
                                <div class="d-flex gap-2">

                                    <button type="submit"
                                        class="btn btn-primary flex-fill d-flex align-items-center justify-content-center gap-1"
                                        style="border-radius:.6rem;font-size:.875rem;font-weight:500">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            fill="currentColor" viewBox="0 0 16 16">
                                            <path
                                                d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
                                            <path
                                                d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z" />
                                        </svg>
                                        Upload
                                    </button>

                                    <a href="{{ asset('template/Template Stok.xlsx') }}"
                                        class="btn btn-outline-secondary flex-fill d-flex align-items-center justify-content-center gap-1"
                                        style="border-radius:.6rem;font-size:.875rem">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            fill="currentColor" viewBox="0 0 16 16">
                                            <path
                                                d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
                                            <path
                                                d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z" />
                                        </svg>
                                        Template
                                    </a>

                                </div>
                            </div>

                        </div>{{-- /row --}}

                        {{-- Validation errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger d-flex align-items-start gap-2 mt-3 mb-0 py-2"
                                style="border-radius:.6rem;font-size:.85rem">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="flex-shrink-0 mt-1" viewBox="0 0 16 16">
                                    <path
                                        d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
                                </svg>
                                <ul class="mb-0 ps-2">
                                    @foreach ($errors->all() as $e)
                                        <li>{{ $e }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                    </form>
                </div>
            </div>

            {{-- ══════════════════════════════════
     📦 SECTION: DATA TERSIMPAN OPINI
══════════════════════════════════ --}}
            <div class="section-card mb-4">
                <div class="card-topbar bg-success"></div>
                <div class="p-4">

                    <div class="d-flex align-items-center gap-2 mb-4">
                        <span class="section-label bg-success bg-opacity-10 text-success">Select</span>
                        <h2 class="mb-0 fw-semibold" style="font-size:1rem;color:#1e2433">Data Opini</h2>
                    </div>

                    {{-- Filter --}}
                    <div class="row g-3 align-items-end mb-4">
                        <div class="col-12 col-md-5">
                            <label class="form-label fw-medium" style="font-size:.85rem">Dataset</label>
                            <select id="filterOpini" class="form-select" style="border-radius:.6rem;font-size:.875rem">
                                <option value="">-- Pilih Dataset --</option>
                                @foreach ($datasetOpini as $d)
                                    <option value="{{ $d }}">{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-2">
                            <button id="btnLoadOpini"
                                class="btn btn-success w-100 d-flex align-items-center justify-content-center gap-1"
                                style="border-radius:.6rem;font-size:.875rem;font-weight:500" disabled>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                    fill="currentColor" viewBox="0 0 16 16">
                                    <path
                                        d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398l3.85 3.85a1 1 0 0 0 1.415-1.415zm-5.242 1.156a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11" />
                                </svg>
                                Tampilkan
                            </button>
                        </div>
                    </div>

                    {{-- Tabel --}}
                    <div id="opiniWrapper" style="display:none">
                        <div class="table-responsive"
                            style="border-radius:.6rem;border:1px solid var(--border-muted);overflow:hidden">
                            <table class="table table-hover mb-0 preview-table">
                                <thead>
                                    <tr>
                                        <th style="width:36px">#</th>
                                        <th>Motor</th>
                                        <th>Nama</th>
                                        <th style="width:100px">Tanggal</th>
                                        <th>Isi Opini</th>
                                        <th class="text-center" style="width:90px">Sentimen</th>
                                        <th class="text-center" style="width:70px">Score</th>
                                    </tr>
                                </thead>
                                <tbody id="opiniBody"></tbody>
                            </table>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-2">
                            <p class="text-muted mb-0" style="font-size:.8rem" id="opiniInfo"></p>
                            <button id="btnResetOpini"
                                class="btn btn-outline-secondary d-flex align-items-center gap-1"
                                style="border-radius:.6rem;font-size:.875rem">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                    fill="currentColor" viewBox="0 0 16 16">
                                    <path
                                        d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z" />
                                    <path fill-rule="evenodd"
                                        d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3M3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9z" />
                                </svg>
                                Reset
                            </button>
                        </div>
                    </div>

                    {{-- Empty state --}}
                    <div id="opiniEmpty" class="text-center py-4 text-muted" style="font-size:.875rem">
                        Pilih dataset untuk menampilkan data opini.
                    </div>

                </div>
            </div>



            {{-- ══════════════════════════════════
     🟡  SECTION: PREVIEW
══════════════════════════════════ --}}
            @if (session('preview_opini'))

                <div class="section-card">
                    <div class="card-topbar" style="background:#f59e0b"></div>

                    <div class="p-4">

                        {{-- Header --}}
                        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="section-label" style="background:#fff8e6;color:#d97706">
                                    Preview
                                </span>
                                <h2 class="mb-0 fw-semibold" style="font-size:1rem;color:#1e2433">
                                    Preview Opini
                                </h2>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-warning bg-opacity-10 text-warning fw-normal"
                                    style="font-size:.78rem;border-radius:2rem;padding:.3rem .75rem">
                                    {{ count(session('preview_opini')) }} baris
                                </span>

                                {{-- STATUS --}}
                                @if (session('saved_opini'))
                                    <span class="badge bg-success bg-opacity-10 text-success">
                                        Sudah disimpan
                                    </span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning">
                                        Belum disimpan
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- TABLE --}}
                        <div class="table-responsive"
                            style="border-radius:.6rem;border:1px solid var(--border-muted);overflow:hidden;overflow-x:auto;-webkit-overflow-scrolling:touch">
                            <table class="table table-hover mb-0 preview-table" style="min-width:360px">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Nama</th>
                                        <th>Motor</th>
                                        {{-- Kolom ini disembunyikan di mobile --}}
                                        <th class="tbl-opini-col">Opini</th>
                                        <th class="tbl-opini-col">Sentimen (AI)</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach (session('preview_opini') as $i => $item)
                                        {{-- Baris utama --}}
                                        <tr>
                                            <td class="text-muted">{{ $i + 1 }}</td>
                                            <td>{{ $item['tanggal'] }}</td>
                                            <td>{{ $item['nama'] }}</td>
                                            <td>
                                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                                    {{ $item['motor'] }}
                                                </span>
                                            </td>

                                            {{-- Desktop only --}}
                                            <td class="tbl-opini-col"
                                                style="max-width:350px;white-space:normal;word-break:break-word">
                                                {{ $item['opini'] }}
                                            </td>
                                            <td class="tbl-opini-col">
                                                @if (!empty($item['ai']))
                                                    @php
                                                        $sent = strtolower(trim($item['ai']));
                                                        $class = match ($sent) {
                                                            'positif' => 'bg-success bg-opacity-10 text-success',
                                                            'negatif' => 'bg-danger bg-opacity-10 text-danger',
                                                            default => 'bg-secondary bg-opacity-10 text-secondary',
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $class }}"
                                                        style="font-size:.75rem;border-radius:2rem">
                                                        {{ ucfirst($sent) }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary"
                                                        style="border:1px dashed #adb5bd;font-size:.75rem;border-radius:2rem">
                                                        ⏳ Menunggu AI
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>

                                        {{-- Baris expand — mobile only, opini & sentimen tampil di sini --}}
                                        <tr class="tbl-expand-row">
                                            <td colspan="4" style="padding:0">
                                                <div class="tbl-expand-inner">
                                                    <span class="tbl-expand-label">Opini</span>
                                                    <div
                                                        style="font-size:.875rem;line-height:1.5;word-break:break-word">
                                                        {{ $item['opini'] }}
                                                    </div>

                                                    <span class="tbl-expand-label">Sentimen AI</span>
                                                    @if (!empty($item['ai']))
                                                        @php
                                                            $sent = strtolower(trim($item['ai']));
                                                            $class = match ($sent) {
                                                                'positif' => 'bg-success bg-opacity-10 text-success',
                                                                'negatif' => 'bg-danger bg-opacity-10 text-danger',
                                                                default => 'bg-secondary bg-opacity-10 text-secondary',
                                                            };
                                                        @endphp
                                                        <span class="badge {{ $class }}"
                                                            style="font-size:.75rem;border-radius:2rem">
                                                            {{ ucfirst($sent) }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary bg-opacity-10 text-secondary"
                                                            style="border:1px dashed #adb5bd;font-size:.75rem;border-radius:2rem">
                                                            ⏳ Menunggu AI
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- FOOTER --}}
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3"
                            style="border-top:1px solid var(--border-muted)">

                            <p class="text-muted mb-0" style="font-size:.82rem">
                                Data akan dianalisis saat disimpan.
                            </p>

                            <div class="d-flex gap-2">

                                {{-- RESET --}}
                                <form method="POST" action="{{ route('opini.clear') }}">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-outline-danger d-flex align-items-center gap-1"
                                        style="border-radius:.6rem;font-size:.875rem">
                                        Reset
                                    </button>
                                </form>

                                {{-- 🔥 SWITCH BUTTON --}}
                                @if (session('saved_opini'))
                                    {{-- TUTUP PREVIEW --}}
                                    <form method="POST" action="{{ route('opini.clear') }}">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-secondary d-flex align-items-center gap-1"
                                            style="border-radius:.6rem;font-size:.875rem">

                                            Tutup Preview
                                        </button>
                                    </form>
                                @else
                                    {{-- SIMPAN --}}
                                    <form id="formSimpan" method="POST" action="{{ route('opini.simpan') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-success d-flex align-items-center gap-1"
                                            style="border-radius:.6rem;font-size:.875rem;font-weight:500">

                                            Simpan & Analisis
                                        </button>
                                    </form>
                                @endif

                            </div>
                        </div>

                    </div>
                </div>
            @else
                <div class="section-card">
                    <div class="card-topbar bg-success"></div>
                    <div class="p-5 text-center">
                        <div class="empty-state-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                fill="var(--accent)" viewBox="0 0 16 16">
                                <path
                                    d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1L14 5.5zM8.5 6.5a.5.5 0 0 0-1 0V9H5a.5.5 0 0 0 0 1h2.5v2.5a.5.5 0 0 0 1 0V10H11a.5.5 0 0 0 0-1H8.5z" />
                            </svg>
                        </div>
                        <p class="fw-medium text-dark mb-1" style="font-size:.95rem">Belum Ada Preview</p>
                        <p class="text-muted mb-0" style="font-size:.84rem">
                            Upload file Excel untuk melihat preview data opini di sini.
                        </p>
                    </div>
                </div>

            @endif

        </div>{{-- /container --}}
    </div>


    {{-- ══════════════════════════════════════════════════
     🔄  LOADING POPUP
══════════════════════════════════════════════════ --}}
    <div id="loadingPopup"
        class="popup-overlay position-fixed top-0 start-0 w-100 h-100 d-none d-flex align-items-center justify-content-center"
        style="z-index:1060">

        <div class="popup-box bg-white p-5 text-center" style="width:340px">
            <div class="spinner-ring mx-auto mb-4"></div>
            <p class="fw-semibold text-dark mb-1" style="font-size:.95rem">Menganalisis Data…</p>
            <p class="text-muted mb-0" style="font-size:.83rem;line-height:1.6">
                Sedang menganalisis opini menggunakan AI.<br>Mohon tunggu beberapa saat.
            </p>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════
     ✅ / ❌  RESULT POPUP
══════════════════════════════════════════════════ --}}
    <div id="resultPopup"
        class="popup-overlay position-fixed top-0 start-0 w-100 h-100 d-none d-flex align-items-center justify-content-center"
        style="z-index:1060">

        <div class="popup-box bg-white p-5 text-center" style="width:380px">

            <div id="popupIconWrap" class="empty-state-icon mx-auto mb-3"></div>
            <h2 id="popupTitle" class="fw-semibold text-dark mb-2" style="font-size:1.05rem"></h2>
            <p id="popupMessage" class="text-muted mb-4" style="font-size:.875rem;line-height:1.65"></p>

            <button onclick="closePopup()" class="btn btn-primary px-4"
                style="border-radius:.6rem;font-size:.875rem;font-weight:500">
                Tutup
            </button>

        </div>

    </div>


    {{-- ══════════════════════════════════════════════════
     SCRIPTS
══════════════════════════════════════════════════ --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            /* ── Loading on save ── */
            const formSimpan = document.getElementById('formSimpan');
            if (formSimpan) {
                formSimpan.addEventListener('submit', function() {
                    const el = document.getElementById('loadingPopup');
                    if (el) el.classList.remove('d-none');
                });
            }

            /* ── Popup helpers ── */
            function showPopup(title, message, type) {
                const iconMap = {
                    success: `<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="#16a34a" viewBox="0 0 16 16">
                <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/>
              </svg>`,
                    error: `<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="#dc2626" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.553.553 0 0 1-1.1 0z"/>
              </svg>`,
                };
                const bgMap = {
                    success: 'var(--success-soft)',
                    error: 'var(--danger-soft)'
                };
                document.getElementById('popupIconWrap').innerHTML = iconMap[type] || iconMap.error;
                document.getElementById('popupIconWrap').style.background = bgMap[type] || bgMap.error;
                document.getElementById('popupTitle').innerText = title;
                document.getElementById('popupMessage').innerText = message;
                document.getElementById('resultPopup').classList.remove('d-none');
            }

            window.closePopup = function() {
                document.getElementById('resultPopup').classList.add('d-none');
            };

            @if (session('success') && session('saved_opini'))
                showPopup("Proses Berhasil", "Data opini berhasil dianalisis dan disimpan dengan sukses.",
                    "success");
            @endif

            @if (session('error'))
                const errorCode = "{{ session('error') }}";
                const messages = {
                    "503": "Layanan AI sedang mengalami lonjakan permintaan. Silakan coba beberapa saat lagi.",
                    "429": "Batas penggunaan API telah tercapai. Silakan tunggu sebelum mencoba kembali.",
                    "401": "Autentikasi API gagal. Periksa konfigurasi API Key Anda.",
                    "403": "Akses ditolak oleh layanan AI.",
                    "404": "Model AI tidak ditemukan.",
                    "500": "Terjadi kesalahan pada server AI.",
                };
                const msg = Object.entries(messages).find(([k]) => errorCode.includes(k));
                showPopup("Terjadi Kesalahan", msg ? msg[1] : errorCode, "error");
            @endif

            /* ── Data Tersimpan ── */
            const sel = document.getElementById('filterOpini');
            const btnLoad = document.getElementById('btnLoadOpini');
            const btnReset = document.getElementById('btnResetOpini');
            const wrapper = document.getElementById('opiniWrapper');
            const empty = document.getElementById('opiniEmpty');
            const tbody = document.getElementById('opiniBody');
            const info = document.getElementById('opiniInfo');

            // Format tanggal ISO → dd/mm/yyyy
            const fmtDate = (raw) => {
                if (!raw) return '-';
                const d = new Date(raw);
                if (isNaN(d)) return raw;
                return d.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            };

            const sentimentBadge = (s) => {
                const map = {
                    positif: ['#ecfdf5', '#059669'],
                    negatif: ['#fef2f2', '#dc2626'],
                    netral: ['#f8fafc', '#64748b'],
                };
                const key = s?.toLowerCase();
                const [bg, color] = map[key] ?? ['#f8fafc', '#64748b'];
                return `<span class="pct-badge" style="background:${bg};color:${color};font-size:.75rem">${s ?? '-'}</span>`;
            };

            const scoreColor = (score) => {
                if (score > 0) return '#059669';
                if (score < 0) return '#dc2626';
                return '#64748b';
            };

            sel.addEventListener('change', function() {
                btnLoad.disabled = !this.value;
                wrapper.style.display = 'none';
                tbody.innerHTML = '';
                info.textContent = '';
                empty.textContent = 'Pilih dataset untuk menampilkan data opini.';
            });

            btnLoad.addEventListener('click', function() {
                const dataset = sel.value;
                if (!dataset) return;

                empty.textContent = 'Memuat data...';
                wrapper.style.display = 'none';

                fetch(`{{ route('opini.loadData') }}?dataset_name=${encodeURIComponent(dataset)}`)
                    .then(r => r.json())
                    .then(data => {
                        if (!data.length) {
                            empty.textContent = 'Data kosong.';
                            return;
                        }

                        tbody.innerHTML = data.map((row, i) => `
    <tr style="cursor:pointer" onclick="toggleOpini(${i})">
        <td class="text-muted" style="font-size:.82rem">${i + 1}</td>
        <td><span class="badge bg-primary bg-opacity-10 text-primary" style="font-size:.75rem;border-radius:2rem">${row.motor?.nama ?? '-'}</span></td>
        <td style="font-size:.875rem">${row.nama ?? '-'}</td>
        <td style="font-size:.82rem;white-space:nowrap;color:#64748b">${fmtDate(row.tanggal)}</td>
        <td style="max-width:260px">
    <div style="font-size:.82rem;color:#475569;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;white-space:normal">
        ${row.isi ?? '-'}
    </div>
</td>
        <td class="text-center">${sentimentBadge(row.sentiment)}</td>
        <td class="text-center fw-semibold" style="color:${scoreColor(row.score)}">${row.score ?? '-'}</td>
    </tr>
    <tr id="expand-${i}" style="display:none;background:#f8fafc">
        <td colspan="7" style="padding:12px 16px;font-size:.85rem;color:#334155;line-height:1.7;border-top:none">
            <span style="font-size:.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:4px">Isi Opini Lengkap</span>
            ${row.isi ?? '-'}
        </td>
    </tr>
`).join('');

                        // tambah ini juga di dalam script
                        window.toggleOpini = function(i) {
                            const row = document.getElementById(`expand-${i}`);
                            row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
                        };

                        info.textContent = `${data.length} opini ditemukan`;
                        wrapper.style.display = 'block';
                        empty.textContent = '';
                    })
                    .catch(() => {
                        empty.textContent = 'Gagal memuat data. Coba lagi.';
                    });
            });

            btnReset.addEventListener('click', function() {
                sel.value = '';
                btnLoad.disabled = true;
                wrapper.style.display = 'none';
                tbody.innerHTML = '';
                info.textContent = '';
                empty.textContent = 'Pilih dataset untuk menampilkan data opini.';
            });

        });
    </script>

</x-app-layout>
