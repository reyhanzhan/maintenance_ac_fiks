@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
{{-- Summary Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
    {{-- Total Service Reports --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $totalReports }}</p>
                <p class="text-xs text-gray-500">Service Report</p>
            </div>
        </div>
        <div class="mt-3 pt-3 border-t border-gray-100">
            <span class="text-xs text-primary-600 font-medium">+{{ $reportsThisMonth }} bulan ini</span>
        </div>
    </div>

    {{-- Total Surat Jalan --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $totalSuratJalan }}</p>
                <p class="text-xs text-gray-500">Surat Jalan</p>
            </div>
        </div>
        <div class="mt-3 pt-3 border-t border-gray-100">
            <span class="text-xs text-amber-600 font-medium">+{{ $suratJalanThisMonth }} bulan ini</span>
        </div>
    </div>

    {{-- Total AC Units --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-cyan-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $totalAcUnits }}</p>
                <p class="text-xs text-gray-500">Unit AC</p>
            </div>
        </div>
    </div>

    {{-- Total Rumah Sakit --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $totalRS }}</p>
                <p class="text-xs text-gray-500">Rumah Sakit</p>
            </div>
        </div>
    </div>

    {{-- Total Teknisi --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $totalTeknisi }}</p>
                <p class="text-xs text-gray-500">Teknisi</p>
            </div>
        </div>
    </div>
</div>

{{-- Chart + Teknisi --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Grafik Service Report 6 Bulan Terakhir --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-5">
        <h3 class="font-semibold text-gray-900 mb-4">Service Report 6 Bulan Terakhir</h3>
        <div class="flex items-end gap-3 h-48">
            @foreach($monthlyReports as $m)
            <div class="flex-1 flex flex-col items-center gap-1">
                <span class="text-xs font-semibold text-gray-700">{{ $m['count'] }}</span>
                <div class="w-full bg-primary-100 rounded-t-lg relative" style="height: {{ $maxMonthly > 0 ? max(($m['count'] / $maxMonthly) * 100, 4) : 4 }}%">
                    <div class="absolute inset-0 bg-primary-500 rounded-t-lg opacity-80"></div>
                </div>
                <span class="text-[10px] text-gray-500 text-center leading-tight mt-1">{{ $m['label'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Performa Teknisi --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Performa Teknisi</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($teknisiPerformance as $tek)
            <div class="px-5 py-3 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $tek->name }}</p>
                    <p class="text-xs text-gray-500">{{ $tek->reports_this_month }} report bulan ini</p>
                </div>
                <span class="text-lg font-bold text-gray-700">{{ $tek->total_reports }}</span>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-gray-400 text-sm">Belum ada teknisi.</div>
            @endforelse
        </div>
    </div>
</div>

{{-- Service per RS + Recent --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Service per Rumah Sakit --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Service per Rumah Sakit</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                        <th class="px-5 py-3 text-left">Rumah Sakit</th>
                        <th class="px-5 py-3 text-center">Unit AC</th>
                        <th class="px-5 py-3 text-center">Total Report</th>
                        <th class="px-5 py-3 text-left">Service Terakhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reportsByRS as $rs)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-5 py-3 font-medium text-gray-900">{{ $rs->nama }}</td>
                        <td class="px-5 py-3 text-center">
                            <span class="px-2 py-0.5 bg-cyan-50 text-cyan-700 rounded-md text-xs font-medium">{{ $rs->ac_units_count }}</span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="px-2 py-0.5 bg-primary-50 text-primary-700 rounded-md text-xs font-medium">{{ $rs->service_reports_count }}</span>
                        </td>
                        <td class="px-5 py-3 text-gray-500 text-xs">
                            {{ $rs->last_service ? \Carbon\Carbon::parse($rs->last_service)->locale('id')->translatedFormat('d M Y') : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Aktivitas Terbaru --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Aktivitas Terbaru</h3>
            <a href="/admin/reports" class="text-primary-600 hover:text-primary-700 text-xs font-medium">Semua &rarr;</a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($recentReports as $report)
            <a href="/admin/reports/{{ $report->id }}" class="block px-5 py-3 hover:bg-gray-50/50 transition">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $report->rumahSakit->nama }}</p>
                    <span class="text-[10px] text-gray-400 whitespace-nowrap ml-2">{{ $report->tanggal_service->locale('id')->translatedFormat('d M Y') }}</span>
                </div>
                <p class="text-xs text-gray-500">{{ $report->ruangan->nama }} &middot; {{ $report->user->name }}</p>
            </a>
            @empty
            <div class="px-5 py-8 text-center text-gray-400 text-sm">Belum ada data.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
