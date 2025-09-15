@extends('layouts.app')
@section('title','Import Santri')

@section('content')
  <h1 class="h4 mb-3">Import Data Santri</h1>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('santris.import') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
          <label class="form-label">Pilih File (CSV/TXT/XLSX/XLS)</label>
          <input type="file" name="file" class="form-control" accept=".csv,.txt,.xlsx,.xls" required>
          <div class="form-text">Pastikan kolom sesuai skema tabel santri kamu.</div>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">Upload</button>
          <a href="{{ route('home') }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
      </form>
    </div>
  </div>
@endsection