@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-4 border-b bg-gray-50">
        <h2 class="text-xl font-bold text-gray-800">Riwayat Transaksi</h2>
    </div>
    <div class="p-6">
        <!-- Filters -->
        <div class="mb-6 flex flex-wrap gap-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Status</label>
                <select class="px-3 py-2 border rounded" onchange="window.location.href = this.value">
                    <option value="{{ route('transactions.index') }}" {{ request('status') == '' ? 'selected' : '' }}>Semua</option>
                    <option value="{{ route('transactions.index', ['status' => 'completed']) }}" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="{{ route('transactions.index', ['status' => 'cancelled']) }}" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kasir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($sales as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('transactions.show', $sale->id) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                {{ $sale->invoice_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $sale->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $sale->user->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $sale->items->count() }} items
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            Rp {{ number_format($sale->total, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $sale->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $sale->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $sale->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                {{ ucfirst($sale->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('transactions.show', $sale->id) }}" class="text-blue-600 hover:text-blue-900 mr-2">Lihat</a>
                            <a href="{{ route('transactions.receipt', $sale->id) }}" class="text-gray-600 hover:text-gray-900 mr-2">Cetak</a>
                            @if($sale->status === 'completed')
                            <form action="{{ route('transactions.cancel', $sale->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin membatalkan transaksi ini?');">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-900">Batal</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @if($sales->isEmpty())
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada transaksi
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $sales->links() }}
        </div>
    </div>
</div>
@endsection
