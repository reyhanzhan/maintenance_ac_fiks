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
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Surat</label>
                <input type="text" name="nomor" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" value="{{ old('nomor') }}" placeholder="Opsional">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" value="{{ old('tanggal', date('Y-m-d')) }}" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rumah Sakit <span class="text-red-500">*</span></label>
                <div @click.away="rsOpen = false" class="relative">
                    <input type="hidden" name="rumah_sakit_id" :value="rsSelected" required>
                    <button type="button" @click="rsOpen = !rsOpen" class="w-full bg-white border border-gray-300 text-left rounded-lg px-3 py-2 text-sm flex items-center justify-between focus:ring-primary-500 focus:border-primary-500 transition">
                        <span x-text="rsSelectedText || '-- Pilih --'" :class="rsSelectedText ? 'text-gray-900' : 'text-gray-400'"></span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="rsOpen" x-cloak x-transition class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
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
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Pekerjaan</label>
                <input type="text" name="deskripsi_pekerjaan" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" value="{{ old('deskripsi_pekerjaan', 'Servis rutin ac split 2pk indor-outdoor') }}" placeholder="Contoh: Servis rutin ac split 2pk indor-outdoor">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Penerima</label>
                <input type="text" name="penerima" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" value="{{ old('penerima') }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mengetahui</label>
                <input type="text" name="mengetahui" x-model="mengetahuiInput" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" value="{{ old('mengetahui') }}">
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
                <div class="flex gap-3 items-start">
                    <div class="w-24">
                        <label x-show="index === 0" class="block text-xs text-gray-500 mb-1">Banyaknya</label>
                        <input type="number" x-model.number="item.banyaknya" :name="'items['+index+'][banyaknya]'" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" min="1" required>
                    </div>
                    <div class="flex-1">
                        <label x-show="index === 0" class="block text-xs text-gray-500 mb-1">Nama Ruangan</label>
                        <div class="flex gap-2">
                            <input type="text" x-model="item.nama_ruangan" :name="'items['+index+'][nama_ruangan]'" class="flex-1 rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Ketik atau pilih ruangan" required :list="'ruangan-options'">
                            <button type="button" @click="removeItem(index)" class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" x-show="items.length > 1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
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

    {{-- Catatan --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Catatan</h3>
        </div>
        <div class="p-5">
            <textarea name="catatan" rows="3" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Tambahkan catatan jika ada...">{{ old('catatan') }}</textarea>
        </div>
    </div>

    <button type="submit" class="w-full bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-semibold py-3 rounded-xl shadow-lg shadow-primary-500/25 transition">
        Simpan Surat Jalan
    </button>
</form>
@endsection

@section('scripts')
<script>
    const ruanganData = @json(
        \App\Models\RumahSakit::with('ruangans')->get()->mapWithKeys(function($rs) {
            return [$rs->id => $rs->ruangans->pluck('nama')->toArray()];
        })
    );

    const mengetahuiData = @json(
        \App\Models\RumahSakit::pluck('mengetahui_surat_jalan', 'id')
    );

    function suratJalanForm() {
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
            items: [{ banyaknya: 1, nama_ruangan: '' }],
            availableRuangans: [],

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

            chooseRs(opt) {
                this.rsSelected = opt.value;
                this.rsSelectedText = opt.text;
                this.rsOpen = false;
                this.rsSearch = '';
                this.availableRuangans = ruanganData[opt.value] || [];
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
                this.items.push({ banyaknya: 1, nama_ruangan: '' });
            },

            removeItem(index) {
                if (this.items.length > 1) this.items.splice(index, 1);
            },

            init() {
                const found = this.rsOptions.find(o => o.value === this.rsSelected);
                if (found) {
                    this.rsSelectedText = found.text;
                    this.availableRuangans = ruanganData[found.value] || [];
                    if (!this.mengetahuiInput) {
                        this.mengetahuiInput = mengetahuiData[found.value] || '';
                    }
                    if (this.departemenSelected && !this.availableRuangans.includes(this.departemenSelected)) {
                        this.departemenSelected = '';
                    }
                }
            }
        }
    }
</script>
@endsection
