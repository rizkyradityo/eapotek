@extends('layouts.app')

@section('title', 'Stok Menipis')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-4 border-b bg-gray-50">
        <h2 class="text-xl font-bold text-gray-800">Obat dengan Stok Menipis</h2>
    </div>
    <div class="p-6">
        @if($medicines->isEmpty())
        <div class="text-center py-8 text-gray-500">
            Tidak ada obat dengan stok menipis
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Obat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stok</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Minimum</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($medicines as $medicine)
                    <tr class="bg-red-50">
                        <td class="px-6 py-4">{{ $medicine->code }}</td>
                        <td class="px-6 py-4">
                            <div class="font-medium">{{ $medicine->name }}</div>
                            <div class="text-sm text-gray-500">{{ $medicine->generic_name }}</div>
                        </td>
                        <td class="px-6 py-4">{{ $medicine->category->name ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $medicine->unit->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-right text-red-600 font-bold">{{ $medicine->total_stock }}</td>
                        <td class="px-6 py-4 text-right">{{ $medicine->min_stock }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('purchases.create') }}" class="text-blue-600 hover:text-blue-900">
                                Beli Stok
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
