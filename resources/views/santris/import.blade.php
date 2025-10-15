@extends('layouts.app')
@section('title','Import Santri')

@section('content')
<div class="card p-4">
  <div class="page-head">
    <h1 class="h5 mb-0">Import Santri</h1>
    <a href="{{ route('hafalans.index') }}" class="btn btn-outline-secondary">Kembali</a>
  </div>
  <p class="text-muted mt-2">Unggah file CSV sesuai format yang disiapkan.</p>

  <form class="mt-3" method="POST" action="{{ route('santris.import') }}" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
      <label class="form-label">File CSV</label>
      <input type="file" name="file" class="form-control" accept=".csv" required>
      <div class="form-text">Kolom minimal: <code>nis, nama, kelas, angkatan</code>.</div>
    </div>
    <button class="btn btn-primary"><i class="bi bi-cloud-arrow-up me-1"></i> Upload</button>
  </form>
</div>
@endsection