@extends('layouts.app')

@section('title', 'Detail Surat Jalan')
@section('page-title', 'Detail Surat Jalan')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div></div>
    <a href="/admin/surat-jalan" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
    </a>
</div>

{{-- Info --}}
<div class="bg-white rounded-2xl border border-gray-200 p-5 mb-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">Nomor</p>
            <p class="font-medium text-gray-900">{{ $suratJalan->nomor ?: '-' }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">Rumah Sakit</p>
            <p class="font-medium text-gray-900">{{ $suratJalan->rumahSakit->nama }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">Departemen</p>
            <p class="font-medium text-gray-900">{{ $suratJalan->departemen ?: '-' }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">Tanggal</p>
            <p class="font-medium text-gray-900">{{ $suratJalan->tanggal->translatedFormat('l, d F Y') }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">Penerima</p>
            <p class="font-medium text-gray-900">{{ $suratJalan->penerima ?: '-' }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">Mengetahui</p>
            <p class="font-medium text-gray-900">{{ $suratJalan->mengetahui ?: '-' }}</p>
        </div>
    </div>
</div>

{{-- Items --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-900">Item Barang</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                    <th class="px-5 py-3 text-left w-24">Banyaknya</th>
                    <th class="px-5 py-3 text-left">Nama Barang</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($suratJalan->items as $item)
                <tr>
                    <td class="px-5 py-3 text-gray-600">{{ $item->banyaknya }}</td>
                    <td class="px-5 py-3 font-medium text-gray-900">{{ $item->nama_barang }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if($suratJalan->catatan)
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-900">Catatan</h3>
    </div>
    <div class="p-5 text-gray-700 text-sm">{{ $suratJalan->catatan }}</div>
</div>
@endif

<div class="flex gap-3">
    <a href="{{ route('admin.surat-jalan.pdf', ['suratJalan' => $suratJalan->id, 'layout' => 'intan']) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
        Export PDF (CV Intan)
    </a>
    <a href="{{ route('admin.surat-jalan.pdf', ['suratJalan' => $suratJalan->id, 'layout' => 'kemilau']) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-xl text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
        Export PDF (CV Kemilau)
    </a>
</div>
@endsection
