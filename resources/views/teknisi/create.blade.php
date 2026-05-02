@extends('layouts.app')

@section('title', 'Buat Service Report')
@section('page-title', 'Buat Service Report')

@section('content')
@if ($errors->has('upload'))
    <div class="max-w-3xl mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        {{ $errors->first('upload') }}
    </div>
@endif

<form action="/teknisi/store" method="POST" enctype="multipart/form-data" id="serviceForm" class="max-w-3xl"
    x-data="{ isSiloam: false, selectedGedung: '' }"
    @rs-selected="isSiloam = $event.detail.name.toLowerCase().includes('siloam'); if(!isSiloam) selectedGedung = ''">
    @csrf

    {{-- Info AC --}}
    <div class="bg-white rounded-2xl border border-gray-200 mb-5 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
            <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <h3 class="font-semibold text-gray-900">Informasi Service</h3>
        </div>
        <div class="p-5 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Rumah Sakit <span class="text-red-500">*</span></label>
                <div x-data="{
                    open: false,
                    search: '',
                    selected: '{{ old('rumah_sakit_id', '') }}',
                    selectedText: '',
                    options: [
                        @foreach($rumahSakits as $rs)
                        { value: '{{ $rs->id }}', text: '{{ $rs->nama }}' },
                        @endforeach
                    ],
                    get filtered() { return this.options.filter(o => o.text.toLowerCase().includes(this.search.toLowerCase())); },
                    choose(opt) { this.selected = opt.value; this.selectedText = opt.text; this.open = false; this.search = ''; document.getElementById('rumah_sakit_id').value = opt.value; document.getElementById('rumah_sakit_id').dispatchEvent(new Event('change')); this.$dispatch('rs-selected', { name: opt.text }); },
                    init() { const found = this.options.find(o => o.value === this.selected); if (found) this.selectedText = found.text; }
                }" @click.away="open = false" class="relative">
                    <input type="hidden" name="rumah_sakit_id" id="rumah_sakit_id" :value="selected" required>
                    <button type="button" @click="open = !open" class="w-full bg-gray-50 border border-gray-300 text-left rounded-xl px-4 py-2.5 text-sm flex items-center justify-between focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                        <span x-text="selectedText || '-- Pilih Rumah Sakit --'" :class="selectedText ? 'text-gray-900' : 'text-gray-400'"></span>
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
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Ruangan <span class="text-red-500">*</span></label>
                <div x-data="{
                    open: false,
                    search: '',
                    selected: '{{ old('ruangan_id', '') }}',
                    selectedText: '',
                    options: [],
                    get filtered() { return this.options.filter(o => o.text.toLowerCase().includes(this.search.toLowerCase())); },
                    choose(opt) { this.selected = opt.value; this.selectedText = opt.text; this.open = false; this.search = ''; },
                    reset() { this.selected = ''; this.selectedText = ''; this.options = []; this.search = ''; },
                    loadOptions(rsId) {
                        this.reset();
                        if (rsId && window.ruanganData && window.ruanganData[rsId]) {
                            this.options = window.ruanganData[rsId].map(r => ({ value: String(r.id), text: r.nama }));
                        }
                    }
                }" x-init="$watch('selected', v => { document.getElementById('ruangan_id_val').value = v; });
                    document.getElementById('rumah_sakit_id').addEventListener('change', (e) => { loadOptions(e.target.value); });" @click.away="open = false" class="relative" id="ruangan_dropdown">
                    <input type="hidden" name="ruangan_id" id="ruangan_id_val" :value="selected" required>
                    <button type="button" @click="open = !open" class="w-full bg-gray-50 border border-gray-300 text-left rounded-xl px-4 py-2.5 text-sm flex items-center justify-between focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                        <span x-text="selectedText || '-- Pilih Ruangan --'" :class="selectedText ? 'text-gray-900' : 'text-gray-400'"></span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-2 border-b border-gray-100">
                            <input type="text" x-model="search" placeholder="Cari ruangan..." class="w-full text-sm border-gray-300 rounded-lg px-3 py-1.5 focus:ring-primary-500 focus:border-primary-500" @click.stop>
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

            {{-- Gedung (khusus Siloam) --}}
            <div x-show="isSiloam" x-cloak>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Gedung <span class="text-red-500">*</span></label>
                <input type="hidden" name="gedung" :value="isSiloam ? selectedGedung : ''">
                <select x-model="selectedGedung" class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                    <option value="">-- Pilih Gedung --</option>
                    <option value="Baru">Gedung Baru</option>
                    <option value="Lama">Gedung Lama</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Merk AC <span class="text-red-500">*</span></label>
                    <div x-data="{
                        open: false,
                        search: '',
                        selected: '{{ old('merk_ac', '') }}',
                        selectedText: '',
                        options: [
                            @foreach(['Daikin','Panasonic','LG','Samsung','Sharp','Mitsubishi','Gree','Midea','Lainnya'] as $m)
                            { value: '{{ $m }}', text: '{{ $m }}' },
                            @endforeach
                        ],
                        get filtered() { return this.options.filter(o => o.text.toLowerCase().includes(this.search.toLowerCase())); },
                        choose(opt) { this.selected = opt.value; this.selectedText = opt.text; this.open = false; this.search = ''; },
                        init() { const found = this.options.find(o => o.value === this.selected); if (found) this.selectedText = found.text; }
                    }" @click.away="open = false" class="relative">
                        <input type="hidden" name="merk_ac" :value="selected" required>
                        <button type="button" @click="open = !open" class="w-full bg-gray-50 border border-gray-300 text-left rounded-xl px-4 py-2.5 text-sm flex items-center justify-between focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                            <span x-text="selectedText || '-- Pilih --'" :class="selectedText ? 'text-gray-900' : 'text-gray-400'"></span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-cloak x-transition class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                            <div class="p-2 border-b border-gray-100">
                                <input type="text" x-model="search" placeholder="Cari merk..." class="w-full text-sm border-gray-300 rounded-lg px-3 py-1.5 focus:ring-primary-500 focus:border-primary-500" @click.stop>
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
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Type AC <span class="text-red-500">*</span></label>
                    <div x-data="{
                        open: false,
                        search: '',
                        selected: '{{ old('type_ac', '') }}',
                        selectedText: '',
                        options: [
                            @foreach(['Split','Cassette','Standing Floor','Ceiling','Window','Central'] as $t)
                            { value: '{{ $t }}', text: '{{ $t }}' },
                            @endforeach
                        ],
                        get filtered() { return this.options.filter(o => o.text.toLowerCase().includes(this.search.toLowerCase())); },
                        choose(opt) { this.selected = opt.value; this.selectedText = opt.text; this.open = false; this.search = ''; },
                        init() { const found = this.options.find(o => o.value === this.selected); if (found) this.selectedText = found.text; }
                    }" @click.away="open = false" class="relative">
                        <input type="hidden" name="type_ac" :value="selected" required>
                        <button type="button" @click="open = !open" class="w-full bg-gray-50 border border-gray-300 text-left rounded-xl px-4 py-2.5 text-sm flex items-center justify-between focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                            <span x-text="selectedText || '-- Pilih --'" :class="selectedText ? 'text-gray-900' : 'text-gray-400'"></span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-cloak x-transition class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                            <div class="p-2 border-b border-gray-100">
                                <input type="text" x-model="search" placeholder="Cari type..." class="w-full text-sm border-gray-300 rounded-lg px-3 py-1.5 focus:ring-primary-500 focus:border-primary-500" @click.stop>
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
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Service <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_service" value="{{ old('tanggal_service', date('Y-m-d')) }}" required
                    class="w-full bg-gray-50 border border-gray-300 text-gray-900 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition text-sm">
            </div>
        </div>
    </div>

    {{-- Checklist --}}
    <div class="bg-white rounded-2xl border border-gray-200 mb-5 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
            <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            <h3 class="font-semibold text-gray-900">Pelaksanaan Pekerjaan</h3>
        </div>

        {{-- Standard Checklist --}}
        <div class="divide-y divide-gray-100" x-show="!isSiloam || selectedGedung !== 'Baru'">
            <fieldset :disabled="isSiloam && selectedGedung === 'Baru'">
                @foreach($pemeriksaans as $no => $nama)
                <div class="px-5 py-3" x-data="{ normal: true }">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="text-xs font-mono text-gray-400 w-5 text-right flex-shrink-0">{{ $no }}</span>
                            <span class="text-sm text-gray-800">{{ $nama }}</span>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="text-xs font-medium" :class="normal ? 'text-emerald-600' : 'text-red-500'" x-text="normal ? 'Normal' : 'Tidak Normal'"></span>
                            <button type="button" @click="normal = !normal"
                                :class="normal ? 'bg-emerald-500' : 'bg-red-500'"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors shadow-inner">
                                <span :class="normal ? 'translate-x-6' : 'translate-x-1'"
                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow"></span>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="items[{{ $no }}][is_normal]" :value="normal ? '1' : '0'">
                    <div x-show="!normal" x-cloak x-transition class="mt-3 ml-8 space-y-2">
                        <textarea name="items[{{ $no }}][keterangan]" rows="2" placeholder="Keterangan tidak normal..."
                            class="w-full bg-red-50 border border-red-200 text-gray-800 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-red-300 focus:border-red-300 placeholder-red-300 transition"></textarea>
                        <input type="file" name="item_photos[{{ $no }}][]" multiple accept="image/*" onchange="previewItemPhotos(this, 'std_{{ $no }}')"
                            class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-red-100 file:text-red-700 hover:file:bg-red-200 transition">
                        <div id="preview_std_{{ $no }}" class="flex flex-wrap gap-2"></div>
                    </div>
                </div>
                @endforeach
            </fieldset>
        </div>

        {{-- Siloam Gedung Baru Checklist --}}
        <div class="divide-y divide-gray-100" x-show="isSiloam && selectedGedung === 'Baru'" x-cloak>
            <fieldset :disabled="!isSiloam || selectedGedung !== 'Baru'">
                @foreach($pemeriksaansSiloamBaru as $no => $data)
                <div class="px-5 py-3" x-data="{ normal: true }">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="text-xs font-mono text-gray-400 w-5 text-right flex-shrink-0">{{ $no }}</span>
                            <div>
                                <span class="text-sm text-gray-800">{{ $data['nama'] }}</span>
                                @if($data['desc'])
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $data['desc'] }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="text-xs font-medium" :class="normal ? 'text-emerald-600' : 'text-red-500'" x-text="normal ? 'Normal' : 'Tidak Normal'"></span>
                            <button type="button" @click="normal = !normal"
                                :class="normal ? 'bg-emerald-500' : 'bg-red-500'"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors shadow-inner">
                                <span :class="normal ? 'translate-x-6' : 'translate-x-1'"
                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow"></span>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="items[{{ $no }}][is_normal]" :value="normal ? '1' : '0'">
                    <div x-show="!normal" x-cloak x-transition class="mt-3 ml-8 space-y-2">
                        <textarea name="items[{{ $no }}][keterangan]" rows="2" placeholder="Keterangan tidak normal..."
                            class="w-full bg-red-50 border border-red-200 text-gray-800 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-red-300 focus:border-red-300 placeholder-red-300 transition"></textarea>
                        <input type="file" name="item_photos[{{ $no }}][]" multiple accept="image/*" onchange="previewItemPhotos(this, 'slm_{{ $no }}')"
                            class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-red-100 file:text-red-700 hover:file:bg-red-200 transition">
                        <div id="preview_slm_{{ $no }}" class="flex flex-wrap gap-2"></div>
                    </div>
                </div>
                @endforeach
            </fieldset>
        </div>
    </div>

    {{-- Upload Foto --}}
    <div class="bg-white rounded-2xl border border-gray-200 mb-5 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
            <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <h3 class="font-semibold text-gray-900">Foto AC (Umum)</h3>
        </div>
        <div class="p-5">
            <label for="general_photos" class="block border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center cursor-pointer hover:border-primary-400 hover:bg-primary-50/30 transition-all group">
                <svg class="w-10 h-10 text-gray-300 group-hover:text-primary-400 mx-auto mb-2 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                <p class="text-sm text-gray-500 group-hover:text-primary-600 transition">Tap untuk upload foto AC <span class="text-xs">(bisa lebih dari 1)</span></p>
            </label>
            <p class="mt-2 text-xs text-gray-500">Foto akan dikompres otomatis sebelum upload agar lebih cepat dan tidak gagal karena ukuran terlalu besar.</p>
            <input type="file" name="general_photos[]" id="general_photos" class="hidden" multiple accept="image/*" onchange="previewGeneralPhotos(this)">
            <div id="general_preview" class="flex flex-wrap gap-2 mt-3"></div>
        </div>
    </div>

    {{-- Uraian Pekerjaan --}}
    <div class="bg-white rounded-2xl border border-gray-200 mb-5 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
            <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
            <h3 class="font-semibold text-gray-900">Uraian Pekerjaan</h3>
        </div>
        <div class="p-5 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Uraian Pekerjaan</label>
                <textarea name="saran" rows="3" placeholder="Tuliskan uraian pekerjaan..."
                    class="w-full bg-gray-50 border border-gray-300 text-gray-800 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 placeholder-gray-400 transition">{{ old('saran', 'Servis rutin AC selesai, kondisi unit normal dan berfungsi dengan baik.') }}</textarea>
            </div>
        </div>
    </div>

    <button type="submit" id="submitBtn"
        class="w-full bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-semibold py-3.5 px-4 rounded-2xl transition-all shadow-lg shadow-primary-500/25 hover:shadow-primary-500/40 flex items-center justify-center gap-2 text-sm mb-8">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        Simpan Service Report
    </button>
</form>

@php
    $ruanganData = [];
    foreach($rumahSakits as $rs) {
        $ruanganData[$rs->id] = $rs->ruangans->map(fn($r) => ['id' => $r->id, 'nama' => $r->nama]);
    }
@endphp
@endsection

@section('scripts')
<script>
    window.ruanganData = @json($ruanganData);
    let isSubmittingReport = false;

    const MAX_FILE_SIZE_AFTER_COMPRESS = 5 * 1024 * 1024; // 5MB
    const MAX_TOTAL_SIZE_AFTER_COMPRESS = 30 * 1024 * 1024; // 30MB
    const MAX_IMAGE_DIMENSION = 1920;
    const BASE_IMAGE_QUALITY = 0.85;

    function getCompressedFileName(fileName, type) {
        const dot = fileName.lastIndexOf('.');
        const base = dot !== -1 ? fileName.slice(0, dot) : fileName;
        const ext = type === 'image/webp' ? 'webp' : 'jpg';
        return base + '-compressed.' + ext;
    }

    function loadImageFromFile(file) {
        return new Promise((resolve, reject) => {
            const url = URL.createObjectURL(file);
            const image = new Image();
            image.onload = () => {
                URL.revokeObjectURL(url);
                resolve(image);
            };
            image.onerror = () => {
                URL.revokeObjectURL(url);
                reject(new Error('Gagal membaca gambar'));
            };
            image.src = url;
        });
    }

    function canvasToBlob(canvas, type, quality) {
        return new Promise((resolve) => {
            canvas.toBlob((blob) => resolve(blob), type, quality);
        });
    }

    async function compressImageFile(file) {
        if (!file.type.startsWith('image/')) {
            return file;
        }

        const image = await loadImageFromFile(file);
        let width = image.width;
        let height = image.height;

        const longestSide = Math.max(width, height);
        if (longestSide > MAX_IMAGE_DIMENSION) {
            const ratio = MAX_IMAGE_DIMENSION / longestSide;
            width = Math.round(width * ratio);
            height = Math.round(height * ratio);
        }

        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;

        const ctx = canvas.getContext('2d');
        ctx.drawImage(image, 0, 0, width, height);

        // PNG camera photos are uncommon; convert non-webp to jpeg for better compression.
        const outputType = file.type === 'image/webp' ? 'image/webp' : 'image/jpeg';

        let quality = BASE_IMAGE_QUALITY;
        let blob = await canvasToBlob(canvas, outputType, quality);

        while (blob && blob.size > MAX_FILE_SIZE_AFTER_COMPRESS && quality > 0.55) {
            quality -= 0.1;
            blob = await canvasToBlob(canvas, outputType, quality);
        }

        if (!blob) {
            return file;
        }

        // Keep original if already smaller to avoid unnecessary quality loss.
        if (blob.size >= file.size && file.size <= MAX_FILE_SIZE_AFTER_COMPRESS) {
            return file;
        }

        return new File([blob], getCompressedFileName(file.name, outputType), {
            type: outputType,
            lastModified: Date.now(),
        });
    }

    async function compressFilesInInput(input) {
        if (!input.files || input.files.length === 0) {
            return;
        }

        const dt = new DataTransfer();
        for (const file of Array.from(input.files)) {
            const compressed = await compressImageFile(file);
            dt.items.add(compressed);
        }

        input.files = dt.files;
    }

    document.getElementById('rumah_sakit_id').addEventListener('change', function() {
        const rsId = this.value;
        const ruanganEl = document.getElementById('ruangan_dropdown');
        if (ruanganEl && ruanganEl.__x) {
            ruanganEl.__x.$data.loadOptions(rsId);
        }
    });

    function previewItemPhotos(input, no) {
        const preview = document.getElementById('preview_' + no);
        if (!preview) return;
        preview.innerHTML = '';
        if (input.files) {
            Array.from(input.files).forEach(function(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-16 h-16 object-cover rounded-lg border border-gray-200';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        }
    }

    function previewGeneralPhotos(input) {
        const preview = document.getElementById('general_preview');
        preview.innerHTML = '';
        if (input.files) {
            Array.from(input.files).forEach(function(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-20 h-20 object-cover rounded-xl border border-gray-200';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        }
    }

    document.getElementById('serviceForm').addEventListener('submit', async function(event) {
        if (isSubmittingReport) {
            return;
        }

        event.preventDefault();

        const form = this;
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Mengompres foto...';

        try {
            const fileInputs = form.querySelectorAll('input[type="file"]');

            for (const input of fileInputs) {
                await compressFilesInInput(input);
            }

            let totalSize = 0;
            for (const input of fileInputs) {
                if (!input.files || input.files.length === 0) {
                    continue;
                }

                for (const file of Array.from(input.files)) {
                    totalSize += file.size;
                    if (file.size > MAX_FILE_SIZE_AFTER_COMPRESS) {
                        alert('Ukuran foto "' + file.name + '" masih di atas 5MB. Ambil ulang foto dengan resolusi lebih rendah.');
                        btn.disabled = false;
                        btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Simpan Service Report';
                        return;
                    }
                }
            }

            if (totalSize > MAX_TOTAL_SIZE_AFTER_COMPRESS) {
                alert('Total ukuran foto masih terlalu besar. Kurangi jumlah foto yang diupload.');
                btn.disabled = false;
                btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Simpan Service Report';
                return;
            }

            isSubmittingReport = true;
            btn.innerHTML = '<svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Menyimpan...';
            form.submit();
        } catch (error) {
            alert('Gagal memproses foto. Coba lagi atau kurangi jumlah foto.');
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Simpan Service Report';
        }
    });
</script>
@endsection
