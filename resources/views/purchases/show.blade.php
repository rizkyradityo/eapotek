@extends('layouts.app')

@section('title', 'Detail Pembelian')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">Detail Pembelian</h2>
        <div class="space-x-2">
            @if($purchase->status === 'pending')
            <form action="{{ route('purchases.receive', $purchase->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin menerima pembelian ini? Stok akan ditambahkan.');">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Terima Stok
                </button>
            </form>
            <form action="{{ route('purchases.cancel', $purchase->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin membatalkan pembelian ini?');">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Batalkan
                </button>
            </form>
            @endif
            <a href="{{ route('purchases.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                Kembali
            </a>
        </div>
    </div>
    <div class="p-6">
        <!-- Info Header -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label class="text-sm text-gray-500">No. PO</label>
                <p class="font-bold text-lg">{{ $purchase->invoice_number }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-500">Tanggal Pembelian</label>
                <p class="font-medium">{{ $purchase->purchase_date->format('d/m/Y') }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-500">Status</label>
                <p class="font-medium">
                    <span class="px-2 py-1 text-xs rounded-full 
                        {{ $purchase->status === 'received' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $purchase->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $purchase->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ ucfirst($purchase->status) }}
                    </span>
                </p>
            </div>
        </div>

        <!-- Items Table -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Items Pembelian</h3>
            <div class="border rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Obat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kadaluarsa</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($purchase->items as $item)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium">{{ $item->medicine->name }}</div>
                                <div class="text-sm text-gray-500">{{ $item->medicine->code }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $item->batch_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $item->expired_date->format('d/m/Y') }}</td>
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
                    <span>Rp {{ number_format($purchase->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($purchase->discount_amount > 0)
                <div class="flex justify-between text-red-600">
                    <span>Discount:</span>
                    <span>- Rp {{ number_format($purchase->discount_amount, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($purchase->tax_amount > 0)
                <div class="flex justify-between">
                    <span>Pajak:</span>
                    <span>Rp {{ number_format($purchase->tax_amount, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between text-lg font-bold border-t pt-2">
                    <span>Total:</span>
                    <span>Rp {{ number_format($purchase->total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-green-600">
                    <span>Dibayar:</span>
                    <span>Rp {{ number_format($purchase->paid_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($purchase->notes)
        <div class="mt-6">
            <label class="text-sm text-gray-500">Catatan</label>
            <p class="font-medium">{{ $purchase->notes }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
