{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Profil')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-7">
    <h1 class="h4 mb-3">Edit Profil</h1>

    {{-- sukses --}}
    @if (session('status') === 'profile-updated')
      <div class="alert alert-success">Profil berhasil diperbarui.</div>
    @endif

    {{-- error validasi --}}
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
      </div>
    @endif

    {{-- FORM UPDATE PROFIL --}}
    <div class="card mb-4">
      <div class="card-body">
        <form method="POST" action="{{ route('profile.update') }}">
          @csrf
          @method('patch')

          <div class="mb-3">
            <label for="name" class="form-label">Nama</label>
            <input id="name" type="text" name="name" class="form-control"
                   value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" class="form-control"
                   value="{{ old('email', $user->email) }}" required autocomplete="username">
          </div>

          <button class="btn btn-primary">Simpan</button>
        </form>
      </div>
    </div>

    {{-- FORM HAPUS AKUN --}}
    <div class="card">
      <div class="card-header">Hapus Akun</div>
      <div class="card-body">
        <p class="text-muted mb-3">Sekali dihapus, akun tidak bisa dipulihkan.</p>

        <form method="POST" action="{{ route('profile.destroy') }}"
              onsubmit="return confirm('Yakin ingin menghapus akun? Tindakan ini tidak bisa dibatalkan.')">
          @csrf
          @method('delete')

          <div class="mb-3">
            <label for="password" class="form-label">Konfirmasi Password</label>
            <input id="password" type="password" name="password" class="form-control"
                   autocomplete="current-password" required>
            {{-- error bag khusus userDeletion --}}
            @error('userDeletion.password')
              <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
          </div>

          <button class="btn btn-outline-danger">Hapus Akun</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection