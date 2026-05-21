@extends('layouts.app')

@section('title', 'Buat Pembelian Baru')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Buat Pembelian Baru</h2>
            <p class="text-slate-500 text-sm mt-1">Catat transaksi pembelian stok obat dari supplier.</p>
        </div>
        <a href="{{ route('purchases.index') }}" class="text-slate-500 hover:text-slate-700 font-medium text-sm flex items-center gap-1.5 transition-colors bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
    </div>

    <!-- Error Alert Container -->
    <div id="error-container" class="hidden mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-sm">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-bold text-red-800">Terdapat kesalahan pada input Anda:</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul id="error-list" class="list-disc pl-5 space-y-1"></ul>
                </div>
            </div>
        </div>
    </div>

    <form id="purchase-form" action="{{ route('purchases.store') }}" method="POST" class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden">
        @csrf
        
        <!-- Header Info -->
        <div class="p-6 sm:p-8 border-b border-slate-100 bg-slate-50/50">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Supplier <span class="text-slate-400 font-normal">(Opsional)</span></label>
                    <select name="supplier_id" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors shadow-sm font-medium text-slate-700">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal Pembelian <span class="text-red-500">*</span></label>
                    <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" required
                        class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors shadow-sm font-medium text-slate-700">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Catatan</label>
                    <input type="text" name="notes" placeholder="Catatan internal..."
                        class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors shadow-sm">
                </div>
            </div>
        </div>

        <!-- Items -->
        <div class="p-6 sm:p-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Daftar Item Obat
                </h3>
                <button type="button" onclick="addItem()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Baris
                </button>
            </div>
            
            <!-- Table Header for items -->
            <div class="hidden md:grid grid-cols-12 gap-3 mb-2 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">
                <div class="col-span-3">Obat</div>
                <div class="col-span-2">No. Batch</div>
                <div class="col-span-2">Kadaluarsa</div>
                <div class="col-span-1">Qty</div>
                <div class="col-span-2 text-right">Harga Beli</div>
                <div class="col-span-2 text-right">Subtotal</div>
            </div>

            <div id="items-container" class="space-y-3">
                <!-- Items will be added here via JavaScript -->
            </div>
        </div>

        <!-- Checkout Summary -->
        <div class="p-6 sm:p-8 bg-slate-50 border-t border-slate-100 pb-20 sm:pb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-8">
                <!-- Left instructions -->
                <div class="w-full sm:w-1/2 text-sm text-slate-500 bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <p class="font-bold text-slate-700 mb-1 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Informasi
                    </p>
                    <p>Subtotal dihitung otomatis. Jika terdapat bagian kolom input bergaris merah nanti, berarti data yang Anda masukkan salah atau kosong sesuai pesan error.</p>
                </div>
                
                <!-- Right calculation -->
                <div class="w-full sm:w-96 space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="font-semibold text-slate-600">Subtotal Item:</span>
                        <span id="subtotal" class="font-bold text-slate-800">Rp 0</span>
                    </div>
                    
                    <div class="flex justify-between items-center text-sm">
                        <span class="font-semibold text-slate-600">Diskon Pembelian:</span>
                        <div class="relative w-32">
                            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                <span class="text-slate-400 font-bold text-xs">- Rp</span>
                            </div>
                            <input type="number" name="discount_amount" value="0" min="0" 
                                class="w-full pl-9 pr-3 py-1.5 bg-white border border-slate-200 rounded-lg text-right text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary-500 transition-colors shadow-sm" onkeyup="calculateTotal()" onchange="calculateTotal()">
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center text-sm">
                        <span class="font-semibold text-slate-600">Pajak Tambahan:</span>
                        <div class="relative w-32">
                            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                <span class="text-slate-400 font-bold text-xs">+ Rp</span>
                            </div>
                            <input type="number" name="tax_amount" value="0" min="0" 
                                class="w-full pl-9 pr-3 py-1.5 bg-white border border-slate-200 rounded-lg text-right text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary-500 transition-colors shadow-sm" onkeyup="calculateTotal()" onchange="calculateTotal()">
                        </div>
                    </div>
                    
                    <div class="border-t border-dashed border-slate-300 my-2 pt-2 flex justify-between items-center">
                        <span class="text-base font-bold text-slate-800">Total Tagihan:</span>
                        <span id="total" class="text-xl font-black text-primary-600">Rp 0</span>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 flex justify-end items-center gap-3 border-t border-slate-200 pt-5">
                <a href="{{ route('purchases.index') }}" class="px-6 py-2.5 text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition-colors shadow-sm">
                    Batal
                </a>
                <button type="submit" id="btn-submit" class="px-8 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 rounded-xl transition-all shadow-glow flex items-center justify-center gap-2 transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    Simpan Pembelian
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Template for new item -->
<template id="item-template">
    <div class="item-row bg-white p-4 rounded-xl border border-slate-200 shadow-sm relative group hover:border-primary-300 transition-colors" data-index="__INDEX__">
        <!-- Delete Button for Mobile -->
        <button type="button" onclick="removeItem(this)" class="md:hidden absolute -top-3 -right-3 bg-red-100 text-red-500 hover:bg-red-500 hover:text-white rounded-full p-1.5 shadow-sm transition-colors border border-white">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
            
            <div class="md:col-span-3">
                <label class="md:hidden block text-xs font-bold text-slate-500 uppercase mb-1">Obat</label>
                <select name="items[__INDEX__][medicine_id]" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors font-medium text-slate-700 shadow-inner validation-target" onchange="updatePrice(this)">
                    <option value="">Pilih Obat...</option>
                    @foreach($medicines as $medicine)
                        <option value="{{ $medicine->id }}" data-price="{{ $medicine->price }}">
                            {{ $medicine->name }} - Rp {{ number_format($medicine->price, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="md:col-span-2">
                <label class="md:hidden block text-xs font-bold text-slate-500 uppercase mb-1">No. Batch</label>
                <input type="text" name="items[__INDEX__][batch_number]" required placeholder="Cth: BATCH-01" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-medium text-slate-700 shadow-inner uppercase validation-target">
            </div>
            
            <div class="md:col-span-2">
                <label class="md:hidden block text-xs font-bold text-slate-500 uppercase mb-1">Kadaluarsa</label>
                <input type="date" name="items[__INDEX__][expired_date]" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-medium text-slate-700 shadow-inner validation-target">
            </div>
            
            <div class="md:col-span-1">
                <label class="md:hidden block text-xs font-bold text-slate-500 uppercase mb-1">Qty</label>
                <input type="number" name="items[__INDEX__][quantity]" value="1" min="1" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-center focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-bold text-slate-700 shadow-inner validation-target" onkeyup="calculateSubtotal(this)" onchange="calculateSubtotal(this)">
            </div>
            
            <div class="md:col-span-2">
                <label class="md:hidden block text-xs font-bold text-slate-500 uppercase mb-1">Harga Beli</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                        <span class="text-slate-400 font-bold text-xs">Rp</span>
                    </div>
                    <input type="number" name="items[__INDEX__][unit_price]" value="0" min="0" required class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-right focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-bold text-slate-700 shadow-inner validation-target" onkeyup="calculateSubtotal(this)" onchange="calculateSubtotal(this)">
                </div>
            </div>

            <div class="md:col-span-2 flex items-center justify-between md:justify-end gap-3 text-right border-t border-slate-100 md:border-t-0 pt-3 md:pt-0 mt-2 md:mt-0">
                <span class="md:hidden text-xs font-bold text-slate-500 uppercase">Subtotal</span>
                <div class="flex items-center gap-3">
                    <span class="item-subtotal font-bold text-primary-600 text-sm">Rp 0</span>
                    <button type="button" onclick="removeItem(this)" class="hidden md:flex text-slate-300 hover:text-red-500 hover:bg-red-50 p-1.5 rounded-lg transition-colors border border-transparent hover:border-red-100" title="Hapus Item">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>
            
        </div>
    </div>
</template>

<script>
let itemIndex = 0;

function addItem() {
    const container = document.getElementById('items-container');
    const template = document.getElementById('item-template').innerHTML;
    
    // Replace placeholder with index
    const html = template.replace(/__INDEX__/g, itemIndex);
    
    const div = document.createElement('div');
    div.innerHTML = html.trim();
    const newNode = div.firstChild;
    
    container.appendChild(newNode);
    itemIndex++;
    
    // Small animation
    newNode.style.opacity = '0';
    newNode.style.transform = 'translateY(10px)';
    setTimeout(() => {
        newNode.style.transition = 'all 0.3s ease-out';
        newNode.style.opacity = '1';
        newNode.style.transform = 'translateY(0)';
    }, 10);
}

function removeItem(btn) {
    const row = btn.closest('.item-row');
    row.style.opacity = '0';
    row.style.transform = 'scale(0.95)';
    setTimeout(() => {
        row.remove();
        calculateTotal();
    }, 200);
}

function updatePrice(select) {
    const selectedOption = select.options[select.selectedIndex];
    const price = selectedOption.dataset.price || 0;
    const row = select.closest('.item-row');
    const priceInput = row.querySelector('input[name*="[unit_price]"]');
    
    if (parseFloat(priceInput.value) === 0 || !priceInput.value) {
        priceInput.value = price;
    }
    
    calculateSubtotal(priceInput);
}

function calculateSubtotal(input) {
    const row = input.closest('.item-row');
    const qty = parseFloat(row.querySelector('input[name*="[quantity]"]').value) || 0;
    const price = parseFloat(row.querySelector('input[name*="[unit_price]"]').value) || 0;
    const subtotal = qty * price;
    row.querySelector('.item-subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    calculateTotal();
}

function calculateTotal() {
    let subtotal = 0;
    document.querySelectorAll('.item-subtotal').forEach(el => {
        const val = el.textContent.replace(/[^0-9]/g, '');
        subtotal += parseFloat(val) || 0;
    });
    
    const discount = parseFloat(document.querySelector('input[name="discount_amount"]').value) || 0;
    const tax = parseFloat(document.querySelector('input[name="tax_amount"]').value) || 0;
    const total = Math.max(0, subtotal - discount + tax);
    
    document.getElementById('subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    document.getElementById('total').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

// Handle Form Submission via Fetch API (AJAX) 
document.getElementById('purchase-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const errorContainer = document.getElementById('error-container');
    const errorList = document.getElementById('error-list');
    const submitBtn = document.getElementById('btn-submit');
    const originalBtnContent = submitBtn.innerHTML;
    
    // Reset all previous error highlights
    document.querySelectorAll('.border-red-500').forEach(el => {
        el.classList.remove('border-red-500', 'ring-1', 'ring-red-500', 'bg-red-50');
    });

    // Client-side quick check
    const items = form.querySelectorAll('.item-row');
    if(items.length === 0) {
        errorList.innerHTML = '<li>Anda belum menambahkan item obat sama sekali. Klik Tambah Baris.</li>';
        errorContainer.classList.remove('hidden');
        window.scrollTo({ top: 0, behavior: 'smooth' });
        return;
    }

    // Loading State
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-3 border-2 border-white border-t-transparent rounded-full" viewBox="0 0 24 24"></svg> Memproses...';
    submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
    errorContainer.classList.add('hidden');
    errorList.innerHTML = '';
    
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        // Laravel returns 422 for validation errors, redirect/200 for success
        if (!response.ok) {
            return response.json().then(errData => {
                throw { status: response.status, data: errData };
            });
        }
        
        // If success, we just check if it's a redirect or JSON message
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
            return response.json();
        } else {
            return null;
        }
    })
    .then(data => {
        // Success path
        window.location.href = "{{ route('purchases.index') }}";
    })
    .catch(error => {
        // Error path
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnContent;
        submitBtn.classList.remove('opacity-80', 'cursor-not-allowed');
        
        if (error.status === 422 && error.data && error.data.errors) {
            let errorHtml = '';
            const errors = error.data.errors;
            
            for (const field in errors) {
                // Highlight input fields
                const inputName = field.replace(/\./g, '][').replace(/\]\[/, '['); // Very rough conversion for nested array logic
                // Usually items.0.medicine_id becomes input[name="items[0][medicine_id]"]
                let finalInputName = field;
                if(field.includes('.')) {
                    let parts = field.split('.');
                    finalInputName = parts[0] + '[' + parts[1] + '][' + parts[2] + ']';
                }
                
                const inputEl = document.querySelector(`[name="${finalInputName}"]`);
                if(inputEl) {
                    inputEl.classList.add('border-red-500', 'ring-1', 'ring-red-500', 'bg-red-50');
                }

                errors[field].forEach(msg => {
                    // Translate matrix index dynamically
                    if(field.startsWith('items.')) {
                        let parts = field.split('.');
                        let rowIndex = parseInt(parts[1]) + 1;
                        let colName = parts[2] || '';
                        let readableCol = {
                            'medicine_id': 'Obat',
                            'batch_number': 'Nomor Batch',
                            'expired_date': 'Tanggal Kadaluarsa',
                            'quantity': 'Kuantitas/Qty',
                            'unit_price': 'Harga'
                        }[colName] || colName;
                        msg = `Baris ke-${rowIndex} (${readableCol}): ${msg.replace(/items\.[0-9]+\.[a-z_]+/, '')}`;
                    }
                    errorHtml += `<li>${msg}</li>`;
                });
            }
            errorList.innerHTML = errorHtml;
            errorContainer.classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            // Internal 500 Error
            errorList.innerHTML = `<li>Terjadi kesalahan sistem: ${error.data?.message || 'Gagal tersambung ke server'}</li>`;
            errorContainer.classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
});

window.onload = function() {
    addItem();
};
</script>
@endsection
