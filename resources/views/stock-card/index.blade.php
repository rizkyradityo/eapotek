@extends('layouts.app')

@section('title', 'Kartu Stok')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-4 border-b bg-gray-50">
        <h2 class="text-xl font-bold text-gray-800">Kartu Stok</h2>
    </div>
    <div class="p-6">
        <form method="GET" class="mb-6 flex flex-wrap gap-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Cari Obat</label>
                <input type="text" name="search" value="{{ $search ?? '' }}"
                    class="px-3 py-2 border rounded" placeholder="Nama / Kode obat...">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Kategori</label>
                <select name="category_id" class="px-3 py-2 border rounded">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ ($categoryId ?? '') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="self-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Filter
                </button>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Obat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Stok</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Min Stok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($medicines as $medicine)
                    <tr class="{{ $medicine->isBelowMinStock() ? 'bg-red-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $medicine->code }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $medicine->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $medicine->category->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm {{ $medicine->isBelowMinStock() ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                            {{ $medicine->total_stock }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">{{ $medicine->min_stock }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $medicine->unit->symbol ?? $medicine->unit->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <a href="{{ route('stock-card.show', $medicine->id) }}"
                               class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                Lihat Kartu
                            </a>
                        </td>
                    </tr>
                    @endforeach
                    @if($medicines->isEmpty())
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada data obat
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $medicines->links() }}
        </div>
    </div>
</div>
@endsection
