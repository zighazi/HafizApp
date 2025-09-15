<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Angkatan;
use App\Models\Kelas;
use App\Models\Strata;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SantriController extends Controller
{
    public function index(Request $request)
    {
        $q = Santri::query()
            ->with(['angkatan','kelas','strata'])
            ->orderBy('nama');

        // optional filter
        if ($request->filled('angkatan_id')) {
            $q->where('angkatan_id', (int)$request->angkatan_id);
        }
        if ($request->filled('kelas_id')) {
            $q->where('kelas_id', (int)$request->kelas_id);
        }
        if ($request->filled('q')) {
            $s = trim($request->q);
            $q->where(function($w) use ($s){
                $w->where('nama', 'like', "%$s%")
                  ->orWhere('nis', 'like', "%$s%");
            });
        }

        $santris = $q->paginate(20)->withQueryString();

        $angkatanList = Angkatan::orderBy('tahun_mulai','desc')->get(['id','nama']);
        $kelasList    = Kelas::orderBy('nama')->get(['id','nama']);

        return view('santris.index', compact('santris','angkatanList','kelasList'));
    }

    public function create()
    {
        [$angkatanList,$kelasList,$strataList] = $this->dropdowns();
        return view('santris.create', compact('angkatanList','kelasList','strataList'));
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        // Guard konsistensi: kelas harus milik angkatan yang dipilih
        $kelas = Kelas::findOrFail($data['kelas_id']);
        if ($kelas->angkatan_id !== $data['angkatan_id']) {
            return back()->withErrors(['kelas_id'=>'Kelas tidak sesuai dengan angkatan yang dipilih.'])->withInput();
        }

        // strata_id boleh null — jika ada, pastikan jenis cocok dengan kelas
        if (!empty($data['strata_id'])) {
            $strata = Strata::findOrFail($data['strata_id']);
            if ($strata->jenis_kelas !== $kelas->jenis) {
                return back()->withErrors(['strata_id'=>'Strata tidak sesuai dengan jenis kelas ('.$kelas->jenis.').'])->withInput();
            }
        }

        Santri::create($data);

        return redirect()->route('santris.index')->with('success','Santri ditambahkan.');
    }

    public function show(Santri $santri)
    {
        $santri->load(['angkatan','kelas','strata']);
        return view('santris.show', compact('santri'));
    }

    public function edit(Santri $santri)
    {
        [$angkatanList,$kelasList,$strataList] = $this->dropdowns();
        $santri->load(['angkatan','kelas','strata']);
        return view('santris.edit', compact('santri','angkatanList','kelasList','strataList'));
    }

    public function update(Request $request, Santri $santri)
    {
        $data = $this->validatePayload($request, $santri->id);

        $kelas = Kelas::findOrFail($data['kelas_id']);
        if ($kelas->angkatan_id !== $data['angkatan_id']) {
            return back()->withErrors(['kelas_id'=>'Kelas tidak sesuai dengan angkatan yang dipilih.'])->withInput();
        }

        if (!empty($data['strata_id'])) {
            $strata = Strata::findOrFail($data['strata_id']);
            if ($strata->jenis_kelas !== $kelas->jenis) {
                return back()->withErrors(['strata_id'=>'Strata tidak sesuai dengan jenis kelas ('.$kelas->jenis.').'])->withInput();
            }
        }

        $santri->update($data);

        return redirect()->route('santris.index')->with('success','Santri diperbarui.');
    }

    public function destroy(Santri $santri)
    {
        $santri->delete();
        return redirect()->route('santris.index')->with('success','Santri dihapus.');
    }

    // ----------------- Helpers -----------------

    private function dropdowns(): array
    {
        $angkatanList = Angkatan::orderBy('tahun_mulai','desc')->get(['id','nama']);
        $kelasList    = Kelas::orderBy('nama')->get(['id','nama','angkatan_id','jenis']);
        $strataList   = Strata::orderBy('jenis_kelas')->orderBy('nama')->get(['id','nama','jenis_kelas']);
        return [$angkatanList,$kelasList,$strataList];
    }

    private function validatePayload(Request $request, ?int $santriId = null): array
    {
        return $request->validate([
            'nis'           => [
                'required','string','max:20',
                Rule::unique('santri','nis')->ignore($santriId)
            ],
            'nama'          => ['required','string','max:200'],
            'jenis_kelamin' => ['required', Rule::in(['L','P'])],
            'angkatan_id'   => ['required','integer','exists:angkatan,id'],
            'kelas_id'      => ['required','integer','exists:kelas,id'],
            'strata_id'     => ['nullable','integer','exists:strata,id'], // boleh kosong
        ]);
    }
}