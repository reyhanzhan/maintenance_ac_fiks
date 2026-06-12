@extends('layouts.app')

@section('title', 'Ruangan & Unit AC - ' . $rumahSakit->nama)
@section('page-title', 'Ruangan & Unit AC ' . $rumahSakit->nama)

@section('content')
<div x-data="{
    showAdd: false,
    showImport: false,
    editId: null,
    selected: [],
    visibleIds: @json($acUnits->pluck('id')->values()),
    importFileName: '',
    get allSelected() {
        return this.visibleIds.length > 0 && this.visibleIds.every(id => this.selected.includes(id));
    },
    toggleAll() {
        this.selected = this.allSelected ? [] : [...this.visibleIds];
    }
}">
    {{-- Back + Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-3">
            <a href="/admin/rumah-sakit" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="font-semibold text-gray-900 text-lg">{{ $rumahSakit->nama }}</h2>
                <p class="text-xs text-gray-400">{{ $rumahSakit->alamat ?: '-' }} &bull; {{ $acUnits->total() }} unit AC</p>
            </div>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <form method="GET" class="flex-1 max-w-xs">
                <div class="relative">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ruangan, merk, jenis..."
                        class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500">
                </div>
            </form>
            <div class="flex items-center gap-2">
                <a href="/admin/rumah-sakit/{{ $rumahSakit->id }}/ac-unit/template" class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 px-3 py-2 rounded-xl text-sm font-medium transition shadow-sm whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 10v6m0 0l-3-3m3 3l3-3m2 7H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V18a2 2 0 01-2 2z"/></svg>
                    Template
                </a>
                <button @click="showImport = !showImport; showAdd = false" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-2 rounded-xl text-sm font-medium transition shadow-sm whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 4v12m0-12l-4 4m4-4l4 4"/></svg>
                    Import Excel
                </button>
                <button @click="showAdd = !showAdd; showImport = false" class="inline-flex items-center gap-2 bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-xl text-sm font-medium transition shadow-sm whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah AC
                </button>
            </div>
        </div>
    </div>

    {{-- Import Excel --}}
    <div x-show="showImport" x-cloak x-transition class="bg-white rounded-2xl border border-gray-200 overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-gray-900">Import Unit AC dari Excel</h3>
                <p class="text-xs text-gray-500 mt-0.5">Kolom template: Gedung, Jenis AC, Merk AC, Kapasitas, Ruangan, Lantai, Frek. Cuci.</p>
            </div>
            <button @click="showImport = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="p-5">
            <form action="/admin/rumah-sakit/{{ $rumahSakit->id }}/ac-unit/import" method="POST" enctype="multipart/form-data" class="grid lg:grid-cols-[1fr_auto] gap-4 items-stretch">
                @csrf
                <label class="border-2 border-dashed border-gray-300 hover:border-emerald-400 bg-gray-50 hover:bg-emerald-50/40 rounded-xl p-5 flex flex-col sm:flex-row sm:items-center gap-3 cursor-pointer transition"
                    @dragover.prevent
                    @drop.prevent="$refs.acImport.files = $event.dataTransfer.files; importFileName = $refs.acImport.files[0]?.name || ''">
                    <input x-ref="acImport" type="file" name="file" accept=".xlsx,.csv,.txt" class="sr-only" required @change="importFileName = $event.target.files[0]?.name || ''">
                    <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01.88-7.903A5 5 0 1118.9 10H19a3 3 0 010 6h-1M12 12v8m0-8l-3 3m3-3l3 3"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900" x-text="importFileName || 'Klik atau drag file Excel ke sini'"></p>
                        <p class="text-xs text-gray-500 mt-1">Download template dulu agar nama kolom sesuai. Format yang diterima: .xlsx, .csv.</p>
                    </div>
                </label>
                <div class="flex lg:flex-col gap-2">
                    <a href="/admin/rumah-sakit/{{ $rumahSakit->id }}/ac-unit/template" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 10v6m0 0l-3-3m3 3l3-3m2 7H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V18a2 2 0 01-2 2z"/></svg>
                        Download Template
                    </a>
                    <button class="inline-flex items-center justify-center gap-2 px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium transition whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 4v12m0-12l-4 4m4-4l4 4"/></svg>
                        Import
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Add Form --}}
    <div x-show="showAdd" x-cloak x-transition class="bg-white rounded-2xl border border-gray-200 overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Tambah Unit AC</h3>
            <button @click="showAdd = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="p-5">
            <form action="/admin/rumah-sakit/{{ $rumahSakit->id }}/ac-unit" method="POST" class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3 items-end">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Gedung</label>
                    <input type="text" name="gedung" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Gedung A">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Jenis AC</label>
                    <input type="text" name="jenis_ac" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Split" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Merk AC</label>
                    <input type="text" name="merk_ac" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Daikin" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Kapasitas</label>
                    <input type="text" name="kapasitas_pk" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="2 PK" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Ruangan</label>
                    <input type="text" name="ruangan" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Nama ruangan" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Lantai</label>
                    <input type="text" name="lantai" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="1" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Frek. Cuci/Thn</label>
                    <div class="flex gap-2">
                        <input type="number" name="frekuensi_cuci" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="0" min="0" value="0">
                        <button class="bg-primary-500 hover:bg-primary-600 text-white px-5 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Bulk Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
        <form action="/admin/ac-unit/bulk" method="POST" @submit="if (selected.length === 0 || !confirm('Hapus ' + selected.length + ' Unit AC terpilih?')) $event.preventDefault()">
            @csrf @method('DELETE')
            <input type="hidden" name="rumah_sakit_id" value="{{ $rumahSakit->id }}">
            <template x-for="id in selected" :key="id">
                <input type="hidden" name="ids[]" :value="id">
            </template>
            <button :disabled="selected.length === 0" :class="selected.length === 0 ? 'opacity-50 cursor-not-allowed' : ''" class="inline-flex items-center gap-2 px-3 py-2 bg-white hover:bg-red-50 text-red-600 border border-red-200 rounded-xl text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Hapus Terpilih <span x-show="selected.length > 0" x-text="'(' + selected.length + ')'"></span>
            </button>
        </form>
        <form action="/admin/ac-unit/bulk" method="POST" onsubmit="return confirm('Hapus SEMUA Unit AC di {{ addslashes($rumahSakit->nama) }}? Tindakan ini tidak bisa dibatalkan.')">
            @csrf @method('DELETE')
            <input type="hidden" name="rumah_sakit_id" value="{{ $rumahSakit->id }}">
            <input type="hidden" name="delete_all" value="1">
            <button class="inline-flex items-center gap-2 px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18.364 18.364A9 9 0 115.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                Hapus Semua Unit AC
            </button>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                        <th class="px-4 py-3 text-center w-10">
                            <input type="checkbox" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" :checked="allSelected" @change="toggleAll()" @click.stop>
                        </th>
                        <th class="px-4 py-3 text-left w-12">No</th>
                        <th class="px-4 py-3 text-left">Gedung</th>
                        <th class="px-4 py-3 text-left">Jenis AC</th>
                        <th class="px-4 py-3 text-left">Merk AC</th>
                        <th class="px-4 py-3 text-left">Kapasitas</th>
                        <th class="px-4 py-3 text-left">Ruangan</th>
                        <th class="px-4 py-3 text-center">Lantai</th>
                        <th class="px-4 py-3 text-center">Frek. Cuci</th>
                        <th class="px-4 py-3 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($acUnits as $index => $ac)
                    <tr class="hover:bg-gray-50/50 transition">
                        {{-- View Mode --}}
                        <template x-if="editId !== {{ $ac->id }}">
                            <td class="px-4 py-2.5 text-center">
                                <input type="checkbox" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" value="{{ $ac->id }}" x-model.number="selected" @click.stop>
                            </td>
                        </template>
                        <template x-if="editId !== {{ $ac->id }}">
                            <td class="px-4 py-2.5 text-gray-400">{{ $acUnits->firstItem() + $index }}</td>
                        </template>
                        <template x-if="editId !== {{ $ac->id }}">
                            <td class="px-4 py-2.5 text-gray-600">{{ $ac->gedung ?: '-' }}</td>
                        </template>
                        <template x-if="editId !== {{ $ac->id }}">
                            <td class="px-4 py-2.5"><span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-xs font-medium">{{ $ac->jenis_ac }}</span></td>
                        </template>
                        <template x-if="editId !== {{ $ac->id }}">
                            <td class="px-4 py-2.5 text-gray-900 font-medium">{{ $ac->merk_ac }}</td>
                        </template>
                        <template x-if="editId !== {{ $ac->id }}">
                            <td class="px-4 py-2.5"><span class="px-2 py-0.5 bg-green-50 text-green-700 rounded text-xs font-medium">{{ $ac->kapasitas_pk }}</span></td>
                        </template>
                        <template x-if="editId !== {{ $ac->id }}">
                            <td class="px-4 py-2.5 text-gray-900">{{ $ac->ruangan }}</td>
                        </template>
                        <template x-if="editId !== {{ $ac->id }}">
                            <td class="px-4 py-2.5 text-center text-gray-600">{{ $ac->lantai }}</td>
                        </template>
                        <template x-if="editId !== {{ $ac->id }}">
                            <td class="px-4 py-2.5 text-center">
                                @if($ac->frekuensi_cuci > 0)
                                    <span class="px-2 py-0.5 bg-orange-50 text-orange-700 rounded text-xs font-medium">{{ $ac->frekuensi_cuci }}x</span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                        </template>
                        <template x-if="editId !== {{ $ac->id }}">
                            <td class="px-4 py-2.5">
                                <div class="flex items-center justify-center gap-1">
                                    <button @click="editId = {{ $ac->id }}" class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Edit">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form action="/admin/ac-unit/{{ $ac->id }}" method="POST" onsubmit="return confirm('Hapus AC unit ini?')">
                                        @csrf @method('DELETE')
                                        <button class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </template>

                        {{-- Edit Mode --}}
                        <template x-if="editId === {{ $ac->id }}">
                            <td colspan="10" class="px-4 py-3">
                                <form action="/admin/ac-unit/{{ $ac->id }}" method="POST" class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-2 items-end">
                                    @csrf @method('PUT')
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-0.5">Gedung</label>
                                        <input type="text" name="gedung" value="{{ $ac->gedung }}" class="w-full rounded-lg border-gray-300 text-xs focus:ring-primary-500 focus:border-primary-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-0.5">Jenis</label>
                                        <input type="text" name="jenis_ac" value="{{ $ac->jenis_ac }}" class="w-full rounded-lg border-gray-300 text-xs focus:ring-primary-500 focus:border-primary-500" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-0.5">Merk</label>
                                        <input type="text" name="merk_ac" value="{{ $ac->merk_ac }}" class="w-full rounded-lg border-gray-300 text-xs focus:ring-primary-500 focus:border-primary-500" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-0.5">Kapasitas</label>
                                        <input type="text" name="kapasitas_pk" value="{{ $ac->kapasitas_pk }}" class="w-full rounded-lg border-gray-300 text-xs focus:ring-primary-500 focus:border-primary-500" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-0.5">Ruangan</label>
                                        <input type="text" name="ruangan" value="{{ $ac->ruangan }}" class="w-full rounded-lg border-gray-300 text-xs focus:ring-primary-500 focus:border-primary-500" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-0.5">Lantai</label>
                                        <input type="text" name="lantai" value="{{ $ac->lantai }}" class="w-full rounded-lg border-gray-300 text-xs focus:ring-primary-500 focus:border-primary-500" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-0.5">Frek.</label>
                                        <input type="number" name="frekuensi_cuci" value="{{ $ac->frekuensi_cuci }}" class="w-full rounded-lg border-gray-300 text-xs focus:ring-primary-500 focus:border-primary-500" min="0">
                                    </div>
                                    <div class="flex gap-1">
                                        <button type="submit" class="px-3 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-xs font-medium transition">Simpan</button>
                                        <button type="button" @click="editId = null" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-xs font-medium transition">Batal</button>
                                    </div>
                                </form>
                            </td>
                        </template>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="px-4 py-8 text-center text-gray-400">Belum ada data AC unit.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($acUnits->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $acUnits->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
