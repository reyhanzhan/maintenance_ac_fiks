@extends('layouts.app')

@section('title', 'Surat Jalan')
@section('page-title', 'Surat Jalan')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div></div>
    <a href="/admin/surat-jalan/create" class="inline-flex items-center gap-2 bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-xl text-sm font-medium transition shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Buat Surat Jalan
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                    <th class="px-5 py-3 text-left">#</th>
                    <th class="px-5 py-3 text-left">Nomor</th>
                    <th class="px-5 py-3 text-left">RS / Departemen</th>
                    <th class="px-5 py-3 text-left">Tanggal</th>
                    <th class="px-5 py-3 text-left">Mengetahui</th>
                    <th class="px-5 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($suratJalans as $i => $sj)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-5 py-3 text-gray-400">{{ $suratJalans->firstItem() + $i }}</td>
                    <td class="px-5 py-3 font-medium text-gray-900">{{ $sj->nomor ?: '-' }}</td>
                    <td class="px-5 py-3">
                        <div class="font-medium text-gray-900">{{ $sj->rumahSakit->nama }}</div>
                        <div class="text-xs text-gray-400">{{ $sj->departemen }}</div>
                    </td>
                    <td class="px-5 py-3 text-gray-500 whitespace-nowrap">{{ $sj->tanggal->format('d/m/Y') }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $sj->mengetahui ?: '-' }}</td>
                    <td class="px-5 py-3 text-center">
                        <div class="flex items-center justify-center gap-1">
                            <a href="/admin/surat-jalan/{{ $sj->id }}" class="p-1.5 text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('admin.surat-jalan.pdf', ['suratJalan' => $sj->id, 'layout' => 'intan']) }}" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition" title="PDF CV Intan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </a>
                            <a href="{{ route('admin.surat-jalan.pdf', ['suratJalan' => $sj->id, 'layout' => 'kemilau']) }}" class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition" title="PDF CV Kemilau">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $suratJalans->links() }}</div>
@endsection
