@extends('layouts.app')

@section('title', 'Hampir Kadaluarsa')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-4 border-b bg-gray-50">
        <h2 class="text-xl font-bold text-gray-800">Obat Hampir Kadaluarsa</h2>
    </div>
    <div class="p-6">
        @if($medicines->isEmpty())
        <div class="text-center py-8 text-gray-500">
            Tidak ada obat yang hampir kadaluarsa
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Obat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kadaluarsa</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stok</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Sisa Hari</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($medicines as $batch)
                    <tr class="bg-yellow-50">
                        <td class="px-6 py-4">
                            <div class="font-medium">{{ $batch->medicine->name }}</div>
                            <div class="text-sm text-gray-500">{{ $batch->medicine->code }}</div>
                        </td>
                        <td class="px-6 py-4">{{ $batch->batch_number }}</td>
                        <td class="px-6 py-4">{{ $batch->expired_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-right">{{ $batch->quantity }}</td>
                        <td class="px-6 py-4 text-right text-yellow-600 font-bold">
                            {{ now()->diffInDays($batch->expired_date) }} hari
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('medicines.show', $batch->medicine_id) }}" class="text-blue-600 hover:text-blue-900">
                                Lihat
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
