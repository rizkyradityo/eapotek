@extends('layouts.app')

@section('title', 'Manajemen Pembelian')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">Manajemen Pembelian</h2>
        <a href="{{ route('purchases.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            + Buat Pembelian
        </a>
    </div>
    <div class="p-6">
        <!-- Filters -->
        <div class="mb-6 flex flex-wrap gap-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Status</label>
                <select class="px-3 py-2 border rounded" onchange="window.location.href = this.value">
                    <option value="{{ route('purchases.index') }}" {{ request('status') == '' ? 'selected' : '' }}>Semua</option>
                    <option value="{{ route('purchases.index', ['status' => 'pending']) }}" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="{{ route('purchases.index', ['status' => 'received']) }}" {{ request('status') == 'received' ? 'selected' : '' }}>Diterima</option>
                    <option value="{{ route('purchases.index', ['status' => 'cancelled']) }}" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. PO</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($purchases as $purchase)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('purchases.show', $purchase->id) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                {{ $purchase->invoice_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $purchase->purchase_date->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $purchase->supplier->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            Rp {{ number_format($purchase->total, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $purchase->status === 'received' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $purchase->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $purchase->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($purchase->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('purchases.show', $purchase->id) }}" class="text-blue-600 hover:text-blue-900 mr-2">Lihat</a>
                            @if($purchase->status === 'pending')
                            <form action="{{ route('purchases.receive', $purchase->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin menerima pembelian ini? Stok akan ditambahkan.');">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-900 mr-2">Terima</button>
                            </form>
                            <form action="{{ route('purchases.cancel', $purchase->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin membatalkan pembelian ini?');">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-900">Batal</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @if($purchases->isEmpty())
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada pembelian
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $purchases->links() }}
        </div>
    </div>
</div>
@endsection
