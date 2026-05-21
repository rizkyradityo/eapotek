@extends('layouts.app')

@section('title', 'Struk Transaksi')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white shadow-lg">
        <!-- Receipt Header -->
        <div class="p-6 text-center border-b">
            <h1 class="text-xl font-bold">E-Apotek</h1>
            <p class="text-sm text-gray-500">Jl. Apotek No. 1</p>
            <p class="text-sm text-gray-500">Telp: 021-1234567</p>
        </div>

        <!-- Receipt Body -->
        <div class="p-6">
            <div class="text-sm space-y-1 mb-4">
                <div class="flex justify-between">
                    <span>No. Invoice:</span>
                    <span class="font-medium">{{ $sale->invoice_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Tanggal:</span>
                    <span>{{ $sale->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Kasir:</span>
                    <span>{{ $sale->user->name ?? '-' }}</span>
                </div>
            </div>

            <div class="border-t border-b py-3 my-3">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500">
                            <th class="pb-2">Item</th>
                            <th class="pb-2 text-right">Qty</th>
                            <th class="pb-2 text-right">Harga</th>
                            <th class="pb-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $item)
                        <tr>
                            <td class="py-1">
                                <div class="font-medium">{{ $item->medicine->name }}</div>
                                <div class="text-xs text-gray-500">{{ $item->medicine->code }}</div>
                            </td>
                            <td class="py-1 text-right">{{ $item->quantity }}</td>
                            <td class="py-1 text-right">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="py-1 text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Summary -->
            <div class="text-sm space-y-1">
                <div class="flex justify-between">
                    <span>Subtotal:</span>
                    <span>Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($sale->discount_amount > 0)
                <div class="flex justify-between text-red-600">
                    <span>Diskon:</span>
                    <span>- Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($sale->tax_amount > 0)
                <div class="flex justify-between">
                    <span>Pajak:</span>
                    <span>Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between text-lg font-bold border-t pt-2 mt-2">
                    <span>TOTAL:</span>
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

            <div class="text-center mt-6 text-sm text-gray-500">
                <p>Terima kasih atas kunjungan Anda</p>
                <p class="mt-2">{{ now()->format('d/m/Y H:i:s') }}</p>
            </div>
        </div>
    </div>

    <!-- Print Button -->
    <div class="mt-4 text-center">
        <button onclick="window.print()" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Cetak Struk
        </button>
        <a href="{{ route('transactions.show', $sale->id) }}" class="ml-2 px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
            Kembali
        </a>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .max-w-md, .max-w-md * {
            visibility: visible;
        }
        .max-w-md {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        button {
            display: none;
        }
    }
</style>
@endsection
