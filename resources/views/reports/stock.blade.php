@extends('layouts.app')

@section('title', 'Laporan Stok')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-4 border-b bg-gray-50">
        <h2 class="text-xl font-bold text-gray-800">Laporan Stok Obat</h2>
    </div>
    <div class="p-6">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Total Obat</p>
                <p class="text-2xl font-bold text-blue-600">{{ $report['summary']['total_medicines'] }}</p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Total Item Stok</p>
                <p class="text-2xl font-bold text-green-600">{{ $report['summary']['total_items'] }}</p>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Nilai Stok</p>
                <p class="text-2xl font-bold text-yellow-600">Rp {{ number_format($report['summary']['total_stock_value'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-red-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Stok Menipis</p>
                <p class="text-2xl font-bold text-red-600">{{ $report['summary']['low_stock_count'] }} item</p>
            </div>
        </div>

        <!-- Low Stock Items -->
        @if(count($report['low_stock_items']) > 0)
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Stok Menipis</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Obat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stok</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Minimum</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($report['low_stock_items'] as $item)
                        <tr class="bg-red-50">
                            <td class="px-6 py-4">{{ $item['code'] }}</td>
                            <td class="px-6 py-4">{{ $item['name'] }}</td>
                            <td class="px-6 py-4">{{ $item['category'] }}</td>
                            <td class="px-6 py-4 text-right text-red-600 font-bold">{{ $item['current_stock'] }}</td>
                            <td class="px-6 py-4 text-right">{{ $item['min_stock'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Near Expiry Items -->
        @if(count($report['near_expiry_items']) > 0)
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Hampir Kadaluarsa (≤ 30 Hari)</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Obat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kadaluarsa</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stok</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Sisa Hari</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($report['near_expiry_items'] as $item)
                        <tr class="bg-yellow-50">
                            <td class="px-6 py-4">
                                <div class="font-medium">{{ $item['name'] }}</div>
                                <div class="text-sm text-gray-500">{{ $item['code'] }}</div>
                            </td>
                            <td class="px-6 py-4">{{ $item['batch_number'] }}</td>
                            <td class="px-6 py-4">{{ $item['expired_date'] }}</td>
                            <td class="px-6 py-4 text-right">{{ $item['quantity'] }}</td>
                            <td class="px-6 py-4 text-right text-yellow-600 font-bold">{{ $item['days_until_expiry'] }} hari</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if(count($report['low_stock_items']) === 0 && count($report['near_expiry_items']) === 0)
        <div class="text-center py-8 text-gray-500">
            Tidak ada stok menipis atau hampir kadaluarsa
        </div>
        @endif
    </div>
</div>
@endsection
