@extends('layouts.app')

@section('title', 'Buat Surat Jalan')
@section('page-title', 'Buat Surat Jalan')

@section('content')
<form action="/admin/surat-jalan" method="POST" class="space-y-6 max-w-4xl" x-data="suratJalanForm()">
    @csrf

    {{-- Informasi --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Informasi</h3>
        </div>
        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Departemen / Ruangan</label>
                <div @click.away="departemenOpen = false" class="relative">
                    <input type="hidden" name="departemen" :value="departemenSelected">
                    <button type="button" @click="departemenOpen = !departemenOpen" class="w-full bg-white border border-gray-300 text-left rounded-lg px-3 py-2 text-sm flex items-center justify-between focus:ring-primary-500 focus:border-primary-500 transition">
                        <span x-text="departemenSelected || '-- Pilih Ruangan --'" :class="departemenSelected ? 'text-gray-900' : 'text-gray-400'"></span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="departemenOpen" x-cloak x-transition class="absolute z-40 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                        <div class="p-2 border-b border-gray-100">
                            <input type="text" x-model="departemenSearch" placeholder="Cari ruangan..." class="w-full text-sm border-gray-300 rounded-lg px-3 py-1.5 focus:ring-primary-500 focus:border-primary-500" @click.stop>
                        </div>
                        <div class="dd-list">
                            <template x-for="ruangan in departemenFiltered" :key="ruangan">
                                <button type="button" @click="chooseDepartemen(ruangan)" class="w-full text-left px-4 py-2 text-sm hover:bg-primary-50 hover:text-primary-600 transition" :class="departemenSelected === ruangan ? 'bg-primary-50 text-primary-600 font-medium' : 'text-gray-700'" x-text="ruangan"></button>
                            </template>
                            <div x-show="departemenFiltered.length === 0" class="px-4 py-2 text-sm text-gray-400">Tidak ditemukan</div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rumah Sakit <span class="text-red-500">*</span></label>
                <div @click.away="rsOpen = false" class="relative">
                    <input type="hidden" name="rumah_sakit_id" :value="rsSelected" required>
                    <button type="button" @click="rsOpen = !rsOpen" class="w-full bg-white border border-gray-300 text-left rounded-lg px-3 py-2 text-sm flex items-center justify-between focus:ring-primary-500 focus:border-primary-500 transition">
                        <span x-text="rsSelectedText || '-- Pilih --'" :class="rsSelectedText ? 'text-gray-900' : 'text-gray-400'"></span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="rsOpen" x-cloak x-transition class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg">
                        <div class="p-2 border-b border-gray-100">
                            <input type="text" x-model="rsSearch" placeholder="Cari rumah sakit..." class="w-full text-sm border-gray-300 rounded-lg px-3 py-1.5 focus:ring-primary-500 focus:border-primary-500" @click.stop>
                        </div>
                        <div class="dd-list">
                            <template x-for="opt in rsFiltered" :key="opt.value">
                                <button type="button" @click="chooseRs(opt)" class="w-full text-left px-4 py-2 text-sm hover:bg-primary-50 hover:text-primary-600 transition" :class="rsSelected === opt.value ? 'bg-primary-50 text-primary-600 font-medium' : 'text-gray-700'" x-text="opt.text"></button>
                            </template>
                            <div x-show="rsFiltered.length === 0" class="px-4 py-2 text-sm text-gray-400">Tidak ditemukan</div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Penerima</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <input type="text" name="penerima" class="w-full bg-white border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" value="{{ old('penerima') }}" placeholder="Nama penerima surat jalan...">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mengetahui</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <input type="text" name="mengetahui" x-model="mengetahuiInput" class="w-full bg-white border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm focus:ring-primary-500 focus:border-primary-500" value="{{ old('mengetahui') }}" placeholder="Nama penanda tangan mengetahui...">
                </div>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" value="{{ old('tanggal', date('Y-m-d')) }}" required>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Pekerjaan</label>
                <input type="text" name="deskripsi_pekerjaan" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" value="{{ old('deskripsi_pekerjaan', 'Servis rutin ac') }}" placeholder="Servis rutin ac">
            </div>
        </div>
    </div>

    {{-- Item Ruangan --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Daftar Ruangan</h3>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500">Total: <strong x-text="totalUnit"></strong> unit</span>
                <button type="button" @click="addItem()" class="inline-flex items-center gap-1 text-primary-600 hover:text-primary-700 text-sm font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah
                </button>
            </div>
        </div>
        <div class="p-5 space-y-3">
            <template x-for="(item, index) in items" :key="index">
                <div class="border border-gray-200 rounded-xl p-3 space-y-3">
                    <div class="flex gap-3 items-start">
                        <div class="w-28">
                            <label x-show="index === 0" class="block text-xs text-gray-500 mb-1">Banyaknya</label>
                            <div class="flex items-center rounded-lg border border-gray-300 overflow-hidden">
                                <button type="button" @click="decreaseQty(index)" class="w-8 h-9 text-gray-600 hover:bg-gray-50">-</button>
                                <input type="number" x-model.number="item.banyaknya" @input="syncUnitDetails(index)" :name="'items['+index+'][banyaknya]'" class="w-full border-0 text-center text-sm focus:ring-0" min="1" required>
                                <button type="button" @click="increaseQty(index)" class="w-8 h-9 text-gray-600 hover:bg-gray-50">+</button>
                            </div>
                        </div>
                        <div class="flex-1">
                            <label x-show="index === 0" class="block text-xs text-gray-500 mb-1">Nama Ruangan</label>
                            <input type="text" x-model="item.nama_ruangan" :name="'items['+index+'][nama_ruangan]'" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Ketik atau pilih ruangan" required :list="'ruangan-options'">
                        </div>
                        <div class="flex items-end pb-0.5">
                            <div x-show="index === 0" class="h-5 mb-1"></div>
                            <button type="button" @click="removeItem(index)" class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" x-show="items.length > 1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="pl-0 md:pl-32">
                        <p class="text-xs text-gray-500 mb-2">Detail Unit per Banyaknya</p>
                        <div class="space-y-2">
                            <template x-for="(unit, unitIndex) in item.unit_details" :key="unitIndex">
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                                    <div class="md:col-span-2 text-xs text-gray-500">
                                        Unit <span x-text="unitIndex + 1"></span>
                                    </div>
                                    <div class="md:col-span-6">
                                        <select x-model="unit.type_ac" :name="'items['+index+'][unit_details]['+unitIndex+'][type_ac]'" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500">
                                            <option value="">Type AC</option>
                                            <template x-for="type in availableTypeAc" :key="type">
                                                <option :value="type" x-text="type"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="md:col-span-4">
                                        <select x-model="unit.pk" :name="'items['+index+'][unit_details]['+unitIndex+'][pk]'" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500">
                                            <option value="">PK</option>
                                            <template x-for="pkOption in availablePk" :key="pkOption">
                                                <option :value="pkOption" x-text="pkOption"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Datalist for autocomplete --}}
    <datalist id="ruangan-options">
        <template x-for="r in availableRuangans" :key="r">
            <option :value="r"></option>
        </template>
    </datalist>

    <button type="submit" class="w-full bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-semibold py-3 rounded-xl shadow-lg shadow-primary-500/25 transition">
        Simpan Surat Jalan
    </button>
</form>
@endsection

@section('scripts')
@php
    $acUnitDataForJs = \App\Models\AcUnit::select('rumah_sakit_id', 'jenis_ac', 'kapasitas_pk')
        ->get()
        ->groupBy('rumah_sakit_id')
        ->map(function($units) {
            return [
                'types' => $units->pluck('jenis_ac')->map(function ($value) {
                    return trim((string) $value);
                })->filter()->unique()->values()->toArray(),
                'pks' => $units->pluck('kapasitas_pk')->map(function ($value) {
                    return trim((string) $value);
                })->filter()->unique()->values()->toArray(),
            ];
        });

    $defaultAcOptionsForJs = [
        'types' => \App\Models\AcUnit::query()->pluck('jenis_ac')->map(function ($value) {
            return trim((string) $value);
        })->filter()->unique()->values()->toArray(),
        'pks' => \App\Models\AcUnit::query()->pluck('kapasitas_pk')->map(function ($value) {
            return trim((string) $value);
        })->filter()->unique()->values()->toArray(),
    ];
@endphp

<script>
    const ruanganData = @json(
        \App\Models\RumahSakit::with('ruangans')->get()->mapWithKeys(function($rs) {
            return [$rs->id => $rs->ruangans->pluck('nama')->toArray()];
        })
    );

    const mengetahuiData = @json(
        \App\Models\RumahSakit::pluck('mengetahui_surat_jalan', 'id')
    );

    const acUnitData = @json($acUnitDataForJs);

    const defaultAcOptions = @json($defaultAcOptionsForJs);

    function suratJalanForm() {
        const oldItems = @json(old('items'));
        const defaultItem = {
            banyaknya: 1,
            nama_ruangan: '',
            unit_details: [{ type_ac: '', pk: '' }],
        };

        const normalizeItems = (rawItems) => {
            if (!Array.isArray(rawItems) || rawItems.length === 0) {
                return [JSON.parse(JSON.stringify(defaultItem))];
            }

            return rawItems.map((item) => {
                const qty = Math.max(1, parseInt(item?.banyaknya) || 1);
                const details = [];
                const oldDetails = Array.isArray(item?.unit_details) ? item.unit_details : [];

                for (let i = 0; i < qty; i++) {
                    details.push({
                        type_ac: oldDetails[i]?.type_ac || '',
                        pk: oldDetails[i]?.pk || '',
                    });
                }

                return {
                    banyaknya: qty,
                    nama_ruangan: item?.nama_ruangan || '',
                    unit_details: details,
                };
            });
        };

        return {
            rsOpen: false,
            rsSearch: '',
            departemenOpen: false,
            departemenSearch: '',
            rsSelected: '{{ old('rumah_sakit_id', '') }}',
            rsSelectedText: '',
            departemenSelected: @json(old('departemen', '')),
            mengetahuiInput: @json(old('mengetahui', '')),
            rsOptions: [
                @foreach($rumahSakits as $rs)
                { value: '{{ $rs->id }}', text: @json($rs->nama) },
                @endforeach
            ],
            items: normalizeItems(oldItems),
            availableRuangans: [],
            availableTypeAc: defaultAcOptions.types || [],
            availablePk: defaultAcOptions.pks || [],

            get rsFiltered() {
                return this.rsOptions.filter(o => o.text.toLowerCase().includes(this.rsSearch.toLowerCase()));
            },

            get departemenFiltered() {
                return this.availableRuangans.filter((ruangan) =>
                    ruangan.toLowerCase().includes(this.departemenSearch.toLowerCase())
                );
            },

            get totalUnit() {
                return this.items.reduce((sum, item) => sum + (parseInt(item.banyaknya) || 0), 0);
            },

            setAcOptions(rsId) {
                const rsOptions = acUnitData[rsId] || null;
                this.availableTypeAc = (rsOptions?.types && rsOptions.types.length > 0)
                    ? rsOptions.types
                    : (defaultAcOptions.types || []);
                this.availablePk = (rsOptions?.pks && rsOptions.pks.length > 0)
                    ? rsOptions.pks
                    : (defaultAcOptions.pks || []);
            },

            chooseRs(opt) {
                this.rsSelected = opt.value;
                this.rsSelectedText = opt.text;
                this.rsOpen = false;
                this.rsSearch = '';
                this.availableRuangans = ruanganData[opt.value] || [];
                this.setAcOptions(opt.value);
                this.departemenOpen = false;
                this.departemenSearch = '';
                if (!this.mengetahuiInput) {
                    this.mengetahuiInput = mengetahuiData[opt.value] || '';
                }
                if (this.departemenSelected && !this.availableRuangans.includes(this.departemenSelected)) {
                    this.departemenSelected = '';
                }
            },

            chooseDepartemen(ruangan) {
                this.departemenSelected = ruangan;
                this.departemenOpen = false;
                this.departemenSearch = '';
            },

            addItem() {
                this.items.push(JSON.parse(JSON.stringify(defaultItem)));
            },

            removeItem(index) {
                if (this.items.length > 1) this.items.splice(index, 1);
            },

            increaseQty(index) {
                const current = Math.max(1, parseInt(this.items[index].banyaknya) || 1);
                this.items[index].banyaknya = current + 1;
                this.syncUnitDetails(index);
            },

            decreaseQty(index) {
                const current = Math.max(1, parseInt(this.items[index].banyaknya) || 1);
                this.items[index].banyaknya = Math.max(1, current - 1);
                this.syncUnitDetails(index);
            },

            syncUnitDetails(index) {
                const item = this.items[index];
                const qty = Math.max(1, parseInt(item.banyaknya) || 1);
                item.banyaknya = qty;

                if (!Array.isArray(item.unit_details)) {
                    item.unit_details = [];
                }

                while (item.unit_details.length < qty) {
                    item.unit_details.push({ type_ac: '', pk: '' });
                }

                if (item.unit_details.length > qty) {
                    item.unit_details = item.unit_details.slice(0, qty);
                }
            },

            init() {
                this.items.forEach((_, index) => this.syncUnitDetails(index));

                const found = this.rsOptions.find(o => o.value === this.rsSelected);
                if (found) {
                    this.rsSelectedText = found.text;
                    this.availableRuangans = ruanganData[found.value] || [];
                    this.setAcOptions(found.value);
                    if (!this.mengetahuiInput) {
                        this.mengetahuiInput = mengetahuiData[found.value] || '';
                    }
                    if (this.departemenSelected && !this.availableRuangans.includes(this.departemenSelected)) {
                        this.departemenSelected = '';
                    }
                } else {
                    this.setAcOptions(this.rsSelected);
                }
            }
        }
    }
</script>
@endsection
