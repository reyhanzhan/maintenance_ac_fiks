@extends('layouts.app')

@section('title', 'Detail Service Report')
@section('page-title', 'Detail Service Report')

@section('content')
<div class="max-w-4xl" x-data="{
    lightboxOpen: false,
    lightboxSrc: '',
    allPhotos: [],
    currentIndex: 0,
    init() {
        this.allPhotos = [...document.querySelectorAll('[data-lightbox-src]')].map(el => el.dataset.lightboxSrc);
    },
    openLightbox(src) {
        this.lightboxSrc = src;
        this.currentIndex = this.allPhotos.indexOf(src);
        this.lightboxOpen = true;
    },
    next() {
        if (this.allPhotos.length === 0) return;
        this.currentIndex = (this.currentIndex + 1) % this.allPhotos.length;
        this.lightboxSrc = this.allPhotos[this.currentIndex];
    },
    prev() {
        if (this.allPhotos.length === 0) return;
        this.currentIndex = (this.currentIndex - 1 + this.allPhotos.length) % this.allPhotos.length;
        this.lightboxSrc = this.allPhotos[this.currentIndex];
    }
}">
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
                <div><span class="text-gray-500">Tanggal Service</span><p class="font-medium text-gray-900 mt-0.5">{{ $report->tanggal_service->locale('id')->translatedFormat('l, d F Y') }}</p></div>
                <div><span class="text-gray-500">Teknisi</span><p class="font-medium text-gray-900 mt-0.5">{{ $report->user->name }}</p></div>
                @if($report->gedung)
                <div><span class="text-gray-500">Gedung</span><p class="font-medium text-gray-900 mt-0.5">{{ $report->gedung }}</p></div>
                @endif
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
                                    <button type="button" data-lightbox-src="{{ asset('storage/' . $photo->photo_path) }}" @click="openLightbox('{{ asset('storage/' . $photo->photo_path) }}')" class="cursor-pointer">
                                        <img src="{{ asset('storage/' . $photo->photo_path) }}" class="w-12 h-12 object-cover rounded-lg border border-gray-200 hover:opacity-80 transition">
                                    </button>
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
                    <button type="button" data-lightbox-src="{{ asset('storage/' . $photo->photo_path) }}" @click="openLightbox('{{ asset('storage/' . $photo->photo_path) }}')" class="aspect-square rounded-xl overflow-hidden border border-gray-200 hover:opacity-80 transition cursor-pointer">
                        <img src="{{ asset('storage/' . $photo->photo_path) }}" class="w-full h-full object-cover">
                    </button>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Uraian Pekerjaan --}}
    @if($report->saran)
    <div class="bg-white rounded-2xl border border-gray-200 mb-5 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Uraian Pekerjaan</h3>
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

    {{-- Lightbox Modal --}}
    <div x-show="lightboxOpen" x-cloak @keydown.escape.window="lightboxOpen = false" @keydown.arrow-right.window="next()" @keydown.arrow-left.window="prev()"
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/80"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        {{-- Close --}}
        <button @click="lightboxOpen = false" class="absolute top-4 right-4 text-white/80 hover:text-white z-10 p-2">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        {{-- Counter --}}
        <div class="absolute top-4 left-4 text-white/70 text-sm font-medium" x-show="allPhotos.length > 1" x-text="(currentIndex + 1) + ' / ' + allPhotos.length"></div>

        {{-- Prev --}}
        <button x-show="allPhotos.length > 1" @click.stop="prev()" class="absolute left-2 sm:left-4 text-white/70 hover:text-white z-10 p-2 rounded-full bg-black/30 hover:bg-black/50 transition">
            <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>

        {{-- Image --}}
        <div class="max-w-[90vw] max-h-[90vh] flex items-center justify-center" @click.stop>
            <img :src="lightboxSrc" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl">
        </div>

        {{-- Next --}}
        <button x-show="allPhotos.length > 1" @click.stop="next()" class="absolute right-2 sm:right-4 text-white/70 hover:text-white z-10 p-2 rounded-full bg-black/30 hover:bg-black/50 transition">
            <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>

        {{-- Backdrop click to close --}}
        <div class="absolute inset-0 -z-10" @click="lightboxOpen = false"></div>
    </div>
</div>
@endsection
