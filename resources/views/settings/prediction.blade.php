<x-app-layout>  

@php
    // --- PROMPT DEWA KIMI DEFAULT (Jika di database/settings belum ada) ---
    $defaultKimiPrompt = <<<EOD
Kamu adalah sistem analisis sentimen NLP canggih untuk pasar otomotif roda dua Indonesia (khusus Banten). 
TUGAS UTAMA: Analisis opini yang diberikan dan kembalikan HANYA format JSON array string.

═══════════════════════════════════════════════════════════════════
ATURAN PENTING (BOBOT & DOMINASI):
═══════════════════════════════════════════════════════════════════

1. BOBOT POSISI KALIMAT:
   - Kalimat TERAKHIR memang lebih penting, TAPI tidak selalu menang.
   - Jika kalimat AWAL ada kata kuat positif (idaman, sultan, mantap jiwa, keren pol, joss, puas banget, super nyaman) → tetap POSITIF meskipun akhir ada 'cuma/sayang'.
   - Jika kalimat AWAL cuma 'lumayan/standar/biasa aja' + akhir ada keluhan → NEGATIF.
   - Jika AWAL positif moderat + akhir keluhan ringan yang dimaklumi → NETRAL.

2. KATA KUNCI POSITIF KUAT (Auto-Positif, kecuali ada nyesel/kecewa/komplain):
   idaman, sultan, mantap jiwa, keren pol, joss, best buy, sangat memuaskan, 
   super nyaman, puas banget, nggak nyesel, nggak salah pilih, nggak diragukan,
   lebih dari ekspektasi, sempurna, luar biasa, juara, legendaris

3. KATA KUNCI NEGATIF KUAT (Auto-Negatif):
   nyesel, kecewa, komplain, gredek, jedug, rewel, rusak, parah (negatif context)
   
4. KATA KUNCI NEGATIF MODERAT (Tergantung konteks):
   keras, boros, kurang, kekecilan, kegedean, tipis, panas, pegal, getar, kasar, 
   lemot, berat, licin, nungging, kurang suka, kurang greget, kurang terang, 
   kurang stabil, kurang responsif, terlalu kecil, tinggi banget, inden lama
   
   → Jika ada kata maklumi (wajar, masih oke, terbayar, selebihnya oke) → NETRAL
   → Jika TIDAK ada maklumi → NEGATIF

5. KATA KUNCI NETRAL (Auto-Netral jika tidak ada kontras kuat):
   biasa aja, standar aja, lumayan, lumayan lah, cukup, cukup oke, ya sudahlah, 
   gitu deh, ya gitu, nggak terlalu spesial, nggak ada masalah, nggak ada komplain,
   gak ada komplain, rutin, normal, sesuai harga

6. ATURAN KHUSUS "TAPI/SAYANG/CUMA/MINUSNYA":
   
   A. Jadi POSITIF jika:
      - Ada kata kuat positif di awal (idaman, sultan, mantap jiwa, keren pol)
      - Keluhan di akhir ringan & tidak mengurangi fungsi utama
      - Contoh: "Sultan matic! Nyaman parah... cuma bodinya kegedean" → POSITIF
   
   B. Jadi NEGATIF jika:
      - Kalimat awal cuma pujian biasa (desain gagah, sporty banget, motor ganteng)
      - Keluhan di akhir mengurangi kenyamanan/fungsi (shock keras, boros, pegal, tipis panas)
      - Contoh: "Desain gagah, tapi shock belakang agak keras" → NEGATIF
      - Contoh: "Sporty banget! Tapi bensin lumayan boros" → NEGATIF
   
   C. Jadi NETRAL jika:
      - Ada kata positif moderat + keluhan ringan yang dimaklumi
      - Contoh: "Puas banget. Sayang colokan charger masih model bulet" → NETRAL
      - Contoh: "Tarikan bawah enak. Sayangnya lampu sein masih bohlam" → NETRAL
      - Contoh: "Boros bensin... tapi wajar kapasitas mesin 155cc" → NETRAL
      - Contoh: "Cukup memuaskan, cuma sayang tangki kekecilan" → NETRAL

7. ATURAN KHUSUS "Lumayan/Biasa aja + Tapi":
   - "Lumayan... tapi joknya agak keras" → NEGATIF (karena awal sudah lemah)
   - "Lumayan... tapi kalau udah jalan di atas 40kmh enak" → NETRAL (ending positif)
   - "Biasa aja... joknya agak lebar jadi jinjit" → NETRAL (keluhan ringan)

8. ATURAN KHUSUS "PARAH":
   - "Boros bensin parah" → NEGATIF (parah = kuat negatif)
   - "Irit bensin parah" → POSITIF (parah di konteks positif = sangat)
   - "Macet parah" → NETRAL (parah menggambarkan situasi, bukan sentimen)

9. ATURAN KHUSUS "INDEN/LAMA":
   - "Inden lumayan lama 2 minggu, tapi terbayar pas motor dateng. Keren pol!" → POSITIF
   - "Inden lama, pelayanan lambat" → NEGATIF (kalau tidak ada kontras positif)
═══════════════════════════════════════════════════════════════════
FORMAT OUTPUT (WAJIB):
═══════════════════════════════════════════════════════════════════

- Output HARUS JSON array string sederhana.
- Elemen HANYA: "positif", "negatif", "netral".
- Urutan SAMA PERSIS dengan urutan opini input.
- DILARANG menambahkan teks, penjelasan, markdown, atau key object.
- DILARANG memikirkan panjang jawaban, fokus ke AKURASI setiap item.

CONTOH OUTPUT BENAR:
["positif", "negatif", "netral", "positif"]
EOD;
@endphp

<div style="max-width: 720px; margin: 0 auto; padding: 24px 16px; position: relative;">

    {{-- HEADER --}}
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
        <a href="{{ route('prediction.index') }}" style="
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            text-decoration: none;
            font-size: 1rem;
            color: #374151;
        ">←</a>
        <div>
            <div style="font-size: 1.1rem; font-weight: 700; color: #111827;">Konfigurasi Sistem Prediksi</div>
            <div style="font-size: 0.75rem; color: #9ca3af;">Bobot SAW, multiplier kategori, prompt NLP, dan aturan bisnis</div>
        </div>
    </div>

    <form method="POST" action="{{ route('settings.prediction.update') }}">
        @if ($errors->any())
            <div style="background: #fef2f2; border: 1px solid #fca5a5; border-left: 4px solid #dc2626; border-radius: 0 8px 8px 0; padding: 16px; margin-bottom: 20px;">
                <strong style="color: #dc2626; font-size: 1rem;">Waduh Baginda, proses simpan digagalkan sistem!</strong>
                <ul style="color: #dc2626; margin-top: 8px; font-size: 0.85rem; padding-left: 16px; list-style-type: disc;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @csrf

        {{-- ALERT ERROR --}}
        @if ($errors->has('saw'))
            <div style="
                background: #fef2f2;
                border: 1px solid #fca5a5;
                border-left: 4px solid #dc2626;
                border-radius: 0 8px 8px 0;
                padding: 12px 16px;
                margin-bottom: 20px;
                color: #dc2626;
                font-size: 0.875rem;
            ">⚠️ {{ $errors->first('saw') }}</div>
        @endif

        {{-- SECTION 1: BOBOT SAW --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 16px;">
            <div style="font-size: 1rem; font-weight: 700; color: #111827; margin-bottom: 4px;">Bobot Kriteria SAW (C1–C4)</div>
            <div style="font-size: 0.8rem; color: #9ca3af; margin-bottom: 20px;">Total bobot harus = 1.00</div>

            @php
                $sawFields = [
                    ['key' => 'saw_w1', 'label' => 'C1 — Penjualan',  'desc' => 'Bobot data penjualan historis',    'value' => env('SAW_W1', 0.45)],
                    ['key' => 'saw_w2', 'label' => 'C2 — Stok Sisa',  'desc' => 'Bobot kondisi stok gudang',        'value' => env('SAW_W2', 0.25)],
                    ['key' => 'saw_w3', 'label' => 'C3 — Sentimen',   'desc' => 'Bobot opini pasar dari LLM',       'value' => env('SAW_W3', 0.15)],
                    ['key' => 'saw_w4', 'label' => 'C4 — Trend',      'desc' => 'Bobot tren pasar Serang-Banten',   'value' => env('SAW_W4', 0.15)],
                ];
            @endphp

            @foreach ($sawFields as $field)
                <div style="margin-bottom: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <div>
                            <span style="font-size: 0.875rem; font-weight: 600; color: #374151;">{{ $field['label'] }}</span>
                            <span style="font-size: 0.75rem; color: #9ca3af; margin-left: 8px;">{{ $field['desc'] }}</span>
                        </div>
                        <span id="label_{{ $field['key'] }}" style="font-size: 0.875rem; font-weight: 700; color: #2563eb; min-width: 40px; text-align: right;">
                            {{ number_format($field['value'], 2) }}
                        </span>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <button type="button" onclick="adjustSlider('{{ $field['key'] }}', false, false, undefined)" style="width:28px; height:28px; background:#f3f4f6; border:1px solid #d1d5db; border-radius:4px; cursor:pointer; font-weight:bold; color:#374151;">-</button>
                        <input type="range" name="{{ $field['key'] }}" id="{{ $field['key'] }}"
                            min="0" max="1" step="0.05" value="{{ $field['value'] }}"
                            style="flex: 1;"
                            oninput="updateLabel('{{ $field['key'] }}', this.value, false); recalcTotal()">
                        <button type="button" onclick="adjustSlider('{{ $field['key'] }}', true, false, undefined)" style="width:28px; height:28px; background:#f3f4f6; border:1px solid #d1d5db; border-radius:4px; cursor:pointer; font-weight:bold; color:#374151;">+</button>
                    </div>
                </div>
            @endforeach

            <div style="display: flex; justify-content: space-between; align-items: center; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px 14px; margin-top: 8px;">
                <span style="font-size: 0.875rem; color: #374151; font-weight: 600;">Total Bobot SAW</span>
                <span id="total_saw" style="font-size: 1rem; font-weight: 700; color: #059669;">1.00</span>
            </div>
        </div>

        {{-- SECTION 2: CATEGORY MULTIPLIER --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 16px;">
            <div style="font-size: 1rem; font-weight: 700; color: #111827; margin-bottom: 4px;">Multiplier Kategori Pasar</div>
            <div style="font-size: 0.8rem; color: #9ca3af; margin-bottom: 20px;">Sesuaikan jika tren pasar Serang-Banten berubah</div>

            @php
                $catFields = [
                    ['key' => 'weight_fast',    'label' => 'Matic (Fast Moving)',    'desc' => 'Dominasi 70–90% pasar lokal',          'value' => env('WEIGHT_FAST_MOVING', 1.2), 'color' => '#059669'],
                    ['key' => 'weight_premium', 'label' => 'Premium / Sport Besar',  'desc' => 'Segmen terbatas di Serang-Banten',      'value' => env('WEIGHT_PREMIUM', 0.6),     'color' => '#d97706'],
                    ['key' => 'weight_slow',    'label' => 'Slow Moving / Bebek',    'desc' => 'Permintaan rendah di wilayah ini',      'value' => env('WEIGHT_SLOW_MOVING', 0.4), 'color' => '#dc2626'],
                ];
            @endphp

            @foreach ($catFields as $field)
                <div style="margin-bottom: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <div>
                            <span style="font-size: 0.875rem; font-weight: 600; color: #374151;">{{ $field['label'] }}</span>
                            <span style="font-size: 0.75rem; color: #9ca3af; margin-left: 8px;">{{ $field['desc'] }}</span>
                        </div>
                        <span id="label_{{ $field['key'] }}" style="font-size: 0.875rem; font-weight: 700; color: {{ $field['color'] }}; min-width: 40px; text-align: right;">
                            {{ number_format($field['value'], 1) }}x
                        </span>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <button type="button" onclick="adjustSlider('{{ $field['key'] }}', false, true, undefined)" style="width:28px; height:28px; background:#f3f4f6; border:1px solid #d1d5db; border-radius:4px; cursor:pointer; font-weight:bold; color:#374151;">-</button>
                        <input type="range" name="{{ $field['key'] }}" id="{{ $field['key'] }}"
                            min="0" max="2" step="0.1" value="{{ $field['value'] }}"
                            style="flex: 1;"
                            oninput="updateLabel('{{ $field['key'] }}', this.value, true)">
                        <button type="button" onclick="adjustSlider('{{ $field['key'] }}', true, true, undefined)" style="width:28px; height:28px; background:#f3f4f6; border:1px solid #d1d5db; border-radius:4px; cursor:pointer; font-weight:bold; color:#374151;">+</button>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- 🔥 SECTION 3: CONFIGURATION PROMPT NLP (REVISI DOSEN) 🔥 --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 16px;">
            <div style="font-size: 1rem; font-weight: 700; color: #111827; margin-bottom: 4px;">Konfigurasi Prompt NLP (Gemini)</div>
            <div style="font-size: 0.8rem; color: #9ca3af; margin-bottom: 16px;">Instruksi kecerdasan analisis sentimen otomotif roda dua</div>
            
            {{-- 🔥 SLOT 1: STATUS PROMPT AKTIF 🔥 --}}
            @php
                // Cari nama prompt yang sedang aktif saat ini
                $activePrompt = $daftarPrompt->firstWhere('id', $settings['active_prompt_id']);
            @endphp
            <div style="margin-bottom: 15px; padding: 12px; background: #ecfdf5; border: 1px solid #10b981; border-radius: 8px; display: flex; align-items: center;">
                <div>
                    <strong style="color: #065f46; display: block; font-size: 0.9rem;">Prompt Aktif Saat Ini:</strong>
                    <span style="color: #047857; font-size: 1rem; font-weight: bold;">{{ $activePrompt ? $activePrompt->nama_prompt : 'Belum Ada (Default)' }}</span>
                </div>
            </div>

            {{-- 🔥 SLOT 2: DROPDOWN PILIHAN & BIKIN BARU 🔥 --}}
            <div style="margin-bottom: 15px;">
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Aksi Prompt NLP</label>
                <select name="prompt_action" id="promptAction" onchange="ubahTampilanPrompt()" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px; font-weight: bold; color: #1f2937;">
                    <optgroup label="Daftar Prompt Tersimpan">
                        @foreach($daftarPrompt as $prompt)
                            <option value="{{ $prompt->id }}" data-isi="{{ $prompt->isi_prompt }}" {{ ($settings['active_prompt_id'] == $prompt->id) ? 'selected' : '' }}>
                                Gunakan / Edit: {{ $prompt->nama_prompt }}
                            </option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Tindakan Lain">
                        <option value="new">+ BUAT PROMPT BARU</option>
                    </optgroup>
                </select>
            </div>

            {{-- 🔥 SLOT TAMBAHAN (SEMBUNYI): NAMA PROMPT BARU 🔥 --}}
            <div id="divNamaPromptBaru" style="margin-bottom: 15px; display: none;">
                <label style="font-weight: bold; display: block; margin-bottom: 5px; color: #2563eb;">Masukkan Nama Prompt Baru</label>
                <input type="text" name="nama_prompt_baru" id="namaPromptBaru" placeholder="Contoh: Prompt Khusus Sidang Dosen" style="width: 100%; padding: 8px; border: 1px solid #93c5fd; border-radius: 4px;">
            </div>

            {{-- 🔥 SLOT 3: TEXTAREA ISI PROMPT 🔥 --}}
            <div style="margin-bottom: 8px;">
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Isi Text Prompt</label>
                <textarea name="isi_prompt" id="isiPromptContent" rows="12" style="
                    width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px;
                    font-family: monospace; font-size: 0.8rem; background: #f9fafb;
                    color: #374151; line-height: 1.5; resize: vertical;
                " required></textarea>
                <small style="color: #6b7280; display: block; margin-top: 5px;">*Teks di atas adalah yang akan dikirim ke API Gemini.</small>
            </div>

            
            <p style="font-size: 0.72rem; color: #9ca3af;">*Catatan: Sistem akan otomatis menyisipkan jumlah batch opini di latar belakang.</p>
        </div>

        {{-- SECTION 4: BUSINESS RULES --}}
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 16px;">
            <div style="font-size: 1rem; font-weight: 700; color: #111827; margin-bottom: 4px;">Aturan Bisnis (Business Rules)</div>
            <div style="font-size: 0.8rem; color: #9ca3af; margin-bottom: 20px;">Batas stok dan cap pengadaan per kategori</div>

            @php
                $ruleFields = [
                    [
                        'title' => 'Slow Moving', 'color' => '#dc2626',
                        'fields' => [
                            ['key' => 'rule_slow_overstock_threshold', 'label' => 'Batas overstock (stok ≥ X → di-cap)',     'value' => $settings['rule_slow_overstock_threshold'], 'min' => 0, 'max' => 200, 'step' => 1, 'suffix' => 'unit'],
                            ['key' => 'rule_slow_overstock_cap',       'label' => 'Cap maksimal order saat overstock',        'value' => $settings['rule_slow_overstock_cap'],       'min' => 0, 'max' => 200, 'step' => 1, 'suffix' => 'unit'],
                            ['key' => 'rule_slow_danger_threshold',    'label' => 'Batas danger zone (stok ≤ X → di-boost)', 'value' => $settings['rule_slow_danger_threshold'],    'min' => 0, 'max' => 200, 'step' => 1, 'suffix' => 'unit'],
                            ['key' => 'rule_slow_danger_boost',        'label' => 'Boost unit saat danger zone',              'value' => $settings['rule_slow_danger_boost'],        'min' => 0, 'max' => 200, 'step' => 1, 'suffix' => 'unit'],
                        ]
                    ],
                    [
                        'title' => 'Premium / Sport', 'color' => '#d97706',
                        'fields' => [
                            ['key' => 'rule_premium_overstock_threshold', 'label' => 'Batas overstock (stok ≥ X → di-cap)',     'value' => $settings['rule_premium_overstock_threshold'], 'min' => 0, 'max' => 200, 'step' => 1, 'suffix' => 'unit'],
                            ['key' => 'rule_premium_overstock_cap',       'label' => 'Cap maksimal order saat overstock',        'value' => $settings['rule_premium_overstock_cap'],       'min' => 0, 'max' => 200, 'step' => 1, 'suffix' => 'unit'],
                            ['key' => 'rule_premium_danger_threshold',    'label' => 'Batas danger zone (stok ≤ X → di-boost)', 'value' => $settings['rule_premium_danger_threshold'],    'min' => 0, 'max' => 200, 'step' => 1, 'suffix' => 'unit'],
                            ['key' => 'rule_premium_danger_boost',        'label' => 'Boost unit saat danger zone',              'value' => $settings['rule_premium_danger_boost'],        'min' => 0, 'max' => 200, 'step' => 1, 'suffix' => 'unit'],
                        ]
                    ],
                    [
                        'title' => 'Fast Moving (Matic)', 'color' => '#059669',
                        'fields' => [
                            ['key' => 'rule_fast_overstock_threshold', 'label' => 'Batas overstock (stok ≥ X → di-cap)',     'value' => $settings['rule_fast_overstock_threshold'], 'min' => 0, 'max' => 200, 'step' => 1, 'suffix' => 'unit'],
                            ['key' => 'rule_fast_overstock_cap',       'label' => 'Cap maksimal order saat overstock',        'value' => $settings['rule_fast_overstock_cap'],       'min' => 0, 'max' => 200, 'step' => 1, 'suffix' => 'unit'],
                            ['key' => 'rule_fast_danger_threshold',    'label' => 'Batas danger zone (stok ≤ X → di-boost)', 'value' => $settings['rule_fast_danger_threshold'],    'min' => 0, 'max' => 200, 'step' => 1, 'suffix' => 'unit'],
                            ['key' => 'rule_fast_danger_boost',        'label' => 'Boost unit saat danger zone',              'value' => $settings['rule_fast_danger_boost'],        'min' => 0, 'max' => 200, 'step' => 1, 'suffix' => 'unit'],
                        ]
                    ],
                    [
                        'title' => 'Motor Baru', 'color' => '#2563eb',
                        'fields' => [
                            ['key' => 'rule_new_fast_min',  'label' => 'Min display motor baru matic',     'value' => $settings['rule_new_fast_min'],  'min' => 0, 'max' => 200, 'step' => 1, 'suffix' => 'unit'],
                            ['key' => 'rule_new_other_min', 'label' => 'Min display motor baru non-matic', 'value' => $settings['rule_new_other_min'], 'min' => 0, 'max' => 200, 'step' => 1, 'suffix' => 'unit'],
                        ]
                    ],
                ];
            @endphp

            @foreach ($ruleFields as $section)
                <div style="margin-bottom: 20px;">
                    <div style="
                        display: inline-block;
                        background: {{ $section['color'] }}18;
                        border: 1px solid {{ $section['color'] }}44;
                        color: {{ $section['color'] }};
                        font-size: 0.72rem;
                        font-weight: 700;
                        padding: 3px 10px;
                        border-radius: 20px;
                        margin-bottom: 12px;
                        letter-spacing: 0.3px;
                    ">{{ $section['title'] }}</div>

                    @foreach ($section['fields'] as $field)
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <span style="font-size: 0.8rem; color: #374151; flex: 1;">{{ $field['label'] }}</span>
                            <div style="display: flex; align-items: center; gap: 8px; min-width: 200px; justify-content: flex-end;">
                                <button type="button" onclick="adjustSlider('{{ $field['key'] }}', false, false, '{{ $field['suffix'] }}')" style="width:24px; height:24px; background:#f3f4f6; border:1px solid #d1d5db; border-radius:4px; cursor:pointer; font-weight:bold; color:#374151; display:flex; align-items:center; justify-content:center;">-</button>
                                
                                <input type="range" name="{{ $field['key'] }}" id="{{ $field['key'] }}"
                                    min="{{ $field['min'] }}" max="{{ $field['max'] }}" step="{{ $field['step'] }}"
                                    value="{{ $field['value'] }}"
                                    style="width: 100px;"
                                    oninput="updateRuleLabel('{{ $field['key'] }}', this.value, '{{ $field['suffix'] }}')">
                                    
                                <button type="button" onclick="adjustSlider('{{ $field['key'] }}', true, false, '{{ $field['suffix'] }}')" style="width:24px; height:24px; background:#f3f4f6; border:1px solid #d1d5db; border-radius:4px; cursor:pointer; font-weight:bold; color:#374151; display:flex; align-items:center; justify-content:center;">+</button>

                                <span id="label_{{ $field['key'] }}" style="font-size: 0.875rem; font-weight: 700; color: {{ $section['color'] }}; min-width: 55px; text-align: right;">
                                    {{ $field['value'] }} {{ $field['suffix'] }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
        <div style="margin-bottom: 25px; padding: 15px; border: 1px solid #d1d5db; border-radius: 8px; background-color: #f9fafb;">
    <h4 style="margin-top: 0; color: #1f2937; margin-bottom: 8px;">Hak Istimewa "Gap Filler" (Auto-Balance)</h4>
    <p style="font-size: 0.85rem; color: #6b7280; margin-bottom: 12px; line-height: 1.5;">
        Pilih kategori mana saja yang berhak menampung/dipotong unit saat terjadi selisih target.<br>
        <span style="color: #047857; font-weight: 500;">Info: Jika tidak ada satupun yang dicentang, fitur Auto-Balance akan MATI (unit sisa akan dibiarkan/dibuang).</span>
    </p>
    
    <div style="display: flex; gap: 20px;">
        <label style="cursor: pointer; font-weight: bold; color: #065f46;">
            <input type="checkbox" name="gap_filler_fast" value="true" {{ ($settings['gap_filler_fast'] ?? 'true') == 'true' ? 'checked' : '' }}>
            Fast Moving / Matic
        </label>
        
        <label style="cursor: pointer; font-weight: bold; color: #d97706;">
            <input type="checkbox" name="gap_filler_premium" value="true" {{ ($settings['gap_filler_premium'] ?? 'false') == 'true' ? 'checked' : '' }}>
            Premium / Motor 250cc
        </label>
        
        <label style="cursor: pointer; font-weight: bold; color: #dc2626;">
            <input type="checkbox" name="gap_filler_slow" value="true" {{ ($settings['gap_filler_slow'] ?? 'false') == 'true' ? 'checked' : '' }}>
            Slow Moving / Selain Matic
        </label>
    </div>
</div>

        {{-- SUBMIT + RESET --}}
        <div style="display: flex; gap: 10px; margin-top: 4px;">
            <button type="submit" style="
                flex: 1;
                background: #2563eb;
                color: white;
                border: none;
                border-radius: 8px;
                padding: 14px;
                font-size: 0.9rem;
                font-weight: 700;
                cursor: pointer;
            ">Simpan Konfigurasi</button>

            <button type="button" onclick="openResetModal()" style="
                background: #f9fafb;
                color: #374151;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                padding: 14px 20px;
                font-size: 0.9rem;
                font-weight: 700;
                cursor: pointer;
                white-space: nowrap;
            ">Reset Default</button>
        </div>
    </form>
</div>

{{-- MODAL POPUP RESET CUSTOM --}}
<div id="customResetModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); z-index: 100; align-items: center; justify-content: center; backdrop-filter: blur(2px);">
    <div style="background: white; border-radius: 12px; padding: 24px; max-width: 340px; width: 90%; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.15); animation: popIn 0.3s ease-out;">
        <div style="width: 50px; height: 50px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold; margin: 0 auto 16px;">!</div>
        <h3 style="margin: 0 0 8px; font-size: 1.25rem; color: #111827; font-weight: 700;">Reset Konfigurasi?</h3>
        <p style="margin: 0 0 24px; font-size: 0.875rem; color: #6b7280; line-height: 1.5;">Semua bobot, prompt NLP, dan aturan bisnis akan dikembalikan ke nilai awal (default). Aksi ini tidak dapat dibatalkan.</p>
        
        <div style="display: flex; gap: 12px;">
            <button onclick="closeResetModal()" style="flex: 1; padding: 12px; border: 1px solid #d1d5db; background: white; color: #374151; border-radius: 8px; font-weight: 600; cursor: pointer;">Batal</button>
            <button onclick="executeReset()" style="flex: 1; padding: 12px; border: none; background: #dc2626; color: white; border-radius: 8px; font-weight: 600; cursor: pointer;">Ya, Reset</button>
        </div>
    </div>
</div>

<style>
    @keyframes popIn {
        0% { transform: scale(0.9); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>

<script>
// --- LOGIKA PLUS MINUS SLIDER ---
function adjustSlider(id, isIncrement, isMultiplier, suffix) {
    const el = document.getElementById(id);
    if (!el) return;
    const step = parseFloat(el.step) || 1;
    const max = parseFloat(el.max);
    const min = parseFloat(el.min);
    let val = parseFloat(el.value);

    if (isIncrement) {
        if (val + step <= max) val += step;
    } else {
        if (val - step >= min) val -= step;
    }

    el.value = (step < 1) ? val.toFixed(2) : Math.round(val);

    if (suffix !== undefined) {
        updateRuleLabel(id, el.value, suffix);
    } else {
        updateLabel(id, el.value, isMultiplier);
        recalcTotal();
    }
}

// --- LOGIKA UPDATE LABEL TEXT ---
function updateLabel(key, val, isMultiplier) {
    const el = document.getElementById('label_' + key);
    if (!el) return;
    if (isMultiplier) {
        el.textContent = parseFloat(val).toFixed(1) + 'x';
    } else {
        el.textContent = parseFloat(val).toFixed(2);
    }
}

function updateRuleLabel(key, val, suffix) {
    const el = document.getElementById('label_' + key);
    if (el) el.textContent = val + ' ' + suffix;
}

// --- LOGIKA TOTAL SAW 1.00 ---
function recalcTotal() {
    const keys = ['saw_w1', 'saw_w2', 'saw_w3', 'saw_w4'];
    let total = 0;
    keys.forEach(k => {
        const el = document.getElementById(k);
        if (el) total += parseFloat(el.value || 0);
    });
    const el = document.getElementById('total_saw');
    if (el) {
        const rounded = Math.round(total * 100) / 100;
        el.textContent = rounded.toFixed(2);
        el.style.color = rounded === 1.00 ? '#059669' : '#dc2626';
    }
}

// --- LOGIKA MODAL POPUP RESET ---
const DEFAULTS = {
    saw_w1: 0.45, saw_w2: 0.25, saw_w3: 0.15, saw_w4: 0.15,
    weight_fast: 1.2, weight_premium: 0.6, weight_slow: 0.4,
    rule_slow_overstock_threshold: 4,  rule_slow_overstock_cap: 0,
    rule_slow_danger_threshold: 1,     rule_slow_danger_boost: 3,
    rule_premium_overstock_threshold: 3, rule_premium_overstock_cap: 0,
    rule_premium_danger_threshold: 1,  rule_premium_danger_boost: 3,
    rule_fast_overstock_threshold: 15, rule_fast_overstock_cap: 10,
    rule_fast_danger_threshold: 5,     rule_fast_danger_boost: 5,
    rule_new_fast_min: 15,             rule_new_other_min: 4,
    // 🔥 DEFAULT PROMPT SUPER DEWA DARI KIMI DI SCRIPT JS 🔥
    sys_prompt: `Kamu adalah sistem analisis sentimen NLP canggih untuk pasar otomotif roda dua Indonesia (khusus Banten). 
TUGAS UTAMA: Analisis opini yang diberikan dan kembalikan HANYA format JSON array string.

═══════════════════════════════════════════════════════════════════
ATURAN PENTING (BOBOT & DOMINASI):
═══════════════════════════════════════════════════════════════════

1. BOBOT POSISI KALIMAT:
   - Kalimat TERAKHIR memang lebih penting, TAPI tidak selalu menang.
   - Jika kalimat AWAL ada kata kuat positif (idaman, sultan, mantap jiwa, keren pol, joss, puas banget, super nyaman) → tetap POSITIF meskipun akhir ada 'cuma/sayang'.
   - Jika kalimat AWAL cuma 'lumayan/standar/biasa aja' + akhir ada keluhan → NEGATIF.
   - Jika AWAL positif moderat + akhir keluhan ringan yang dimaklumi → NETRAL.

2. KATA KUNCI POSITIF KUAT (Auto-Positif, kecuali ada nyesel/kecewa/komplain):
   idaman, sultan, mantap jiwa, keren pol, joss, best buy, sangat memuaskan, 
   super nyaman, puas banget, nggak nyesel, nggak salah pilih, nggak diragukan,
   lebih dari ekspektasi, sempurna, luar biasa, juara, legendaris

3. KATA KUNCI NEGATIF KUAT (Auto-Negatif):
   nyesel, kecewa, komplain, gredek, jedug, rewel, rusak, parah (negatif context)
   
4. KATA KUNCI NEGATIF MODERAT (Tergantung konteks):
   keras, boros, kurang, kekecilan, kegedean, tipis, panas, pegal, getar, kasar, 
   lemot, berat, licin, nungging, kurang suka, kurang greget, kurang terang, 
   kurang stabil, kurang responsif, terlalu kecil, tinggi banget, inden lama
   
   → Jika ada kata maklumi (wajar, masih oke, terbayar, selebihnya oke) → NETRAL
   → Jika TIDAK ada maklumi → NEGATIF

5. KATA KUNCI NETRAL (Auto-Netral jika tidak ada kontras kuat):
   biasa aja, standar aja, lumayan, lumayan lah, cukup, cukup oke, ya sudahlah, 
   gitu deh, ya gitu, nggak terlalu spesial, nggak ada masalah, nggak ada komplain,
   gak ada komplain, rutin, normal, sesuai harga

6. ATURAN KHUSUS "TAPI/SAYANG/CUMA/MINUSNYA":
   
   A. Jadi POSITIF jika:
      - Ada kata kuat positif di awal (idaman, sultan, mantap jiwa, keren pol)
      - Keluhan di akhir ringan & tidak mengurangi fungsi utama
      - Contoh: "Sultan matic! Nyaman parah... cuma bodinya kegedean" → POSITIF
   
   B. Jadi NEGATIF jika:
      - Kalimat awal cuma pujian biasa (desain gagah, sporty banget, motor ganteng)
      - Keluhan di akhir mengurangi kenyamanan/fungsi (shock keras, boros, pegal, tipis panas)
      - Contoh: "Desain gagah, tapi shock belakang agak keras" → NEGATIF
      - Contoh: "Sporty banget! Tapi bensin lumayan boros" → NEGATIF
   
   C. Jadi NETRAL jika:
      - Ada kata positif moderat + keluhan ringan yang dimaklumi
      - Contoh: "Puas banget. Sayang colokan charger masih model bulet" → NETRAL
      - Contoh: "Tarikan bawah enak. Sayangnya lampu sein masih bohlam" → NETRAL
      - Contoh: "Boros bensin... tapi wajar kapasitas mesin 155cc" → NETRAL
      - Contoh: "Cukup memuaskan, cuma sayang tangki kekecilan" → NETRAL

7. ATURAN KHUSUS "Lumayan/Biasa aja + Tapi":
   - "Lumayan... tapi joknya agak keras" → NEGATIF (karena awal sudah lemah)
   - "Lumayan... tapi kalau udah jalan di atas 40kmh enak" → NETRAL (ending positif)
   - "Biasa aja... joknya agak lebar jadi jinjit" → NETRAL (keluhan ringan)

8. ATURAN KHUSUS "PARAH":
   - "Boros bensin parah" → NEGATIF (parah = kuat negatif)
   - "Irit bensin parah" → POSITIF (parah di konteks positif = sangat)
   - "Macet parah" → NETRAL (parah menggambarkan situasi, bukan sentimen)

9. ATURAN KHUSUS "INDEN/LAMA":
   - "Inden lumayan lama 2 minggu, tapi terbayar pas motor dateng. Keren pol!" → POSITIF
   - "Inden lama, pelayanan lambat" → NEGATIF (kalau tidak ada kontras positif)
═══════════════════════════════════════════════════════════════════
FORMAT OUTPUT (WAJIB):
═══════════════════════════════════════════════════════════════════

- Output HARUS JSON array string sederhana.
- Elemen HANYA: "positif", "negatif", "netral".
- Urutan SAMA PERSIS dengan urutan opini input.
- DILARANG menambahkan teks, penjelasan, markdown, atau key object.
- DILARANG memikirkan panjang jawaban, fokus ke AKURASI setiap item.

CONTOH OUTPUT BENAR:
["positif", "negatif", "netral", "positif"]`
};

function openResetModal() {
    document.getElementById('customResetModal').style.display = 'flex';
}

function closeResetModal() {
    document.getElementById('customResetModal').style.display = 'none';
}

function executeReset() {
    closeResetModal();

    Object.entries(DEFAULTS).forEach(([key, val]) => {
        const el = document.getElementById(key);
        if (!el) return;
        el.value = val;

        const label = document.getElementById('label_' + key);
        if (!label) return; // Bakal bypass sys_prompt karena gak punya label text saw

        if (key.startsWith('weight_')) {
            label.textContent = parseFloat(val).toFixed(1) + 'x';
        } else if (key.startsWith('saw_')) {
            label.textContent = parseFloat(val).toFixed(2);
        } else {
            label.textContent = val + ' unit';
        }
    });

    recalcTotal();
}


            
                function ubahTampilanPrompt() {
                    let select = document.getElementById('promptAction');
                    let selectedOption = select.options[select.selectedIndex];
                    let contentBox = document.getElementById('isiPromptContent');
                    let newNameDiv = document.getElementById('divNamaPromptBaru');

                    if (select.value === 'new') {
                        // Kalau pilih bikin baru, kosongkan textarea dan munculkan input nama
                        contentBox.value = '';
                        contentBox.placeholder = 'Ketik prompt baru yang sakti di sini, Baginda...';
                        newNameDiv.style.display = 'block';
                    } else {
                        // Kalau pilih prompt lama, langsung isi textarea dengan datanya
                        contentBox.value = selectedOption.getAttribute('data-isi');
                        newNameDiv.style.display = 'none';
                    }
                }
                // Jalankan sihir ini otomatis saat web pertama kali dibuka
                window.onload = ubahTampilanPrompt;
            
</script>

</x-app-layout>