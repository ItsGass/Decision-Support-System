<style>
    .rotate-90 {
        transform: rotate(90deg);
    }
</style>
<nav class="bg-white shadow-sm border-b relative z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <!-- LEFT: Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ route('dashboard') }}" class="font-bold tracking-wide flex flex-col justify-center">
                    <!-- Tampil di HP/Tablet: Cuma IHDSS (1 baris, besar) -->
                    <span class="block lg:hidden text-xl md:text-2xl text-blue-600">
                        IHDSS
                    </span>

                    <!-- Tampil di Laptop/Desktop: Teks lengkap (2 baris, bertingkat) -->
                    <span class="hidden lg:flex flex-col">
                        <span
                            class="text-2xl font-bold text-blue-600 leading-tight uppercase font-['Rajdhani'] tracking-widest">
                            Intelligent Hybrid
                        </span>
                        <span class="text-xs font-semibold text-gray-500 tracking-wider">
                            Decision Support System
                        </span>
                    </span>
                </a>
            </div>

            <!-- CENTER: Menu (Desktop Saja) -->
            <!-- Pindah pakai lg:flex dan flex-1 justify-center biar gak tabrakan (Gak pakai absolute lagi) -->
            <div class="hidden lg:flex flex-1 justify-center gap-8 text-sm font-semibold">
                <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-blue-600 transition">Dashboard</a>
                <a href="{{ route('penjualan') }}" class="text-gray-600 hover:text-blue-600 transition">Data
                    Penjualan</a>
                <a href="{{ route('stok.index') }}" class="text-gray-600 hover:text-blue-600 transition">Stok</a>
                <a href="{{ route('opini') }}" class="text-gray-600 hover:text-blue-600 transition">Opini</a>
                <a href="{{ route('trend.index') }}" class="text-gray-600 hover:text-blue-600 transition">Trend</a>

                <a href="{{ route('prediction.index') }}"
                    class="text-gray-600 hover:text-blue-600 transition">Prediksi</a>
                <a href="{{ route('motor.index') }}" class="text-gray-600 hover:text-blue-600 transition">Motor</a>
                <a href="{{ route('settings.prediction') }}" class="text-gray-600 hover:text-blue-600 transition">Settings</a>
            </div>

            <!-- RIGHT: Buttons & Profile -->
            <div class="flex items-center gap-2 sm:gap-4">

                <!-- 🔥 CLEAR BUTTON (Teks disembunyikan di HP, sisa icon/padding kecil) -->
                <button onclick="openClearModal()"
                    class="px-3 py-2 sm:px-4 sm:py-2 rounded-lg text-xs sm:text-sm font-medium bg-red-500 text-white hover:bg-red-600 transition flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                        viewBox="0 0 16 16" class="sm:hidden block">
                        <path
                            d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                        <path
                            d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                    </svg>
                    <span class="hidden sm:block">Manage Data</span>
                </button>

                <!-- USER DROPDOWN (Bawaan Laravel) -->
                <div class="hidden sm:flex items-center">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="flex items-center gap-2 text-sm text-gray-600 hover:text-blue-600 transition">
                                <span>{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    Logout
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- 🔥 HAMBURGER BUTTON (Tampil di HP/Tablet) -->
                <button onclick="toggleMobileMenu()"
                    class="lg:hidden p-2 text-gray-600 hover:text-blue-600 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path id="hamburgerIcon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path id="closeIcon" class="hidden" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

            </div>
        </div>
    </div>

    <!-- 🔥 MOBILE MENU PANEL (Tampil saat Hamburger di-klik) -->
    <div id="mobileMenu" class="hidden lg:hidden border-t border-gray-100 bg-white absolute w-full shadow-lg">
        <div class="px-4 pt-2 pb-4 space-y-1">
            <a href="{{ route('dashboard') }}"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">Dashboard</a>
            <a href="{{ route('penjualan') }}"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">Data
                Penjualan</a>
            <a href="{{ route('stok.index') }}"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">Stok</a>
            <a href="{{ route('opini') }}"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">Opini</a>
           <a href="{{ route('trend.index') }}"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">Trend</a>
                <a href="{{ route('prediction.index') }}"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">Prediksi</a>
            <a href="{{ route('motor.index') }}"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">Motor</a>
            <a href="{{ route('settings.prediction') }}"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">Settings</a>

            <div class="border-t border-gray-200 mt-4 pt-4 pb-2">
                <div class="px-3 text-sm font-medium text-gray-500">Hi, {{ Auth::user()->name }}</div>
                <a href="{{ route('profile.edit') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 mt-1">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full text-left block px-3 py-2 rounded-md text-base font-medium text-red-600 hover:bg-red-50">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>



<!-- 🔥 MODAL (WAJIB DI LUAR NAV) -->
<div id="clearModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded-xl w-full max-w-md shadow-lg max-h-[80vh] flex flex-col">

        <h2 class="text-lg font-semibold mb-1">Hapus Data</h2>
        <p class="text-sm text-gray-500 mb-4">Expand tabel, lalu pilih dataset yang ingin dihapus.</p>

        <div id="modalContent" class="overflow-y-auto flex-1 space-y-2 mb-4">
            <p class="text-sm text-gray-400">Memuat dataset...</p>
        </div>

        <form method="POST" action="{{ route('data.clearSelected') }}" id="clearForm">
            @csrf
            <div id="hiddenInputs"></div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeClearModal()"
                    class="px-4 py-2 bg-gray-100 rounded-lg text-sm">Batal</button>
                <button type="submit" id="btnHapus"
                    class="px-4 py-2 bg-red-500 text-white rounded-lg text-sm hover:bg-red-600 disabled:opacity-40 disabled:cursor-not-allowed"
                    disabled>Hapus yang dipilih</button>
            </div>
        </form>
    </div>
</div>

<!-- 🔥 SCRIPT -->

<script>
    const TABLE_META = {
        penjualan_analisis: {
            label: 'Penjualan Analisis',
            key: 'dataset_name'
        },
        penjualan: {
            label: 'Penjualan',
            key: 'dataset_name'
        },
        opini: {
            label: 'Opini',
            key: 'dataset_name'
        },
        stok: {
            label: 'Stok',
            key: 'snapshot_name'
        },
    };

    let datasetData = {};

    async function openClearModal() {
        const modal = document.getElementById('clearModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        const res = await fetch('{{ route('data.datasetNames') }}');
        datasetData = await res.json();
        renderModal();
    }

    function closeClearModal() {
        document.getElementById('clearModal').classList.add('hidden');
    }

    function renderModal() {
        const container = document.getElementById('modalContent');
        container.innerHTML = '';

        for (const [table, meta] of Object.entries(TABLE_META)) {
            const names = datasetData[table] || [];
            const group = document.createElement('div');
            group.className = 'border rounded-lg overflow-hidden';
            group.innerHTML = `
            <div class="flex items-center gap-3 px-3 py-2 bg-gray-50 cursor-pointer hover:bg-gray-100"
                 onclick="toggleGroup('${table}')">
                <input type="checkbox" id="tbl-${table}" class="accent-red-500"
                       onclick="event.stopPropagation(); toggleTableAll('${table}', this)">
                <span class="flex-1 text-sm font-medium">${meta.label}</span>
                <span class="text-xs text-gray-400">${names.length} dataset</span>
                <span id="chv-${table}" class="text-xs text-gray-400 transition-transform inline-block">></span>            </div>
            <div id="list-${table}" class="hidden divide-y">
                ${names.map(name => `
                    <label class="flex items-center gap-3 px-4 py-2 pl-9 cursor-pointer hover:bg-red-50">
                        <input type="checkbox" class="accent-red-500 ds-cb-${table}"
                               data-table="${table}" data-name="${name}"
                               onchange="onDatasetChange('${table}')">
                        <span class="text-xs font-mono text-gray-700">${name}</span>
                    </label>`).join('')}
            </div>`;
            container.appendChild(group);
        }
    }

    function toggleGroup(table) {
        document.getElementById('list-' + table).classList.toggle('hidden');
        const chv = document.getElementById('chv-' + table);
        chv.classList.toggle('rotate-90');
    }

    function toggleTableAll(table, master) {
        document.querySelectorAll(`.ds-cb-${table}`).forEach(cb => cb.checked = master.checked);
        if (master.checked) document.getElementById('list-' + table).classList.remove('hidden');
        syncDeleteButton();
    }

    function onDatasetChange(table) {
        const all = document.querySelectorAll(`.ds-cb-${table}`);
        const checked = document.querySelectorAll(`.ds-cb-${table}:checked`);
        const master = document.getElementById('tbl-' + table);
        master.checked = all.length === checked.length;
        master.indeterminate = checked.length > 0 && checked.length < all.length;
        syncDeleteButton();
    }

    function syncDeleteButton() {
        const any = document.querySelectorAll('input[data-table]:checked').length > 0;
        document.getElementById('btnHapus').disabled = !any;

        // Rebuild hidden inputs
        const container = document.getElementById('hiddenInputs');
        container.innerHTML = '';
        document.querySelectorAll('input[data-table]:checked').forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `selections[${cb.dataset.table}][]`;
            input.value = cb.dataset.name;
            container.appendChild(input);
        });
    }

    //RESPONSIVE MENU
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        const hamburger = document.getElementById('hamburgerIcon');
        const close = document.getElementById('closeIcon');

        menu.classList.toggle('hidden');
        hamburger.classList.toggle('hidden');
        close.classList.toggle('hidden');
    }
</script>
