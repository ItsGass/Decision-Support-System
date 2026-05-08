<x-app-layout>

    {{-- ============================================================
     DASHBOARD PENJUALAN  |  Bootstrap 5  |  Refactored UI
     ============================================================ --}}

    <style>
        :root {
            --accent: #0d6efd;
            --accent-soft: #e8f0fe;
            --success: #16a34a;
            --success-soft: #e6f9f0;
            --purple: #7c3aed;
            --purple-soft: #f3eeff;
            --card-radius: 1rem;
            --border-muted: #e9ecef;
            --text-muted2: #8c97a8;
        }

        .dashboard-page {
            background: #f4f6fb;
            min-height: 100vh;
            padding: 2.5rem 1.5rem;
        }

        /* ── Cards ── */
        .section-card {
            border: 1px solid var(--border-muted);
            border-radius: var(--card-radius);
            background: #fff;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .05);
            overflow: hidden;
        }

        /* ── Stat card ── */
        .stat-card {
            border: 1px solid var(--border-muted);
            border-radius: var(--card-radius);
            background: #fff;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .05);
            padding: 1.35rem 1.5rem;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            border-radius: var(--card-radius) 0 0 var(--card-radius);
        }

        .stat-card.blue::after {
            background: var(--accent);
        }

        .stat-card.green::after {
            background: var(--success);
        }

        .stat-card.purple::after {
            background: var(--purple);
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: .6rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-value {
            font-size: 1.65rem;
            font-weight: 700;
            letter-spacing: -.03em;
            line-height: 1.1;
        }

        .stat-label {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--text-muted2);
            margin-bottom: .3rem;
        }

        .stat-sub {
            font-size: .78rem;
            color: var(--text-muted2);
            margin-top: .25rem;
        }

        /* ── Filter card ── */
        .filter-card {
            border: 1px solid var(--border-muted);
            border-radius: var(--card-radius);
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .04);
            padding: 1.1rem 1.5rem;
        }

        /* ── Chart card ── */
        .chart-card {
            border: 1px solid var(--border-muted);
            border-radius: var(--card-radius);
            background: #fff;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .05);
            height: 100%;
        }

        /* ── Activity list ── */
        .activity-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
            margin-top: 6px;
        }

        .activity-item {
            padding: .65rem 0;
            border-bottom: 1px solid var(--border-muted);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        /* ── Live badge ── */
        .live-badge {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            background: #e8f0fe;
            color: var(--accent);
            border-radius: 2rem;
            font-size: .75rem;
            font-weight: 600;
            padding: .3rem .85rem;
        }

        .live-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--accent);
            animation: pulse 1.8s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: .4;
                transform: scale(.7);
            }
        }

        /* Form control consistent size */
        .filter-control {
            border-radius: .55rem;
            font-size: .875rem;
            border: 1px solid var(--border-muted);
            height: 38px;
            padding: .4rem .75rem;
            color: #3d4451;
            transition: border-color .15s, box-shadow .15s;
        }

        .filter-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(13, 110, 253, .1);
            outline: none;
        }

        select.filter-control {
            padding-right: 2rem;
        }

        /* Section label */
        .section-label {
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: .22rem .6rem;
            border-radius: 2rem;
        }

        /* ── Custom Scrollbar untuk Top List ── */
        .scrollable-list::-webkit-scrollbar {
            width: 6px;
        }

        .scrollable-list::-webkit-scrollbar-track {
            background: transparent;
        }

        .scrollable-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .scrollable-list::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>

    <div class="dashboard-page">
        <div class="container-xl px-0">

            {{-- ── PAGE HEADER ── --}}
            <div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
                <div>
                    <h1 class="fw-semibold text-dark mb-1" style="font-size:1.6rem;letter-spacing:-.02em">
                        Dashboard Penjualan Motor
                    </h1>
                    <p class="text-muted mb-0" style="font-size:.9rem">
                        Visualisasi data penjualan
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="live-badge">
                        <span class="live-dot"></span>
                        Live Data
                    </div>
                    
                </div>
            </div>

            {{-- ══════════════════════════════════
             🔍  FILTER TANGGAL
        ══════════════════════════════════ --}}
            <div class="filter-card mb-4">
                <div class="d-flex align-items-end flex-wrap gap-3">

                    <div>
                        <label class="form-label fw-medium mb-1" style="font-size:.82rem;color:var(--text-muted2)">
                            Dari Tanggal
                        </label>
                        <input type="date" name="start" value="{{ request('start') }}" class="filter-control"
                            style="width:165px">
                    </div>

                    <div class="pb-1 text-muted" style="font-size:1.1rem;line-height:38px">—</div>

                    <div>
                        <label class="form-label fw-medium mb-1" style="font-size:.82rem;color:var(--text-muted2)">
                            Sampai Tanggal
                        </label>
                        <input type="date" name="end" value="{{ request('end') }}" class="filter-control"
                            style="width:165px">
                    </div>

                    {{-- divider --}}
                    <div class="d-none d-md-block" style="width:1px;height:38px;background:var(--border-muted)"></div>

                    <div class="d-flex gap-2 pb-0">

                        <button type="submit"
                            class="btn btn-primary d-flex align-items-center justify-content-center gap-1"
                            style="border-radius:.55rem;font-size:.875rem;font-weight:500;height:38px;padding:0 1.25rem">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                            </svg>
                            Filter
                        </button>

                        <button type="button" id="resetFilter"
                            class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-1"
                            style="border-radius:.55rem;font-size:.875rem;height:38px;padding:0 1.1rem">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41m-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9" />
                                <path fill-rule="evenodd"
                                    d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5 5 0 0 0 8 3M3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9z" />
                            </svg>
                            Reset
                        </button>

                    </div>

                </div>
            </div>

            {{-- ══════════════════════════════════
             📊  STAT CARDS
        ══════════════════════════════════ --}}
            <div class="row g-3 mb-4">

                {{-- Growth Rate --}}
                <div class="col-12 col-md-4">
                    <div class="stat-card blue">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <p class="stat-label">Growth Rate</p>
                                <div class="stat-value {{ $growthRate >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $growthRate > 0 ? '+' : '' }}{{ $growthRate }}%
                                </div>
                                <p class="stat-sub">vs bulan lalu</p>
                            </div>
                            <div class="stat-icon" style="background:var(--accent-soft)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                    fill="var(--accent)" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M0 0h1v15h15v1H0zm14.817 3.113a.5.5 0 0 1 .07.704l-4.5 5.5a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61 4.15-5.073a.5.5 0 0 1 .704-.07" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Penjualan --}}
                <div class="col-12 col-md-4">
                    <div class="stat-card green">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <p class="stat-label">Total Penjualan</p>
                                <div id="totalPenjualan" class="stat-value" style="color:var(--success)">0</div>
                                <p class="stat-sub">Unit terjual</p>
                            </div>
                            <div class="stat-icon" style="background:var(--success-soft)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                    fill="var(--success)" viewBox="0 0 16 16">
                                    <path
                                        d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Motor Terlaris --}}
                <div class="col-12 col-md-4">
                    <div class="stat-card purple">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <p id="labelMotor" class="stat-label">Motor Terlaris</p>
                                <div id="topMotor" class="stat-value" style="color:var(--purple)">—</div>
                                <p class="stat-sub">Berdasarkan filter aktif</p>
                            </div>
                            <div class="stat-icon" style="background:var(--purple-soft)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                    fill="var(--purple)" viewBox="0 0 16 16">
                                    <path
                                        d="M2.5 3a.5.5 0 0 0 0 1h11a.5.5 0 0 0 0-1zm2 3a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1zM4 9.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m.5 2.5a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ══════════════════════════════════
             📈  CHART + AKTIVITAS
        ══════════════════════════════════ --}}
            <div class="row g-3">

                {{-- Grafik --}}
                <div class="col-12 col-lg-8">
                    <div class="chart-card p-4">

                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                            <div class="d-flex align-items-center gap-2">
                                <span class="section-label bg-success bg-opacity-10 text-success">Grafik</span>
                                <h2 class="mb-0 fw-semibold" style="font-size:1rem;color:#1e2433">
                                    Tren Penjualan
                                </h2>
                            </div>

                            <select id="filterMotor" name="motor_id" class="filter-control"
                                style="width:auto;min-width:150px">
                                <option value="">Semua Motor</option>
                                @foreach ($motors as $motor)
                                    <option value="{{ $motor->id }}">{{ $motor->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <canvas id="chartPenjualan" style="max-height:280px"></canvas>

                    </div>
                </div>

                {{-- Stok Kritis --}}
                <div class="col-12 col-lg-4">
                    <div class="chart-card p-4 d-flex flex-column">

                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="section-label bg-danger bg-opacity-10 text-danger">Stok</span>
                            <h2 class="mb-0 fw-semibold" style="font-size:1rem;color:#1e2433">
                                Stok Kritis
                            </h2>
                        </div>

                        <ul class="list-unstyled mb-0 mt-2 scrollable-list"
                            style="max-height: 310px; overflow-y: auto; padding-right: 8px;">
                            @forelse ($stokKritis as $motor)
                                <li class="activity-item d-flex align-items-center gap-3">
                                    <span class="activity-dot"
                                        style="background:{{ $motor->stok_sisa == 0 ? '#ef4444' : '#f97316' }}"></span>
                                    <div class="flex-fill">
                                        <p class="mb-0 fw-medium" style="font-size:.85rem;color:#1e2433">
                                            {{ $motor->nama }}</p>
                                        <p class="mb-0 text-muted" style="font-size:.78rem">Sisa stok</p>
                                    </div>
                                    <span
                                        class="badge {{ $motor->stok_sisa == 0 ? 'bg-danger text-danger' : 'bg-warning text-warning' }} bg-opacity-10 fw-semibold"
                                        style="font-size:.75rem;border-radius:2rem;padding:.25rem .7rem">
                                        {{ $motor->stok_sisa }} unit
                                    </span>
                                </li>
                            @empty
                                <div class="text-center text-muted mt-4">
                                    <p style="font-size: .85rem;">✅ Semua stok dalam kondisi aman.</p>
                                </div>
                            @endforelse
                        </ul>


                    </div>
                </div>

            </div>

        </div>{{-- /container --}}
    </div>

    {{-- ══════════════════════════════════════
     SCRIPTS  (logic identik, zero changes)
══════════════════════════════════════ --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('chartPenjualan');

    function formatTanggal(labels) {
        return labels.map(date => new Date(date).toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        }));
    }

    let chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Penjualan',
                data: [],
                borderColor: '#16a34a',
                backgroundColor: 'rgba(22,163,74,0.07)',
                tension: 0.45,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#16a34a',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    cornerRadius: 10,
                    displayColors: false,
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 }, color: '#94a3b8' }
                },
                y: {
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: { font: { size: 11 }, color: '#94a3b8', stepSize: 1 },
                    beginAtZero: true
                }
            }
        }
    });

    function loadDashboard() {
        let motorId = document.getElementById('filterMotor').value;
        let start = document.querySelector('input[name="start"]').value;
        let end = document.querySelector('input[name="end"]').value;

        fetch(`/dashboard/data?motor_id=${motorId}&start=${start}&end=${end}`)
            .then(res => res.json())
            .then(data => {
                // 1. Update Chart
                chart.data.labels = formatTanggal(data.labels);
                chart.data.datasets[0].data = data.data;
                chart.update();

                // 2. Update Total Penjualan
                document.getElementById('totalPenjualan').innerText = data.total;

                // 3. Update Label Motor Terlaris
                if (data.selected_motor) {
                    document.getElementById('labelMotor').innerText = 'Jenis Motor';
                    document.getElementById('topMotor').innerText = data.selected_motor;
                } else {
                    document.getElementById('labelMotor').innerText = 'Motor Terlaris';
                    document.getElementById('topMotor').innerText = data.top_motor ?? '—';
                }

                // 4. 🔥 UPDATE GROWTH RATE (DINAMIS)
                updateGrowthUI(data.growth);
            });
    }

    // Fungsi Pembantu untuk Update UI Growth Rate
    function updateGrowthUI(growth) {
        // Cari elemen growth (biasanya di stat-card blue)
        const growthValue = document.querySelector('.stat-card.blue .stat-value');
        if (!growthValue) return;

        let sign = growth > 0 ? '+' : '';
        let arrow = growth >= 0 ? '↑' : '↓';
        
        // Update Angka & Panah
        growthValue.innerHTML = `${sign}${growth}% <span style="font-size:.85rem;font-weight:500">${arrow}</span>`;

        // Update Warna (Biru jika naik/tetap, Merah jika turun)
        if (growth >= 0) {
            growthValue.classList.remove('text-danger');
            growthValue.classList.add('text-primary');
        } else {
            growthValue.classList.remove('text-primary');
            growthValue.classList.add('text-danger');
        }
    }

    // Event Listeners
    document.getElementById('filterMotor').addEventListener('change', loadDashboard);
    document.querySelector('input[name="start"]').addEventListener('change', loadDashboard);
    document.querySelector('input[name="end"]').addEventListener('change', loadDashboard);

    document.getElementById('resetFilter').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('filterMotor').value = '';
        document.querySelector('input[name="start"]').value = '';
        document.querySelector('input[name="end"]').value = '';
        loadDashboard();
    });

    // Jalankan pertama kali saat load
    loadDashboard();
</script>

</x-app-layout>
