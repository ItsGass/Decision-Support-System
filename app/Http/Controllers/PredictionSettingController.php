<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;
use App\Models\Prompt;

class PredictionSettingController extends Controller
{
    // =========================================================================
    // INDEX — Tampil halaman settings
    // =========================================================================
    public function index()
    {
        $saved = session('pred_settings', []);

        // 🔥 AUTO-SEED: Kalau tabel prompts masih kosong, otomatis buatin 1 prompt default!
        if (Prompt::count() === 0) {
            Prompt::create([
                'nama_prompt' => 'Prompt Default Dewa Kimi',
                'isi_prompt'  => $this->getDefaultPrompt()
            ]);
        }

        // 🔥 AMBIL SEMUA DAFTAR PROMPT UNTUK DROPDOWN
        $daftarPrompt = Prompt::all();

        // 🔥 AMBIL ID PROMPT YANG SEDANG AKTIF DARI SETTINGS
        $activePromptId = Setting::where('key', 'active_prompt_id')->value('value');

        $settings = [
            'saw_w1'                           => $saved['saw_w1']                           ?? (float) env('SAW_W1', 0.45),
            'saw_w2'                           => $saved['saw_w2']                           ?? (float) env('SAW_W2', 0.25),
            'saw_w3'                           => $saved['saw_w3']                           ?? (float) env('SAW_W3', 0.15),
            'saw_w4'                           => $saved['saw_w4']                           ?? (float) env('SAW_W4', 0.15),
            'weight_fast'                      => $saved['weight_fast']                      ?? (float) env('WEIGHT_FAST_MOVING', 1.2),
            'weight_premium'                   => $saved['weight_premium']                   ?? (float) env('WEIGHT_PREMIUM', 0.6),
            'weight_slow'                      => $saved['weight_slow']                      ?? (float) env('WEIGHT_SLOW_MOVING', 0.4),
            'rule_slow_overstock_threshold'    => $saved['rule_slow_overstock_threshold']    ?? (int) env('RULE_SLOW_OVERSTOCK_THRESHOLD', 5),
            'rule_slow_overstock_cap'          => $saved['rule_slow_overstock_cap']          ?? (int) env('RULE_SLOW_OVERSTOCK_CAP', 0),
            'rule_slow_danger_threshold'       => $saved['rule_slow_danger_threshold']       ?? (int) env('RULE_SLOW_DANGER_THRESHOLD', 0),
            'rule_slow_danger_boost'           => $saved['rule_slow_danger_boost']           ?? (int) env('RULE_SLOW_DANGER_BOOST', 0),
            'rule_premium_overstock_threshold' => $saved['rule_premium_overstock_threshold'] ?? (int) env('RULE_PREMIUM_OVERSTOCK_THRESHOLD', 2),
            'rule_premium_overstock_cap'       => $saved['rule_premium_overstock_cap']       ?? (int) env('RULE_PREMIUM_OVERSTOCK_CAP', 0),
            'rule_premium_danger_threshold'    => $saved['rule_premium_danger_threshold']    ?? (int) env('RULE_PREMIUM_DANGER_THRESHOLD', 0),
            'rule_premium_danger_boost'        => $saved['rule_premium_danger_boost']        ?? (int) env('RULE_PREMIUM_DANGER_BOOST', 0),
            'rule_fast_overstock_threshold'    => $saved['rule_fast_overstock_threshold']    ?? (int) env('RULE_FAST_OVERSTOCK_THRESHOLD', 15),
            'rule_fast_overstock_cap'          => $saved['rule_fast_overstock_cap']          ?? (int) env('RULE_FAST_OVERSTOCK_CAP', 10),
            'rule_fast_danger_threshold'       => $saved['rule_fast_danger_threshold']       ?? (int) env('RULE_FAST_DANGER_THRESHOLD', 5),
            'rule_fast_danger_boost'           => $saved['rule_fast_danger_boost']           ?? (int) env('RULE_FAST_DANGER_BOOST', 5),
            'rule_new_fast_min'                => $saved['rule_new_fast_min']                ?? (int) env('RULE_NEW_FAST_MIN_DISPLAY', 15),
            'rule_new_other_min'               => $saved['rule_new_other_min']               ?? (int) env('RULE_NEW_OTHER_MIN_DISPLAY', 5),
            'active_prompt_id'                 => $activePromptId, 
            
            // 🔥 BACA STATUS GAP FILLER DARI ENV (HANYA BACA, BUKAN REQUEST)
            'gap_filler_fast'                  => $saved['gap_filler_fast']                  ?? env('GAP_FILLER_FAST', 'true'),
            'gap_filler_premium'               => $saved['gap_filler_premium']               ?? env('GAP_FILLER_PREMIUM', 'false'),
            'gap_filler_slow'                  => $saved['gap_filler_slow']                  ?? env('GAP_FILLER_SLOW', 'false'),
        ];

        session()->forget('pred_settings');

        // 🔥 Kirim daftarPrompt ke Blade agar bisa dilooping di dropdown
        return view('settings.prediction', compact('settings', 'daftarPrompt'));
    }

    // =========================================================================
    // UPDATE — Simpan konfigurasi
    // =========================================================================
    // =========================================================================
    // UPDATE — Simpan konfigurasi
    // =========================================================================
    public function update(Request $request)
    {
        $request->validate([
            'saw_w1'                           => 'required|numeric|min:0|max:1',
            'saw_w2'                           => 'required|numeric|min:0|max:1',
            'saw_w3'                           => 'required|numeric|min:0|max:1',
            'saw_w4'                           => 'required|numeric|min:0|max:1',
            'weight_fast'                      => 'required|numeric|min:0|max:3',
            'weight_premium'                   => 'required|numeric|min:0|max:3',
            'weight_slow'                      => 'required|numeric|min:0|max:3',
            'rule_slow_overstock_threshold'    => 'required|integer|min:0',
            'rule_slow_overstock_cap'          => 'required|integer|min:0',
            'rule_slow_danger_threshold'       => 'required|integer|min:0',
            'rule_slow_danger_boost'           => 'required|integer|min:0',
            'rule_premium_overstock_threshold' => 'required|integer|min:0',
            'rule_premium_overstock_cap'       => 'required|integer|min:0',
            'rule_premium_danger_threshold'    => 'required|integer|min:0',
            'rule_premium_danger_boost'        => 'required|integer|min:0',
            'rule_fast_overstock_threshold'    => 'required|integer|min:0',
            'rule_fast_overstock_cap'          => 'required|integer|min:0',
            'rule_fast_danger_threshold'       => 'required|integer|min:0',
            'rule_fast_danger_boost'           => 'required|integer|min:0',
            'rule_new_fast_min'                => 'required|integer|min:0',
            'rule_new_other_min'               => 'required|integer|min:0',
            
            // 🔥 VALIDASI BARU UNTUK FITUR 3 SLOT BANK PROMPT
            'prompt_action'                    => 'required', 
            'isi_prompt'                       => 'required|string',
            'nama_prompt_baru'                 => 'required_if:prompt_action,new' // Wajib diisi kalau pilih bikin baru
        ]);

        // Validasi total bobot SAW harus = 1.00
        $totalSaw = $request->saw_w1 + $request->saw_w2 + $request->saw_w3 + $request->saw_w4;
        if (round($totalSaw, 2) !== 1.00) {
            return back()
                ->withErrors(['saw' => 'Total bobot SAW harus = 1.00. Saat ini: ' . round($totalSaw, 2)])
                ->withInput();
        }

        // ==========================================================
        // 🔥 LOGIKA SIMPAN BANK PROMPT (BARU & UPDATE)
        // ==========================================================
        if ($request->prompt_action === 'new') {
            // 1. Jika Bikin Baru: Masukkan ke tabel prompts sebagai data baru
            $newPrompt = \App\Models\Prompt::create([
                'nama_prompt' => $request->nama_prompt_baru,
                'isi_prompt'  => $request->isi_prompt
            ]);
            
            // Set prompt baru ini sebagai prompt yang aktif dipakai sistem
            Setting::updateOrCreate(
                ['key' => 'active_prompt_id'], 
                ['value' => $newPrompt->id]
            );
            
        } else {
            // 2. Jika Mengedit Prompt Lama: Update isinya di tabel prompts
            $promptLama = \App\Models\Prompt::find($request->prompt_action);
            if ($promptLama) {
                $promptLama->update(['isi_prompt' => $request->isi_prompt]);
            }
            
            // Set prompt lama ini (yang barusan diedit) sebagai prompt aktif
            Setting::updateOrCreate(
                ['key' => 'active_prompt_id'], 
                ['value' => $request->prompt_action]
            );
        }

        // ==========================================================
        // 🔥 SIMPAN ATURAN SPK KE .ENV
        // ==========================================================
        $this->updateEnv([
            'SAW_W1'                           => $request->saw_w1,
            'SAW_W2'                           => $request->saw_w2,
            'SAW_W3'                           => $request->saw_w3,
            'SAW_W4'                           => $request->saw_w4,
            'WEIGHT_FAST_MOVING'               => $request->weight_fast,
            'WEIGHT_PREMIUM'                   => $request->weight_premium,
            'WEIGHT_SLOW_MOVING'               => $request->weight_slow,
            'RULE_SLOW_OVERSTOCK_THRESHOLD'    => $request->rule_slow_overstock_threshold,
            'RULE_SLOW_OVERSTOCK_CAP'          => $request->rule_slow_overstock_cap,
            'RULE_SLOW_DANGER_THRESHOLD'       => $request->rule_slow_danger_threshold,
            'RULE_SLOW_DANGER_BOOST'           => $request->rule_slow_danger_boost,
            'RULE_PREMIUM_OVERSTOCK_THRESHOLD' => $request->rule_premium_overstock_threshold,
            'RULE_PREMIUM_OVERSTOCK_CAP'       => $request->rule_premium_overstock_cap,
            'RULE_PREMIUM_DANGER_THRESHOLD'    => $request->rule_premium_danger_threshold,
            'RULE_PREMIUM_DANGER_BOOST'        => $request->rule_premium_danger_boost,
            'RULE_FAST_OVERSTOCK_THRESHOLD'    => $request->rule_fast_overstock_threshold,
            'RULE_FAST_OVERSTOCK_CAP'          => $request->rule_fast_overstock_cap,
            'RULE_FAST_DANGER_THRESHOLD'       => $request->rule_fast_danger_threshold,
            'RULE_FAST_DANGER_BOOST'           => $request->rule_fast_danger_boost,
            'RULE_NEW_FAST_MIN_DISPLAY'        => $request->rule_new_fast_min,
            'RULE_NEW_OTHER_MIN_DISPLAY'       => $request->rule_new_other_min,
            
            // 🔥 SIMPAN PENGATURAN GAP FILLER KE ENV
            'GAP_FILLER_FAST'                  => $request->has('gap_filler_fast') ? 'true' : 'false',
            'GAP_FILLER_PREMIUM'               => $request->has('gap_filler_premium') ? 'true' : 'false',
            'GAP_FILLER_SLOW'                  => $request->has('gap_filler_slow') ? 'true' : 'false',
        ]);

        // Paksa PHP baca nilai baru di request ini
        putenv("SAW_W1={$request->saw_w1}");
        putenv("SAW_W2={$request->saw_w2}");
        putenv("SAW_W3={$request->saw_w3}");
        putenv("SAW_W4={$request->saw_w4}");
        putenv("WEIGHT_FAST_MOVING={$request->weight_fast}");
        putenv("WEIGHT_PREMIUM={$request->weight_premium}");
        putenv("WEIGHT_SLOW_MOVING={$request->weight_slow}");
        putenv("RULE_SLOW_OVERSTOCK_THRESHOLD={$request->rule_slow_overstock_threshold}");
        putenv("RULE_SLOW_OVERSTOCK_CAP={$request->rule_slow_overstock_cap}");
        putenv("RULE_SLOW_DANGER_THRESHOLD={$request->rule_slow_danger_threshold}");
        putenv("RULE_SLOW_DANGER_BOOST={$request->rule_slow_danger_boost}");
        putenv("RULE_PREMIUM_OVERSTOCK_THRESHOLD={$request->rule_premium_overstock_threshold}");
        putenv("RULE_PREMIUM_OVERSTOCK_CAP={$request->rule_premium_overstock_cap}");
        putenv("RULE_PREMIUM_DANGER_THRESHOLD={$request->rule_premium_danger_threshold}");
        putenv("RULE_PREMIUM_DANGER_BOOST={$request->rule_premium_danger_boost}");
        putenv("RULE_FAST_OVERSTOCK_THRESHOLD={$request->rule_fast_overstock_threshold}");
        putenv("RULE_FAST_OVERSTOCK_CAP={$request->rule_fast_overstock_cap}");
        putenv("RULE_FAST_DANGER_THRESHOLD={$request->rule_fast_danger_threshold}");
        putenv("RULE_FAST_DANGER_BOOST={$request->rule_fast_danger_boost}");
        putenv("RULE_NEW_FAST_MIN_DISPLAY={$request->rule_new_fast_min}");
        putenv("RULE_NEW_OTHER_MIN_DISPLAY={$request->rule_new_other_min}");
        
        // 🔥 UPDATE MEMORI SISTEM UNTUK GAP FILLER
        putenv("GAP_FILLER_FAST=" . ($request->has('gap_filler_fast') ? 'true' : 'false'));
        putenv("GAP_FILLER_PREMIUM=" . ($request->has('gap_filler_premium') ? 'true' : 'false'));
        putenv("GAP_FILLER_SLOW=" . ($request->has('gap_filler_slow') ? 'true' : 'false'));

        // Simpan ke session agar blade tampil nilai baru setelah redirect (kecuali field prompt)
        session(['pred_settings' => $request->except(['_token', '_method', 'prompt_action', 'isi_prompt', 'nama_prompt_baru'])]);

        return redirect()->route('settings.prediction')
            ->with('success', 'Konfigurasi dan Data Prompt berhasil disimpan, Baginda!');
    }
    // =========================================================================
    // RESET — Kembalikan ke nilai default
    // =========================================================================
    public function reset()
    {
        $defaults = [
            'SAW_W1'                           => 0.45,
            'SAW_W2'                           => 0.25,
            'SAW_W3'                           => 0.15,
            'SAW_W4'                           => 0.15,
            'WEIGHT_FAST_MOVING'               => 1.2,
            'WEIGHT_PREMIUM'                   => 0.6,
            'WEIGHT_SLOW_MOVING'               => 0.4,
            'RULE_SLOW_OVERSTOCK_THRESHOLD'    => 5,
            'RULE_SLOW_OVERSTOCK_CAP'          => 0,
            'RULE_SLOW_DANGER_THRESHOLD'       => 0,
            'RULE_SLOW_DANGER_BOOST'           => 0,
            'RULE_PREMIUM_OVERSTOCK_THRESHOLD' => 2,
            'RULE_PREMIUM_OVERSTOCK_CAP'       => 0,
            'RULE_PREMIUM_DANGER_THRESHOLD'    => 0,
            'RULE_PREMIUM_DANGER_BOOST'        => 0,
            'RULE_FAST_OVERSTOCK_THRESHOLD'    => 15,
            'RULE_FAST_OVERSTOCK_CAP'          => 10,
            'RULE_FAST_DANGER_THRESHOLD'       => 5,
            'RULE_FAST_DANGER_BOOST'           => 5,
            'RULE_NEW_FAST_MIN_DISPLAY'        => 15,
            'RULE_NEW_OTHER_MIN_DISPLAY'       => 5,
        ];

        $this->updateEnv($defaults);

        // 🔥 RESET ID PROMPT KE 1 (Biasanya prompt default Kimi ada di ID 1)
        Setting::updateOrCreate(
            ['key' => 'active_prompt_id'],
            ['value' => 1]
        );

        session(['pred_settings' => [
            'saw_w1'                           => 0.45,
            'saw_w2'                           => 0.25,
            'saw_w3'                           => 0.15,
            'saw_w4'                           => 0.15,
            'weight_fast'                      => 1.2,
            'weight_premium'                   => 0.6,
            'weight_slow'                      => 0.4,
            'rule_slow_overstock_threshold'    => 5,
            'rule_slow_overstock_cap'          => 0,
            'rule_slow_danger_threshold'       => 0,
            'rule_slow_danger_boost'           => 0,
            'rule_premium_overstock_threshold' => 2,
            'rule_premium_overstock_cap'       => 0,
            'rule_premium_danger_threshold'    => 0,
            'rule_premium_danger_boost'        => 0,
            'rule_fast_overstock_threshold'    => 15,
            'rule_fast_overstock_cap'          => 10,
            'rule_fast_danger_threshold'       => 5,
            'rule_fast_danger_boost'           => 5,
            'rule_new_fast_min'                => 15,
            'rule_new_other_min'               => 5,
        ]]);

        return redirect()->route('settings.prediction')
            ->with('success', 'Konfigurasi berhasil direset ke nilai default.');

            
    }

    // =========================================================================
    // HELPER — Tulis ke .env
    // =========================================================================
    private function updateEnv(array $data): void
    {
        $envPath    = base_path('.env');
        $envContent = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            if (str_contains($envContent, $key . '=')) {
                $envContent = preg_replace(
                    '/^' . preg_quote($key, '/') . '=.*/m',
                    $key . '=' . $value,
                    $envContent
                );
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $envContent);
    }

    // =========================================================================
    // HELPER — Ambil Default Prompt Kimi (Untuk Auto-Seed Pertama Kali)
    // =========================================================================
    private function getDefaultPrompt(): string
    {
        return <<<EOD
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
    }
}