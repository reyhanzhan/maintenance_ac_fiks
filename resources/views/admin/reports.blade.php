@extends('layouts.app')

@section('title', 'Service Reports')
@section('page-title', 'Service Reports')

@section('content')
{{-- Filter --}}
<div class="bg-white rounded-2xl border border-gray-200 p-5 mb-6">
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Rumah Sakit</label>
            <div x-data="{
                open: false,
                search: '',
                selected: '{{ request('rumah_sakit_id', '') }}',
                selectedText: '',
                options: [
                    { value: '', text: 'Semua' },
                    @foreach($rumahSakits as $rs)
                    { value: '{{ $rs->id }}', text: '{{ $rs->nama }}' },
                    @endforeach
                ],
                get filtered() { return this.options.filter(o => o.text.toLowerCase().includes(this.search.toLowerCase())); },
                choose(opt) { this.selected = opt.value; this.selectedText = opt.value ? opt.text : ''; this.open = false; this.search = ''; },
                init() { const found = this.options.find(o => o.value === this.selected); if (found && found.value) this.selectedText = found.text; }
            }" @click.away="open = false" class="relative">
                <input type="hidden" name="rumah_sakit_id" :value="selected">
                <button type="button" @click="open = !open" class="w-full bg-white border border-gray-300 text-left rounded-lg px-3 py-2 text-sm flex items-center justify-between focus:ring-primary-500 focus:border-primary-500 transition">
                    <span x-text="selectedText || 'Semua'" :class="selectedText ? 'text-gray-900' : 'text-gray-500'"></span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-cloak x-transition class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                    <div class="p-2 border-b border-gray-100">
                        <input type="text" x-model="search" placeholder="Cari rumah sakit..." class="w-full text-sm border-gray-300 rounded-lg px-3 py-1.5 focus:ring-primary-500 focus:border-primary-500" @click.stop>
                    </div>
                    <div class="dd-list">
                        <template x-for="opt in filtered" :key="opt.value">
                            <button type="button" @click="choose(opt)" class="w-full text-left px-4 py-2 text-sm hover:bg-primary-50 hover:text-primary-600 transition" :class="selected === opt.value ? 'bg-primary-50 text-primary-600 font-medium' : 'text-gray-700'" x-text="opt.text"></button>
                        </template>
                        <div x-show="filtered.length === 0" class="px-4 py-2 text-sm text-gray-400">Tidak ditemukan</div>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Teknisi</label>
            <div x-data="{
                open: false,
                search: '',
                selected: '{{ request('user_id', '') }}',
                selectedText: '',
                options: [
                    { value: '', text: 'Semua' },
                    @foreach($teknisis as $t)
                    { value: '{{ $t->id }}', text: '{{ $t->name }}' },
                    @endforeach
                ],
                get filtered() { return this.options.filter(o => o.text.toLowerCase().includes(this.search.toLowerCase())); },
                choose(opt) { this.selected = opt.value; this.selectedText = opt.value ? opt.text : ''; this.open = false; this.search = ''; },
                init() { const found = this.options.find(o => o.value === this.selected); if (found && found.value) this.selectedText = found.text; }
            }" @click.away="open = false" class="relative">
                <input type="hidden" name="user_id" :value="selected">
                <button type="button" @click="open = !open" class="w-full bg-white border border-gray-300 text-left rounded-lg px-3 py-2 text-sm flex items-center justify-between focus:ring-primary-500 focus:border-primary-500 transition">
                    <span x-text="selectedText || 'Semua'" :class="selectedText ? 'text-gray-900' : 'text-gray-500'"></span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-cloak x-transition class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                    <div class="p-2 border-b border-gray-100">
                        <input type="text" x-model="search" placeholder="Cari teknisi..." class="w-full text-sm border-gray-300 rounded-lg px-3 py-1.5 focus:ring-primary-500 focus:border-primary-500" @click.stop>
                    </div>
                    <div class="dd-list">
                        <template x-for="opt in filtered" :key="opt.value">
                            <button type="button" @click="choose(opt)" class="w-full text-left px-4 py-2 text-sm hover:bg-primary-50 hover:text-primary-600 transition" :class="selected === opt.value ? 'bg-primary-50 text-primary-600 font-medium' : 'text-gray-700'" x-text="opt.text"></button>
                        </template>
                        <div x-show="filtered.length === 0" class="px-4 py-2 text-sm text-gray-400">Tidak ditemukan</div>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
            <input type="date" name="tanggal_dari" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" value="{{ request('tanggal_dari') }}">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
            <input type="date" name="tanggal_sampai" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" value="{{ request('tanggal_sampai') }}">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Data Per Halaman</label>
            <select name="per_page" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500">
                <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                <option value="15" {{ request('per_page', '15') == '15' ? 'selected' : '' }}>15</option>
                <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button class="flex-1 bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                <svg class="w-4 h-4 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg> Filter
            </button>
            <a href="/admin/reports" class="px-4 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-lg transition">Reset</a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                    <th class="px-5 py-3 text-left">
                        <div class="inline-flex items-center gap-1">
                            <span>No</span>
                            <a href="{{ request()->fullUrlWithQuery(array_merge(request()->except('page'), ['sort' => 'asc'])) }}" class="text-xs {{ request('sort') === 'asc' ? 'text-primary-600 font-bold' : 'text-gray-400 hover:text-gray-600' }}" title="Urutkan ascending">▲</a>
                            <a href="{{ request()->fullUrlWithQuery(array_merge(request()->except('page'), ['sort' => 'desc'])) }}" class="text-xs {{ request('sort', 'desc') === 'desc' ? 'text-primary-600 font-bold' : 'text-gray-400 hover:text-gray-600' }}" title="Urutkan descending">▼</a>
                        </div>
                    </th>
                    <th class="px-5 py-3 text-left">Tanggal</th>
                    <th class="px-5 py-3 text-left">RS</th>
                    <th class="px-5 py-3 text-left">Ruangan</th>
                    <th class="px-5 py-3 text-left">Merk / Type</th>
                    <th class="px-5 py-3 text-left">Teknisi</th>
                    <th class="px-5 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($reports as $i => $report)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-5 py-3 text-gray-400">{{ $reports->firstItem() + $i }}.</td>
                    <td class="px-5 py-3 text-gray-500 whitespace-nowrap">{{ $report->tanggal_service->locale('id')->translatedFormat('l, d F Y') }}</td>
                    <td class="px-5 py-3 font-medium text-gray-900">{{ $report->rumahSakit->nama }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $report->ruangan->nama }}</td>
                    <td class="px-5 py-3"><span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded-md text-xs font-medium">{{ $report->merk_ac }} / {{ $report->type_ac }}</span></td>
                    <td class="px-5 py-3 text-gray-600">{{ $report->user->name }}</td>
                    <td class="px-5 py-3 text-center">
                        <div class="flex items-center justify-center gap-1">
                            <a href="/admin/reports/{{ $report->id }}" class="p-1.5 text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('admin.reports.pdf', ['report' => $report->id, 'layout' => 'intan']) }}" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition text-xs font-medium" title="PDF CV Intan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </a>
                            <a href="{{ route('admin.reports.pdf', ['report' => $report->id, 'layout' => 'kemilau']) }}" class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition text-xs font-medium" title="PDF CV Kemilau">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
    <p class="text-xs text-gray-500">
        Menampilkan {{ $reports->firstItem() ?? 0 }} - {{ $reports->lastItem() ?? 0 }} dari {{ $reports->total() }} data ({{ $reports->perPage() }} data/halaman)
    </p>
    <div>{{ $reports->withQueryString()->links() }}</div>
</div>
@endsection
