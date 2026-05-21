<div class="flex flex-col lg:flex-row gap-6 h-[calc(100vh-140px)] min-h-[600px] pb-4">
    
    <!-- Left Panel - Products -->
    <div class="w-full lg:w-2/3 flex flex-col h-full bg-white/80 backdrop-blur-xl rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden">
        <!-- Search Header -->
        <div class="p-5 sm:p-6 border-b border-slate-100 bg-white/50">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" wire:model.live="search" placeholder="Cari obat (nama atau kode)..."
                    class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors shadow-sm">
            </div>
        </div>

        <!-- Products Grid -->
        <div class="flex-1 overflow-y-auto p-5 sm:p-6 bg-slate-50/50 relative custom-scrollbar">
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5">
                @forelse($medicines as $medicine)
                    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 cursor-pointer hover:shadow-md hover:border-primary-300 hover:-translate-y-1 transition-all duration-200 group flex flex-col h-full relative overflow-hidden flex-shrink-0" wire:click="addToCart({{ $medicine->id }})">
                        
                        <!-- Hover Glow -->
                        <div class="absolute inset-0 bg-gradient-to-br from-primary-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        
                        <div class="relative z-10 flex-1">
                            <div class="font-bold text-slate-800 mb-1 leading-tight group-hover:text-primary-700 transition-colors">{{ $medicine->name }}</div>
                            <div class="text-[11px] font-medium text-slate-400 mb-3 bg-slate-100 inline-block px-2 py-0.5 rounded-md">{{ $medicine->code }}</div>
                        </div>
                        
                        <div class="relative z-10 mt-auto border-t border-slate-50 pt-3 flex justify-between items-end">
                            <div>
                                <div class="text-xs text-slate-500 mb-0.5">Harga</div>
                                <span class="text-primary-600 font-bold text-sm sm:text-base">
                                    Rp {{ number_format($medicine->price, 0, ',', '.') }}
                                </span>
                            </div>
                            @php
                                $sellableStock = $medicine->batches->sum('quantity');
                            @endphp
                            <div class="text-right">
                                <span class="text-xs font-semibold px-2 py-1 bg-slate-100 text-slate-600 rounded-lg whitespace-nowrap">
                                    {{ $sellableStock }} <span class="font-normal">{{ $medicine->unit->symbol ?? '' }}</span>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Stock Status -->
                        <div class="relative z-10 mt-2">
                            @if($medicine->batches->count() > 0)
                                <div class="text-[10px] font-medium text-primary-600 bg-primary-50 px-2 py-1 rounded-md inline-block border border-primary-100">
                                    Batch: {{ $medicine->batches->first()->batch_number }}
                                </div>
                            @elseif($medicine->total_stock > 0)
                                <div class="text-[10px] font-medium text-amber-600 bg-amber-50 px-2 py-1 rounded-md inline-block border border-amber-100" title="Ada sisa stok secara fisik namun sudah kadaluarsa atau tidak aktif">
                                    Stok Kadaluarsa/Nonaktif
                                </div>
                            @else
                                <div class="text-[10px] font-medium text-red-600 bg-red-50 px-2 py-1 rounded-md inline-block border border-red-100">
                                    Stok Habis
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center py-12 text-slate-400">
                        <svg class="w-16 h-16 mb-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <p class="text-lg font-medium text-slate-500">Pencarian tidak ditemukan</p>
                    </div>
                @endforelse
            </div>
            <!-- Bottom padding for scroll -->
            <div class="h-6"></div>
        </div>
    </div>

    <!-- Right Panel - Cart -->
    <div class="w-full lg:w-1/3 flex flex-col h-full bg-white/90 backdrop-blur-xl rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-200/60 overflow-hidden">
        
        <!-- Cart Header -->
        <div class="p-5 border-b border-slate-100 bg-slate-800 text-white flex justify-between items-center shrink-0 shadow-sm relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-slate-800 to-slate-700"></div>
            <h2 class="text-lg font-bold relative z-10 flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Keranjang
            </h2>
            <span class="bg-primary-500 text-white text-xs font-bold px-2.5 py-1 rounded-full relative z-10 shadow-glow">
                {{ count($cart) }} Item
            </span>
        </div>

        <!-- Cart Items list -->
        <div class="flex-1 overflow-y-auto p-3 bg-slate-50/30 custom-scrollbar">
            @if(empty($cart))
                <div class="flex flex-col items-center justify-center h-full text-slate-400 p-6 text-center">
                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <p class="font-medium text-slate-500">Keranjang masih kosong</p>
                    <p class="text-xs text-slate-400 mt-1">Pilih obat dari daftar untuk menambahkan ke keranjang.</p>
                </div>
            @else
                <div class="space-y-2">
                    @foreach($cart as $key => $item)
                        <div class="bg-white p-3.5 rounded-2xl border border-slate-100 shadow-sm flex flex-col gap-2 hover:border-primary-200 transition-colors">
                            <div class="flex justify-between items-start">
                                <div class="font-bold text-slate-800 text-sm leading-tight pr-2">{{ $item['medicine_name'] }}</div>
                                <button wire:click="removeFromCart('{{ $key }}')" class="text-slate-300 hover:text-red-500 transition-colors p-1 bg-slate-50 hover:bg-red-50 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                            
                            <div class="flex items-center justify-between mt-1">
                                <div class="flex items-center gap-2 bg-slate-50 p-1 rounded-xl border border-slate-100">
                                    <button class="w-7 h-7 flex items-center justify-center text-slate-500 hover:text-primary-600 hover:bg-white rounded-lg transition-colors font-bold shadow-sm" wire:click="updateQuantity('{{ $key }}', {{ is_array($item['quantity']) ? current($item['quantity']) : $item['quantity'] }} - 1)">-</button>
                                    <input type="number" min="1" value="{{ is_array($item['quantity']) ? current($item['quantity']) : $item['quantity'] }}" wire:change="updateQuantity('{{ $key }}', $event.target.value)" class="w-8 text-center text-sm font-bold text-slate-700 bg-transparent border-none p-0 focus:ring-0">
                                    <button class="w-7 h-7 flex items-center justify-center text-slate-500 hover:text-primary-600 hover:bg-white rounded-lg transition-colors font-bold shadow-sm" wire:click="updateQuantity('{{ $key }}', {{ is_array($item['quantity']) ? current($item['quantity']) : $item['quantity'] }} + 1)">+</button>
                                </div>
                                <div class="text-right">
                                    <div class="text-[10px] text-slate-400 mb-0.5">@ Rp {{ number_format($item['unit_price'], 0, ',', '.') }}</div>
                                    <div class="font-bold text-primary-600 text-sm">
                                        Rp {{ number_format($item['unit_price'] * (is_array($item['quantity']) ? current($item['quantity']) : $item['quantity']), 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Cart Summary & Actions -->
        <div class="p-5 bg-white border-t border-slate-100 shrink-0 shadow-[0_-4px_20px_rgba(0,0,0,0.02)] z-10">
            
            <div class="space-y-2 mb-4 pb-4 border-b border-dashed border-slate-200">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-slate-500 font-medium">Subtotal</span>
                    <span class="font-semibold text-slate-700">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                
                @if($discountTotal > 0)
                <div class="flex justify-between items-center text-sm text-red-500">
                    <span class="font-medium">Total Diskon</span>
                    <span class="font-bold">- Rp {{ number_format($discountTotal, 0, ',', '.') }}</span>
                </div>
                @endif
                
                <div class="flex justify-between items-center pt-1">
                    <span class="text-base font-bold text-slate-800">Total Akhir</span>
                    <span class="text-xl font-black text-primary-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="space-y-3 mb-4">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 ml-1">Diskon (%)</label>
                        <input type="number" min="0" max="100" wire:model.live="discount_percent"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors text-slate-700 font-semibold text-center">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 ml-1">Diskon (Rp)</label>
                        <input type="number" min="0" wire:model.live="discount_amount"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors text-slate-700 font-semibold text-center">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 ml-1">Metode Bayar</label>
                        <select wire:model.live="payment_method" class="w-full px-2 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                            <option value="cash">💵 Tunai (Cash)</option>
                            <option value="debit">💳 Kartu Debit</option>
                            <option value="credit">💳 Kartu Kredit</option>
                            <option value="ewallet">📱 E-Wallet/QRIS</option>
                        </select>
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[10px] font-bold text-primary-600 uppercase tracking-wider mb-1 ml-1">Jumlah Bayar</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                <span class="text-slate-400 font-bold text-xs">Rp</span>
                            </div>
                            <input type="number" min="0" wire:model.live.debounce.300ms="paid_amount"
                                class="w-full pl-7 pr-2 py-2 bg-primary-50 border border-primary-200 rounded-xl text-primary-700 font-bold text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors shadow-inner">
                        </div>
                    </div>
                </div>

                @if($paid_amount > 0)
                <div class="bg-{{ $change >= 0 ? 'emerald' : 'red' }}-50 border border-{{ $change >= 0 ? 'emerald' : 'red' }}-100 p-2 rounded-xl flex justify-between items-center px-3">
                    <span class="text-xs font-bold text-{{ $change >= 0 ? 'emerald' : 'red' }}-700">
                        {{ $change >= 0 ? 'Kembalian' : 'Kurang Bayar' }}
                    </span>
                    <span class="text-sm font-black text-{{ $change >= 0 ? 'emerald' : 'red' }}-600">
                        Rp {{ number_format(abs($change), 0, ',', '.') }}
                    </span>
                </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2">
                <button wire:click="clearCart" 
                    class="flex-1 bg-slate-100 text-slate-600 py-3 rounded-xl font-bold hover:bg-slate-200 hover:text-slate-700 transition-colors text-xs sm:text-sm flex items-center justify-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    Batal
                </button>
                <button wire:click="processPayment" 
                    class="flex-[2] bg-gradient-to-r from-primary-500 to-primary-600 text-white py-3 rounded-xl font-bold hover:from-primary-600 hover:to-primary-700 transition-all duration-300 shadow-glow disabled:opacity-50 disabled:shadow-none flex items-center justify-center gap-1.5 group transform hover:-translate-y-0.5 text-xs sm:text-sm"
                    {{ empty($cart) || $paid_amount < $total ? 'disabled' : '' }}>
                    <svg class="w-5 h-5 transform group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Bayar & Cetak
                </button>
            </div>
        </div>
    </div>

    <!-- Flash Messages (Overlaid) -->
    @if(session()->has('success'))
        <div class="fixed top-24 right-4 bg-white border-l-4 border-emerald-500 text-slate-800 p-4 rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.1)] z-[100] flex items-start gap-3 animate-[slideIn_0.3s_ease-out_forwards]">
            <div class="text-emerald-500">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-800">Berhasil</h4>
                <p class="text-sm text-slate-600 mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="fixed top-24 right-4 bg-white border-l-4 border-red-500 text-slate-800 p-4 rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.1)] z-[100] flex items-start gap-3 animate-[slideIn_0.3s_ease-out_forwards]">
            <div class="text-red-500">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-800">Gagal</h4>
                <p class="text-sm text-slate-600 mt-0.5">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</div>
