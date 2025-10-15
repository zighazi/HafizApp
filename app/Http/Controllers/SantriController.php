<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Kelas;
use App\Models\Strata;
use App\Models\Angkatan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SantriController extends Controller
{
    /** LIST + SEARCH + FILTER + SORT + PAGINATE */
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        $filters = [
            'kelas_id'    => $request->integer('kelas_id'),
            'strata_id'   => $request->integer('strata_id'),
            'angkatan_id' => $request->integer('angkatan_id'),
            'jk'          => $request->input('jk'), // 'L' / 'P'
        ];

        $allowedSorts = ['nama','nis','kelas_id','strata_id','angkatan_id','created_at'];
        $sort = in_array($request->input('sort'), $allowedSorts, true) ? $request->input('sort') : 'nama';
        $dir  = $request->input('dir') === 'desc' ? 'desc' : 'asc';

        $per  = (int) $request->input('per', 20);
        $per  = max(5, min(100, $per));

        $santris = $this->baseQuery($filters, $q)
            ->orderBy($sort, $dir)
            ->paginate($per)
            ->withQueryString();

        return view('santri.index', [
            'santris'  => $santris,
            'q'        => $q,
            'filters'  => $filters,
            'sort'     => $sort,
            'dir'      => $dir,
            'per'      => $per,
            'kelas'    => Kelas::orderBy('nama')->get(['id','nama']),
            'strata'   => Strata::orderBy('nama')->get(['id','nama']),
            'angkatan' => Angkatan::orderBy('tahun','desc')->get(['id','tahun']),
        ]);
    }

    /** CREATE + STORE */
    public function create()
    {
        return view('santri.create', [
            'kelas'    => Kelas::orderBy('nama')->get(),
            'strata'   => Strata::orderBy('nama')->get(),
            'angkatan' => Angkatan::orderBy('tahun','desc')->get(),
        ]);
    }

    public function store(Request $r)
    {
        $data = $r->validate($this->rules());
        Santri::create($data);
        return redirect()->route('santri.index')->with('ok','Santri ditambahkan.');
    }

    /** SHOW (opsional) */
    public function show(Santri $santri)
    {
        $santri->load(['kelas','strata','angkatan']);
        return view('santri.show', compact('santri'));
    }

    /** EDIT + UPDATE */
    public function edit(Santri $santri)
    {
        return view('santri.edit', [
            'santri'   => $santri->load(['kelas','strata','angkatan']),
            'kelas'    => Kelas::orderBy('nama')->get(),
            'strata'   => Strata::orderBy('nama')->get(),
            'angkatan' => Angkatan::orderBy('tahun','desc')->get(),
        ]);
    }

    public function update(Request $r, Santri $santri)
    {
        $data = $r->validate($this->rules($santri));
        $santri->update($data);
        return redirect()->route('santri.index')->with('ok','Santri diperbarui.');
    }

    /** DELETE */
    public function destroy(Santri $santri)
    {
        $santri->delete();
        return back()->with('ok','Santri dihapus.');
    }

    /** EXPORT CSV (menghormati filter & search saat ini) */
    public function export(Request $request): StreamedResponse
    {
        $q = trim((string) $request->input('q', ''));
        $filters = [
            'kelas_id'    => $request->integer('kelas_id'),
            'strata_id'   => $request->integer('strata_id'),
            'angkatan_id' => $request->integer('angkatan_id'),
            'jk'          => $request->input('jk'),
        ];

        $filename = 'santri_export_'.now()->format('Ymd_His').'.csv';

        $callback = function () use ($filters, $q) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['nis','nama','jenis_kelamin','kelas_id','strata_id','angkatan_id','created_at']);
            $this->baseQuery($filters, $q)
                ->orderBy('nama')
                ->chunk(1000, function ($rows) use ($out) {
                    foreach ($rows as $s) {
                        fputcsv($out, [
                            $s->nis,
                            $s->nama,
                            $s->jenis_kelamin,
                            $s->kelas_id,
                            $s->strata_id,
                            $s->angkatan_id,
                            $s->created_at,
                        ]);
                    }
                });
            fclose($out);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /** IMPORT CSV (upsert by NIS) */
    public function importForm()
    {
        return view('santri.import');
    }

    public function importStore(Request $r)
    {
        $r->validate([
            'file' => ['required','file','mimes:csv,txt'],
            'mode' => ['nullable', Rule::in(['insert','upsert'])], // default upsert
        ]);

        $mode = $r->input('mode','upsert');
        $path = $r->file('file')->getRealPath();

        $handle = fopen($path, 'r');
        if (!$handle) {
            return back()->withErrors(['file'=>'Tidak bisa membaca file']);
        }

        $header = fgetcsv($handle);
        $map    = $this->detectHeaderMap($header);

        $inserted = 0; $updated = 0;
        while (($row = fgetcsv($handle)) !== false) {
            $payload = [
                'nis'           => $row[$map['nis']] ?? null,
                'nama'          => $row[$map['nama']] ?? null,
                'jenis_kelamin' => $row[$map['jenis_kelamin']] ?? null,
                'kelas_id'      => isset($map['kelas_id']) ? ($row[$map['kelas_id']] ?: null) : null,
                'strata_id'     => isset($map['strata_id']) ? ($row[$map['strata_id']] ?: null) : null,
                'angkatan_id'   => isset($map['angkatan_id']) ? ($row[$map['angkatan_id']] ?: null) : null,
            ];
            if (!($payload['nis'] && $payload['nama'])) continue;

            if ($mode === 'insert') {
                if (!Santri::where('nis',$payload['nis'])->exists()) {
                    Santri::create($payload); $inserted++;
                }
            } else {
                $s = Santri::firstWhere('nis', $payload['nis']);
                if ($s) { $s->update($payload); $updated++; }
                else    { Santri::create($payload); $inserted++; }
            }
        }
        fclose($handle);

        return redirect()->route('santri.index')->with('ok', "Import selesai. Insert: $inserted, Update: $updated");
    }

    /** ===== Helpers ===== */
    private function rules(?Santri $current = null): array
    {
        $kelasTable    = (new Kelas)->getTable();
        $strataTable   = (new Strata)->getTable();
        $angkatanTable = (new Angkatan)->getTable();

        return [
            'nis'           => [
                'required','string','max:20',
                $current
                    ? Rule::unique((new Santri)->getTable(), 'nis')->ignore($current->id)
                    : Rule::unique((new Santri)->getTable(), 'nis'),
            ],
            'nama'          => ['required','string','max:120'],
            'jenis_kelamin' => ['required', Rule::in(['L','P'])],
            'kelas_id'      => ['required', Rule::exists($kelasTable, 'id')],
            'strata_id'     => ['nullable', Rule::exists($strataTable, 'id')],
            'angkatan_id'   => ['required', Rule::exists($angkatanTable, 'id')],
        ];
    }

    private function baseQuery(array $filters, string $q): Builder
    {
        return Santri::query()
            ->with(['kelas','strata','angkatan'])
            ->when($q !== '', fn (Builder $w) =>
                $w->where(fn (Builder $x) =>
                    $x->where('nis','like',"%$q%")
                      ->orWhere('nama','like',"%$q%")
                ))
            ->when($filters['kelas_id'], fn (Builder $w,$v) => $w->where('kelas_id',$v))
            ->when($filters['strata_id'], fn (Builder $w,$v) => $w->where('strata_id',$v))
            ->when($filters['angkatan_id'], fn (Builder $w,$v) => $w->where('angkatan_id',$v))
            ->when(in_array($filters['jk'], ['L','P'], true), fn (Builder $w,$v) => $w->where('jenis_kelamin',$v));
    }

    private function detectHeaderMap(array $header): array
    {
        $map = []; $norm = fn($s) => strtolower(trim($s));
        foreach ($header as $i => $h) {
            $h = $norm($h);
            if (in_array($h, ['nis','nomor_induk']))              $map['nis'] = $i;
            elseif (in_array($h, ['nama','nama_santri']))         $map['nama'] = $i;
            elseif (in_array($h, ['jk','jenis_kelamin','gender']))$map['jenis_kelamin'] = $i;
            elseif (in_array($h, ['kelas_id','id_kelas']))        $map['kelas_id'] = $i;
            elseif (in_array($h, ['strata_id','id_strata']))      $map['strata_id'] = $i;
            elseif (in_array($h, ['angkatan_id','id_angkatan']))  $map['angkatan_id'] = $i;
        }
        return $map;
    }
}