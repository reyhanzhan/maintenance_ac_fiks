@extends('layouts.app')

@section('title', 'Teknisi')
@section('page-title', 'Manajemen Teknisi')

@section('styles')
<style>
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear {
        display: none;
    }
</style>
@endsection

@section('content')
<div x-data="{ showAdd: false, editId: null }">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <form method="GET" class="flex-1 max-w-sm">
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari teknisi..."
                    class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
        </form>
        <button @click="showAdd = !showAdd" class="inline-flex items-center gap-2 bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-xl text-sm font-medium transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Teknisi
        </button>
    </div>

    {{-- Add Form --}}
    <div x-show="showAdd" x-cloak x-transition class="bg-white rounded-2xl border border-gray-200 overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Tambah Teknisi Baru</h3>
            <button @click="showAdd = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="p-5">
            <form action="/admin/teknisi" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" class="w-full h-10 rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Nama lengkap" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Username</label>
                    <input type="text" name="username" class="w-full h-10 rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Username login" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Password</label>
                    <div class="flex gap-2 items-stretch">
                        <div class="relative flex-1">
                            <input id="add-password" type="password" name="password" class="w-full h-10 rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500 pr-10" placeholder="Min 3 karakter" required minlength="3">
                            <button type="button" data-toggle-password="add-password" class="absolute inset-y-0 right-3 my-auto w-4 h-4 p-0 text-gray-400 hover:text-gray-600 transition flex items-center justify-center leading-none" aria-label="Tampilkan password" aria-pressed="false">
                                <svg data-eye-open class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg data-eye-off class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.27-2.943-9.542-7a9.961 9.961 0 012.042-3.368M9.88 9.88A3 3 0 0114.12 14.12"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.228 6.228A9.956 9.956 0 0112 5c4.478 0 8.27 2.943 9.542 7a9.97 9.97 0 01-4.347 5.162M3 3l18 18"/>
                                </svg>
                            </button>
                        </div>
                        <button class="h-10 bg-primary-500 hover:bg-primary-600 text-white px-6 rounded-lg text-sm font-medium transition whitespace-nowrap">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                        <th class="px-5 py-3 text-left">Teknisi</th>
                        <th class="px-5 py-3 text-left">Username</th>
                        <th class="px-5 py-3 text-left">Tanda Tangan</th>
                        <th class="px-5 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($teknisis as $teknisi)
                    <tr class="hover:bg-gray-50/50 transition">
                        {{-- View Mode --}}
                        <td class="px-5 py-3" x-show="editId !== {{ $teknisi->id }}">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0">
                                    {{ strtoupper(substr($teknisi->name, 0, 1)) }}
                                </div>
                                <span class="font-medium text-gray-900">{{ $teknisi->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3" x-show="editId !== {{ $teknisi->id }}">
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded-md text-xs font-medium">{{ $teknisi->username }}</span>
                        </td>
                        <td class="px-5 py-3" x-show="editId !== {{ $teknisi->id }}">
                            @if($teknisi->signature_path)
                                <img src="{{ asset('storage/' . $teknisi->signature_path) }}" class="h-10 object-contain">
                            @else
                                <span class="text-gray-400 text-xs italic">Belum ada</span>
                            @endif
                        </td>
                        <td class="px-5 py-3" x-show="editId !== {{ $teknisi->id }}">
                            <div class="flex items-center justify-center gap-1">
                                <button @click="editId = {{ $teknisi->id }}" class="p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form action="/admin/teknisi/{{ $teknisi->id }}/signature" method="POST" enctype="multipart/form-data" class="inline-flex items-center">
                                    @csrf
                                    <label class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition cursor-pointer" title="Upload TTD">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        <input type="file" name="signature" class="hidden" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" onchange="this.closest('form').submit()" required>
                                    </label>
                                </form>
                                <form action="/admin/teknisi/{{ $teknisi->id }}" method="POST" onsubmit="return confirm('Hapus teknisi {{ $teknisi->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>

                        {{-- Edit Mode --}}
                        <td colspan="4" class="px-5 py-3" x-show="editId === {{ $teknisi->id }}" x-cloak>
                            <form action="/admin/teknisi/{{ $teknisi->id }}" method="POST" class="flex flex-wrap items-end gap-3">
                                @csrf @method('PUT')
                                <div class="flex-1 min-w-[140px]">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Nama</label>
                                    <input type="text" name="name" value="{{ $teknisi->name }}" class="w-full h-10 rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                                </div>
                                <div class="flex-1 min-w-[120px]">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Username</label>
                                    <input type="text" name="username" value="{{ $teknisi->username }}" class="w-full h-10 rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500" required>
                                </div>
                                <div class="flex-1 min-w-[120px]">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Password <span class="text-gray-400">(kosongkan jika tidak diubah)</span></label>
                                    <div class="relative">
                                        <input id="edit-password-{{ $teknisi->id }}" type="password" name="password" class="w-full h-10 rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500 pr-10" placeholder="Biarkan kosong">
                                        <button type="button" data-toggle-password="edit-password-{{ $teknisi->id }}" class="absolute inset-y-0 right-3 my-auto w-4 h-4 p-0 text-gray-400 hover:text-gray-600 transition flex items-center justify-center leading-none" aria-label="Tampilkan password" aria-pressed="false">
                                            <svg data-eye-open class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <svg data-eye-off class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.27-2.943-9.542-7a9.961 9.961 0 012.042-3.368M9.88 9.88A3 3 0 0114.12 14.12"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.228 6.228A9.956 9.956 0 0112 5c4.478 0 8.27 2.943 9.542 7a9.97 9.97 0 01-4.347 5.162M3 3l18 18"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit" class="h-10 px-4 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm font-medium transition">Simpan</button>
                                    <button type="button" @click="editId = null" class="h-10 px-4 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-sm font-medium transition">Batal</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400">Belum ada data teknisi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('click', function (event) {
        const toggleBtn = event.target.closest('[data-toggle-password]');
        if (!toggleBtn) {
            return;
        }

        const input = document.getElementById(toggleBtn.getAttribute('data-toggle-password'));
        if (!input) {
            return;
        }

        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';

        const eyeOpen = toggleBtn.querySelector('[data-eye-open]');
        const eyeOff = toggleBtn.querySelector('[data-eye-off]');
        if (eyeOpen && eyeOff) {
            eyeOpen.classList.toggle('hidden', isHidden);
            eyeOff.classList.toggle('hidden', !isHidden);
        }

        toggleBtn.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
        toggleBtn.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
    });
</script>
@endsection
