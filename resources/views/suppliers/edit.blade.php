@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Edit Supplier</h2>
            <p class="text-slate-500 text-sm mt-1">Perbarui informasi untuk supplier {{ $supplier->name }}.</p>
        </div>
        <a href="{{ route('suppliers.index') }}" class="text-slate-500 hover:text-slate-700 font-medium text-sm flex items-center gap-1.5 transition-colors bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
    </div>

    <form action="{{ route('suppliers.update', $supplier) }}" method="POST" class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-200/60 overflow-hidden">
        @csrf
        @method('PUT')
        
        <div class="p-6 sm:p-8">
            <h3 class="text-lg font-bold text-slate-800 mb-6 border-b border-slate-100 pb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Informasi Utama
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label for="code" class="block text-sm font-bold text-slate-700 mb-2">Kode Supplier <span class="text-red-500">*</span></label>
                    <input type="text" name="code" id="code" value="{{ old('code', $supplier->code) }}" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors shadow-inner @error('code') border-red-500 ring-1 ring-red-500 @enderror">
                    @error('code')
                        <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-bold text-slate-700 mb-2">Nama Perusahaan / Supplier <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $supplier->name) }}" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors shadow-inner @error('name') border-red-500 ring-1 ring-red-500 @enderror">
                    @error('name')
                        <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <h3 class="text-lg font-bold text-slate-800 mb-6 border-b border-slate-100 pb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Informasi Kontak
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label for="contact_person" class="block text-sm font-bold text-slate-700 mb-2">Contact Person (PIC)</label>
                    <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors shadow-inner">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-bold text-slate-700 mb-2">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $supplier->phone) }}"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors shadow-inner">
                </div>

                <div>
                    <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $supplier->email) }}"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors shadow-inner">
                </div>

                <div>
                    <label for="city" class="block text-sm font-bold text-slate-700 mb-2">Kota / Wilayah</label>
                    <input type="text" name="city" id="city" value="{{ old('city', $supplier->city) }}"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors shadow-inner">
                </div>
            </div>
            
            <div class="mb-8">
                <label for="address" class="block text-sm font-bold text-slate-700 mb-2">Alamat Lengkap</label>
                <textarea name="address" id="address" rows="3"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors shadow-inner">{{ old('address', $supplier->address) }}</textarea>
            </div>

            <div class="mb-8">
                <label for="notes" class="block text-sm font-bold text-slate-700 mb-2">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" id="notes" rows="2"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors shadow-inner">{{ old('notes', $supplier->notes) }}</textarea>
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $supplier->is_active ? '1' : '0') == '1' ? 'checked' : '' }}
                    class="w-5 h-5 text-primary-600 bg-slate-50 border-slate-300 rounded focus:ring-primary-500">
                <label for="is_active" class="text-sm font-bold text-slate-700 cursor-pointer">Supplier Aktif</label>
            </div>
        </div>

        <div class="px-6 py-5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="{{ route('suppliers.index') }}" class="px-6 py-2.5 text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition-colors shadow-sm">
                Batal
            </a>
            <button type="submit" class="px-8 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 rounded-xl transition-all shadow-glow transform hover:-translate-y-0.5">
                Perbarui Data
            </button>
        </div>
    </form>
</div>
@endsection
