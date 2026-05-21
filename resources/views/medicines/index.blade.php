@extends('layouts.app')

@section('title', 'Manajemen Obat')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">Manajemen Obat</h2>
        <a href="{{ route('medicines.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            + Tambah Obat
        </a>
    </div>
    <div class="p-4">
        @livewire('medicine-table')
    </div>
</div>
@endsection
