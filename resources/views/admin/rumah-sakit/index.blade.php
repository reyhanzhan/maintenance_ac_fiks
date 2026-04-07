@extends('layouts.app')

@section('title', 'Rumah Sakit')
@section('page-title', 'Rumah Sakit & Unit AC')

@section('content')
<div x-data="{ showAdd: false, editId: null }">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <form method="GET" class="flex-1 max-w-sm">
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari rumah sakit..."
                    class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
        </form>
        <button @click="showAdd = !showAdd" class="inline-flex items-center gap-2 bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-xl text-sm font-medium transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah RS
        </button>
    </div>

    {{-- Tambah RS --}}
    <div x-show="showAdd" x-cloak x-transition class="bg-white rounded-2xl border border-gray-200 overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Tambah Rumah Sakit Baru</h3>
            <button @click="showAdd = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="p-5">
            <form action="/admin/rumah-sakit" method="POST" class="flex flex-col sm:flex-row gap-3 items-end">
                @csrf
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Nama RS</label>
                    <input type="text" name="nama" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Masukkan nama rumah sakit" required>
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Alamat</label>
                    <input type="text" name="alamat" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Masukkan alamat">
                </div>
                <button class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap">Simpan</button>
            </form>
        </div>
    </div>

    {{-- Table RS --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                        <th class="px-5 py-3 text-left w-8">No</th>
                        <th class="px-5 py-3 text-left">Nama RS</th>
                        <th class="px-5 py-3 text-left">Alamat</th>
                        <th class="px-5 py-3 text-center">Ruangan & Unit AC</th>
                        <th class="px-5 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rumahSakits as $index => $rs)
                    <tr class="hover:bg-gray-50/50 transition">
                        {{-- View Mode --}}
                        <td class="px-5 py-3 text-gray-400" x-show="editId !== {{ $rs->id }}">{{ $rumahSakits->firstItem() + $index }}</td>
                        <td class="px-5 py-3 font-medium text-gray-900" x-show="editId !== {{ $rs->id }}">{{ $rs->nama }}</td>
                        <td class="px-5 py-3 text-gray-500" x-show="editId !== {{ $rs->id }}">{{ $rs->alamat ?: '-' }}</td>
                        <td class="px-5 py-3 text-center" x-show="editId !== {{ $rs->id }}">
                            <a href="/admin/rumah-sakit/{{ $rs->id }}/ac-unit" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs font-semibold transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                                {{ $rs->ac_units_count }} unit
                            </a>
                        </td>
                        <td class="px-5 py-3" x-show="editId !== {{ $rs->id }}">
                            <div class="flex items-center justify-center gap-1">
                                <button @click="editId = {{ $rs->id }}" class="p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form action="/admin/rumah-sakit/{{ $rs->id }}" method="POST" onsubmit="return confirm('Hapus {{ $rs->nama }}? Semua data ruangan & laporan terkait akan ikut terhapus.')">
                                    @csrf @method('DELETE')
                                    <button class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>

                        {{-- Edit Mode --}}
                        <td class="px-5 py-3 text-gray-400" x-show="editId === {{ $rs->id }}" x-cloak>{{ $rumahSakits->firstItem() + $index }}</td>
                        <td colspan="4" class="px-5 py-3" x-show="editId === {{ $rs->id }}" x-cloak>
                            <form action="/admin/rumah-sakit/{{ $rs->id }}" method="POST" class="flex flex-wrap items-end gap-3">
                                @csrf @method('PUT')
                                <div class="flex-1 min-w-[160px]">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Nama RS</label>
                                    <input type="text" name="nama" value="{{ $rs->nama }}" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                                </div>
                                <div class="flex-1 min-w-[160px]">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Alamat</label>
                                    <input type="text" name="alamat" value="{{ $rs->alamat }}" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm font-medium transition">Simpan</button>
                                    <button type="button" @click="editId = null" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-sm font-medium transition">Batal</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">Belum ada data rumah sakit.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($rumahSakits->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $rumahSakits->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
