@extends('layouts.app')

@section('title', 'Koordinator Lapangan RS')
@section('page-title', 'Koordinator Lapangan RS')

@section('content')
<div class="max-w-5xl">
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Pengaturan Koordinator Lapangan per Rumah Sakit</h3>
            <p class="text-sm text-gray-500 mt-1">Nama di sini akan otomatis dipakai di PDF Service Report.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                        <th class="px-5 py-3 text-left w-16">No</th>
                        <th class="px-5 py-3 text-left">Rumah Sakit</th>
                        <th class="px-5 py-3 text-left">Koordinator Lapangan</th>
                        <th class="px-5 py-3 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rumahSakits as $index => $rs)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3 text-gray-400">{{ $index + 1 }}</td>
                        <td class="px-5 py-3 text-gray-900 font-medium">{{ $rs->nama }}</td>
                        <td class="px-5 py-3">
                            <form action="{{ route('admin.koordinator-rs.update', $rs) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <input type="text" name="koordinator_lapangan" value="{{ old('koordinator_lapangan', $rs->koordinator_lapangan) }}" placeholder="Contoh: M. Choiruddin"
                                    class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500">
                        </td>
                        <td class="px-5 py-3 text-center">
                                <button class="inline-flex items-center bg-primary-500 hover:bg-primary-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition">Simpan</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-gray-400">Data rumah sakit belum ada.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
