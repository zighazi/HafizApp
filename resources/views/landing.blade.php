@extends('layouts.app')
@section('title', 'HafizApp – Monitoring Hafalan Santri')

@push('styles')
<style>
  .hero {
    background: radial-gradient(1200px 600px at 10% -10%, rgba(13,110,253,.12), transparent 70%),
                radial-gradient(800px 400px at 100% 0, rgba(25,135,84,.10), transparent 60%);
    border-radius: 1.25rem;
  }
  .feature-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: grid; place-items: center;
    background: rgba(13,110,253,.08);
    font-size: 22px;
  }
  .step-number {
    width: 36px; height: 36px; border-radius: 50%;
    display: grid; place-items: center;
    background: #0d6efd; color: #fff; font-weight: 600;
  }
  .shadow-soft { box-shadow: 0 8px 24px rgba(0,0,0,.06); }
  .badge-soft { background: rgba(13,110,253,.1); color:#0d6efd; }
</style>
@endpush

@section('content')
  {{-- HERO --}}
  <section class="hero p-4 p-md-5 mb-4 shadow-soft">
    <div class="row align-items-center g-4">
      <div class="col-lg-6">
        <div class="d-flex align-items-center gap-2 mb-3">
          <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo Sekolah" style="height:40px">
          <span class="badge badge-soft rounded-pill">HafizApp</span>
        </div>
        <h1 class="display-6 fw-bold mb-3">
          Monitoring Hafalan <span class="text-primary">lebih rapi</span>, laporan <span class="text-success">lebih cepat</span>.
        </h1>
        <p class="lead text-muted mb-4">
          Pantau setoran santri, rekap per kelas, dan impor data dari Excel/Google Sheet. Sederhana, cepat, dan siap dipakai guru maupun orang tua.
        </p>
        <div class="d-flex flex-wrap gap-2">
          <a href="{{ route('hafalans.index') }}" class="btn btn-primary btn-lg">Lihat Daftar Hafalan</a>
          <a href="{{ route('hafalans.create') }}" class="btn btn-outline-primary btn-lg">Tambah Hafalan</a>
          <a href="{{ route('rekap.kelas') }}" class="btn btn-success btn-lg">Rekap Kelas</a>
          <a href="{{ route('santris.import.form') }}" class="btn btn-outline-secondary btn-lg">Import Santri</a>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card border-0 shadow-soft">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <div class="fw-semibold">Ringkasan Hari Ini</div>
                <div class="text-muted small">Tanggal: {{ now()->toDateString() }}</div>
              </div>
              <span class="badge bg-success">Aktif</span>
            </div>
            <div class="row g-3">
              <div class="col-6">
                <div class="p-3 border rounded-3">
                  <div class="text-muted small">Total Setoran</div>
                  <div class="fs-4 fw-bold">21</div>
                </div>
              </div>
              <div class="col-6">
                <div class="p-3 border rounded-3">
                  <div class="text-muted small">Kelas Aktif</div>
                  <div class="fs-4 fw-bold">8</div>
                </div>
              </div>
              <div class="col-6">
                <div class="p-3 border rounded-3">
                  <div class="text-muted small">Santri</div>
                  <div class="fs-4 fw-bold">200</div>
                </div>
              </div>
              <div class="col-6">
                <div class="p-3 border rounded-3">
                  <div class="text-muted small">Target Bulan Ini</div>
                  <div class="fs-4 fw-bold">95%</div>
                </div>
              </div>
            </div>
            <div class="text-end mt-3">
              <a href="{{ route('rekap.kelas') }}" class="btn btn-sm btn-outline-secondary">Lihat detail rekap</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- FITUR --}}
  <section class="mb-5">
    <div class="text-center mb-4">
      <h2 class="h3 fw-bold">Fitur Utama</h2>
      <p class="text-muted mb-0">Semua alat yang kamu butuhkan untuk memantau hafalan di satu tempat.</p>
    </div>
    <div class="row g-3">
      <div class="col-md-6 col-lg-3">
        <div class="p-3 border rounded-3 h-100">
          <div class="feature-icon mb-3">📚</div>
          <div class="fw-semibold mb-1">Daftar Hafalan</div>
          <div class="text-muted small mb-3">Lihat progres setoran per santri lengkap.</div>
          <a href="{{ route('hafalans.index') }}" class="btn btn-sm btn-outline-primary">Buka</a>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="p-3 border rounded-3 h-100">
          <div class="feature-icon mb-3" style="background:rgba(25,135,84,.08)">➕</div>
          <div class="fw-semibold mb-1">Tambah Hafalan</div>
          <div class="text-muted small mb-3">Catat setoran baru dalam hitungan detik.</div>
          <a href="{{ route('hafalans.create') }}" class="btn btn-sm btn-outline-success">Tambah</a>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="p-3 border rounded-3 h-100">
          <div class="feature-icon mb-3" style="background:rgba(255,193,7,.15)">📊</div>
          <div class="fw-semibold mb-1">Rekap Kelas</div>
          <div class="text-muted small mb-3">Laporan harian/bulanan, export CSV & cetak.</div>
          <a href="{{ route('rekap.kelas') }}" class="btn btn-sm btn-outline-warning">Rekap</a>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="p-3 border rounded-3 h-100">
          <div class="feature-icon mb-3" style="background:rgba(108,117,125,.15)">⬆️</div>
          <div class="fw-semibold mb-1">Import Santri</div>
          <div class="text-muted small mb-3">Masukkan data dari CSV/Excel agar siap dipantau.</div>
          <a href="{{ route('santris.import.form') }}" class="btn btn-sm btn-outline-secondary">Import</a>
        </div>
      </div>
    </div>
  </section>

  {{-- FOOTER --}}
  <footer class="text-center text-muted small mt-5">
    <div>© {{ now()->year }} HafizApp. Dibuat untuk monitoring tahfizh yang lebih baik.</div>
  </footer>
@endsection