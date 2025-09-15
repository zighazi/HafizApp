<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Santri;

class HafalanController extends Controller
{
    /** GET /hafalans */
    public function index()
    {
        // Dummy list agar tidak error sebelum ada tabel hafalans
        $hafalans = [
            ['santri_nama' => 'Ahmad Fauzi',  'surah_nama' => 'Al-Fatihah', 'ayat_awal' => 1, 'ayat_akhir' => 7,  'tanggal' => now()->toDateString()],
            ['santri_nama' => 'Aisyah Rahma', 'surah_nama' => 'Al-Baqarah', 'ayat_awal' => 1, 'ayat_akhir' => 5,  'tanggal' => now()->toDateString()],
            ['santri_nama' => 'Dika W.',      'surah_nama' => 'Ali Imran',  'ayat_awal' => 1, 'ayat_akhir' => 3,  'tanggal' => now()->toDateString()],
        ];

        return view('hafalans.index', compact('hafalans'));
    }

    /** GET /hafalans/create */
    public function create()
    {
        // Dropdown Nama Santri (anda sudah punya tabel santris)
        $santris = Santri::orderBy('nama')->get(['id','nama']);

        // ============== BAGIAN TANGGUH UNTUK TABEL SURAH ==============
        // 1) Tentukan nama tabel: 'surahs' atau 'surah'
        $surahTable = Schema::hasTable('surahs') ? 'surahs' : (Schema::hasTable('surah') ? 'surah' : null);
        if (!$surahTable) {
            // Kalau benar-benar tidak ada tabel surah — kasih pesan yang ramah
            abort(500, "Tabel surah tidak ditemukan. Pastikan ada tabel 'surahs' atau 'surah'.");
        }

        // 2) Peta kemungkinan nama kolom di database kamu
        $colNamaCandidates   = ['nama', 'name', 'nama_latin', 'surah', 'nama_surah', 'surah_name', 'latin'];
        $colNomorCandidates  = ['nomor', 'number', 'index', 'no', 'id_surah', 'surah_id', 'urut'];
        $colJmlAyatCandidates= ['jumlah_ayat', 'ayat', 'number_of_ayah', 'total_ayat', 'verses_count', 'ayat_count'];

        // 3) Pilih yang tersedia
        $colNama   = $this->pickFirstExistingColumn($surahTable, $colNamaCandidates);
        $colNomor  = $this->pickFirstExistingColumn($surahTable, $colNomorCandidates, 'id'); // fallback 'id'
        $colJumlah = $this->pickFirstExistingColumn($surahTable, $colJmlAyatCandidates);

        // 4) Ambil data dan alias ke nama standar (nomor,nama,jumlah_ayat)
        //    -> supaya view tidak perlu tahu variasi skema
        $surahs = DB::table($surahTable)
            ->select([
                'id',
                DB::raw("`{$colNomor}`  as nomor"),
                DB::raw("`{$colNama}`   as nama"),
                DB::raw("`{$colJumlah}` as jumlah_ayat"),
            ])
            ->orderBy($colNomor, 'asc')
            ->get();

        return view('hafalans.create', compact('santris', 'surahs'));
    }

    /** POST /hafalans */
    public function store(Request $request)
    {
        // Cari info surah terpilih utk batas maksimum (pakai cara tangguh yang sama)
        $surahTable = Schema::hasTable('surahs') ? 'surahs' : (Schema::hasTable('surah') ? 'surah' : null);
        if (!$surahTable) {
            abort(500, "Tabel surah tidak ditemukan. Pastikan ada tabel 'surahs' atau 'surah'.");
        }

        $colJmlAyat = $this->pickFirstExistingColumn($surahTable, ['jumlah_ayat','ayat','number_of_ayah','total_ayat','verses_count','ayat_count']);
        // ambil jumlah ayat surah yang dipilih
        $surah = DB::table($surahTable)->where('id', $request->input('surah_id'))->first([$colJmlAyat.' as jumlah_ayat']);
        if (!$surah) {
            return back()->withErrors(['surah_id' => 'Surah tidak ditemukan.'])->withInput();
        }

        $validated = $request->validate([
            'santri_id'   => ['required','exists:santris,id'],
            'surah_id'    => ['required','integer'],
            'ayat_awal'   => ['required','integer','min:1','max:'.$surah->jumlah_ayat],
            'ayat_akhir'  => ['required','integer','min:1','max:'.$surah->jumlah_ayat],
            'tanggal'     => ['required','date'],
        ], [
            'ayat_awal.max'  => "Maksimal ayat untuk surah ini adalah {$surah->jumlah_ayat}.",
            'ayat_akhir.max' => "Maksimal ayat untuk surah ini adalah {$surah->jumlah_ayat}.",
        ]);

        if ((int)$validated['ayat_awal'] > (int)$validated['ayat_akhir']) {
            return back()->withErrors(['ayat_akhir' => 'Ayat akhir harus ≥ ayat awal.'])->withInput();
        }

        // TODO: simpan ke DB jika tabel hafalans sudah ada.
        // \App\Models\Hafalan::create([
        //     'santri_id'  => $validated['santri_id'],
        //     'surah_id'   => $validated['surah_id'],
        //     'ayat_awal'  => $validated['ayat_awal'],
        //     'ayat_akhir' => $validated['ayat_akhir'],
        //     'tanggal'    => $validated['tanggal'],
        // ]);

        return redirect()->route('hafalans.index')
            ->with('success', 'Setoran hafalan berhasil ditambahkan (dummy, belum disimpan ke DB).');
    }

    // Helper: pilih nama kolom pertama yang eksis di tabel
    private function pickFirstExistingColumn(string $table, array $candidates, ?string $default = null): string
    {
        foreach ($candidates as $c) {
            if (Schema::hasColumn($table, $c)) return $c;
        }
        if ($default !== null) return $default;
        // Jika benar-benar tidak ketemu, lempar pesan jelas
        abort(500, "Kolom tidak ditemukan di tabel '{$table}'. Dicari: ".implode(', ', $candidates));
    }

    // Resource lain tak dipakai
    public function show($id) { abort(404); }
    public function edit($id) { abort(404); }
    public function update(Request $r, $id) { abort(404); }
    public function destroy($id) { abort(404); }
}