<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hafalan;
use App\Models\Santri;
use App\Models\Surah;

class HafalanController extends Controller
{
    /** GET /hafalans */
    public function index()
    {
        $hafalans = Hafalan::with(['santri:id,nama', 'surah:id,nama_id'])
            ->latest('tanggal')
            ->paginate(15);

        return view('hafalans.index', compact('hafalans'));
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
            'santri_id'   => ['required','exists:santri,id'],
            'surah_id'    => ['required','exists:surahs,id'],
            'ayat_awal'   => ['required','integer','min:1','max:'.$surah->jumlah_ayat],
            'ayat_akhir'  => ['required','integer','min:1','max:'.$surah->jumlah_ayat],
            'tanggal'     => ['required','date'],
        ], [
            'ayat_awal.max'  => "Maksimal ayat untuk {$surah->nama_id} adalah {$surah->jumlah_ayat}.",
            'ayat_akhir.max' => "Maksimal ayat untuk {$surah->nama_id} adalah {$surah->jumlah_ayat}.",
        ]);

        if ((int)$validated['ayat_awal'] > (int)$validated['ayat_akhir']) {
            return back()->withErrors([
                'ayat_akhir' => 'Ayat akhir harus lebih besar atau sama dengan ayat awal.'
            ])->withInput();
        }

        Hafalan::create($validated);

        return redirect()->route('hafalans.index')->with('success','Setoran hafalan berhasil disimpan.');
    }

    // method resource lain tidak dipakai
    public function show($id) { abort(404); }
    public function edit($id) { abort(404); }
    public function update(Request $r, $id) { abort(404); }
    public function destroy($id) { abort(404); }
}