@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">Detail Transaksi</h2>
        <div class="space-x-2">
            <a href="{{ route('transactions.receipt', $sale->id) }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                Cetak Struk
            </a>
            <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                Kembali
            </a>
        </div>
    </div>
    <div class="p-6">
        <!-- Invoice Info -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label class="text-sm text-gray-500">No. Invoice</label>
                <p class="font-bold text-lg">{{ $sale->invoice_number }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-500">Tanggal</label>
                <p class="font-medium">{{ $sale->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-500">Kasir</label>
                <p class="font-medium">{{ $sale->user->name ?? '-' }}</p>
            </div>
        </div>

        <!-- Items Table -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Items</h3>
            <div class="border rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Obat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($sale->items as $item)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium">{{ $item->medicine->name }}</div>
                                <div class="text-sm text-gray-500">{{ $item->medicine->code }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $item->batch->batch_number ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary -->
        <div class="flex justify-end">
            <div class="w-64 space-y-2">
                <div class="flex justify-between">
                    <span>Subtotal:</span>
                    <span>Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($sale->discount_amount > 0)
                <div class="flex justify-between text-red-600">
                    <span>Discount:</span>
                    <span>- Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($sale->tax_amount > 0)
                <div class="flex justify-between">
                    <span>Pajak:</span>
                    <span>Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between text-lg font-bold border-t pt-2">
                    <span>Total:</span>
                    <span>Rp {{ number_format($sale->total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-green-600">
                    <span>Dibayar:</span>
                    <span>Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Kembalian:</span>
                    <span>Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($sale->notes)
        <div class="mt-6">
            <label class="text-sm text-gray-500">Catatan</label>
            <p class="font-medium">{{ $sale->notes }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
