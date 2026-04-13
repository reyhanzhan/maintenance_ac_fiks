@extends('layouts.app')

@section('title', 'Report Saya')
@section('page-title', 'Report Saya')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <p class="text-gray-500 text-sm">Daftar semua service report yang sudah dibuat</p>
    </div>
    <a href="/teknisi/create" class="inline-flex items-center gap-2 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-medium py-2.5 px-5 rounded-xl shadow-sm shadow-primary-500/20 transition-all text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Buat Report
    </a>
</div>

@if($reports->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <p class="text-gray-500 mb-4">Belum ada service report.</p>
        <a href="/teknisi/create" class="inline-flex items-center gap-2 bg-primary-500 hover:bg-primary-600 text-white font-medium py-2 px-5 rounded-xl transition text-sm">
            Buat Report Pertama
        </a>
    </div>
@else
    <div class="grid gap-4">
        @foreach($reports as $report)
        <div class="bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0 flex-1">
                    <h3 class="font-semibold text-gray-900">{{ $report->rumahSakit->nama }}</h3>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $report->ruangan->nama }}</p>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg bg-gray-100 text-gray-700 text-xs font-medium">{{ $report->merk_ac }}</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg bg-gray-100 text-gray-700 text-xs font-medium">{{ $report->type_ac }}</span>
                    </div>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-medium flex-shrink-0">
                    {{ $report->tanggal_service->locale('id')->translatedFormat('l, d F Y') }}
                </span>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100">
                <a href="/teknisi/report/{{ $report->id }}" class="inline-flex items-center gap-1.5 text-primary-600 hover:text-primary-700 text-sm font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Lihat Detail
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">{{ $reports->links() }}</div>
@endif
@endsection
