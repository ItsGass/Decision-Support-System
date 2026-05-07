<x-app-layout>
<div class="p-8 bg-[#f8f9fc] min-h-screen">

    <!-- HEADER -->
    <div class="mb-8">
        <h1 class="text-3xl font-semibold text-gray-900">Prediksi Penjualan</h1>
        <p class="text-gray-400 mt-1">Gabungkan data untuk menentukan distribusi stok</p>
    </div>

    <!-- INPUT -->
    <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-500">

        <!-- 🔥 UBAH JADI 4 KOLOM -->
        <div class="grid md:grid-cols-4 gap-4">

            <!-- DATA PENJUALAN -->
            <div>
                <label class="text-sm text-gray-500">Data Penjualan</label>
                <select id="penjualan"
                    class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    <option value="">Pilih dataset</option>
                    @foreach ($penjualan as $p)
                        <option value="{{ $p }}">{{ $p }}</option>
                    @endforeach
                </select>
            </div>

            <!-- STOK -->
            <div>
                <label class="text-sm text-gray-500">Data Stok</label>
                <select id="stok"
                    class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    <option value="">(opsional)</option>
                    @foreach ($stok as $s)
                        <option value="{{ $s }}">{{ $s }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 🔥 OPINI (BARU) -->
            <div>
                <label class="text-sm text-gray-500">Data Opini</label>
                <select id="opini"
                    class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    <option value="">(opsional)</option>
                    @foreach ($opini as $o)
                        <option value="{{ $o }}">{{ $o }}</option>
                    @endforeach
                </select>
            </div>

            <!-- TARGET -->
            <div>
                <label class="text-sm text-gray-500">Target Unit</label>
                <input type="number" id="target"
                    class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg text-sm"
                    placeholder="Contoh: 100">
            </div>

        </div>

        <!-- BUTTON -->
        <div class="mt-4 flex gap-2">
            <button id="btnProses"
                class="px-5 py-2 rounded-lg text-sm font-medium bg-black text-white hover:opacity-90">
                Proses
            </button>

            <button id="btnReset"
                class="px-5 py-2 rounded-lg text-sm font-medium bg-red-100 text-red-500 hover:bg-red-200">
                Reset
            </button>
        </div>

    </div>

    <!-- HASIL -->
    <div id="hasilContainer" class="mt-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-500 text-center text-gray-400 text-sm">
            Pilih data lalu klik <span class="font-medium text-gray-800">Proses</span>
        </div>
    </div>

</div>

<script>
function renderTable(data) {

    if (!data || data.length === 0) {
        document.getElementById('hasilContainer').innerHTML =
        `<div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-red-500 text-center text-sm text-gray-400">
            Tidak ada data
        </div>`;
        return;
    }

    let html = `
    <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-purple-500">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-gray-400 text-xs uppercase">
                    <th class="py-3 text-left">Motor</th>
                    <th class="py-3 text-center">%</th>
                    <th class="py-3 text-center">Unit</th>
                </tr>
            </thead>
            <tbody>
    `;

    data.forEach((item, i) => {
        let highlight = i === 0 ? "bg-gray-50 font-semibold" : "";

        html += `
        <tr class="border-t ${highlight}">
            <td class="py-3">${item.motor}</td>
            <td class="py-3 text-center">${item.percent}%</td>
            <td class="py-3 text-center">${item.unit}</td>
        </tr>`;
    });

    html += `</tbody></table></div>`;

    document.getElementById('hasilContainer').innerHTML = html;
}

// PROSES
document.getElementById('btnProses').addEventListener('click', function () {

    let penjualan = document.getElementById('penjualan').value;
    let stok = document.getElementById('stok').value;
    let opini = document.getElementById('opini').value;
    let target = document.getElementById('target').value;

    if (!penjualan || !target) {
        document.getElementById('hasilContainer').innerHTML =
        `<div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-red-500 text-center text-sm text-gray-400">
            Lengkapi data terlebih dahulu
        </div>`;
        return;
    }

    fetch(`/prediksi/final?penjualan=${penjualan}&stok=${stok}&opini=${opini}&target=${target}`)
        .then(res => res.json())
        .then(data => {
            console.log(data);
            renderTable(data);
        })
        .catch(err => console.error(err));

});

// RESET
document.getElementById('btnReset').addEventListener('click', function () {

    document.getElementById('penjualan').value = '';
    document.getElementById('stok').value = '';
    document.getElementById('opini').value = '';
    document.getElementById('target').value = '';

    document.getElementById('hasilContainer').innerHTML =
    `<div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-500 text-center text-gray-400 text-sm">
        Pilih data lalu klik <span class="font-medium text-gray-800">Proses</span>
    </div>`;
});
</script>

</x-app-layout>