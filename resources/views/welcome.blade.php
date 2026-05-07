<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalesPredict</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: #e8e8e8;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.5rem;
            font-family: 'Instrument Sans', system-ui, sans-serif;
        }

        .navbar {
            width: 100%;
            max-width: 860px;
            background: #f2f2f2;
            border: 0.5px solid #d8d8d8;
            border-radius: 12px;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .nav-brand {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .nav-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #6c63ff;
            display: inline-block;
        }

        .nav-links {
            display: flex;
            gap: 8px;
        }

        .btn-ghost {
            font-size: 12px;
            font-weight: 500;
            padding: 6px 16px;
            border-radius: 7px;
            background: transparent;
            color: #555;
            border: 0.5px solid #ccc;
            text-decoration: none;
        }

        .btn-dark {
            font-size: 12px;
            font-weight: 500;
            padding: 6px 16px;
            border-radius: 7px;
            background: #1a1a1a;
            color: #fff;
            text-decoration: none;
        }

        .card {
            width: 100%;
            max-width: 860px;
            background: #f5f5f5;
            border: 0.5px solid #d8d8d8;
            border-radius: 16px;
            padding: 2.5rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #eeecff;
            border: 0.5px solid #b8b0f5;
            color: #5b4fcf;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 2rem;
            margin-bottom: 20px;
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #7c6ff7;
            flex-shrink: 0;
            display: inline-block;
        }

        h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #1a1a1a;
            line-height: 1.25;
            margin-bottom: 10px;
        }

        h1 span {
            color: #6c63ff;
        }

        .sub {
            font-size: 14px;
            color: #666;
            line-height: 1.65;
            margin-bottom: 24px;
            max-width: 460px;
        }

        .cta-row {
            display: flex;
            gap: 8px;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }

        .cta-primary {
            padding: 9px 22px;
            background: #1a1a1a;
            color: #fff;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            border: none;
        }

        .cta-secondary {
            padding: 9px 22px;
            background: #eeecff;
            color: #5b4fcf;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border: 0.5px solid #c4bef9;
            cursor: pointer;
        }

        .accent-line {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 1.5rem;
        }

        .accent-line-bar {
            flex: 1;
            height: 1px;
            background: #e2e2e2;
        }

        .accent-line-text {
            font-size: 10px;
            color: #aaa;
            font-weight: 500;
            letter-spacing: .05em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: #ececec;
            border: 0.5px solid #d5d5d5;
            border-radius: 10px;
            padding: 14px 16px;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }

        .stat-card:nth-child(1)::before {
            background: #6c63ff;
        }

        .stat-card:nth-child(2)::before {
            background: #10b981;
        }

        .stat-card:nth-child(3)::before {
            background: #f59e0b;
        }

        .stat-num {
            font-size: 22px;
            font-weight: 700;
        }

        .stat-label {
            font-size: 11px;
            color: #777;
            margin-top: 2px;
        }

        .pill {
            display: inline-block;
            margin-top: 5px;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 2rem;
            font-weight: 600;
        }

        .pill-green {
            background: #d1fae5;
            color: #065f46;
        }

        .pill-blue {
            background: #dbeafe;
            color: #1e40af;
        }

        .pill-amber {
            background: #fef3c7;
            color: #92400e;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }

        .feat-card {
            background: #ececec;
            border: 0.5px solid #d5d5d5;
            border-radius: 10px;
            padding: 16px;
        }

        .feat-icon {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            border: 0.5px solid transparent;
        }

        .feat-icon-purple {
            background: #eeecff;
            border-color: #c4bef9;
        }

        .feat-icon-teal {
            background: #d1fae5;
            border-color: #6ee7b7;
        }

        .feat-icon-amber {
            background: #fef3c7;
            border-color: #fcd34d;
        }

        .feat-title {
            font-size: 12px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 4px;
        }

        .feat-desc {
            font-size: 11px;
            color: #777;
            line-height: 1.5;
        }

        .stat-card {
            transition: all .2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, .08);
        }
    </style>
</head>

<body>

    <nav class="navbar">
        <span class="nav-brand">
            <span class="nav-dot"></span>
            Intelligent Hybrid Decision Support System
        </span>
        <div class="nav-links">
            <a href="{{ route('login') }}" class="btn-ghost">Log in</a>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn-dark">Register</a>
            @endif
        </div>
    </nav>

    <main class="card">

        <div class="badge">
            <span class="badge-dot"></span>
            Powered by  Data Analytics & Machine Learning 
        </div>

        <h1>Prediksi Penjualan<br><span>Motor</span> Lebih Akurat</h1>

        <p class="sub">
            Analisis data historis penjualan secara otomatis untuk membantu
            keputusan stok yang lebih tepat dan efisien.
        </p>
<!-- 
        <div class="cta-row">
            @auth
                <a href="{{ url('/dashboard') }}" class="cta-primary">
                    Masuk ke Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="cta-primary">
                    Masuk ke Dashboard
                </a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="cta-secondary">
                        Daftar Sekarang →
                    </a>
                @endif
            @endauth
        </div>
        -->

        <div class="accent-line">
            <div class="accent-line-bar"></div>
            <span class="accent-line-text">Statistik Platform</span>
            <div class="accent-line-bar"></div>
        </div>

        <!-- STATS GRID -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Akurasi model saat ini</div>
                <div class="stat-num" style="color:#6c63ff">99%</div>
                <span class="pill pill-blue">↑ dibanding model lalu</span>
            </div>

            <div class="stat-card">
                <div class="stat-label">Data historis dianalisis</div>
                <div class="stat-num" style="color:#059669">1.000+</div>
                <span class="pill pill-green">Per Jam</span>
            </div>

            <div class="stat-card">
                <div class="stat-label">Model motor tersedia</div>
                <div class="stat-num" style="color:#d97706">100+</div>
                <span class="pill pill-amber">siap diprediksi</span>
            </div>
        </div>

        <div class="features-grid">

    <!-- 1 -->
    <div class="stat-card feat-card">
        <div class="feat-icon feat-icon-purple">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <rect x="2" y="8" width="3" height="6" rx="1" fill="#6c63ff"/>
                <rect x="6.5" y="5" width="3" height="9" rx="1" fill="#6c63ff" opacity=".7"/>
                <rect x="11" y="2" width="3" height="12" rx="1" fill="#6c63ff" opacity=".5"/>
            </svg>
        </div>
        <div class="feat-title">Prediksi Stok Cerdas</div>
        <div class="feat-desc">
            Menghitung kebutuhan stok motor berdasarkan data penjualan historis dan pola permintaan.
        </div>
    </div>

    <!-- 2 -->
    <div class="stat-card feat-card">
        <div class="feat-icon feat-icon-teal">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <path d="M2 12 L5 8 L8 9.5 L11 5 L14 6" stroke="#059669" stroke-width="1.5"
                    stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div class="feat-title">Analisis Sentimen AI</div>
        <div class="feat-desc">
            Mengolah opini pelanggan menjadi sentimen positif, netral, atau negatif untuk memahami kepuasan pasar.
        </div>
    </div>

    <!-- 3 -->
    <div class="stat-card feat-card">
        <div class="feat-icon feat-icon-amber">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <circle cx="8" cy="8" r="5.5" stroke="#d97706" stroke-width="1.5"/>
                <path d="M8 5v3.5l2 2" stroke="#d97706" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="feat-title">Rekomendasi Stok</div>
        <div class="feat-desc">
            Memberikan saran jumlah stok optimal berdasarkan kombinasi data penjualan dan hasil analisis AI.
        </div>
    </div>

</div>

    </main>

</body>

</html>
