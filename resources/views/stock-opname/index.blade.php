@extends('layouts.app')

@section('title', 'Stock Opname')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">Stock Opname</h2>
        <a href="{{ route('stock-opname.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            + Buat Opname Baru
        </a>
    </div>
    <div class="p-6">
        <div class="mb-4 flex flex-wrap gap-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Status</label>
                <select name="status" class="px-3 py-2 border rounded" onchange="window.location.href = this.value">
                    <option value="{{ route('stock-opname.index') }}" {{ request('status') == '' ? 'selected' : '' }}>Semua</option>
                    <option value="{{ route('stock-opname.index', ['status' => 'draft']) }}" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="{{ route('stock-opname.index', ['status' => 'completed']) }}" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="{{ route('stock-opname.index', ['status' => 'cancelled']) }}" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
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

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Opname</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Petugas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($opnames as $opname)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $opname->opname_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $opname->opname_date->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                            {{ $opname->description ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $opname->items_count ?? $opname->items()->count() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $opname->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $opname->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $opname->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($opname->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $opname->user->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('stock-opname.show', $opname->id) }}"
                               class="text-blue-600 hover:text-blue-900 mr-3">Detail</a>
                        </td>
                    </tr>
                    @endforeach
                    @if($opnames->isEmpty())
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            Belum ada data stock opname
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $opnames->links() }}
        </div>
    </div>
</div>
@endsection
