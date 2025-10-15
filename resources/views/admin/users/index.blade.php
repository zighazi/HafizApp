@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Manajemen Pengguna</h1>
        @if (session('status'))
            <div class="px-3 py-2 text-sm rounded-lg bg-green-100 text-green-700 border border-green-300">
                {{ session('status') }}
            </div>
        @endif
    </div>

    <form method="GET" class="mb-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama/email..."
                   class="border rounded-lg px-3 py-2 w-full" />
            <select name="role" class="border rounded-lg px-3 py-2 w-full">
                <option value="">— Semua Role —</option>
                @foreach (['admin'=>'Admin','orangtua'=>'Orangtua','guru'=>'Guru','user'=>'User'] as $key=>$label)
                    <option value="{{ $key }}" @selected($role===$key)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="px-4 py-2 rounded-lg bg-blue-600 text-white">Terapkan</button>
        </div>
    </form>

    <div class="overflow-x-auto border rounded-2xl">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="text-left py-2 px-3">Nama</th>
                    <th class="text-left py-2 px-3">Email</th>
                    <th class="text-left py-2 px-3">Role</th>
                    <th class="text-left py-2 px-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $u)
                    <tr class="border-b last:border-0">
                        <td class="py-2 px-3">{{ $u->name }}</td>
                        <td class="py-2 px-3">{{ $u->email }}</td>
                        <td class="py-2 px-3">
                            <span class="px-2 py-1 rounded-full border text-xs
                                @class([
                                    'bg-blue-100 text-blue-700 border-blue-300' => $u->role==='admin',
                                    'bg-green-100 text-green-700 border-green-300' => $u->role==='orangtua',
                                    'bg-yellow-100 text-yellow-700 border-yellow-300' => $u->role==='guru',
                                    'bg-gray-100 text-gray-700 border-gray-300' => $u->role==='user',
                                ])
                            ">{{ ucfirst($u->role ?? 'user') }}</span>
                        </td>
                        <td class="py-2 px-3">
                            <a href="{{ route('admin.users.edit', $u) }}"
                               class="px-3 py-1 rounded-lg border hover:bg-gray-50 inline-block">Edit Role</a>
                        </td>
                    </tr>
                @empty
                    <tr><td class="py-3 px-3" colspan="4">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>

<style>
:root { color-scheme: light dark; }
html.dark body { background-color: #0b1220; color: #e5e7eb; }
html.dark .border { border-color: #1f2937; }
html.dark .bg-gray-50 { background-color: #0e172a; }
</style>
@endsection