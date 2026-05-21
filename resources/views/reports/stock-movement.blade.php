@extends('layouts.app')

@section('title', 'Laporan Pergerakan Stok')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-4 border-b bg-gray-50">
        <h2 class="text-xl font-bold text-gray-800">Laporan Pergerakan Stok</h2>
    </div>
    <div class="p-6">
        <!-- Filters -->
        <div class="mb-6 flex flex-wrap gap-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Obat</label>
                <select name="medicine_id" class="px-3 py-2 border rounded" onchange="window.location.href = this.value">
                    <option value="{{ route('reports.stock-movement') }}" {{ request('medicine_id') == '' ? 'selected' : '' }}>Semua Obat</option>
                    @foreach($medicines as $medicine)
                        <option value="{{ route('reports.stock-movement', ['medicine_id' => $medicine->id]) }}" {{ request('medicine_id') == $medicine->id ? 'selected' : '' }}>
                            {{ $medicine->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Jenis</label>
                <select name="type" class="px-3 py-2 border rounded" onchange="window.location.href = this.value">
                    <option value="{{ route('reports.stock-movement') }}" {{ request('type') == '' ? 'selected' : '' }}>Semua</option>
                    <option value="{{ route('reports.stock-movement', ['type' => 'in']) }}" {{ request('type') == 'in' ? 'selected' : '' }}>Masuk</option>
                    <option value="{{ route('reports.stock-movement', ['type' => 'out']) }}" {{ request('type') == 'out' ? 'selected' : '' }}>Keluar</option>
                    <option value="{{ route('reports.stock-movement', ['type' => 'adjustment']) }}" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Penyesuaian</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Dari Tanggal</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" 
                    class="px-3 py-2 border rounded" onchange="this.form.submit()">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Sampai Tanggal</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" 
                    class="px-3 py-2 border rounded" onchange="this.form.submit()">
            </div>
        </div>

        <!-- Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-green-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Stok Masuk</p>
                <p class="text-2xl font-bold text-green-600">{{ $report['summary']['stock_in'] }}</p>
            </div>
            <div class="bg-red-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Stok Keluar</p>
                <p class="text-2xl font-bold text-red-600">{{ $report['summary']['stock_out'] }}</p>
            </div>
            <div class="bg-blue-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Net Movement</p>
                <p class="text-2xl font-bold text-blue-600">{{ $report['summary']['net_movement'] }}</p>
            </div>
        </div>

        <!-- Movements Table -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Pergerakan</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Obat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stok Sebelum</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stok Sesudah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($report['movements'] as $movement)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $movement->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium">{{ $movement->medicine->name ?? '-' }}</div>
                                <div class="text-sm text-gray-500">{{ $movement->medicine->code ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $movement->type === 'in' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $movement->type === 'out' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $movement->type === 'adjustment' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $movement->type === 'return' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                    {{ ucfirst($movement->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">{{ $movement->quantity }}</td>
                            <td class="px-6 py-4 text-right">{{ $movement->previous_stock }}</td>
                            <td class="px-6 py-4 text-right">{{ $movement->new_stock }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $movement->notes }}</td>
                        </tr>
                        @endforeach
                        @if($report['movements']->isEmpty())
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                Tidak ada data pergerakan stok
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $report['movements']->links() }}
        </div>
    </div>
</div>
@endsection
