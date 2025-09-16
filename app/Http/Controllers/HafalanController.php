<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\Hafalan;
use App\Models\Santri;
use App\Models\Surah;

class HafalanController extends Controller
{
    /** GET /hafalans */
    public function index(Request $request)
    {
        // Cek apakah kolom ada di tabel santri
        $hasKelas    = Schema::hasColumn('santri', 'kelas');
        $hasAngkatan = Schema::hasColumn('santri', 'angkatan');

        $kelas    = $hasKelas    ? $request->query('kelas')    : null;
        $angkatan = $hasAngkatan ? $request->query('angkatan') : null;
        $keyword  = $request->query('q');

        // compose select untuk eager-load santri sesuai kolom yang tersedia
        $santriSelect = ['id','nama'];
        if ($hasKelas)    $santriSelect[] = 'kelas';
        if ($hasAngkatan) $santriSelect[] = 'angkatan';

        $q = Hafalan::query()
            ->with([
                'santri:' . implode(',', $santriSelect),
                'surah:id,nama_id',
            ])
            ->latest('tanggal_setor');

        if ($hasKelas && $kelas) {
            $q->whereHas('santri', fn($s) => $s->where('kelas', $kelas));
        }
        if ($hasAngkatan && $angkatan) {
            $q->whereHas('santri', fn($s) => $s->where('angkatan', $angkatan));
        }
        if ($keyword) {
            $q->whereHas('santri', fn($s) => $s->where('nama', 'like', "%{$keyword}%"));
        }

        $hafalans = $q->paginate(15)->appends($request->query());

        // sumber dropdown filter (hanya jika kolomnya ada)
        $kelasList    = $hasKelas
            ? Santri::whereNotNull('kelas')->distinct()->orderBy('kelas')->pluck('kelas')
            : collect();
        $angkatanList = $hasAngkatan
            ? Santri::whereNotNull('angkatan')->distinct()->orderBy('angkatan','desc')->pluck('angkatan')
            : collect();

        return view('hafalans.index', [
            'hafalans'     => $hafalans,
            'kelasList'    => $kelasList,
            'angkatanList' => $angkatanList,
            'kelas'        => $kelas,
            'angkatan'     => $angkatan,
            'keyword'      => $keyword,
            'hasKelas'     => $hasKelas,
            'hasAngkatan'  => $hasAngkatan,
        ]);
    }

    /** GET /hafalans/create */
    public function create()
    {
        // Ambil hanya kolom yang PASTI ada
        $santris = Santri::orderBy('nama')->get(['id','nama']);
        $surahs  = Surah::orderBy('nomor')->get(['id','nomor','nama_id','jumlah_ayat']);

        return view('hafalans.create', compact('santris','surahs'));
    }

    /** POST /hafalans */
    public function store(Request $request)
    {
        $surah = Surah::findOrFail($request->input('surah_id'));

        $validated = $request->validate([
            'santri_id'     => ['required','exists:santri,id'],
            'surah_id'      => ['required','exists:surahs,id'],
            'ayat_mulai'    => ['required','integer','min:1','max:'.$surah->jumlah_ayat],
            'ayat_selesai'  => ['required','integer','min:1','max:'.$surah->jumlah_ayat],
            'tanggal_setor' => ['required','date'],
            'metode'        => ['nullable','string','max:50'],
            'penilai_guru'  => ['nullable','string','max:100'],
            'catatan'       => ['nullable','string','max:500'],
        ], [
            'ayat_mulai.max'   => "Maksimal ayat untuk {$surah->nama_id} adalah {$surah->jumlah_ayat}.",
            'ayat_selesai.max' => "Maksimal ayat untuk {$surah->nama_id} adalah {$surah->jumlah_ayat}.",
        ]);

        if ((int)$validated['ayat_mulai'] > (int)$validated['ayat_selesai']) {
            return back()->withErrors(['ayat_selesai' => 'Ayat akhir harus ≥ ayat awal.'])->withInput();
        }

        // Cek overlap di tanggal yang sama
        $overlap = Hafalan::where('santri_id', $validated['santri_id'])
            ->where('surah_id', $validated['surah_id'])
            ->whereDate('tanggal_setor', $validated['tanggal_setor'])
            ->where(function ($q) use ($validated) {
                $a = (int)$validated['ayat_mulai'];
                $b = (int)$validated['ayat_selesai'];
                $q->where('ayat_mulai', '<=', $b)->where('ayat_selesai', '>=', $a);
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors([
                'ayat_mulai'   => 'Rentang ayat bertabrakan dengan setoran lain di tanggal ini.',
                'ayat_selesai' => 'Atur ulang rentang atau ganti tanggal.',
            ])->withInput();
        }

        Hafalan::create($validated);

        return redirect()->route('hafalans.index')->with('success','Setoran hafalan berhasil disimpan.');
    }

    public function show($id)    { abort(404); }
    public function edit($id)    { abort(404); }
    public function update()     { abort(404); }
    public function destroy()    { abort(404); }
}