@extends('layouts.app')
@section('title','Santri') @section('page','Santri')
@section('content')
  <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-4 bg-white dark:bg-gray-950">
    <p>Daftar santri (placeholder). Total: {{ $santris->total() ?? 0 }}</p>
  </div>
@endsection