@extends('layouts.app')

@section('title', 'Riwayat Stok - ' . $medicine->name)

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Riwayat Stok</h2>
            <p class="text-sm text-gray-500">{{ $medicine->code }} - {{ $medicine->name }}</p>
        </div>
        <a href="{{ route('stock-card.index') }}" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50 text-sm">
            Kembali
        </a>
    </div>

    <div class="p-6">
        <!-- Info Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Stok Saat Ini</p>
                <p class="text-2xl font-bold text-blue-600">{{ $totalStock }}</p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Total Pembelian</p>
                <p class="text-2xl font-bold text-green-600">{{ $stockIn }}</p>
            </div>
            <div class="bg-red-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Total Pengeluaran</p>
                <p class="text-2xl font-bold text-red-600">{{ $stockOut }}</p>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Min. Stok</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $medicine->min_stock }}</p>
            </div>
        </div>

        <!-- Filter Tanggal -->
        <div class="mb-4">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Dari Tanggal</label>
                    <input type="date" name="from_date" value="{{ $fromDate ?? '' }}"
                        class="px-3 py-2 border rounded text-sm">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Sampai Tanggal</label>
                    <input type="date" name="to_date" value="{{ $toDate ?? '' }}"
                        class="px-3 py-2 border rounded text-sm">
                </div>
                <div>
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700 text-sm">
                        Filter
                    </button>
                </div>
                @if($fromDate || $toDate)
                <div>
                    <a href="{{ route('stock-card.show', $medicine->id) }}"
                       class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50 text-sm">
                        Reset
                    </a>
                </div>
                @endif
            </form>
        </div>

        <!-- Stock History Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 text-sm">
                <thead>
                    <tr class="bg-amber-50 border-b border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 whitespace-nowrap">Tanggal Jam</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Jenis</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Asal / Tujuan</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Masuk</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Keluar</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Sisa</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($movements as $movement)
                    @php
                        $isPurchase = $movement->reference_type === 'purchase';
                        $masuk = $isPurchase ? $movement->quantity : 0;
                        $keluar = !$isPurchase ? $movement->quantity : 0;

                        if ($isPurchase) {
                            $purchase = $purchases->get($movement->reference_id);
                            $asal = $purchase?->supplier?->name ?? ('Invoice #' . $movement->reference_id);
                            $subInfo = $purchase?->invoice_number;
                        } else {
                            $sale = $sales->get($movement->reference_id);
                            $asal = $sale?->invoice_number ?? ('Penjualan #' . $movement->reference_id);
                            $subInfo = null;
                        }
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-gray-600">
                            {{ $movement->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($isPurchase)
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 font-medium">Pembelian</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 font-medium">Pengeluaran</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-700">
                            {{ $asal }}
                            @if($subInfo)
                                <br><span class="text-xs text-gray-400">{{ $subInfo }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center font-medium {{ $masuk > 0 ? 'text-green-600' : 'text-gray-300' }}">
                            {{ $masuk > 0 ? $masuk : '0' }}
                        </td>
                        <td class="px-4 py-3 text-center font-medium {{ $keluar > 0 ? 'text-red-600' : 'text-gray-300' }}">
                            {{ $keluar > 0 ? $keluar : '0' }}
                        </td>
                        <td class="px-4 py-3 text-center font-bold text-gray-800">
                            {{ $movement->new_stock }}
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $movement->user->name ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                            Tidak ada riwayat stok untuk periode ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $movements->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
