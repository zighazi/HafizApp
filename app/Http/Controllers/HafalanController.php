<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Hafalan;
use App\Models\Santri;
use App\Models\Surah;

class HafalanController extends Controller
{
    /** GET /hafalans */
    public function index(Request $request)
    {
        [$kelasCol, $kelasLabel]       = $this->resolveKelasColumn();
        [$angkatanCol, $angkatanLabel] = $this->resolveAngkatanColumn();

        $kelas    = $kelasCol    ? $request->query('kelas')    : null;
        $angkatan = $angkatanCol ? $request->query('angkatan') : null;
        $keyword  = trim((string) $request->query('q', ''));

        $santriSelect = ['id','nama'];
        if ($kelasCol)    $santriSelect[] = $kelasCol;
        if ($angkatanCol) $santriSelect[] = $angkatanCol;

        $q = Hafalan::query()
            ->with([
                'santri:' . implode(',', $santriSelect),
                'surah:id,nama_id',
            ])
            ->latest('tanggal_setor');

        if ($kelasCol && $kelas) {
            $q->whereHas('santri', fn($s) => $s->where($kelasCol, $kelas));
        }
        if ($angkatanCol && $angkatan) {
            $q->whereHas('santri', fn($s) => $s->where($angkatanCol, $angkatan));
        }
        if ($keyword !== '') {
            $q->whereHas('santri', fn($s) => $s->where('nama','like',"%{$keyword}%"));
        }

        $hafalans = $q->paginate(15)->appends($request->query());

        $kelasList = $kelasCol
            ? Santri::whereNotNull($kelasCol)->distinct()->orderBy($kelasCol)->pluck($kelasCol)
            : collect();
        $angkatanList = $angkatanCol
            ? Santri::whereNotNull($angkatanCol)->distinct()->orderBy($angkatanCol,'desc')->pluck($angkatanCol)
            : collect();

        return view('hafalans.index', compact(
            'hafalans','kelasList','angkatanList','kelas','angkatan','keyword',
            'kelasCol','angkatanCol','kelasLabel','angkatanLabel'
        ))->with([
            'hasKelas'    => (bool) $kelasCol,
            'hasAngkatan' => (bool) $angkatanCol,
        ]);
    }

    /** GET /hafalans/create */
    public function create()
    {
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
        ]);

        if ((int)$validated['ayat_mulai'] > (int)$validated['ayat_selesai']) {
            return back()->withErrors(['ayat_selesai' => 'Ayat akhir harus ≥ ayat awal.'])->withInput();
        }

        $overlap = Hafalan::where('santri_id',$validated['santri_id'])
            ->where('surah_id',$validated['surah_id'])
            ->whereDate('tanggal_setor',$validated['tanggal_setor'])
            ->where(function($q) use ($validated){
                $a = (int)$validated['ayat_mulai'];
                $b = (int)$validated['ayat_selesai'];
                $q->where('ayat_mulai','<=',$b)->where('ayat_selesai','>=',$a);
            })->exists();

        if ($overlap) {
            return back()->withErrors([
                'ayat_mulai'   => 'Rentang ayat bertabrakan dengan setoran lain di tanggal ini.',
                'ayat_selesai' => 'Atur ulang rentang atau ganti tanggal.',
            ])->withInput();
        }

        try {
            DB::transaction(fn() => Hafalan::create($validated));
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['general'=>'Terjadi kesalahan saat menyimpan.']);
        }

        return redirect()->route('hafalans.index')->with('success','Setoran hafalan berhasil disimpan.');
    }

    public function show($id){ abort(404); }
    public function edit($id){ abort(404); }
    public function update(){ abort(404); }
    public function destroy(){ abort(404); }

    private function resolveKelasColumn(): array
    {
        $candidates = ['kelas','kelas_kode','kelas_nama','rombel','kelas_id'];
        foreach ($candidates as $c) {
            if (Schema::hasColumn('santri',$c)) {
                $label = $c === 'rombel' ? 'Rombel' : 'Kelas';
                return [$c,$label];
            }
        }
        return [null,'Kelas'];
    }

    private function resolveAngkatanColumn(): array
    {
        $candidates = ['angkatan','angkatan_tahun','tahun_masuk'];
        foreach ($candidates as $c) {
            if (Schema::hasColumn('santri',$c)) {
                $label = $c === 'tahun_masuk' ? 'Tahun Masuk' : 'Angkatan';
                return [$c,$label];
            }
        }
        return [null,'Angkatan'];
    }
}