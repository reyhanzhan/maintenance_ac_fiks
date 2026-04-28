@extends('layouts.app')

@section('title', 'Backup Data')
@section('page-title', 'Backup Data')

@section('content')
<div class="max-w-3xl space-y-6" x-data="{
    mode: 'months',
    tanggal_dari: '',
    tanggal_sampai: '',
    months: '1',
    isValid() {
        if (this.mode === 'months') return this.months !== '';
        return this.tanggal_dari !== '' && this.tanggal_sampai !== '' && this.tanggal_dari <= this.tanggal_sampai;
    }
}">
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900">Unduh Backup Data</h3>
        <p class="mt-2 text-sm text-gray-600">
            Backup berisi data Service Report, Surat Jalan, dan foto AC sesuai periode yang dipilih.
            File diunduh sebagai ZIP dan tersimpan ke folder unduhan laptop Anda.
        </p>

        {{-- Mode Toggle --}}
        <div class="mt-5 flex rounded-lg border border-gray-300 overflow-hidden w-fit">
            <button type="button" @click="mode = 'months'"
                :class="mode === 'months' ? 'bg-primary-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                class="px-4 py-2 text-sm font-medium transition">
                Pilih Jumlah Bulan
            </button>
            <button type="button" @click="mode = 'range'"
                :class="mode === 'range' ? 'bg-primary-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                class="px-4 py-2 text-sm font-medium border-l border-gray-300 transition">
                Pilih Rentang Tanggal
            </button>
        </div>

        <form method="GET" action="{{ route('admin.backup.download') }}" class="mt-4 space-y-4">

            {{-- Mode: Jumlah Bulan --}}
            <div x-show="mode === 'months'" x-cloak>
                <label class="block text-sm font-medium text-gray-700 mb-1">Periode Backup</label>
                <select name="months" x-model="months" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="1">1 bulan terakhir</option>
                    <option value="3">3 bulan terakhir</option>
                    <option value="6">6 bulan terakhir</option>
                    <option value="12">12 bulan terakhir</option>
                    <option value="24">24 bulan terakhir</option>
                    <option value="36">36 bulan terakhir</option>
                </select>
            </div>

            {{-- Mode: Rentang Tanggal --}}
            <div x-show="mode === 'range'" x-cloak>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
                        <input type="date" name="tanggal_dari" x-model="tanggal_dari"
                            class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                        <input type="date" name="tanggal_sampai" x-model="tanggal_sampai"
                            class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>
                <p x-show="tanggal_dari && tanggal_sampai && tanggal_dari > tanggal_sampai"
                    class="mt-1 text-xs text-red-500">Tanggal mulai tidak boleh lebih dari tanggal akhir.</p>
            </div>

            {{-- Hidden field to tell controller which mode --}}
            <input type="hidden" name="mode" :value="mode">

            <button type="submit" :disabled="!isValid()"
                class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 disabled:opacity-40 disabled:cursor-not-allowed text-white text-sm font-medium px-4 py-2.5 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16v-8m0 8l-3-3m3 3l3-3M4 17a4 4 0 014-4h1a4 4 0 117.746 1H17a3 3 0 110 6H8a4 4 0 01-4-4z"/>
                </svg>
                Download Backup ZIP
            </button>
        </form>
    </div>

    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800">
        <p class="font-semibold">Catatan</p>
        <p class="mt-1">Jika data dan foto banyak, proses pembuatan ZIP bisa membutuhkan waktu lebih lama.</p>
    </div>
</div>
@endsection
