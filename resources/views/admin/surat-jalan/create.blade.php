@extends('layouts.app')

@section('title', 'Buat Surat Jalan')
@section('page-title', 'Buat Surat Jalan')

@section('content')
<form action="/admin/surat-jalan" method="POST" class="space-y-6">
    @csrf

    {{-- Informasi --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Informasi</h3>
        </div>
        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Surat</label>
                <input type="text" name="nomor" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" value="{{ old('nomor') }}" placeholder="Opsional">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" value="{{ old('tanggal', date('Y-m-d')) }}" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rumah Sakit <span class="text-red-500">*</span></label>
                <div x-data="{
                    open: false,
                    search: '',
                    selected: '{{ old('rumah_sakit_id', '') }}',
                    selectedText: '',
                    options: [
                        @foreach($rumahSakits as $rs)
                        { value: '{{ $rs->id }}', text: '{{ $rs->nama }}' },
                        @endforeach
                    ],
                    get filtered() { return this.options.filter(o => o.text.toLowerCase().includes(this.search.toLowerCase())); },
                    choose(opt) { this.selected = opt.value; this.selectedText = opt.text; this.open = false; this.search = ''; },
                    init() { const found = this.options.find(o => o.value === this.selected); if (found) this.selectedText = found.text; }
                }" @click.away="open = false" class="relative">
                    <input type="hidden" name="rumah_sakit_id" :value="selected" required>
                    <button type="button" @click="open = !open" class="w-full bg-white border border-gray-300 text-left rounded-lg px-3 py-2 text-sm flex items-center justify-between focus:ring-primary-500 focus:border-primary-500 transition">
                        <span x-text="selectedText || '-- Pilih --'" :class="selectedText ? 'text-gray-900' : 'text-gray-400'"></span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-2 border-b border-gray-100">
                            <input type="text" x-model="search" placeholder="Cari rumah sakit..." class="w-full text-sm border-gray-300 rounded-lg px-3 py-1.5 focus:ring-primary-500 focus:border-primary-500" @click.stop>
                        </div>
                        <div class="dd-list">
                            <template x-for="opt in filtered" :key="opt.value">
                                <button type="button" @click="choose(opt)" class="w-full text-left px-4 py-2 text-sm hover:bg-primary-50 hover:text-primary-600 transition" :class="selected === opt.value ? 'bg-primary-50 text-primary-600 font-medium' : 'text-gray-700'" x-text="opt.text"></button>
                            </template>
                            <div x-show="filtered.length === 0" class="px-4 py-2 text-sm text-gray-400">Tidak ditemukan</div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Departemen / Ruangan</label>
                <input type="text" name="departemen" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" value="{{ old('departemen') }}" placeholder="Contoh: Depo Farmasi">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Penerima</label>
                <input type="text" name="penerima" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" value="{{ old('penerima') }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mengetahui</label>
                <input type="text" name="mengetahui" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" value="{{ old('mengetahui') }}">
            </div>
        </div>
    </div>

    {{-- Item Barang --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden" x-data="{ itemIndex: 1 }">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Item Barang</h3>
            <button type="button" @click="
                const c = document.getElementById('items-container');
                const html = `<div class='flex gap-3 items-start item-row'>
                    <input type='number' name='items[`+itemIndex+`][banyaknya]' class='w-24 rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500' value='1' min='1' placeholder='Qty' required>
                    <input type='text' name='items[`+itemIndex+`][nama_barang]' class='flex-1 rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500' placeholder='Nama barang / keterangan' required>
                    <button type='button' onclick='this.closest(\".item-row\").remove()' class='p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition'><svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'/></svg></button>
                </div>`;
                c.insertAdjacentHTML('beforeend', html);
                itemIndex++;
            " class="inline-flex items-center gap-1 text-primary-600 hover:text-primary-700 text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah
            </button>
        </div>
        <div class="p-5 space-y-3" id="items-container">
            <div class="flex gap-3 items-start item-row">
                <input type="number" name="items[0][banyaknya]" class="w-24 rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" value="1" min="1" placeholder="Qty" required>
                <input type="text" name="items[0][nama_barang]" class="flex-1 rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Nama barang / keterangan" required>
                <button type="button" onclick="if(document.querySelectorAll('.item-row').length > 1) this.closest('.item-row').remove()" class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Catatan --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Catatan</h3>
        </div>
        <div class="p-5">
            <textarea name="catatan" rows="3" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Tambahkan catatan jika ada...">{{ old('catatan') }}</textarea>
        </div>
    </div>

    <button type="submit" class="w-full bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-semibold py-3 rounded-xl shadow-lg shadow-primary-500/25 transition">
        Simpan Surat Jalan
    </button>
</form>
@endsection
