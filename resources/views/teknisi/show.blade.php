@extends('layouts.app')

@section('title', 'Detail Service Report')
@section('page-title', 'Detail Service Report')

@section('content')
<div class="max-w-4xl">
    <div class="flex items-center justify-between mb-6">
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-1.5 text-gray-500 hover:text-gray-700 text-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
        @if(auth()->user()->isAdmin())
        <div class="flex gap-2">
            <a href="{{ route('admin.reports.pdf', ['report' => $report->id, 'layout' => 'intan']) }}" class="inline-flex items-center gap-1.5 bg-red-50 text-red-700 hover:bg-red-100 px-3 py-1.5 rounded-lg text-xs font-medium transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                PDF Intan
            </a>
            <a href="{{ route('admin.reports.pdf', ['report' => $report->id, 'layout' => 'kemilau']) }}" class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-xs font-medium transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                PDF Kemilau
            </a>
        </div>
        @endif
    </div>

    {{-- Info --}}
    <div class="bg-white rounded-2xl border border-gray-200 mb-5 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-primary-500 to-primary-600">
            <h3 class="font-semibold text-white">Informasi Service</h3>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div><span class="text-gray-500">Rumah Sakit</span><p class="font-medium text-gray-900 mt-0.5">{{ $report->rumahSakit->nama }}</p></div>
                <div><span class="text-gray-500">Ruangan</span><p class="font-medium text-gray-900 mt-0.5">{{ $report->ruangan->nama }}</p></div>
                <div><span class="text-gray-500">Merk AC</span><p class="font-medium text-gray-900 mt-0.5">{{ $report->merk_ac }}</p></div>
                <div><span class="text-gray-500">Type AC</span><p class="font-medium text-gray-900 mt-0.5">{{ $report->type_ac }}</p></div>
                <div><span class="text-gray-500">Tanggal Service</span><p class="font-medium text-gray-900 mt-0.5">{{ $report->tanggal_service->translatedFormat('l, d F Y') }}</p></div>
                <div><span class="text-gray-500">Teknisi</span><p class="font-medium text-gray-900 mt-0.5">{{ $report->user->name }}</p></div>
            </div>
        </div>
    </div>

    {{-- Pemeriksaan --}}
    <div class="bg-white rounded-2xl border border-gray-200 mb-5 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Hasil Pemeriksaan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                        <th class="px-4 py-3 text-center w-12">No</th>
                        <th class="px-4 py-3 text-left">Pemeriksaan</th>
                        <th class="px-4 py-3 text-center w-28">Kondisi</th>
                        <th class="px-4 py-3 text-left">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($report->items as $item)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-center text-gray-400 font-mono text-xs">{{ $item->nomor }}</td>
                        <td class="px-4 py-3 text-gray-800">{{ $item->nama_pemeriksaan }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($item->is_normal)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-medium">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    Normal
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-50 text-red-700 text-xs font-medium">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Tidak Normal
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $item->keterangan ?? '-' }}
                            @if($item->photos->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                @foreach($item->photos as $photo)
                                    <a href="{{ asset('storage/' . $photo->photo_path) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $photo->photo_path) }}" class="w-12 h-12 object-cover rounded-lg border border-gray-200 hover:opacity-80 transition">
                                    </a>
                                @endforeach
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Foto --}}
    @if($report->generalPhotos->isNotEmpty())
    <div class="bg-white rounded-2xl border border-gray-200 mb-5 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Foto AC</h3>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                @foreach($report->generalPhotos as $photo)
                    <a href="{{ asset('storage/' . $photo->photo_path) }}" target="_blank" class="aspect-square rounded-xl overflow-hidden border border-gray-200 hover:opacity-80 transition">
                        <img src="{{ asset('storage/' . $photo->photo_path) }}" class="w-full h-full object-cover">
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Saran --}}
    @if($report->saran)
    <div class="bg-white rounded-2xl border border-gray-200 mb-5 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Saran-Saran</h3>
        </div>
        <div class="p-5 text-sm text-gray-700">{{ $report->saran }}</div>
    </div>
    @endif

    {{-- TTD --}}
    @if($report->user->signature_path)
    <div class="bg-white rounded-2xl border border-gray-200 mb-5 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Tanda Tangan Teknisi</h3>
        </div>
        <div class="p-5 text-center">
            <img src="{{ asset('storage/' . $report->user->signature_path) }}" class="h-16 mx-auto">
            <p class="text-sm text-gray-500 mt-2">{{ $report->user->name }}</p>
        </div>
    </div>
    @endif
</div>
@endsection
