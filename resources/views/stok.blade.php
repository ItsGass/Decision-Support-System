<x-app-layout>

    {{-- ============================================================
     STOK — MANAJEMEN STOK  |  Bootstrap 5  |  Refactored UI
     ============================================================ --}}

    <style>
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

        .stok-page {
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

        .section-card .card-topbar {
            height: 4px;
            width: 100%;
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

        /* Section label badge */
        .section-label {
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: .25rem .65rem;
            border-radius: 2rem;
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

        /* Empty state icon */
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

        /* Stock number badge */
        .stock-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 52px;
            padding: .28rem .7rem;
            border-radius: 2rem;
            font-size: .8rem;
            font-weight: 600;
            background: var(--accent-soft);
            color: var(--accent);
        }
    </style>

    <div class="stok-page">
        <div class="container-xl px-0">

            {{-- ── PAGE HEADER ── --}}
<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="fw-semibold text-dark mb-1" style="font-size:1.6rem;letter-spacing:-.02em">
            Manajemen Stok
        </h1>
        <p class="text-muted mb-0" style="font-size:.9rem">
            {{ auth()->user()->role !== 'user' ? 'Kelola stok motor untuk kebutuhan analisis' : 'Lihat status stok motor saat ini' }}
        </p>
    </div>
</div>

{{-- ══════════════════════════════════
     🔵  SECTION: UPLOAD (Hanya Muncul Jika Bukan 'user')
     ══════════════════════════════════ --}}
@if(auth()->user()->role !== 'user')
    <div class="section-card mb-4">
        <div class="card-topbar bg-primary"></div>

        <div class="p-4">
            <div class="d-flex align-items-center gap-2 mb-4">
                <span class="section-label bg-primary bg-opacity-10 text-primary">Upload</span>
                <h2 class="mb-0 fw-semibold" style="font-size:1rem;color:#1e2433">Data Sisa Stok</h2>
            </div>

            <form action="{{ route('stok.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3 align-items-end">

                    {{-- Nama Snapshot --}}
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-medium" style="font-size:.85rem">Nama Snapshot</label>
                        <input type="text" name="snapshot_name" class="form-control"
                            value="{{ old('snapshot_name', session('snapshot_name')) }}"
                            placeholder="Contoh: P1 Stok" style="border-radius:.6rem;font-size:.875rem">
                    </div>

                    {{-- File --}}
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

                            <a href="{{ asset('template/Template Stok.xlsx') }}"
                                class="btn btn-outline-secondary flex-fill d-flex align-items-center justify-content-center gap-1"
                                style="border-radius:.6rem;font-size:.875rem">
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

                {{-- Validation errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger d-flex align-items-start gap-2 mt-3 mb-0 py-2"
                        style="border-radius:.6rem;font-size:.85rem">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                            fill="currentColor" class="flex-shrink-0 mt-1" viewBox="0 0 16 16">
                            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
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
@endif

            {{-- ══════════════════════════════════
     📦 SECTION: DATA TERSIMPAN STOK
══════════════════════════════════ --}}
<div class="section-card mb-4">
    <div class="card-topbar bg-success"></div>
    <div class="p-4">

        <div class="d-flex align-items-center gap-2 mb-4">
            <span class="section-label bg-success bg-opacity-10 text-success">Select</span>
            <h2 class="mb-0 fw-semibold" style="font-size:1rem;color:#1e2433">Data Sisa Stok</h2>
        </div>

        {{-- Filter --}}
        <div class="row g-3 align-items-end mb-4">
            <div class="col-12 col-md-5">
                <label class="form-label fw-medium" style="font-size:.85rem">Snapshot</label>
                <select id="filterSnapshot" class="form-select" style="border-radius:.6rem;font-size:.875rem">
                    <option value="">Pilih Snapshot</option>
                    @foreach ($datasetStok as $snap)
                        <option value="{{ $snap }}">{{ $snap }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2">
                <button id="btnLoadStok" class="btn btn-success w-100 d-flex align-items-center justify-content-center gap-1"
                    style="border-radius:.6rem;font-size:.875rem;font-weight:500" disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398l3.85 3.85a1 1 0 0 0 1.415-1.415zm-5.242 1.156a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11"/>
                    </svg>
                    Tampilkan
                </button>
            </div>
        </div>

        {{-- Tabel --}}
        <div id="stokWrapper" style="display:none">
            <div class="table-responsive" style="border-radius:.6rem;border:1px solid var(--border-muted);overflow:hidden">
                <table class="table table-hover mb-0 preview-table">
                    <thead>
                        <tr>
                            <th style="width:36px">#</th>
                            <th>Motor</th>
                            <th class="text-center">Stok Sisa</th>
                        </tr>
                    </thead>
                    <tbody id="stokBody"></tbody>
                </table>
            </div>
            <div class="d-flex align-items-center justify-content-between mt-2">
                <p class="text-muted mb-0" style="font-size:.8rem" id="stokInfo"></p>
                <button id="btnResetStok" class="btn btn-outline-secondary d-flex align-items-center gap-1"
                    style="border-radius:.6rem;font-size:.875rem">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z"/>
                        <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3M3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9z"/>
                    </svg>
                    Reset
                </button>
            </div>
        </div>

        {{-- Empty state --}}
        <div id="stokEmpty" class="text-center py-4 text-muted" style="font-size:.875rem">
            Pilih snapshot untuk menampilkan data stok.
        </div>

    </div>
</div>


            {{-- ══════════════════════════════════
             🟡  SECTION: PREVIEW
        ══════════════════════════════════ --}}
            @if (session('preview_stok'))

                <div class="section-card mb-4">
                    <div class="card-topbar" style="background:#f59e0b"></div>

                    <div class="p-4">

                        {{-- Header --}}
                        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="section-label" style="background:#fff8e6;color:#d97706">Preview</span>
                                <h2 class="mb-0 fw-semibold" style="font-size:1rem;color:#1e2433">Preview Stok</h2>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-warning bg-opacity-10 text-warning fw-normal"
                                    style="font-size:.78rem;border-radius:2rem;padding:.3rem .75rem">
                                    Belum disimpan
                                </span>
                                <span class="badge bg-primary bg-opacity-10 text-primary fw-normal"
                                    style="font-size:.78rem;border-radius:2rem;padding:.3rem .75rem">
                                    {{ count(session('preview_stok')) }} item
                                </span>
                            </div>
                        </div>

                        {{-- Table --}}
                        <div class="table-responsive"
    style="border-radius:.6rem;border:1px solid var(--border-muted);overflow:hidden;overflow-x:auto;-webkit-overflow-scrolling:touch">
    <table class="table table-hover mb-0 preview-table" style="min-width:520px">
                                <thead>
                                    <tr>
                                        <th style="width:40px">#</th>
                                        <th>Motor</th>
                                        <th class="text-center" style="width:140px">Stok Tersedia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (session('preview_stok') as $i => $item)
                                        <tr>
                                            <td class="text-muted">{{ $i + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span
                                                        class="d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10"
                                                        style="width:28px;height:28px;flex-shrink:0">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="13"
                                                            height="13" fill="var(--accent)" viewBox="0 0 16 16">
                                                            <path
                                                                d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
                                                        </svg>
                                                    </span>
                                                    <span class="fw-medium"
                                                        style="font-size:.875rem">{{ $item['motor'] }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="stock-badge">{{ $item['jumlah'] }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Footer actions --}}
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3"
                            style="border-top:1px solid var(--border-muted)">

                            <p class="text-muted mb-0" style="font-size:.82rem">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                    fill="currentColor" class="me-1 mb-1 text-warning" viewBox="0 0 16 16">
                                    <path
                                        d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                                </svg>
                                Periksa data sebelum disimpan ke sistem.
                            </p>

                            <div class="d-flex gap-2">

                                <form method="POST" action="{{ route('stok.clear') }}">
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
                                        Reset
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('stok.simpan') }}">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-success d-flex align-items-center justify-content-center gap-1"
                                        style="border-radius:.6rem;font-size:.875rem;font-weight:500;padding:.45rem 1.25rem">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            fill="currentColor" viewBox="0 0 16 16">
                                            <path
                                                d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z" />
                                        </svg>
                                        Simpan Stok
                                    </button>
                                </form>

                            </div>
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
                    fill="#64748b" viewBox="0 0 16 16">
                    <path
                        d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
                </svg>
            </div>
            <p class="fw-medium text-dark mb-1" style="font-size:.95rem">Belum Ada Data yang Ditampilkan</p>
            <p class="text-muted mb-0" style="font-size:.84rem">
                {{-- 🔒 Teks menyesuaikan Role --}}
                @if(auth()->user()->role !== 'user')
                    Upload file Excel untuk melihat preview, atau pilih snapshot pada Data Tersimpan.
                @else
                    Silakan pilih snapshot pada kolom Data Tersimpan di atas untuk melihat data stok.
                @endif
            </p>
        </div>
    </div>
@endif

        </div>{{-- /container --}}
    </div>

    
<script>
(function () {
    const selSnapshot = document.getElementById('filterSnapshot');
    const btnLoad     = document.getElementById('btnLoadStok');
    const btnReset    = document.getElementById('btnResetStok');
    const wrapper     = document.getElementById('stokWrapper');
    const empty       = document.getElementById('stokEmpty');
    const tbody       = document.getElementById('stokBody');
    const info        = document.getElementById('stokInfo');
    
    // 🔥 TANGKAP ELEMEN EMPTY STATE UTAMA
    const mainEmptyStateCard = document.getElementById('emptyStateCard');

    selSnapshot.addEventListener('change', function () {
        btnLoad.disabled = !this.value;
        wrapper.style.display = 'none';
        tbody.innerHTML = '';
        info.textContent = '';
        empty.textContent = 'Pilih snapshot untuk menampilkan data stok.';
    });

    btnLoad.addEventListener('click', function () {
        const snapshot = selSnapshot.value;
        if (!snapshot) return;

        empty.textContent = 'Memuat data...';
        wrapper.style.display = 'none';

        fetch(`{{ route('stok.loadData') }}?snapshot_name=${encodeURIComponent(snapshot)}`)
            .then(r => r.json())
            .then(data => {
                if (!data.length) {
                    empty.textContent = 'Data kosong.';
                    return;
                }

                tbody.innerHTML = data.map((row, i) => `
                    <tr>
                        <td class="text-muted">${i + 1}</td>
                        <td class="fw-medium">${row.motor?.nama ?? '-'}</td>
                        <td class="text-center fw-semibold">${row.stok_sisa}</td>
                    </tr>
                `).join('');

                info.textContent = `${data.length} motor ditemukan`;
                wrapper.style.display = 'block';
                empty.textContent = '';
                
                // 🔥 MANTRA PENGHILANG EMPTY STATE UTAMA
                if (mainEmptyStateCard) mainEmptyStateCard.style.display = 'none';
            })
            .catch(() => {
                empty.textContent = 'Gagal memuat data. Coba lagi.';
            });
    });

    btnReset.addEventListener('click', function () {
        selSnapshot.value = '';
        btnLoad.disabled = true;
        wrapper.style.display = 'none';
        tbody.innerHTML = '';
        info.textContent = '';
        empty.textContent = 'Pilih snapshot untuk menampilkan data stok.';
        
        // 🔥 MANTRA PEMANGGIL KEMBALI EMPTY STATE UTAMA
        if (mainEmptyStateCard) mainEmptyStateCard.style.display = 'block';
    });
})();
</script>

</x-app-layout>
