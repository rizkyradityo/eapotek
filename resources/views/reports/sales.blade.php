@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Laporan Penjualan</h2>
        <p class="text-slate-500 text-sm mt-1">Ringkasan transaksi dan performa penjualan apotek Anda.</p>
    </div>
</div>

<div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden mb-8 p-6 sm:p-8 relative">
    
    <!-- Background Decoration -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-primary-100/50 rounded-full blur-3xl -z-10 pointer-events-none transform translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 left-0 w-48 h-48 bg-blue-100/50 rounded-full blur-2xl -z-10 pointer-events-none transform -translate-x-1/2 translate-y-1/2"></div>

    <!-- Filters Form -->
    <form class="mb-8 p-5 bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100">
        <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            Filter Rentang Waktu
        </h3>
        <div class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-slate-600 mb-1.5">Dari Tanggal</label>
                <div class="relative">
                    <input type="date" name="from_date" value="{{ request('from_date') }}" 
                        class="w-full pl-3 pr-10 py-2.5 bg-slate-50 border border-slate-200 text-slate-800 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors shadow-sm" onchange="this.form.submit()">
                </div>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-slate-600 mb-1.5">Sampai Tanggal</label>
                <div class="relative">
                    <input type="date" name="to_date" value="{{ request('to_date') }}" 
                        class="w-full pl-3 pr-10 py-2.5 bg-slate-50 border border-slate-200 text-slate-800 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors shadow-sm" onchange="this.form.submit()">
                </div>
            </div>
        </div>
    </form>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-10">
        <!-- Card 1 -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-6 rounded-2xl shadow-sm text-white relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
            <svg class="absolute -right-4 -bottom-4 w-24 h-24 text-white opacity-20 transform group-hover:scale-110 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            <p class="text-blue-100 font-medium text-sm mb-1 uppercase tracking-wide">Total Transaksi</p>
            <p class="text-3xl font-bold">{{ $report['summary']['total_sales'] }}</p>
        </div>
        
        <!-- Card 2 -->
        <div class="bg-gradient-to-br from-primary-500 to-primary-600 p-6 rounded-2xl shadow-sm text-white relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
            <svg class="absolute -right-4 -bottom-4 w-24 h-24 text-white opacity-20 transform group-hover:scale-110 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-primary-100 font-medium text-sm mb-1 uppercase tracking-wide">Total Pendapatan</p>
            <p class="text-2xl font-bold truncate" title="Rp {{ number_format($report['summary']['total_revenue'], 0, ',', '.') }}">
                Rp {{ number_format($report['summary']['total_revenue'], 0, ',', '.') }}
            </p>
        </div>
        
        <!-- Card 3 -->
        <div class="bg-gradient-to-br from-yellow-400 to-yellow-500 p-6 rounded-2xl shadow-sm text-white relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
            <svg class="absolute -right-4 -bottom-4 w-24 h-24 text-white opacity-20 transform group-hover:scale-110 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            <p class="text-yellow-100 font-medium text-sm mb-1 uppercase tracking-wide">Total Diskon</p>
            <p class="text-2xl font-bold truncate">Rp {{ number_format($report['summary']['total_discount'], 0, ',', '.') }}</p>
        </div>
        
        <!-- Card 4 -->
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 p-6 rounded-2xl shadow-sm text-white relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
            <svg class="absolute -right-4 -bottom-4 w-24 h-24 text-white opacity-20 transform group-hover:scale-110 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            <p class="text-indigo-100 font-medium text-sm mb-1 uppercase tracking-wide">Rata-rata Transaksi</p>
            <p class="text-2xl font-bold truncate">Rp {{ number_format($report['summary']['average_sale'], 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Daily Sales Chart (Placeholder) -->
    <div class="mb-10">
        <h3 class="text-lg font-bold text-slate-800 mb-5 flex items-center gap-2">
            Grafik Penjualan Harian
        </h3>
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] relative">
            <div class="flex items-end justify-between space-x-1 sm:space-x-2 h-56 pt-6">
                <!-- Using Primary theme color for bars -->
                @forelse($report['daily_sales'] as $day)
                    @php
                        $maxValue = max($report['daily_sales']->max('revenue'), 1);
                        $heightPercent = ($day->revenue / $maxValue) * 100;
                    @endphp
                    <div class="flex-1 flex flex-col items-center group relative h-full justify-end">
                        <div class="absolute -top-10 opacity-0 group-hover:opacity-100 transition-opacity bg-slate-800 text-white text-xs py-1 px-2 rounded-lg pointer-events-none whitespace-nowrap z-10 shadow-lg">
                            Rp {{ number_format($day->revenue, 0, ',', '.') }}
                        </div>
                        <div class="w-full sm:w-10/12 bg-gradient-to-t from-primary-400 to-primary-600 rounded-t-lg group-hover:from-primary-500 group-hover:to-primary-700 transition-all duration-300 group-hover:shadow-[0_0_15px_rgba(16,185,129,0.5)]" style="height: {{ $heightPercent }}%;"></div>
                        <span class="text-[10px] sm:text-xs font-semibold text-slate-500 mt-2 transform -rotate-45 sm:rotate-0 origin-top-left sm:translate-y-1">
                            {{ \Carbon\Carbon::parse($day->date)->format('d/m') }}
                        </span>
                    </div>
                @empty
                    <div class="w-full h-full flex items-center justify-center text-slate-400 font-medium text-sm">
                        Tidak ada data penjualan
                    </div>
                @endforelse
            </div>
            <!-- Grid lines -->
            <div class="absolute top-6 left-6 right-6 bottom-8 border-t border-b border-dashed border-slate-200 pointer-events-none -z-10 flex flex-col justify-between">
                <div></div><div></div><div></div>
            </div>
        </div>
    </div>

    <!-- Data Tables Section Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        
        <!-- Payment Methods -->
        <div class="xl:col-span-1 border border-slate-100 rounded-2xl p-6 bg-white shadow-[0_2px_15px_-3px_rgba(0,0,0,0.02)]">
            <h3 class="text-lg font-bold text-slate-800 mb-5">Metode Pembayaran</h3>
            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50/80">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-slate-600">Metode</th>
                            <th class="px-4 py-3 text-right font-semibold text-slate-600">Jml</th>
                            <th class="px-4 py-3 text-right font-semibold text-slate-600">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($report['payment_methods'] as $method)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-slate-800 capitalize">{{ $method->payment_method }}</td>
                            <td class="px-4 py-3 text-right text-slate-600 font-bold bg-slate-50/50">{{ $method->count }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-primary-600">Rp {{ number_format($method->total, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-slate-400">Belum ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="xl:col-span-2 border border-slate-100 rounded-2xl p-6 bg-white shadow-[0_2px_15px_-3px_rgba(0,0,0,0.02)]">
            <h3 class="text-lg font-bold text-slate-800 mb-5">Obat Terlaris</h3>
            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50/80">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600">Obat</th>
                            <th class="px-5 py-3 text-right font-semibold text-slate-600">Terjual</th>
                            <th class="px-5 py-3 text-right font-semibold text-slate-600">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($topMedicines as $index => $item)
                        <tr class="hover:bg-primary-50/50 transition-colors group">
                            <td class="px-5 py-3.5 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full {{ $index < 3 ? 'bg-primary-100 text-primary-600' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center font-bold text-xs shadow-sm">
                                    #{{ $index + 1 }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800">{{ $item->medicine->name ?? '-' }}</div>
                                    <div class="text-xs text-slate-500 font-medium">{{ $item->medicine->code ?? '-' }}</div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-right font-bold text-slate-700 bg-slate-50/30">
                                {{ $item->total_quantity }} <span class="text-slate-400 text-xs font-normal ml-1">{{ $item->medicine->unit->symbol ?? '' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-right font-semibold text-primary-600 group-hover:text-primary-700">
                                Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-5 py-8 text-center text-slate-400">Belum ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
</div>
@endsection