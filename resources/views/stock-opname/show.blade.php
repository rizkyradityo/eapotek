@extends('layouts.app')

@section('title', 'Detail Stock Opname')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Detail Stock Opname</h2>
            <p class="text-sm text-gray-500">{{ $opname->opname_number }} - {{ $opname->opname_date->format('d/m/Y') }}</p>
        </div>
        <div class="flex gap-2">
            @if($opname->isDraft())
                <form action="{{ route('stock-opname.apply', $opname->id) }}" method="POST" class="inline"
                    onsubmit="return confirm('Yakin ingin menerapkan semua adjustment? Stok akan berubah.')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Terapkan Adjustment
                    </button>
                </form>
                <form action="{{ route('stock-opname.cancel', $opname->id) }}" method="POST" class="inline"
                    onsubmit="return confirm('Yakin ingin membatalkan opname ini?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Batalkan
                    </button>
                </form>
            @endif
            <a href="{{ route('stock-opname.index') }}" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50">
                Kembali
            </a>
        </div>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Status</p>
                <p class="text-lg font-semibold">
                    <span class="px-2 py-1 text-xs rounded-full
                        {{ $opname->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $opname->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $opname->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ ucfirst($opname->status) }}
                    </span>
                </p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Total Item</p>
                <p class="text-lg font-semibold">{{ $opname->items->count() }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600">Item Dengan Selisih</p>
                <p class="text-lg font-semibold {{ $opname->items->where('difference', '!=', 0)->count() > 0 ? 'text-red-600' : 'text-green-600' }}">
                    {{ $opname->items->where('difference', '!=', 0)->count() }}
                </p>
            </div>
        </div>

        @if($opname->description)
        <div class="mb-4">
            <p class="text-sm font-medium text-gray-700">Deskripsi:</p>
            <p class="text-sm text-gray-600">{{ $opname->description }}</p>
        </div>
        @endif

        @if($opname->notes)
        <div class="mb-4">
            <p class="text-sm font-medium text-gray-700">Catatan:</p>
            <p class="text-sm text-gray-600">{{ $opname->notes }}</p>
        </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Obat</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stok Sistem</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stok Aktual</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Selisih</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($opname->items as $item)
                    <tr class="{{ $item->difference != 0 ? 'bg-red-50' : '' }}">
                        <td class="px-4 py-3">
                            <div class="font-medium text-sm">{{ $item->medicine->name ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $item->medicine->code ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            @if($item->batch)
                                <div>{{ $item->batch->batch_number }}</div>
                                <div class="text-xs text-gray-400">Exp: {{ $item->batch->expired_date->format('d/m/Y') }}</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-sm font-medium" id="sys-qty-{{ $item->id }}">
                            {{ $item->system_quantity }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($opname->isDraft())
                            <input type="number" min="0" value="{{ $item->actual_quantity }}"
                                class="actual-input w-20 text-center px-2 py-1 border border-gray-300 rounded text-sm"
                                data-item-id="{{ $item->id }}"
                                data-system="{{ $item->system_quantity }}">
                            @else
                            <span class="text-sm font-medium">{{ $item->actual_quantity }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="difference-value text-sm font-bold {{ $item->difference > 0 ? 'text-green-600' : ($item->difference < 0 ? 'text-red-600' : 'text-gray-500') }}">
                                {{ $item->difference > 0 ? '+' : '' }}{{ $item->difference }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 max-w-xs truncate">
                            {{ $item->notes ?? '-' }}
                        </td>
                    </tr>
                    @endforeach
                    @if($opname->items->isEmpty())
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            Tidak ada item dalam opname ini
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($opname->isDraft())
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.actual-input');
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                const itemId = this.dataset.itemId;
                const system = parseInt(this.dataset.system);
                const actual = parseInt(this.value) || 0;
                const diff = actual - system;

                const row = this.closest('tr');
                const diffSpan = row.querySelector('.difference-value');
                diffSpan.textContent = diff > 0 ? '+' + diff : diff;
                diffSpan.className = 'difference-value text-sm font-bold ' +
                    (diff > 0 ? 'text-green-600' : (diff < 0 ? 'text-red-600' : 'text-gray-500'));

                row.className = diff !== 0 ? 'bg-red-50' : '';

                fetch('{{ route("stock-opname.update-item", $opname->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        item_id: itemId,
                        actual_quantity: actual
                    })
                });
            });
        });
    });
</script>
@endpush
@endif
@endsection
