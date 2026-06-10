@extends('layouts.app')

@section('title', 'Kartu Stok - ' . $medicine->name)

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Kartu Stok</h2>
            <p class="text-sm text-gray-500">{{ $medicine->code }} - {{ $medicine->name }}</p>
        </div>
        <a href="{{ route('stock-card.index') }}" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50">
            Kembali
        </a>
    </div>

    <div class="p-6">
        <!-- Info Summary -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Stok Saat Ini</p>
                <p class="text-2xl font-bold text-blue-600">{{ $totalStock }}</p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Stok Masuk</p>
                <p class="text-2xl font-bold text-green-600">{{ $stockIn }}</p>
            </div>
            <div class="bg-red-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Stok Keluar</p>
                <p class="text-2xl font-bold text-red-600">{{ $stockOut }}</p>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Min. Stok</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $medicine->min_stock }}</p>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Satuan</p>
                <p class="text-2xl font-bold text-purple-600">{{ $medicine->unit->symbol ?? $medicine->unit->name ?? '-' }}</p>
            </div>
        </div>

        <!-- Batch Info -->
        @if($batches->isNotEmpty())
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Batch / Expired Date</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">Batch</th>
                            <th class="px-4 py-2 text-center font-medium text-gray-500 uppercase">Qty</th>
                            <th class="px-4 py-2 text-center font-medium text-gray-500 uppercase">Expired</th>
                            <th class="px-4 py-2 text-right font-medium text-gray-500 uppercase">Harga Beli</th>
                            <th class="px-4 py-2 text-right font-medium text-gray-500 uppercase">Harga Jual</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($batches as $batch)
                        <tr class="{{ $batch->isExpired() ? 'bg-red-50' : ($batch->isNearExpiry() ? 'bg-yellow-50' : '') }}">
                            <td class="px-4 py-2">{{ $batch->batch_number }}</td>
                            <td class="px-4 py-2 text-center">{{ $batch->quantity }}</td>
                            <td class="px-4 py-2 text-center {{ $batch->isExpired() ? 'text-red-600 font-bold' : '' }}">
                                {{ $batch->expired_date->format('d/m/Y') }}
                                @if($batch->isExpired())
                                    <span class="text-xs text-red-500">(Expired)</span>
                                @elseif($batch->isNearExpiry())
                                    <span class="text-xs text-yellow-500">(Akan expired)</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right">Rp {{ number_format($batch->purchase_price, 0, ',', '.') }}</td>
                            <td class="px-4 py-2 text-right">Rp {{ number_format($batch->selling_price, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Filter Tanggal -->
        <div class="mb-4">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Dari Tanggal</label>
                    <input type="date" name="from_date" value="{{ $fromDate ?? '' }}"
                        class="px-3 py-2 border rounded">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Sampai Tanggal</label>
                    <input type="date" name="to_date" value="{{ $toDate ?? '' }}"
                        class="px-3 py-2 border rounded">
                </div>
                <div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Filter
                    </button>
                </div>
                @if($fromDate || $toDate)
                <div>
                    <a href="{{ route('stock-card.show', $medicine->id) }}"
                       class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50">
                        Reset
                    </a>
                </div>
                @endif
            </form>
        </div>

        <!-- Stock Movement Table -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Riwayat Pergerakan Stok</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Masuk</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Keluar</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Sisa Stok</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Petugas</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @if($movements->isNotEmpty())
                        <tr class="bg-gray-100 font-semibold">
                            <td colspan="3" class="px-4 py-3 text-sm text-gray-700">Saldo Awal</td>
                            <td class="px-4 py-3 text-center text-sm">-</td>
                            <td class="px-4 py-3 text-center text-sm">-</td>
                            <td class="px-4 py-3 text-center text-sm font-bold">{{ $movements->first()->previous_stock }}</td>
                            <td colspan="2" class="px-4 py-3"></td>
                        </tr>
                        @endif
                        @foreach($movements as $movement)
                        @php
                            $in = ($movement->type === 'in' || $movement->type === 'return') ? $movement->quantity : 0;
                            $out = ($movement->type === 'out' || $movement->type === 'expired') ? $movement->quantity : 0;

                            $typeLabel = match($movement->type) {
                                'in' => match($movement->reference_type) {
                                    'purchase' => 'Pembelian',
                                    'adjustment' => 'Penyesuaian',
                                    'opname' => 'Opname',
                                    default => 'Masuk',
                                },
                                'out' => match($movement->reference_type) {
                                    'sale' => 'Penjualan',
                                    'adjustment' => 'Penyesuaian',
                                    'opname' => 'Opname',
                                    default => 'Keluar',
                                },
                                'return' => 'Retur',
                                'expired' => 'Expired',
                                default => ucfirst($movement->type),
                            };

                            $typeColor = match($movement->type) {
                                'in' => 'bg-green-100 text-green-800',
                                'out' => 'bg-red-100 text-red-800',
                                'return' => 'bg-yellow-100 text-yellow-800',
                                'expired' => 'bg-gray-100 text-gray-800',
                                default => 'bg-blue-100 text-blue-800',
                            };
                        @endphp
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $movement->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full {{ $typeColor }}">
                                    {{ $typeLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $movement->batch->batch_number ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center text-sm {{ $in > 0 ? 'text-green-600 font-medium' : 'text-gray-400' }}">
                                {{ $in > 0 ? $in : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center text-sm {{ $out > 0 ? 'text-red-600 font-medium' : 'text-gray-400' }}">
                                {{ $out > 0 ? $out : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center text-sm font-bold">
                                {{ $movement->new_stock }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 max-w-xs truncate">
                                {{ $movement->notes ?? '-' }}
                                @if($movement->reference_type === 'sale' && $movement->reference_id)
                                    <br><span class="text-xs text-blue-500">#{{ $movement->reference_id }}</span>
                                @endif
                                @if($movement->reference_type === 'purchase' && $movement->reference_id)
                                    <br><span class="text-xs text-blue-500">PO #{{ $movement->reference_id }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $movement->user->name ?? '-' }}
                            </td>
                        </tr>
                        @endforeach
                        @if($movements->isEmpty())
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                Tidak ada pergerakan stok
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $movements->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
