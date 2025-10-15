@extends('layouts.app')
@section('title','Santri')
@section('page','Santri')
@section('content')
  <x-card :title="'Daftar Santri'" :actions="view('santri._actions', ['q'=>$q])"/>
  <div class="mt-3 overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950">
    <table class="w-full text-sm">
      <thead class="text-left border-b border-gray-200 dark:border-gray-800">
        <tr>
          <th class="p-3">NIS</th>
          <th class="p-3">Nama</th>
          <th class="p-3">Kelas</th>
          <th class="p-3">Strata</th>
        </tr>
      </thead>
      <tbody>
        @foreach($santris as $s)
        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
          <td class="p-3">{{ $s->nis }}</td>
          <td class="p-3">{{ $s->nama }}</td>
          <td class="p-3">{{ $s->kelas->nama ?? '-' }}</td>
          <td class="p-3">{{ $s->strata->nama ?? '-' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="mt-3">{{ $santris->links() }}</div>
@endsection