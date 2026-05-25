<x-app-layout>

    {{-- ============================================================
     PENJUALAN — DATA PENJUALAN  |  Bootstrap 5  |  Refactored UI
     ============================================================ --}}

    <style>
        :root {
            --accent: #0d6efd;
            --accent-soft: #e8f0fe;
            --success-soft: #e6f9f0;
            --warning-soft: #fff8e6;
            --danger-soft: #fff0f0;
            --purple: #7c3aed;
            --purple-soft: #f3eeff;
            --card-radius: 1rem;
            --border-muted: #e9ecef;
            --text-muted2: #8c97a8;
        }

        .penjualan-page {
            background: #f4f6fb;
            min-height: 100vh;
            padding: 2.5rem 1.5rem;
        }

        .section-card {
            border: 1px solid var(--border-muted);
            border-radius: var(--card-radius);
            background: #fff;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .05);
            overflow: hidden;
        }

        .section-card .card-topbar {
            height: 4px;
            width: 100%;
        }

        .section-label {
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: .25rem .65rem;
            border-radius: 2rem;
        }

        /* File input */
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

        /* Percent badge */
        .pct-badge {
            display: inline-block;
            padding: .22rem .7rem;
            border-radius: 2rem;
            font-size: .78rem;
            font-weight: 600;
        }

        /* Progress bar inside grouped table */
        .progress-thin {
            height: 5px;
            border-radius: 99px;
            background: #e9ecef;
            margin-top: 4px;
        }

        .progress-thin .progress-fill {
            height: 100%;
            border-radius: 99px;
            background: var(--accent);
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

        /* Purple button */
        .btn-purple {
            background: var(--purple);
            color: #fff;
            border: none;
        }

        .btn-purple:hover {
            background: #6d28d9;
            color: #fff;
        }

        .btn-outline-purple {
            border: 1px solid var(--purple);
            color: var(--purple);
            background: transparent;
        }

        .btn-outline-purple:hover {
            background: var(--purple-soft);
        }
    </style>

    <div class="penjualan-page">
        <div class="container-xl px-0">

            {{-- ── PAGE HEADER ── --}}
            <div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="fw-semibold text-dark mb-1" style="font-size:1.6rem;letter-spacing:-.02em">
            Data Penjualan
        </h1>
        <p class="text-muted mb-0" style="font-size:.9rem">
            {{ auth()->user()->role !== 'user' ? 'Upload dan analisis data penjualan motor' : 'Lihat dan pantau riwayat penjualan motor' }}
        </p>
    </div>
</div>

{{-- ══════════════════════════════════
     🔵  SECTION: UPLOAD (Hanya Admin)
     ══════════════════════════════════ --}}
@if(auth()->user()->role !== 'user')
    <div class="section-card mb-4">
        <div class="card-topbar bg-primary"></div>
        <div class="p-4">

            <div class="d-flex align-items-center gap-2 mb-4">
                <span class="section-label bg-primary bg-opacity-10 text-primary">Upload</span>
                <h2 class="mb-0 fw-semibold" style="font-size:1rem;color:#1e2433">Data Penjualan</h2>
            </div>

            <form action="{{ route('penjualan.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-medium" style="font-size:.85rem">Nama Dataset</label>
                        <input type="text" name="dataset_name" class="form-control"
                            value="{{ old('dataset_name', session('dataset_name')) }}"
                            placeholder="Contoh: P1 Penjualan" style="border-radius:.6rem;font-size:.875rem">
                    </div>

                    <div class="col-12 col-md-5">
                        <label class="form-label fw-medium" style="font-size:.85rem">File Excel (.xlsx)</label>
                        <input type="file" name="file" class="form-control form-control-file-custom"
                            accept=".xlsx,.xls,.csv" style="border-radius:.6rem;font-size:.875rem">
                    </div>

                    <div class="col-12 col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit"
                                class="btn btn-primary flex-fill d-flex align-items-center justify-content-center gap-1"
                                style="border-radius:.6rem;font-size:.875rem;font-weight:500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                    fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
                                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z" />
                                </svg>
                                Upload
                            </button>

                            <a href="{{ asset('template/Template Penjualan.xlsx') }}"
                                class="btn btn-outline-secondary flex-fill d-flex align-items-center justify-content-center gap-1"
                                style="border-radius:.6rem;font-size:.875rem" title="Download Template">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                    fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
                                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z" />
                                </svg>
                                Template
                            </a>
                        </div>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger d-flex align-items-start gap-2 mt-3 mb-0 py-2"
                        style="border-radius:.6rem;font-size:.85rem">
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
@endif

            {{-- ══════════════════════════════════
     📦 SECTION: DATA TERSIMPAN
══════════════════════════════════ --}}
            <div class="section-card mb-4">
                <div class="card-topbar bg-success"></div>
                <div class="p-4">

                    <div class="d-flex align-items-center gap-2 mb-4">
                        <span class="section-label bg-success bg-opacity-10 text-success">Select</span>
                        <h2 class="mb-0 fw-semibold" style="font-size:1rem;color:#1e2433">Data Penjualan</h2>
                    </div>

                    {{-- Filter --}}
                    <div class="row g-3 align-items-end mb-4">
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-medium" style="font-size:.85rem">Tipe Data</label>
                            <select id="filterTipe" class="form-select" style="border-radius:.6rem;font-size:.875rem">
                                <option value="">Pilih Tipe</option>
                                <option value="penjualan">Penjualan</option>
                                <option value="analisis">Penjualan Analisis</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-medium" style="font-size:.85rem">Dataset</label>
                            <select id="filterDataset" class="form-select" style="border-radius:.6rem;font-size:.875rem"
                                disabled>
                                <option value="">Pilih Dataset</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-2">
                            <button id="btnLoad"
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
                    <div id="tabelWrapper" style="display:none">
                        <div class="table-responsive"
                            style="border-radius:.6rem;border:1px solid var(--border-muted);overflow:hidden">
                            <table class="table table-hover mb-0 preview-table">
                                <thead id="tabelHead"></thead>
                                <tbody id="tabelBody"></tbody>
                            </table>
                        </div>
                        <p class="text-muted mt-2 mb-0" style="font-size:.8rem" id="tabelInfo"></p>
                        <div class="d-flex justify-content-end mt-3">
                            <button id="btnReset" class="btn btn-outline-secondary d-flex align-items-center gap-1"
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

                    {{-- Empty / Loading state --}}
                    <div id="tabelEmpty" class="text-center py-4 text-muted" style="font-size:.875rem">
                        Pilih tipe dan dataset untuk menampilkan data.
                    </div>

                </div>
            </div>



            {{-- ══════════════════════════════════
             🟡  SECTION: PREVIEW PENJUALAN
        ══════════════════════════════════ --}}
            @if (session('preview_penjualan'))

                <div class="section-card mb-4">
                    <div class="card-topbar" style="background:#f59e0b"></div>
                    <div class="p-4">

                        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="section-label" style="background:#fff8e6;color:#d97706">Preview</span>
                                <h2 class="mb-0 fw-semibold" style="font-size:1rem;color:#1e2433">Data Penjualan</h2>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge bg-warning bg-opacity-10 text-warning fw-normal"
                                    style="font-size:.78rem;border-radius:2rem;padding:.3rem .75rem">
                                    Belum disimpan
                                </span>
                                @if (session('dataset_name'))
                                    <span class="badge bg-dark bg-opacity-10 text-dark fw-semibold"
                                        style="font-size:.78rem;border-radius:2rem;padding:.3rem .75rem">
                                        {{ session('dataset_name') }}
                                    </span>
                                @endif
                                <span class="badge bg-primary bg-opacity-10 text-primary fw-normal"
                                    style="font-size:.78rem;border-radius:2rem;padding:.3rem .75rem">
                                    {{ count(session('preview_penjualan')) }} baris
                                </span>
                            </div>
                        </div>

                        <div class="table-responsive"
                            style="border-radius:.6rem;border:1px solid var(--border-muted);overflow:hidden;overflow-x:auto;-webkit-overflow-scrolling:touch">
                            <table class="table table-hover mb-0 preview-table" style="min-width:520px">
                                <thead>
                                    <tr>
                                        <th style="width:36px">#</th>
                                        <th>Tanggal</th>
                                        <th>Motor</th>
                                        <th class="text-center">Jumlah</th>
                                        <th class="text-center">Persentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (session('preview_penjualan') as $idx => $item)
                                        @php
                                            $pct = $item['percent'] ?? 0;
                                            $r = round(34 + (239 - 34) * (1 - $pct / 100));
                                            $g = round(197 + (68 - 197) * (1 - $pct / 100));
                                            $b = round(94 + (68 - 94) * (1 - $pct / 100));
                                            $bgColor = "rgba({$r},{$g},{$b},0.12)";
                                            $txtColor = "rgb({$r},{$g},{$b})";
                                        @endphp
                                        <tr>
                                            <td class="text-muted">{{ $idx + 1 }}</td>
                                            <td style="white-space:nowrap">{{ $item['tanggal'] ?? '-' }}</td>
                                            <td>
                                                <span class="fw-medium">{{ $item['motor'] }}</span>
                                            </td>
                                            <td class="text-center fw-semibold">{{ $item['jumlah'] }}</td>
                                            <td class="text-center">
                                                <span class="pct-badge"
                                                    style="background:{{ $bgColor }};color:{{ $txtColor }}">
                                                    {{ $pct }}%
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

                {{-- ══════════════════════════════════
                 🟣  SECTION: HASIL GROUPING
            ══════════════════════════════════ --}}
                @if (session('preview_grouped'))
                    <div class="section-card mb-4">
                        <div class="card-topbar" style="background:var(--purple)"></div>
                        <div class="p-4">

                            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="section-label"
                                        style="background:var(--purple-soft);color:var(--purple)">Grouping</span>
                                    <h2 class="mb-0 fw-semibold" style="font-size:1rem;color:#1e2433">
                                        Hasil Grouping
                                    </h2>
                                </div>
                                <span class="badge fw-normal"
                                    style="background:var(--purple-soft);color:var(--purple);font-size:.78rem;border-radius:2rem;padding:.3rem .75rem">
                                    {{ count(session('preview_grouped')) }} model
                                </span>
                            </div>

                            <div class="table-responsive"
                                style="border-radius:.6rem;border:1px solid var(--border-muted);overflow:hidden;overflow-x:auto;-webkit-overflow-scrolling:touch">
                                <table class="table table-hover mb-0 preview-table" style="min-width:520px">
                                    <thead>
                                        <tr>
                                            <th style="width:36px">#</th>
                                            <th>Motor</th>
                                            <th class="text-center" style="width:110px">Jumlah</th>
                                            <th style="width:200px">Porsi</th>
                                            <th class="text-center" style="width:90px">%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach (session('preview_grouped') as $idx => $item)
                                            @php
                                                $pct = $item['percent'] ?? 0;
                                                $r = round(34 + (239 - 34) * (1 - $pct / 100));
                                                $g = round(197 + (68 - 197) * (1 - $pct / 100));
                                                $b = round(94 + (68 - 94) * (1 - $pct / 100));
                                                $bgColor = "rgba({$r},{$g},{$b},0.12)";
                                                $txtColor = "rgb({$r},{$g},{$b})";
                                            @endphp
                                            <tr>
                                                <td class="text-muted">{{ $idx + 1 }}</td>
                                                <td class="fw-medium">{{ $item['motor'] }}</td>
                                                <td class="text-center fw-semibold">{{ $item['jumlah'] }}</td>
                                                <td>
                                                    <div class="progress-thin">
                                                        <div class="progress-fill"
                                                            style="width:{{ $pct }}%;background:{{ $txtColor }}">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="pct-badge"
                                                        style="background:{{ $bgColor }};color:{{ $txtColor }}">
                                                        {{ $pct }}%
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Simpan Analisis --}}
                            <div class="d-flex justify-content-end mt-4 pt-3"
                                style="border-top:1px solid var(--border-muted)">

                                <form method="POST" action="{{ route('penjualan.simpanAnalisis') }}">
                                    @csrf

                                    <button
                                        class="btn btn-purple d-flex align-items-center justify-content-center gap-1"
                                        style="border-radius:.6rem;font-size:.875rem;font-weight:500;padding:.45rem 1.4rem">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            fill="currentColor" viewBox="0 0 16 16">
                                            <path
                                                d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z" />
                                        </svg>
                                        Simpan Analisis
                                    </button>

                                </form>
                            </div>

                        </div>
                    </div>
                @endif

                {{-- ── ACTION BUTTONS ── --}}
                <div class="section-card">
                    <div class="p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">

                        <p class="text-muted mb-0" style="font-size:.82rem">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                fill="currentColor" class="me-1 mb-1 text-warning" viewBox="0 0 16 16">
                                <path
                                    d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                            </svg>
                            Pilih aksi untuk data preview ini.
                        </p>

                        <div class="d-flex gap-2 flex-wrap align-items-center">

                            <form method="POST" action="{{ route('penjualan.clear') }}">
                                @csrf
                                <button type="submit"
                                    class="btn btn-outline-danger d-flex align-items-center justify-content-center gap-1"
                                    style="border-radius:.6rem;font-size:.875rem">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                        fill="currentColor" viewBox="0 0 16 16">
                                        <path
                                            d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                        <path
                                            d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                    </svg>
                                    Hapus Preview
                                </button>
                            </form>

                            <div style="width:1px;height:28px;background:var(--border-muted)"></div>

                            <form method="POST" action="{{ route('penjualan.group') }}">
                                @csrf
                                <button type="submit"
                                    class="btn btn-outline-purple d-flex align-items-center justify-content-center gap-1"
                                    style="border-radius:.6rem;font-size:.875rem;font-weight:500">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                        fill="currentColor" viewBox="0 0 16 16">
                                        <path
                                            d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5zm8 0A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5zm-8 8A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5zm8 0A1.5 1.5 0 0 1 10.5 9h3A1.5 1.5 0 0 1 15 10.5v3A1.5 1.5 0 0 1 13.5 15h-3A1.5 1.5 0 0 1 9 13.5z" />
                                    </svg>
                                    Grouping
                                </button>
                            </form>

                            <form method="POST" action="{{ route('penjualan.simpanRaw') }}">
                                @csrf
                                <button type="submit"
                                    class="btn btn-success d-flex align-items-center justify-content-center gap-1"
                                    style="border-radius:.6rem;font-size:.875rem;font-weight:500;padding:.45rem 1.25rem">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                        fill="currentColor" viewBox="0 0 16 16">
                                        <path
                                            d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z" />
                                    </svg>
                                    Simpan Data
                                </button>
                            </form>

                        </div>
                    </div>
                </div>
            @else
    {{-- 🔥 Tambahkan ID di sini --}}
    <div class="section-card mb-4" id="emptyStateCard">
        <div class="card-topbar bg-success"></div>
        <div class="p-5 text-center">
            <div class="empty-state-icon" style="background:#f1f5f9">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                    fill="var(--accent)" viewBox="0 0 16 16">
                    <path
                        d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1L14 5.5zM8.5 6.5a.5.5 0 0 0-1 0V9H5a.5.5 0 0 0 0 1h2.5v2.5a.5.5 0 0 0 1 0V10H11a.5.5 0 0 0 0-1H8.5z" />
                </svg>
            </div>
            <p class="fw-medium text-dark mb-1" style="font-size:.95rem">Belum Ada Data yang Ditampilkan</p>
            <p class="text-muted mb-0" style="font-size:.84rem">
                {{-- 🔒 Teks menyesuaikan Role --}}
                @if(auth()->user()->role !== 'user')
                    Upload file Excel untuk melihat preview, atau pilih dataset pada Data Tersimpan.
                @else
                    Silakan pilih tipe dan dataset pada kolom Data Tersimpan di atas untuk melihat data penjualan.
                @endif
            </p>
        </div>
    </div>
@endif

        </div>{{-- /container --}}
    </div>

    <script>
    (function() {
        const datasets = {
            penjualan: @json($datasetPenjualan),
            analisis: @json($datasetAnalisis),
        };

        const selTipe = document.getElementById('filterTipe');
        const selDataset = document.getElementById('filterDataset');
        const btnLoad = document.getElementById('btnLoad');
        const wrapper = document.getElementById('tabelWrapper');
        const empty = document.getElementById('tabelEmpty');
        const thead = document.getElementById('tabelHead');
        const tbody = document.getElementById('tabelBody');
        const info = document.getElementById('tabelInfo');
        
        // 🔥 TANGKAP ELEMEN EMPTY STATE UTAMA
        const mainEmptyStateCard = document.getElementById('emptyStateCard');

        selTipe.addEventListener('change', function() {
            const tipe = this.value;
            selDataset.innerHTML = '<option value="">-- Pilih Dataset --</option>';
            selDataset.disabled = true;
            btnLoad.disabled = true;
            wrapper.style.display = 'none';
            empty.textContent = 'Pilih tipe dan dataset untuk menampilkan data.';

            if (tipe && datasets[tipe]) {
                datasets[tipe].forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d;
                    opt.textContent = d;
                    selDataset.appendChild(opt);
                });
                selDataset.disabled = false;
            }
        });

        selDataset.addEventListener('change', function() {
            btnLoad.disabled = !this.value;
        });

        btnLoad.addEventListener('click', function() {
            const tipe = selTipe.value;
            const dataset = selDataset.value;
            if (!tipe || !dataset) return;

            empty.textContent = 'Memuat data...';
            wrapper.style.display = 'none';

            fetch(`{{ route('penjualan.loadData') }}?tipe=${tipe}&dataset_name=${encodeURIComponent(dataset)}`)
                .then(r => r.json())
                .then(data => {
                    if (!data.length) {
                        empty.textContent = 'Data kosong.';
                        return;
                    }

                    // Header
                    if (tipe === 'penjualan') {
                        thead.innerHTML = `<tr>
                            <th style="width:36px">#</th>
                            <th>Motor</th>
                            <th class="text-center">Tanggal</th>
                            <th class="text-center">Jumlah</th>
                        </tr>`;
                    } else {
                        thead.innerHTML = `<tr>
                            <th style="width:36px">#</th>
                            <th>Motor</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">Persentase</th>
                        </tr>`;
                    }

                    // Body
                    tbody.innerHTML = data.map((row, i) => {
                        if (tipe === 'penjualan') {
                            return `<tr>
                                <td class="text-muted">${i + 1}</td>
                                <td class="fw-medium">${row.motor?.nama ?? '-'}</td>
                                <td class="text-center">${row.tanggal ?? '-'}</td>
                                <td class="text-center fw-semibold">${row.jumlah}</td>
                            </tr>`;
                        } else {
                            const pct = parseFloat(row.percent ?? 0).toFixed(1);
                            return `<tr>
                                <td class="text-muted">${i + 1}</td>
                                <td class="fw-medium">${row.motor?.nama ?? '-'}</td>
                                <td class="text-center fw-semibold">${row.jumlah}</td>
                                <td class="text-center">
                                    <span class="pct-badge" style="background:rgba(99,102,241,.1);color:#6366f1;padding:.25rem .65rem;border-radius:2rem;font-size:.75rem;font-weight:600">
                                        ${pct}%
                                    </span>
                                </td>
                            </tr>`;
                        }
                    }).join('');

                    info.textContent = `${data.length} baris ditemukan`;
                    wrapper.style.display = 'block';
                    empty.textContent = '';
                    
                    // 🔥 MANTRA PENGHILANG EMPTY STATE UTAMA
                    if (mainEmptyStateCard) mainEmptyStateCard.style.display = 'none';
                })
                .catch(() => {
                    empty.textContent = 'Gagal memuat data. Coba lagi.';
                });
        });

        document.getElementById('btnReset').addEventListener('click', function() {
            selTipe.value = '';
            selDataset.innerHTML = '<option value="">-- Pilih Dataset --</option>';
            selDataset.disabled = true;
            btnLoad.disabled = true;
            wrapper.style.display = 'none';
            tbody.innerHTML = '';
            thead.innerHTML = '';
            info.textContent = '';
            empty.textContent = 'Pilih tipe dan dataset untuk menampilkan data.';
            
            // 🔥 MANTRA PEMANGGIL KEMBALI EMPTY STATE UTAMA
            if (mainEmptyStateCard) mainEmptyStateCard.style.display = 'block';
        });
    })();
</script>

</x-app-layout>
