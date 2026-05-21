@extends('layouts.app')

@section('title', 'Detail Obat')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">Detail Obat</h2>
        <div class="space-x-2">
            <a href="{{ route('medicines.edit', $medicine->id) }}" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                Edit
            </a>
            <a href="{{ route('medicines.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                Kembali
            </a>
        </div>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Umum</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm text-gray-500">Kode</label>
                        <p class="font-medium">{{ $medicine->code }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Nama</label>
                        <p class="font-medium">{{ $medicine->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Nama Generik</label>
                        <p class="font-medium">{{ $medicine->generic_name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Kategori</label>
                        <p class="font-medium">{{ $medicine->category->name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Satuan</label>
                        <p class="font-medium">{{ $medicine->unit->name ?? '-' }} ({{ $medicine->unit->symbol ?? '-' }})</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Harga Jual</label>
                        <p class="font-medium">Rp {{ number_format($medicine->price, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Stok Minimum</label>
                        <p class="font-medium">{{ $medicine->min_stock }} {{ $medicine->unit->symbol ?? '' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Status</label>
                        <p class="font-medium">
                            @if($medicine->is_active)
                                <span class="text-green-600">Aktif</span>
                            @else
                                <span class="text-red-600">Tidak Aktif</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Tambahan</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm text-gray-500">Pabrik</label>
                        <p class="font-medium">{{ $medicine->manufacturer ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Komposisi</label>
                        <p class="font-medium">{{ $medicine->composition ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Kegunaan</label>
                        <p class="font-medium">{{ $medicine->description ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Efek Samping</label>
                        <p class="font-medium">{{ $medicine->side_effects ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Cara Penggunaan</label>
                        <p class="font-medium">{{ $medicine->usage_instruction ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stok Information -->
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Stok</h3>
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm text-gray-500">Total Stok</label>
                        <p class="text-2xl font-bold {{ $medicine->isBelowMinStock() ? 'text-red-600' : 'text-green-600' }}">
                            {{ $medicine->total_stock }} {{ $medicine->unit->symbol ?? '' }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Stok Minimum</label>
                        <p class="text-2xl font-bold text-gray-700">
                            {{ $medicine->min_stock }} {{ $medicine->unit->symbol ?? '' }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Status Stok</label>
                        <p class="text-2xl font-bold {{ $medicine->isBelowMinStock() ? 'text-red-600' : 'text-green-600' }}">
                            {{ $medicine->isBelowMinStock() ? 'Stok Menipis' : 'Aman' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Batches Table -->
            <div class="bg-white border rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kadaluarsa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Beli</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Jual</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($medicine->batches as $batch)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $batch->batch_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $batch->expired_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $batch->quantity }} {{ $medicine->unit->symbol ?? '' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                Rp {{ number_format($batch->purchase_price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                Rp {{ number_format($batch->selling_price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($batch->isExpired())
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Kadaluarsa</span>
                                @elseif($batch->isNearExpiry())
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Hampir Kadaluarsa</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Aktif</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        @if($medicine->batches->isEmpty())
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                Tidak ada batch
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
