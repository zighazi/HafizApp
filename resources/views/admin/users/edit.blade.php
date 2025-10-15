@extends('layouts.app')

@section('title', 'Ubah Role Pengguna')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-2xl">
    <h1 class="text-2xl font-bold mb-4">Ubah Role Pengguna</h1>

    <div class="p-4 rounded-2xl border mb-4">
        <div class="text-sm mb-2"><strong>Nama:</strong> {{ $user->name }}</div>
        <div class="text-sm"><strong>Email:</strong> {{ $user->email }}</div>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
        @csrf
        @method('PATCH')

        <div>
            <label class="block text-sm font-medium mb-1">Role</label>
            <select name="role" class="border rounded-lg px-3 py-2 w-full">
                @foreach ($roles as $value => $label)
                    <option value="{{ $value }}" @selected(old('role', $user->role ?? 'user') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('role')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-lg border">Batal</a>
            <button class="px-4 py-2 rounded-lg bg-blue-600 text-white">Simpan</button>
        </div>
    </form>
</div>

<style>
:root { color-scheme: light dark; }
html.dark body { background-color: #0b1220; color: #e5e7eb; }
html.dark .border { border-color: #1f2937; }
</style>
@endsection