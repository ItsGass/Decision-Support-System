<x-app-layout>

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

        .trend-page {
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

        .section-label {
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: .25rem .65rem;
            border-radius: 2rem;
        }

        .pct-badge {
            display: inline-block;
            padding: .25rem .65rem;
            border-radius: 2rem;
            font-size: .75rem;
            font-weight: 600;
        }
    </style>

    <div class="trend-page">
        <div class="container-xl px-0">

            <div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
                <div>
                    <h1 class="fw-semibold text-dark mb-1" style="font-size:1.6rem;letter-spacing:-.02em">Analisis Trend
                        Banten</h1>
                    <p class="text-muted mb-0" style="font-size:.9rem">Pantau minat pasar lokal menggunakan AI Gemini</p>
                </div>
            </div>

            {{-- ══════════════════════════════════
     🔵 SECTION: KONTROL (Hanya Admin)
══════════════════════════════════ --}}
@if(auth()->user()->role !== 'user')
    <div class="section-card mb-4">
        <div class="card-topbar bg-primary"></div>
        <div class="p-4">
            <div class="d-flex align-items-center gap-2 mb-4">
                <span class="section-label bg-primary bg-opacity-10 text-primary">Generate</span>
                <h2 class="mb-0 fw-semibold" style="font-size:1rem;color:#1e2433">Trend</h2>
            </div>

            <div class="row align-items-end g-3">
                <div class="col-md-7">
                    <form id="formGenerate" method="POST" action="{{ route('trend.generate') }}"
                        class="d-flex gap-2 align-items-end">
                        @csrf
                        <div class="flex-grow-1">
                            <label class="form-label fw-medium" style="font-size:.85rem">Nama Dataset</label>
                            <input type="text" name="periode" class="form-control"
                                placeholder="Contoh: Lebaran 2026" required
                                style="border-radius:.6rem;font-size:.875rem"
                                value="{{ old('periode', session('periode')) }}">
                        </div>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-1"
                            style="border-radius:.6rem;font-size:.875rem;font-weight:500;height:38px">
                            Generate AI
                        </button>
                    </form>
                    @error('periode')
                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Form Reset hidden — di luar semua form --}}
    <form id="formReset" method="POST" action="{{ route('trend.clear') }}" style="display:none">
        @csrf
    </form>
@endif

{{-- ══════════════════════════════════
     🟢 HASIL FILTER (Semua Role Bisa Lihat)
══════════════════════════════════ --}}
@if ($trends->isNotEmpty())
    <div class="section-card mb-4">
        <div class="card-topbar bg-success"></div>
        <div class="p-4">
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="section-label bg-success bg-opacity-10 text-success">Database</span>
                    <h2 class="mb-0 fw-semibold" style="font-size:1rem;color:#1e2433">
                        {{ request('periode') ? 'Periode: ' . request('periode') : 'Semua Data Trend' }}
                    </h2>
                </div>
                <span class="badge bg-success bg-opacity-10 text-success fw-normal"
                    style="font-size:.78rem;border-radius:2rem;padding:.3rem .75rem">
                    {{ $trends->count() }} Data
                </span>
            </div>

            <div class="table-responsive" style="border-radius:.6rem;border:1px solid var(--border-muted)">
                <table class="table table-hover mb-0 preview-table">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="12%">Periode</th>
                            <th width="18%">Nama Motor</th>
                            <th width="12%" class="text-center">Kategori</th>
                            <th width="12%" class="text-center">Skor Trend</th>
                            <th>Alasan AI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($trends as $i => $t)
                            @php
                                $color = match ($t->motor->category) {
                                    'fast_moving' => 'success',
                                    'slow_moving' => 'warning',
                                    'premium' => 'danger',
                                    default => 'secondary',
                                };
                            @endphp
                            <tr onclick="toggleTrend({{ $i }})" style="cursor:pointer">
                                <td class="text-muted">{{ $i + 1 }}</td>
                                <td class="fw-bold text-primary">{{ $t->periode }}</td>
                                <td class="fw-medium">{{ $t->motor->nama }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }}"
                                        style="border-radius:2rem">
                                        {{ strtoupper(str_replace('_', ' ', $t->motor->category)) }}
                                    </span>
                                </td>
                                <td class="text-center fw-bold">{{ number_format($t->skor_trend, 2) }}</td>
                                <td>
                                    <div style="font-size:.82rem;color:#475569;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;white-space:normal">
                                        {{ $t->alasan_ai ?? '-' }}
                                    </div>
                                </td>
                            </tr>
                            <tr id="expand-trend-{{ $i }}" style="display:none;background:#f8fafc">
                                <td colspan="6" style="padding:12px 16px;font-size:.85rem;color:#334155;line-height:1.7;border-top:none">
                                    <span style="font-size:.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:4px">Alasan AI Lengkap</span>
                                    {{ $t->alasan_ai ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

{{-- ══════════════════════════════════
     🟡 PREVIEW (Hanya Admin)
══════════════════════════════════ --}}
@if(auth()->user()->role !== 'user')
    @if (session('preview_trend'))
        <div class="section-card mb-4">
            <div class="card-topbar" style="background:#f59e0b"></div>
            <div class="p-4">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="section-label" style="background:#fff8e6;color:#d97706">Preview</span>
                        <h2 class="mb-0 fw-semibold" style="font-size:1rem;color:#1e2433">
                            Preview Periode: {{ session('periode') }}
                        </h2>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if (session('saved_trend'))
                            <span class="badge bg-success bg-opacity-10 text-success">Sudah disimpan</span>
                        @else
                            <span class="badge bg-warning bg-opacity-10 text-warning">Belum disimpan</span>
                        @endif
                    </div>
                </div>

                <div class="table-responsive" style="border-radius:.6rem;border:1px solid var(--border-muted)">
                    <table class="table table-hover mb-0 preview-table">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="20%">Nama Motor</th>
                                <th width="15%" class="text-center">Kategori</th>
                                <th width="12%" class="text-center">Skor Trend</th>
                                <th>Alasan AI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (session('preview_trend') as $i => $t)
                                @php
                                    $color = match ($t['kategori']) {
                                        'fast_moving' => 'success',
                                        'slow_moving' => 'warning',
                                        'premium' => 'danger',
                                        default => 'secondary',
                                    };
                                @endphp
                                <tr style="cursor:pointer" onclick="togglePreview({{ $i }})">
                                    <td class="text-muted">{{ $i + 1 }}</td>
                                    <td class="fw-medium text-dark">{{ $t['nama_motor'] }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }}"
                                            style="border-radius:2rem">
                                            {{ str_replace('_', ' ', strtoupper($t['kategori'])) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold fs-6" style="color:#1e2433">{{ number_format($t['skor_trend'], 2) }}</span>
                                    </td>
                                    <td>
                                        <div style="font-size:.82rem;color:#475569;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;white-space:normal">
                                            {{ $t['alasan_ai'] }}
                                        </div>
                                    </td>
                                </tr>
                                <tr id="expand-preview-{{ $i }}" style="display:none;background:#f8fafc">
                                    <td colspan="5" style="padding:12px 16px;font-size:.85rem;color:#334155;line-height:1.7;border-top:none">
                                        <span style="font-size:.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:4px">Alasan AI Lengkap</span>
                                        {{ $t['alasan_ai'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-3"
                    style="border-top:1px solid var(--border-muted)">
                    <p class="text-muted mb-0" style="font-size:.82rem">Pastikan skor masuk akal sebelum disimpan ke Database.</p>
                    <div class="d-flex gap-2">
                        <form method="POST" action="{{ route('trend.clear') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger" style="border-radius:.6rem;font-size:.875rem">Reset</button>
                        </form>
                        @if (session('saved_trend'))
                            <form method="POST" action="{{ route('trend.clear') }}">
                                @csrf
                                <button type="submit" class="btn btn-secondary" style="border-radius:.6rem;font-size:.875rem">Tutup Preview</button>
                            </form>
                        @else
                            <form id="formSimpan" method="POST" action="{{ route('trend.simpan') }}">
                                @csrf
                                <button type="submit" class="btn btn-success d-flex align-items-center gap-1"
                                    style="border-radius:.6rem;font-size:.875rem;font-weight:500">
                                    Simpan Data Trend
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif

            {{-- ══════════════════════════════════
         📦 SECTION: DATA TERSIMPAN (AJAX)
    ══════════════════════════════════ --}}
            <div class="section-card mb-4">
                <div class="card-topbar bg-success"></div>
                <div class="p-4">

                    <div class="d-flex align-items-center gap-2 mb-4">
                        <span class="section-label bg-success bg-opacity-10 text-success">Select</span>
                        <h2 class="mb-0 fw-semibold" style="font-size:1rem;color:#1e2433">Data Trend</h2>
                    </div>

                    <div class="row g-3 align-items-end mb-4">
                        <div class="col-12 col-md-5">
                            <label class="form-label fw-medium" style="font-size:.85rem">Periode</label>
                            <select id="filterTrend" class="form-select"
                                style="border-radius:.6rem;font-size:.875rem">
                                <option value="">Pilih Periode</option>
                                @foreach ($datasetTrend as $d)
                                    <option value="{{ $d }}">{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-2">
                            <button id="btnLoadTrend"
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

                    <div id="trendWrapper" style="display:none">
                        <div class="table-responsive"
                            style="border-radius:.6rem;border:1px solid var(--border-muted);overflow:hidden">
                            <table class="table table-hover mb-0 preview-table">
                                <thead>
                                    <tr>
                                        <th style="width:36px">#</th>
                                        <th>Nama Motor</th>
                                        <th class="text-center" style="width:110px">Kategori</th>
                                        <th class="text-center" style="width:100px">Skor Trend</th>
                                        <th>Alasan AI</th>
                                    </tr>
                                </thead>
                                <tbody id="trendBody"></tbody>
                            </table>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-2">
                            <p class="text-muted mb-0" style="font-size:.8rem" id="trendInfo"></p>
                            <button id="btnResetTrend"
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

                    <div id="trendEmpty" class="text-center py-4 text-muted" style="font-size:.875rem">
                        Pilih periode untuk menampilkan data trend.
                    </div>

                </div>
            </div>

            {{-- ══════════════════════════════════
     ⚪ EMPTY STATE
══════════════════════════════════ --}}
@if (!session('preview_trend') && $trends->isEmpty())
    <div class="section-card mb-4" id="emptyStateCard">
        <div class="card-topbar bg-secondary"></div>
        <div class="p-5 text-center">
            <div class="empty-state-icon" style="background:#f1f5f9">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="#64748b" viewBox="0 0 16 16">
                    <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1L14 5.5zM8.5 6.5a.5.5 0 0 0-1 0V9H5a.5.5 0 0 0 0 1h2.5v2.5a.5.5 0 0 0 1 0V10H11a.5.5 0 0 0 0-1H8.5z" />
                </svg>
            </div>
            <p class="fw-medium text-dark mb-1" style="font-size:.95rem">Belum Ada Data yang Ditampilkan</p>
            <p class="text-muted mb-0" style="font-size:.84rem">
                @if(auth()->user()->role !== 'user')
                    Silakan Generate AI atau pilih periode pada Data Tersimpan di atas.
                @else
                    Pilih periode pada kolom Data Tersimpan untuk melihat analisis trend.
                @endif
            </p>
        </div>
    </div>
@endif

        </div>

        {{-- Loading Popup --}}
        <div id="loadingPopup"
            class="popup-overlay position-fixed top-0 start-0 w-100 h-100 d-none d-flex align-items-center justify-content-center"
            style="z-index:1060">
            <div class="popup-box bg-white p-5 text-center" style="width:340px">
                <div class="spinner-ring mx-auto mb-4"></div>
                <p class="fw-semibold text-dark mb-1" style="font-size:.95rem">Memanggil AI Gemini…</p>
                <p class="text-muted mb-0" style="font-size:.83rem;line-height:1.6">Menganalisa sentimen pasar motor
                    Banten.<br>Mohon tunggu sekitar 5-10 detik.</p>
            </div>
        </div>

        {{-- Result Popup --}}
        <div id="resultPopup"
            class="popup-overlay position-fixed top-0 start-0 w-100 h-100 d-none d-flex align-items-center justify-content-center"
            style="z-index:1060">
            <div class="popup-box bg-white p-5 text-center" style="width:380px">
                <div id="popupIconWrap" class="empty-state-icon mx-auto mb-3"></div>
                <h2 id="popupTitle" class="fw-semibold text-dark mb-2" style="font-size:1.05rem"></h2>
                <p id="popupMessage" class="text-muted mb-4" style="font-size:.875rem;line-height:1.65"></p>
                <button onclick="closePopup()" class="btn btn-primary px-4"
                    style="border-radius:.6rem;font-size:.875rem;font-weight:500">Tutup</button>
            </div>
        </div>

        <script>
    document.addEventListener("DOMContentLoaded", function() {

        /* ── Loading on generate & simpan ── */
        const formGenerate = document.getElementById('formGenerate');
        if (formGenerate) {
            formGenerate.addEventListener('submit', function() {
                document.getElementById('loadingPopup').classList.remove('d-none');
            });
        }
        const formSimpan = document.getElementById('formSimpan');
        if (formSimpan) {
            formSimpan.addEventListener('submit', function() {
                document.getElementById('loadingPopup').classList.remove('d-none');
            });
        }

        /* ── Popup helpers ── */
        function showPopup(title, message, type) {
            const iconMap = {
                success: `<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="#16a34a" viewBox="0 0 16 16"><path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/></svg>`,
                error: `<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="#dc2626" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.553.553 0 0 1-1.1 0z"/></svg>`,
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

        @if (session('success'))
            showPopup("Berhasil!", "{{ session('success') }}", "success");
        @endif
        @if (session('error'))
            showPopup("Waduh Error", "{{ session('error') }}", "error");
        @endif

        /* ── Expand rows preview & filter ── */
        window.togglePreview = function(i) {
            const row = document.getElementById(`expand-preview-${i}`);
            if (row) row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
        };

        window.toggleTrend = function(i) {
            const row = document.getElementById(`expand-trend-${i}`);
            if (row) row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
        };

        /* ── Data Tersimpan Trend (AJAX) ── */
        const sel = document.getElementById('filterTrend');
        const btnLoad = document.getElementById('btnLoadTrend');
        const btnReset = document.getElementById('btnResetTrend');
        const wrapper = document.getElementById('trendWrapper');
        const empty = document.getElementById('trendEmpty'); // Ini empty state kecil milik AJAX
        const tbody = document.getElementById('trendBody');
        const info = document.getElementById('trendInfo');
        
        // 🔥 ELEMEN EMPTY STATE UTAMA YG ADA DI BAWAH HALAMAN
        const mainEmptyStateCard = document.getElementById('emptyStateCard'); 

        if (sel) {
            const categoryBadge = (cat) => {
                const map = {
                    'fast_moving': ['#ecfdf5', '#059669'],
                    'slow_moving': ['#fffbeb', '#d97706'],
                    'premium': ['#fef2f2', '#dc2626'],
                };
                const [bg, color] = map[cat] ?? ['#f8fafc', '#64748b'];
                const label = (cat ?? '-').replace('_', ' ').toUpperCase();
                return `<span class="pct-badge" style="background:${bg};color:${color};font-size:.75rem">${label}</span>`;
            };

            const skorColor = (skor) => {
                if (skor >= 0.7) return '#059669';
                if (skor >= 0.4) return '#d97706';
                return '#dc2626';
            };

            sel.addEventListener('change', function() {
                btnLoad.disabled = !this.value;
                wrapper.style.display = 'none';
                tbody.innerHTML = '';
                info.textContent = '';
                empty.textContent = 'Pilih periode untuk menampilkan data trend.';
            });

            btnLoad.addEventListener('click', function() {
                const periode = sel.value;
                if (!periode) return;

                empty.textContent = 'Memuat data...';
                wrapper.style.display = 'none';

                fetch(`{{ route('trend.loadData') }}?periode=${encodeURIComponent(periode)}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => {
                        if (!r.ok) {
                            return r.json().then(err => Promise.reject(err));
                        }
                        return r.json();
                    })
                    .then(data => {
                        if (data.success === false) {
                            empty.textContent = data.message || 'Gagal memuat data.';
                            return;
                        }

                        if (!Array.isArray(data) || data.length === 0) {
                            empty.textContent = 'Data kosong untuk periode ini.';
                            return;
                        }

                        tbody.innerHTML = data.map((row, i) => `
                            <tr style="cursor:pointer" onclick="toggleTrendRow(${i})">
                                <td class="text-muted" style="font-size:.82rem">${i + 1}</td>
                                <td class="fw-medium">${row.motor?.nama ?? '-'}</td>
                                <td class="text-center">${categoryBadge(row.motor?.category)}</td>
                                <td class="text-center fw-bold" style="color:${skorColor(row.skor_trend)}">${parseFloat(row.skor_trend).toFixed(2)}</td>
                                <td>
                                    <div style="font-size:.82rem;color:#475569;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;white-space:normal">
                                        ${row.alasan_ai ?? '-'}
                                    </div>
                                </td>
                            </tr>
                            <tr id="expand-trend-db-${i}" style="display:none;background:#f8fafc">
                                <td colspan="5" style="padding:12px 16px;font-size:.85rem;color:#334155;line-height:1.7;border-top:none">
                                    <span style="font-size:.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:4px">Alasan AI Lengkap</span>
                                    ${row.alasan_ai ?? '-'}
                                </td>
                            </tr>
                        `).join('');

                        info.textContent = `${data.length} motor ditemukan`;
                        wrapper.style.display = 'block';
                        empty.style.display = 'none';

                        // 🔥 MANTRA PENGHILANG EMPTY STATE UTAMA
                        if (mainEmptyStateCard) mainEmptyStateCard.style.display = 'none';
                    })
                    .catch(err => {
                        console.error('AJAX Error:', err);
                        empty.textContent = err.message || 'Gagal memuat data. Coba lagi.';
                        empty.style.display = 'block';
                    });
            });

            btnReset.addEventListener('click', function() {
                sel.value = '';
                btnLoad.disabled = true;
                wrapper.style.display = 'none';
                tbody.innerHTML = '';
                info.textContent = '';
                empty.textContent = 'Pilih periode untuk menampilkan data trend.';
                empty.style.display = 'block';

                // 🔥 MANTRA PEMANGGIL KEMBALI EMPTY STATE UTAMA
                if (mainEmptyStateCard) mainEmptyStateCard.style.display = 'block';
            });

            window.toggleTrendRow = function(i) {
                const row = document.getElementById(`expand-trend-db-${i}`);
                if (row) row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
            };
        }

    });
</script>

</x-app-layout>
