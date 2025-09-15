<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SantriImportController extends Controller
{
    /** GET /santris/import */
    public function form()
    {
        return view('santris.import');
    }

    /** POST /santris/import */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls'],
        ]);

        // Simpan file ke storage/app/imports
        $path = $request->file('file')->store('imports');

        // TODO: parsing & insert ke DB sesuai struktur tabel santri kamu.
        return back()->with('success', 'File berhasil diunggah: '.$path.' (Parsing belum diaktifkan).');
    }
}