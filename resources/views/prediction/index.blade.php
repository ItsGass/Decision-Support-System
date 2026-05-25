{{-- resources/views/prediction/index.blade.php --}}
<x-app-layout>


    <style>
        /* ================================================================
       CSS Variables & Reset
    ================================================================ */
        :root {
            --bg: #f0f2f5;
            --surface: #ffffff;
            --surface-2: #f8f9fa;
            --surface-3: #f1f3f5;

            --border: rgba(0, 0, 0, 0.07);
            --border-hover: rgba(0, 0, 0, 0.14);

            --accent: #0d6efd;
            --accent-glow: rgba(13, 110, 253, 0.18);
            --accent-2: #0dcaf0;

            --success: #198754;
            --success-bg: rgba(25, 135, 84, 0.08);
            --warning: #b45309;
            --warning-bg: rgba(245, 158, 11, 0.10);
            --danger: #dc3545;
            --danger-bg: rgba(220, 53, 69, 0.08);

            --text: #111827;
            --text-muted: #6b7280;
            --text-dim: #374151;

            --radius: 10px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            --transition: 0.18s cubic-bezier(0.4, 0, 0.2, 1);

            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08), 0 2px 4px rgba(0, 0, 0, 0.04);
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            min-height: 100vh;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* ================================================================
       Layout
    ================================================================ */
        .page-wrapper {
            background-color: var(--bg);
            min-height: 100vh;
            max-width: 1280px;
            margin: 0 auto;
            padding: 40px 28px 80px;
        }

        /* ================================================================
       Header
    ================================================================ */
        .page-header {
            margin-bottom: 36px;
            position: relative;
            padding-left: 18px;
        }

        .page-header::before {
            content: '';
            position: absolute;
            left: 0;
            top: 3px;
            bottom: 3px;
            width: 3px;
            background: linear-gradient(180deg, var(--accent), var(--accent-2));
            border-radius: 4px;
        }

        .page-header h1 {
            font-family: 'Sora', sans-serif;
            font-size: clamp(1.45rem, 3.5vw, 2.1rem);
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--text);
            line-height: 1.2;
        }

        .page-header p {
            margin-top: 5px;
            color: var(--text-muted);
            font-size: 0.88rem;
            font-weight: 400;
            letter-spacing: 0.01em;
        }

        /* ================================================================
       Card
    ================================================================ */
        .card {
            background: var(--surface);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 28px 32px;
            margin-bottom: 24px;
            transition: box-shadow var(--transition);
        }

        .card:hover {
            box-shadow: var(--shadow-md);
        }

        .card-title {
            font-family: 'Sora', sans-serif;
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title .icon {
            width: 30px;
            height: 30px;
            background: var(--accent-glow);
            border-radius: 8px;
            display: grid;
            place-items: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        /* ================================================================
       Form
    ================================================================ */
        .form-grid {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 36px;
            align-items: start;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 16px;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.07em;
        }

        .form-input {
            background: var(--surface-2);
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 500;
            padding: 10px 14px;
            width: 100%;
            transition: border-color var(--transition), box-shadow var(--transition), background var(--transition);
            outline: none;
            -moz-appearance: textfield;
            appearance: none;
        }

        .form-input::-webkit-outer-spin-button,
        .form-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
        }

        .form-input:hover {
            border-color: var(--border-hover);
            background: var(--surface);
        }

        .form-input:focus {
            border-color: var(--accent);
            background: var(--surface);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .form-input.is-error {
            border-color: var(--danger);
            box-shadow: 0 0 0 3px var(--danger-bg);
        }

        /* Total target — big number input */
        input#total_target.form-input {
            font-family: 'Sora', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            padding: 14px 18px;
            letter-spacing: -0.02em;
        }

        .form-error {
            font-size: 0.78rem;
            color: var(--danger);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .form-error::before {
            content: '⚠';
            font-size: 0.7rem;
        }

        /* Motor baru checkbox grid */
        .motor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(168px, 1fr));
            gap: 8px;
        }

        .motor-checkbox-label {
            display: flex;
            align-items: center;
            gap: 9px;
            background: var(--surface-2);
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            padding: 9px 13px;
            cursor: pointer;
            transition: border-color var(--transition), background var(--transition), box-shadow var(--transition);
            user-select: none;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-dim);
        }

        .motor-checkbox-label:hover {
            border-color: var(--border-hover);
            background: var(--surface);
        }

        .motor-checkbox-label input[type="checkbox"] {
            accent-color: var(--accent);
            width: 15px;
            height: 15px;
            flex-shrink: 0;
        }

        .motor-checkbox-label.checked {
            border-color: var(--accent);
            background: rgba(13, 110, 253, 0.06);
            box-shadow: 0 0 0 1px var(--accent-glow);
            color: var(--text);
        }

        /* ================================================================
       Buttons
    ================================================================ */
        .btn-row {
            display: flex;
            gap: 10px;
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 10px 20px;
            border-radius: var(--radius);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: transform var(--transition), box-shadow var(--transition), background var(--transition), opacity var(--transition);
            white-space: nowrap;
            line-height: 1;
        }

        .btn:active {
            transform: scale(0.97);
        }

        .btn svg {
            flex-shrink: 0;
        }

        .btn-primary {
            background: var(--accent);
            color: #fff;
            box-shadow: 0 2px 8px var(--accent-glow);
        }

        .btn-primary:hover {
            background: #0b5ed7;
            box-shadow: 0 4px 16px rgba(13, 110, 253, 0.35);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--surface-2);
            color: var(--text-dim);
            border: 1.5px solid var(--border);
        }

        .btn-secondary:hover {
            border-color: var(--border-hover);
            background: var(--surface-3);
            color: var(--text);
        }

        .btn-success {
            background: #166534;
            color: #bbf7d0;
            border: 1px solid #15803d;
            box-shadow: 0 2px 8px rgba(21, 128, 61, 0.2);
        }

        .btn-success:hover {
            background: #15803d;
            box-shadow: 0 4px 14px rgba(21, 128, 61, 0.3);
            transform: translateY(-1px);
        }

        /* ================================================================
       Score Badge
    ================================================================ */
        .score-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 9px;
            border-radius: 99px;
            font-size: 0.72rem;
            font-weight: 700;
            font-family: 'Sora', monospace;
            letter-spacing: 0.02em;
        }

        .score-high {
            background: var(--success-bg);
            color: var(--success);
        }

        .score-mid {
            background: var(--warning-bg);
            color: var(--warning);
        }

        .score-low {
            background: var(--danger-bg);
            color: var(--danger);
        }

        /* ================================================================
       Results Table
    ================================================================ */
        .table-wrapper {
            overflow-x: auto;
            border-radius: 0 0 var(--radius-xl) var(--radius-xl);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        thead th {
            background: var(--surface-3);
            color: var(--text-muted);
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background var(--transition);
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody tr:nth-child(even) {
            background: rgba(0, 0, 0, 0.015);
        }

        tbody tr:hover {
            background: rgba(13, 110, 253, 0.04);
        }

        td {
            padding: 13px 16px;
            vertical-align: middle;
            color: var(--text);
        }

        td.no-col {
            color: var(--text-muted);
            font-family: 'Sora', monospace;
            font-size: 0.78rem;
            width: 44px;
            text-align: center;
        }

        td.nama-col {
            font-weight: 600;
            font-family: 'Sora', sans-serif;
            font-size: 0.875rem;
        }

        td.qty-col {
            font-family: 'Sora', monospace;
            font-weight: 800;
            font-size: 1.05rem;
            color: var(--accent);
            text-align: center;
        }

        td.alasan-col {
            color: var(--text-muted);
            font-size: 0.82rem;
            line-height: 1.55;
            max-width: 340px;
        }

        .badge-new {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 6px;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            background: rgba(13, 202, 240, 0.12);
            color: var(--accent-2);
            margin-left: 6px;
            vertical-align: middle;
        }

        /* Score bar visual */
        .score-bar-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 110px;
        }

        .score-bar-track {
            flex: 1;
            height: 4px;
            background: var(--surface-3);
            border-radius: 4px;
            overflow: hidden;
        }

        .score-bar-fill {
            height: 100%;
            border-radius: 4px;
            background: linear-gradient(90deg, var(--accent), var(--accent-2));
            transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .score-val {
            font-family: 'Sora', monospace;
            font-size: 0.75rem;
            color: var(--text-muted);
            min-width: 38px;
            text-align: right;
        }

        /* ================================================================
       Summary bar
    ================================================================ */
        .summary-bar {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(156px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }

        .summary-item {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 16px 20px;
            box-shadow: var(--shadow-sm);
            transition: box-shadow var(--transition), transform var(--transition);
        }

        .summary-item:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-1px);
        }

        .summary-item .label {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .summary-item .value {
            font-family: 'Sora', sans-serif;
            font-size: 1.45rem;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -0.02em;
            line-height: 1.1;
        }

        .summary-item .value.accent {
            color: var(--accent);
        }

        .summary-item .value.success {
            color: var(--success);
        }

        /* ================================================================
       Empty state
    ================================================================ */
        .empty-state {
            text-align: center;
            padding: 72px 24px;
            color: var(--text-muted);
        }

        .empty-state .icon {
            font-size: 2.4rem;
            margin-bottom: 14px;
            opacity: 0.7;
        }

        .empty-state h3 {
            font-family: 'Sora', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-dim);
            margin-bottom: 6px;
        }

        .empty-state p {
            font-size: 0.875rem;
        }

        /* ================================================================
       Alert
    ================================================================ */
        .alert {
            padding: 14px 18px;
            border-radius: var(--radius);
            border-left: 3px solid;
            margin-bottom: 20px;
            font-size: 0.875rem;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .alert-error {
            background: var(--danger-bg);
            border-color: var(--danger);
            color: #b91c1c;
        }

        .alert ul {
            padding-left: 16px;
            margin-top: 4px;
        }

        .alert li {
            margin-top: 2px;
        }

        /* ================================================================
       Loading overlay
    ================================================================ */
        #loading-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(10, 12, 18, 0.65);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            z-index: 9999;
            place-items: center;
        }

        #loading-overlay.active {
            display: grid;
        }

        .loading-box {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 40px 52px;
            text-align: center;
            box-shadow: var(--shadow-md);
        }

        .spinner {
            width: 36px;
            height: 36px;
            border: 2.5px solid var(--border);
            border-top-color: var(--accent);
            border-radius: 50%;
            animation: spin 0.65s linear infinite;
            margin: 0 auto 16px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .loading-box p {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-top: 4px;
        }

        .loading-box strong {
            color: var(--text);
            font-family: 'Sora', sans-serif;
            font-size: 1rem;
        }

        /* ================================================================
       Responsive
    ================================================================ */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .page-wrapper {
                padding: 24px 16px 60px;
            }

            .card {
                padding: 20px 18px;
            }

            .btn-row {
                flex-wrap: wrap;
            }

            .btn-row .btn {
                flex: 1;
                min-width: 120px;
            }

            .summary-bar {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .summary-bar {
                grid-template-columns: 1fr 1fr;
            }

            .motor-grid {
                grid-template-columns: 1fr 1fr;
            }
        }


        .tbl-wrap {
            width: 100%;
            overflow-x: auto;
        }

        .tbl-wrap table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .tbl-wrap thead tr {
            border-bottom: 2px solid #e2e8f0;
        }

        .tbl-wrap th {
            padding: 10px 14px;
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            white-space: nowrap;
            background: #f8fafc;
        }

        .tbl-wrap tbody tr {
            border-bottom: 0.5px solid #f1f5f9;
            transition: background 0.15s;
        }

        .tbl-wrap tbody tr:hover {
            background: #f8fafc;
        }

        .tbl-wrap tbody tr:last-child {
            border-bottom: none;
        }

        .tbl-wrap td {
            padding: 18px 14px;
            vertical-align: top;
        }

        .motor-name {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
        }

        .num-old {
            font-size: 12px;
            color: #94a3b8;
            margin-bottom: 2px;
        }

        .num-new {
            font-size: 22px;
            font-weight: 700;
        }

        .num-persen {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
        }

        .num-score {
            font-size: 13px;
            color: #64748b;
            font-variant-numeric: tabular-nums;
        }

        .stok-val {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            text-align: center;
        }

        .no-cell {
            font-size: 13px;
            font-weight: 600;
            color: #94a3b8;
            text-align: center;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .badge-danger {
            background: #fef2f2;
            color: #dc2626;
        }

        .badge-danger .badge-dot {
            background: #dc2626;
        }

        .badge-review {
            background: #fffbeb;
            color: #d97706;
        }

        .badge-review .badge-dot {
            background: #d97706;
        }

        .badge-info {
            background: #eff6ff;
            color: #2563eb;
        }

        .badge-info .badge-dot {
            background: #2563eb;
        }

        .badge-safe {
            background: #ecfdf5;
            color: #059669;
        }

        .badge-safe .badge-dot {
            background: #059669;
        }

        .criteria-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 6px;
            margin-bottom: 10px;
        }

        .criteria-card {
            background: #f8fafc;
            border-radius: 8px;
            padding: 8px 10px;
            border: 0.5px solid #e2e8f0;
        }

        .criteria-label {
            font-size: 10px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 2px;
        }

        .criteria-val {
            font-size: 12px;
            font-weight: 600;
            color: #1e293b;
        }

        .conclusion-box {
            padding: 12px 14px;
            border-radius: 0 8px 8px 0;
            border: 0.5px solid #e2e8f0;
            background: #fff;
        }

        .conclusion-box.danger {
            border-left: 3px solid #dc2626;
        }

        .conclusion-box.review {
            border-left: 3px solid #d97706;
        }

        .conclusion-box.info {
            border-left: 3px solid #2563eb;
        }

        .conclusion-box.safe {
            border-left: 3px solid #059669;
        }

        .conclusion-label {
            font-size: 10px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 4px;
        }

        .conclusion-text {
            font-size: 13px;
            line-height: 1.6;
            color: #1e293b;
        }
    </style>

    <body>

        {{-- Loading overlay --}}
        <div id="loading-overlay">
            <div class="loading-box">
                <div class="spinner"></div>
                <strong>Memproses Prediksi...</strong>
                <p>Sedang menganalisis data.</p>
            </div>
        </div>

        <div class="page-wrapper">

            {{-- Header --}}
            <div class="page-header">
                <h1>Sistem Prediksi Stok Motor</h1>
                <p>Decision Support System · Berbasis Penjualan, Sentimen, dan Stok</p>
            </div>

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="alert alert-error">
                    <span>⚠️</span>
                    <div>
                        <strong>Terdapat kesalahan input:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- ============================================================
                FORM CARD
            ============================================================ --}}
            <div class="card">
                <div class="card-title">
                    <span class="icon">⚙️</span>
                    Parameter Prediksi
                </div>

                <form id="predictionForm" method="POST" action="{{ route('prediction.preview') }}"
                    onsubmit="showLoading()">
                    @csrf

                    <div class="form-grid">

                        {{-- Kolom kiri: dropdowns + total target --}}
                        <div>
                            {{-- Dropdown Data Penjualan --}}
                            <div class="form-group">
                                <label class="form-label" for="periode_penjualan">Data Penjualan</label>
                                <select id="periode_penjualan" name="periode_penjualan" class="form-input">
                                    <option value="">Pilih data penjualan</option>
                                    @foreach ($penjualan as $p)
                                        <option value="{{ $p }}"
                                            {{ ($periodePenjualan ?? '') == $p ? 'selected' : '' }}>
                                            {{ $p }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Dropdown Data Stok --}}
                            <div class="form-group">
                                <label class="form-label" for="periode_stok">Data Stok</label>
                                <select id="periode_stok" name="periode_stok" class="form-input">
                                    <option value="">Pilih data stok</option>
                                    @foreach ($stok as $s)
                                        <option value="{{ $s }}"
                                            {{ ($periodeStok ?? '') == $s ? 'selected' : '' }}>
                                            {{ $s }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Dropdown Data Sentimen --}}
                            <div class="form-group">
                                <label class="form-label" for="periode_opini">Data Sentimen</label>
                                <select id="periode_opini" name="periode_opini" class="form-input">
                                    <option value="">Pilih data sentimen</option>
                                    @foreach ($opini as $o)
                                        <option value="{{ $o }}"
                                            {{ ($periodeOpini ?? '') == $o ? 'selected' : '' }}>
                                            {{ $o }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="periode_trend">Data Trend</label>
                                <select id="periode_trend" name="periode_trend" class="form-input">
                                    <option value="">Pilih data trend</option>
                                    @foreach ($trend as $t)
                                        <option value="{{ $t }}"
                                            {{ ($periodeTrend ?? '') == $t ? 'selected' : '' }}>
                                            {{ $t }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Total Target --}}
                            <div class="form-group">
                                <label class="form-label" for="total_target">Total Target Unit</label>
                                <input type="number" id="total_target" name="total_target"
                                    class="form-input {{ $errors->has('total_target') ? 'is-error' : '' }}"
                                    value="{{ old('total_target', $totalTarget ?? '') }}" placeholder="50"
                                    min="1" required>
                                @error('total_target')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Kolom kanan: Motor Baru checkboxes --}}
                        <div class="form-group">
                            <label class="form-label">Motor Baru (opsional)</label>
                            <div class="motor-grid">
                                @foreach ($motors as $motor)
                                    @php $checked = in_array($motor->id, $motorBaruIds ?? []); @endphp
                                    <label class="motor-checkbox-label {{ $checked ? 'checked' : '' }}"
                                        id="label-{{ $motor->id }}">
                                        <input type="checkbox" name="motor_baru[]" value="{{ $motor->id }}"
                                            {{ $checked ? 'checked' : '' }} onchange="toggleCheckboxStyle(this)">
                                        {{ $motor->nama }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                    </div>{{-- /form-grid --}}

                    <div class="btn-row">
                        <button type="submit" class="btn btn-primary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="5 3 19 12 5 21 5 3" />
                            </svg>
                            Jalankan Prediksi
                        </button>

                        @if (isset($results) && $results->isNotEmpty())
                            <button type="submit" form="exportForm" class="btn btn-success">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                    <polyline points="7 10 12 15 17 10" />
                                    <line x1="12" y1="15" x2="12" y2="3" />
                                </svg>
                                Export Excel
                            </button>
                        @endif

                        <button type="button" class="btn btn-secondary"
                            onclick="window.location='{{ route('prediction.index') }}'">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                                <path d="M3 3v5h5" />
                            </svg>
                            Reset
                        </button>
                    </div>
                </form>

                {{-- Hidden export form --}}
@if (isset($results) && $results->isNotEmpty())
    <form id="exportForm" method="POST" action="{{ route('prediction.export') }}"
        style="display:none">
        @csrf
        <input type="hidden" name="total_target" value="{{ $totalTarget }}">
        <input type="hidden" name="periode_penjualan" value="{{ $periodePenjualan }}">
        <input type="hidden" name="periode_stok"      value="{{ $periodeStok }}">
        <input type="hidden" name="periode_opini"     value="{{ $periodeOpini }}">
        <input type="hidden" name="periode_trend"     value="{{ $periodeTrend }}">
        @foreach ($motorBaruIds ?? [] as $id)
            <input type="hidden" name="motor_baru[]" value="{{ $id }}">
        @endforeach
    </form>
@endif
            </div>{{-- /card --}}

            {{-- ============================================================
             RESULTS
             ============================================================ --}}
            @if (isset($results) && $results->isNotEmpty())

                {{-- Summary bar --}}
                <div class="summary-bar">
                    <div class="summary-item">
                        <div class="label">Total Target</div>
                        <div class="value accent">{{ number_format($totalTarget) }} <small
                                style="font-size:0.6em;font-weight:600;opacity:0.7">unit</small></div>
                    </div>
                    <div class="summary-item">
                        <div class="label">Jumlah Motor</div>
                        <div class="value">{{ $results->count() }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">Total Rekomendasi</div>
                        <div class="value success">{{ number_format($results->sum('rekomendasiJumlah')) }} <small
                                style="font-size:0.6em;font-weight:600;opacity:0.7">unit</small></div>
                    </div>
                    <div class="summary-item">
                        <div class="label">Motor Baru</div>
                        <div class="value">{{ $results->where('isNew', true)->count() }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">Skor Tertinggi</div>
                        <div class="value">{{ number_format($results->max('finalScore'), 3) }}</div>
                    </div>
                </div>

                {{-- 🔥 CARD INTERVENSI AUTO-BALANCE (TAMPIL JIKA ADA GAP FILLER) 🔥 --}}
                @php
                    $totalDitambah = collect($results)->where('gapAdjustment', '>', 0)->sum('gapAdjustment');
                    $totalDipotong = abs(collect($results)->where('gapAdjustment', '<', 0)->sum('gapAdjustment'));
                @endphp

                @if($totalDitambah > 0 || $totalDipotong > 0)
                <div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 15px; margin-bottom: 20px; border-radius: 8px;">
                    <h4 style="color: #1e3a8a; margin-top: 0; margin-bottom: 8px; font-size: 1.1rem;">Intervensi Auto-Balance (Target: {{ number_format($totalTarget) }} Unit)</h4>
                    <p style="font-size: 0.9rem; color: #1e40af; margin: 0; line-height: 1.5;">
                        Sistem mendeteksi adanya selisih unit akibat bentrokan aturan batas stok (Overstock / Danger Zone).<br>
                        Tindakan penyesuaian otomatis yang dilakukan sistem: 
                        @if($totalDitambah > 0) 
                            <strong style="color: #059669; background: #d1fae5; padding: 2px 6px; border-radius: 4px;">+{{ $totalDitambah }} Unit</strong> dibagikan untuk memenuhi target. 
                        @endif
                        @if($totalDipotong > 0) 
                            <strong style="color: #dc2626; background: #fee2e2; padding: 2px 6px; border-radius: 4px;">-{{ $totalDipotong }} Unit</strong> dipotong agar tidak melebihi batas target. 
                        @endif
                    </p>
                </div>
                @endif
                {{-- 🔥 END CARD AUTO-BALANCE 🔥 --}}

                {{-- Table card --}}
                <div class="card" style="padding: 0; overflow: hidden;">
                    <div style="padding: 22px 28px 18px;">
                        <div class="card-title" style="margin-bottom: 0;">
                            <span class="icon">📊</span>
                            Hasil Prediksi
                        </div>
                    </div>

                    <div class="tbl-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th rowspan="2" style="text-align:center; vertical-align:middle; width:36px;">
                                        No</th>
                                    <th rowspan="2" style="vertical-align:middle;">Nama Motor</th>
                                    <th colspan="2" style="text-align:center; border-bottom: 1px solid #e2e8f0;">
                                        Rekomendasi</th>
                                    <th rowspan="2" style="text-align:center; vertical-align:middle;">% Penjualan
                                    </th>
                                    <th rowspan="2" style="text-align:center; vertical-align:middle;">Skor Akhir
                                    </th>
                                    <th rowspan="2" style="text-align:center; vertical-align:middle;">Stok Sisa
                                    </th>
                                    <th rowspan="2" style="min-width: 420px; vertical-align:middle;">Alasan</th>
                                </tr>
                                <tr>
                                    <th style="text-align:center; font-size:12px; color:#64748b;">Lama</th>
                                    <th style="text-align:center; font-size:12px; color:#64748b;">Baru</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($results as $i => $result)
                                    @php
                                        // ==========================================================
                                        // 🔥 KEMBALIKAN REGEX (SINGLE SOURCE OF TRUTH) 🔥
                                        // ==========================================================
                                        $alasanRaw = $result->alasan ?? '';
                                        $alasanPlain = strip_tags($alasanRaw);

                                        preg_match(
                                            '/Penjualan \(C1\):\s*(.+?)(?=Kondisi Stok|$)/si',
                                            $alasanPlain,
                                            $mC1,
                                        );
                                        preg_match(
                                            '/Kondisi Stok \(C2\):\s*(.+?)(?=Sentimen|$)/si',
                                            $alasanPlain,
                                            $mC2,
                                        );
                                        preg_match('/Sentimen \(C3\):\s*(.+?)(?=Trend Pasar|$)/si', $alasanPlain, $mC3);
                                        preg_match(
                                            '/Trend Pasar \(C4\):\s*(.+?)(?=Kesimpulan|$)/si',
                                            $alasanPlain,
                                            $mC4,
                                        );
                                        preg_match('/Kesimpulan:\s*(.+)/si', $alasanPlain, $mKes);

                                        // Ambil kata pertamanya aja (Tinggi, Stabil, Kosong, dll)
                                        $salesLabel = explode(' (', explode(' — ', trim($mC1[1] ?? '—'))[0])[0];
                                        $stockLabel = explode(' (', explode(' — ', trim($mC2[1] ?? '—'))[0])[0];
                                        $sentimenLabel = explode(' (', explode(' — ', trim($mC3[1] ?? '—'))[0])[0];
                                        $trendLabel = explode(' (', explode(' — ', trim($mC4[1] ?? '—'))[0])[0];

                                        $kesimpulan = trim($mKes[1] ?? 'Tidak ada kesimpulan.');

                                        // Map Status UI (Sesuai Class CSS buatan lu)
                                        $statusMap = [
                                            'danger' => [
                                                'badge' => 'badge-danger',
                                                'label' => 'Perlu Intervensi',
                                                'cls' => 'danger',
                                                'numColor' => '#dc2626',
                                            ],
                                            'review' => [
                                                'badge' => 'badge-review',
                                                'label' => 'Perlu Review',
                                                'cls' => 'review',
                                                'numColor' => '#d97706',
                                            ],
                                            'info' => [
                                                'badge' => 'badge-info',
                                                'label' => 'Strategi Produk',
                                                'cls' => 'info',
                                                'numColor' => '#2563eb',
                                            ],
                                            'safe' => [
                                                'badge' => 'badge-safe',
                                                'label' => 'Eksekusi Aman',
                                                'cls' => 'safe',
                                                'numColor' => '#059669',
                                            ],
                                        ];

                                        $ui = $statusMap[$result->status ?? 'safe'] ?? $statusMap['safe'];
                                        $gapAdj = $result->gapAdjustment ?? 0;
                                    @endphp

                                    <tr>
                                        <td class="no-cell" style="text-align:center; vertical-align:middle;">
                                            {{ $i + 1 }}</td>

                                        <td style="vertical-align:middle;">
                                            <div class="motor-name">{{ $result->namaMotor }}</div>
                                            @if ($result->isNew)
                                                <div style="margin-top: 4px;">
                                                    <span
                                                        style="background:#2563eb; color:white; padding:2px 6px; border-radius:4px; font-size:10px; font-weight:bold;">NEW
                                                        MODEL</span>
                                                </div>
                                            @endif
                                        </td>

                                        {{-- 🔥 KOLOM REKOMENDASI LAMA (+ BADGE AUTO-BALANCE) 🔥 --}}
                                        <td
                                            style="text-align:center; vertical-align:middle; border-left:1px solid #edf2f7;">
                                            @if (!$result->isNew)
                                                <div class="num-new"
                                                    style="color: {{ $ui['numColor'] }}; font-weight: bold; font-size: 18px;">
                                                    {{ number_format($result->rekomendasiJumlah) }}
                                                    @if($gapAdj > 0)
                                                        <span style="color: #059669; font-size: 12px; background: #d1fae5; padding: 2px 6px; border-radius: 12px; margin-left: 4px; vertical-align: middle;" title="Ditambah dari sisa Gap">+{{ $gapAdj }}</span>
                                                    @elseif($gapAdj < 0)
                                                        <span style="color: #dc2626; font-size: 12px; background: #fee2e2; padding: 2px 6px; border-radius: 12px; margin-left: 4px; vertical-align: middle;" title="Dipotong karena melebihi target">{{ $gapAdj }}</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span style="color: #cbd5e0; font-size: 18px;">—</span>
                                            @endif
                                        </td>

                                        {{-- 🔥 KOLOM REKOMENDASI BARU (+ BADGE AUTO-BALANCE) 🔥 --}}
                                        <td
                                            style="text-align:center; vertical-align:middle; border-left:1px solid #edf2f7;">
                                            @if ($result->isNew)
                                                <div class="num-new"
                                                    style="color: #2563eb; font-weight: bold; font-size: 18px;">
                                                    {{ number_format($result->rekomendasiJumlah) }}
                                                    @if($gapAdj > 0)
                                                        <span style="color: #059669; font-size: 12px; background: #d1fae5; padding: 2px 6px; border-radius: 12px; margin-left: 4px; vertical-align: middle;" title="Ditambah dari sisa Gap">+{{ $gapAdj }}</span>
                                                    @elseif($gapAdj < 0)
                                                        <span style="color: #dc2626; font-size: 12px; background: #fee2e2; padding: 2px 6px; border-radius: 12px; margin-left: 4px; vertical-align: middle;" title="Dipotong karena melebihi target">{{ $gapAdj }}</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span style="color: #cbd5e0; font-size: 18px;">—</span>
                                            @endif
                                        </td>

                                        <td style="text-align:center; vertical-align:middle;">
                                            <div class="num-persen">{{ number_format($result->percent, 1) }}%</div>
                                        </td>

                                        <td style="text-align:center; vertical-align:middle;">
                                            <div class="num-score">{{ number_format($result->finalScore, 3) }}</div>
                                        </td>

                                        <td style="vertical-align:middle; text-align:center;">
                                            <div class="stok-val" style="font-weight:bold; font-size: 16px;">
                                                {{ $result->stokSisa }}
                                            </div>
                                        </td>

                                        <td style="vertical-align:middle;">
                                            {{-- Badge Status --}}
                                            <div class="badge {{ $ui['badge'] }}" style="margin-bottom: 12px;">
                                                <span class="badge-dot"></span>
                                                {{ $ui['label'] }}
                                            </div>

                                            {{-- Grid Kriteria C1–C4 --}}
                                            <div class="criteria-grid">
                                                <div class="criteria-card">
                                                    <div class="criteria-label">C1 Penjualan</div>
                                                    <div class="criteria-val">{{ $salesLabel }}</div>
                                                </div>
                                                <div class="criteria-card">
                                                    <div class="criteria-label">C2 Stok</div>
                                                    <div class="criteria-val">{{ $stockLabel }}</div>
                                                </div>
                                                <div class="criteria-card">
                                                    <div class="criteria-label">C3 Sentimen</div>
                                                    <div class="criteria-val">{{ $sentimenLabel }}</div>
                                                </div>
                                                <div class="criteria-card">
                                                    <div class="criteria-label">C4 Trend</div>
                                                    <div class="criteria-val">{{ $trendLabel }}</div>
                                                </div>
                                            </div>

                                            {{-- Box Kesimpulan --}}
                                            <div class="conclusion-box {{ $ui['cls'] }}"
                                                style="margin-top: 12px;">
                                                <div class="conclusion-label">Kesimpulan Strategis</div>
                                                <div class="conclusion-text">{{ $kesimpulan }}</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            @elseif(request()->isMethod('post'))
                <div class="card">
                    <div class="empty-state">
                        <div class="icon">🔍</div>
                        <h3>Tidak ada data ditemukan</h3>
                        <p>Pastikan data penjualan, stok, dan motor sudah tersedia di database.</p>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="empty-state">
                        <div class="icon">📈</div>
                        <h3>Belum ada prediksi dijalankan</h3>
                        <p>Masukkan total target unit lalu klik <strong>Jalankan Prediksi</strong>.</p>
                    </div>
                </div>
            @endif

        </div>{{-- /page-wrapper --}}

        <script>
            function showLoading() {
                document.getElementById('loading-overlay').classList.add('active');
            }

            function toggleCheckboxStyle(checkbox) {
                const label = checkbox.closest('.motor-checkbox-label');
                label.classList.toggle('checked', checkbox.checked);
            }
        </script>

    </body>

</x-app-layout>
