<x-app-layout>

{{-- ============================================================
     DATA MOTOR  |  Bootstrap 5  |  Refactored UI
     ============================================================ --}}

<style>
    :root {
        --accent       : #0d6efd;
        --accent-soft  : #e8f0fe;
        --danger-soft  : #fff0f0;
        --card-radius  : 1rem;
        --border-muted : #e9ecef;
        --text-muted2  : #8c97a8;
    }

    .motor-page {
        background: #f4f6fb;
        min-height: 100vh;
        padding: 2.5rem 1.5rem;
    }

    /* ── Main card ── */
    .main-card {
        border: 1px solid var(--border-muted);
        border-radius: var(--card-radius);
        background: #fff;
        box-shadow: 0 2px 12px rgba(0,0,0,.05);
        overflow: hidden;
    }

    /* ── Table ── */
    .motor-table thead th {
        background: #f8f9ff;
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: var(--text-muted2);
        border-bottom: 2px solid var(--border-muted);
        padding: .85rem 1.25rem;
        white-space: nowrap;
    }
    .motor-table tbody td {
        font-size: .875rem;
        padding: .8rem 1.25rem;
        color: #3d4451;
        vertical-align: middle;
        border-bottom: 1px solid #f3f4f6;
    }
    .motor-table tbody tr:last-child td { border-bottom: none; }
    .motor-table tbody tr:hover td { background: #f8f9ff; }

    /* ── Search input ── */
    .search-input {
        border: 1px solid var(--border-muted);
        border-radius: .55rem;
        font-size: .875rem;
        height: 38px;
        padding: .4rem .75rem .4rem 2.25rem;
        transition: border-color .15s, box-shadow .15s;
        width: 260px;
    }
    .search-input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(13,110,253,.1);
        outline: none;
    }
    .search-wrap { position: relative; }
    .search-wrap .search-icon {
        position: absolute;
        left: .7rem; top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted2);
        pointer-events: none;
    }

    /* ── Motor avatar ── */
    .motor-avatar {
        width: 34px; height: 34px;
        border-radius: .5rem;
        background: var(--accent-soft);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: .75rem; font-weight: 700;
        color: var(--accent);
        flex-shrink: 0;
    }

    /* ── Delete popup overlay ── */
    .popup-overlay {
        position: fixed; inset: 0;
        background: rgba(10,14,26,.55);
        backdrop-filter: blur(3px);
        display: flex; align-items: center; justify-content: center;
        z-index: 1060;
        opacity: 0; pointer-events: none;
        transition: opacity .2s;
    }
    .popup-overlay.active {
        opacity: 1; pointer-events: all;
    }
    .popup-box {
        background: #fff;
        border-radius: 1.25rem;
        border: 1px solid var(--border-muted);
        box-shadow: 0 20px 60px rgba(0,0,0,.18);
        padding: 2.25rem 2rem;
        width: 360px;
        transform: translateY(12px);
        transition: transform .2s;
    }
    .popup-overlay.active .popup-box { transform: translateY(0); }

    .danger-icon-wrap {
        width: 56px; height: 56px;
        background: var(--danger-soft);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1.1rem;
    }

    /* ── Empty state ── */
    .empty-state-icon {
        width: 52px; height: 52px;
        background: var(--accent-soft);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1rem;
    }

    /* Row number badge */
    .row-num {
        display: inline-flex; align-items: center; justify-content: center;
        width: 26px; height: 26px;
        border-radius: .4rem;
        background: #f3f4f6;
        font-size: .78rem;
        font-weight: 600;
        color: var(--text-muted2);
    }
</style>

<div class="motor-page">
    <div class="container-xl px-0">

        {{-- ── PAGE HEADER ── --}}
        <div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
            <div>
                <h1 class="fw-semibold text-dark mb-1" style="font-size:1.6rem;letter-spacing:-.02em">
                    Data Motor
                </h1>
                <p class="text-muted mb-0" style="font-size:.9rem">
                    Kelola jenis motor yang tersedia untuk prediksi stok
                </p>
            </div>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

    

    <!-- RIGHT -->
    <a href="{{ route('motor.create') }}"
       class="btn btn-primary d-flex align-items-center gap-1"
       style="border-radius:.6rem;font-size:.875rem;font-weight:500;padding:.45rem 1.25rem">
        
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
             fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
        </svg>

        Tambah Motor
    </a>

</div>
        </div>

        {{-- ══════════════════════════════════
             📋  TABEL DATA MOTOR
        ══════════════════════════════════ --}}
        <div class="main-card">

            {{-- Card Header: Search + info --}}
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 px-4 py-3"
                 style="border-bottom:1px solid var(--border-muted)">

                <div class="search-wrap">
                    <span class="search-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                             fill="currentColor" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                        </svg>
                    </span>
                    <form method="GET" action="{{ route('motor.index') }}" class="mb-0">
                        <input type="text" name="search"
                               value="{{ request('search') }}"
                               oninput="this.form.submit()"
                               placeholder="Cari nama motor…"
                               class="search-input">
                    </form>
                </div>

                <div class="d-flex align-items-center gap-3">
                    @if (request('search'))
                        <a href="{{ route('motor.index') }}"
                           class="d-flex align-items-center gap-1 text-muted text-decoration-none"
                           style="font-size:.83rem">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                 fill="currentColor" viewBox="0 0 16 16">
                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                            </svg>
                            Reset pencarian
                        </a>
                    @endif
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-normal"
                          style="font-size:.78rem;border-radius:2rem;padding:.3rem .75rem">
                        {{ $motors->count() }} motor
                    </span>
                </div>

            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table mb-0 motor-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:70px">No</th>
                            <th>Nama Motor</th>
                            <th class="text-center" style="width:160px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($motors as $index => $motor)
                            <tr>
                                <td class="text-center">
                                    <span class="row-num">{{ $index + 1 }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        
                                        <span class="badge fs-6 px-3 py-2 bg-primary bg-opacity-10 text-primary ">{{ $motor->nama }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">

                                        <a href="{{ route('motor.edit', $motor->id) }}"
                                           class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                                           style="border-radius:.5rem;font-size:.8rem;padding:.3rem .75rem">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                 fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
                                            </svg>
                                            Edit
                                        </a>

                                        <form action="{{ route('motor.destroy', $motor->id) }}"
                                              method="POST" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                    onclick="openDeletePopup(this)"
                                                    class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1"
                                                    style="border-radius:.5rem;font-size:.8rem;padding:.3rem .75rem">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                     fill="currentColor" viewBox="0 0 16 16">
                                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-5 text-center">
                                    <div class="empty-state-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                             fill="var(--accent)" viewBox="0 0 16 16">
                                            <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/>
                                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/>
                                        </svg>
                                    </div>
                                    <p class="fw-medium text-dark mb-1" style="font-size:.95rem">
                                        {{ request('search') ? 'Motor tidak ditemukan' : 'Belum ada data motor' }}
                                    </p>
                                    <p class="text-muted mb-0" style="font-size:.84rem">
                                        {{ request('search') ? 'Coba kata kunci lain atau reset pencarian.' : 'Klik tombol "+ Tambah Motor" untuk menambahkan data.' }}
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Card Footer --}}
            @if($motors->count() > 0)
            <div class="px-4 py-3 d-flex align-items-center justify-content-between flex-wrap gap-2"
                 style="border-top:1px solid var(--border-muted);background:#fafbff">
                <span class="text-muted" style="font-size:.8rem">
                    Menampilkan <strong>{{ $motors->count() }}</strong> data motor
                    @if(request('search'))
                        untuk pencarian "<strong>{{ request('search') }}</strong>"
                    @endif
                </span>
            </div>
            @endif

        </div>

    </div>{{-- /container --}}
</div>

{{-- ══════════════════════════════════════════════════
     ⚠️  DELETE CONFIRMATION POPUP
══════════════════════════════════════════════════ --}}
<div id="deletePopup" class="popup-overlay">
    <div class="popup-box text-center">

        <div class="danger-icon-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                 fill="#dc2626" viewBox="0 0 16 16">
                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
            </svg>
        </div>

        <h2 class="fw-semibold text-dark mb-2" style="font-size:1.05rem">
            Hapus Data Motor?
        </h2>
        <p class="text-muted mb-4" style="font-size:.875rem;line-height:1.65">
            Data motor ini akan dihapus secara permanen dan tidak dapat dikembalikan.
        </p>

        <div class="d-flex justify-content-center gap-2">
            <button onclick="closePopup()"
                    class="btn btn-outline-secondary"
                    style="border-radius:.6rem;font-size:.875rem;padding:.45rem 1.25rem;min-width:90px">
                Batal
            </button>
            <button onclick="confirmDelete()"
                    class="btn btn-danger"
                    style="border-radius:.6rem;font-size:.875rem;font-weight:500;padding:.45rem 1.25rem;min-width:90px">
                Ya, Hapus
            </button>
        </div>

    </div>
</div>

<script>
    let selectedForm = null;

    function openDeletePopup(button) {
        selectedForm = button.closest('form');
        document.getElementById('deletePopup').classList.add('active');
    }

    function confirmDelete() {
        if (selectedForm) selectedForm.submit();
    }

    function closePopup() {
        document.getElementById('deletePopup').classList.remove('active');
    }

    // Close on overlay click
    document.getElementById('deletePopup').addEventListener('click', function (e) {
        if (e.target === this) closePopup();
    });
</script>

</x-app-layout>