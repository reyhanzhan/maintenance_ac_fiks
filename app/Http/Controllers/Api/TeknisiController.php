<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RumahSakit;
use App\Models\Ruangan;
use App\Models\ServiceReport;
use App\Models\ServiceReportItem;
use App\Models\ServiceReportPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TeknisiController extends Controller
{
    private const PEMERIKSAAN_LIST = [
        1 => 'Cleaning air filter Indoor',
        2 => 'Cleaning indoor unit',
        3 => 'Cek fungsi fan Indoor',
        4 => 'Cek kontrol elektronik',
        5 => 'Cek fungsi remot kontrol',
        6 => 'Cek Saluran Pembuangan',
        7 => 'Cleaning outdoor unit',
        8 => 'Cek Fungsi Kapasitor fan outdoor',
        9 => 'Cek Fungsi Kontaktor',
        10 => 'Cek Fungsi Kompresor',
        11 => 'Cek Fungsi Fan Outdoor',
        12 => 'Cek Ampere',
        13 => 'Cek Tekanan Freon',
        14 => 'Cek Sistem Elektronik',
        15 => 'Cek Tegangan Listrik',
        16 => 'Cek Kebocoran',
    ];

    private const PEMERIKSAAN_SILOAM_BARU = [
        1 => ['nama' => 'Periksa Blower', 'desc' => 'Normal Condition: Sirip Blower bersih dan bearing harus ada pelumas, Ruangan blower harus bersih dan tidak bocor'],
        2 => ['nama' => 'Periksa kekancangan V-Belt', 'desc' => 'Normal Condition: Apabila ditekan V-Belt dengan tangan/ibu jari, lingkaran luar atas dan bawah tidak saling berbenturan pada saat operasi'],
        3 => ['nama' => 'Periksa Motorize Valve (Inlet, Outlet, Check Valve, Thermostat)', 'desc' => ''],
        4 => ['nama' => 'Periksa Flexible connection', 'desc' => 'Normal Condition: Kencang dan tidak bocor'],
        5 => ['nama' => 'Pengukuran Ampere, Suhu Air dan Tekanan Air', 'desc' => 'Tulis Nilai Ampere, Suhu Air dan Tekanan Air'],
        6 => ['nama' => 'Cek Kondisi insulasi', 'desc' => 'Normal Condition: Tidak terdapat retakan, sobekan dan tidak berkeringat'],
        7 => ['nama' => 'Periksa Spring Mountain', 'desc' => 'Normal Condition: Kencang, tidak korosi dan rata'],
        8 => ['nama' => 'Periksa bodi unit dari kerusakan ataupun karat', 'desc' => ''],
        9 => ['nama' => 'Cek Distribusi Supply udara pada Diffuser dan Return', 'desc' => 'Normal Condition: Semua diffuser dan return berfungsi normal'],
        10 => ['nama' => 'Test equipment used', 'desc' => ''],
    ];

    public function sync()
    {
        return response()->json([
            'rumah_sakits' => RumahSakit::with(['ruangans', 'acUnits'])->orderBy('nama')->get()->map(fn (RumahSakit $rumahSakit) => [
                'id' => $rumahSakit->id,
                'nama' => $rumahSakit->nama,
                'alamat' => $rumahSakit->alamat,
                'koordinator_lapangan' => $rumahSakit->koordinator_lapangan,
                'mengetahui_surat_jalan' => $rumahSakit->mengetahui_surat_jalan,
                'ruangans' => $rumahSakit->ruangans->map(fn ($ruangan) => [
                    'id' => $ruangan->id,
                    'rumah_sakit_id' => $ruangan->rumah_sakit_id,
                    'nama' => $ruangan->nama,
                ])->values(),
                'ac_units' => $rumahSakit->acUnits->map(fn ($acUnit) => [
                    'id' => $acUnit->id,
                    'rumah_sakit_id' => $acUnit->rumah_sakit_id,
                    'gedung' => $acUnit->gedung,
                    'jenis_ac' => $acUnit->jenis_ac,
                    'merk_ac' => $acUnit->merk_ac,
                    'kapasitas_pk' => $acUnit->kapasitas_pk,
                    'ruangan' => $acUnit->ruangan,
                    'lantai' => $acUnit->lantai,
                    'frekuensi_cuci' => $acUnit->frekuensi_cuci,
                ])->values(),
            ])->values(),
            'pemeriksaan_default' => $this->inspectionPayload(self::PEMERIKSAAN_LIST),
            'pemeriksaan_siloam_baru' => $this->inspectionPayload(self::PEMERIKSAAN_SILOAM_BARU),
        ]);
    }

    public function getRuangan(RumahSakit $rumahSakit)
    {
        return response()->json($rumahSakit->ruangans->map(fn (Ruangan $ruangan) => [
            'id' => $ruangan->id,
            'rumah_sakit_id' => $ruangan->rumah_sakit_id,
            'nama' => $ruangan->nama,
        ])->values());
    }

    public function reports(Request $request)
    {
        $reports = ServiceReport::with(['rumahSakit', 'ruangan', 'items.photos', 'generalPhotos'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate($request->integer('per_page', 10));

        return response()->json($reports->through(fn (ServiceReport $report) => $this->reportPayload($report)));
    }

    public function storeReport(Request $request)
    {
        $validated = $request->validate([
            'rumah_sakit_id' => ['required', 'exists:rumah_sakits,id'],
            'ruangan_id' => [
                'required',
                Rule::exists('ruangans', 'id')->where('rumah_sakit_id', $request->input('rumah_sakit_id')),
            ],
            'gedung' => ['nullable', 'string', Rule::in(['Baru', 'Lama'])],
            'merk_ac' => ['required', 'string', 'max:255'],
            'type_ac' => ['required', 'string', 'max:255'],
            'tanggal_service' => ['required', 'date'],
            'saran' => ['nullable', 'string'],
            'nama_penerima' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array'],
            'items.*.is_normal' => ['required', 'boolean'],
            'items.*.keterangan' => ['nullable', 'string'],
            'item_photos' => ['nullable', 'array'],
            'item_photos.*' => ['nullable', 'array'],
            'item_photos.*.*' => ['nullable', 'image', 'max:5120'],
            'general_photos' => ['nullable', 'array'],
            'general_photos.*' => ['nullable', 'image', 'max:5120'],
        ]);

        $report = DB::transaction(function () use ($request, $validated) {
            $rumahSakit = RumahSakit::findOrFail($validated['rumah_sakit_id']);
            $isSiloamBaru = str_contains(strtolower($rumahSakit->nama), 'siloam') && ($validated['gedung'] ?? null) === 'Baru';
            $pemeriksaanList = $isSiloamBaru ? self::PEMERIKSAAN_SILOAM_BARU : self::PEMERIKSAAN_LIST;

            $report = ServiceReport::create([
                'user_id' => $request->user()->id,
                'rumah_sakit_id' => $validated['rumah_sakit_id'],
                'ruangan_id' => $validated['ruangan_id'],
                'gedung' => $validated['gedung'] ?? null,
                'merk_ac' => $validated['merk_ac'],
                'type_ac' => $validated['type_ac'],
                'tanggal_service' => $validated['tanggal_service'],
                'saran' => $validated['saran'] ?? null,
                'nama_penerima' => $validated['nama_penerima'] ?? null,
            ]);

            foreach ($pemeriksaanList as $nomor => $entry) {
                $nama = is_array($entry) ? $entry['nama'] : $entry;
                $itemData = $request->input("items.{$nomor}", []);

                $item = ServiceReportItem::create([
                    'service_report_id' => $report->id,
                    'nomor' => $nomor,
                    'nama_pemeriksaan' => $nama,
                    'is_normal' => $itemData['is_normal'] ?? true,
                    'keterangan' => $itemData['keterangan'] ?? null,
                ]);

                foreach ($request->file("item_photos.{$nomor}", []) as $photo) {
                    $this->storePhoto($report->id, $photo, 'tidak_normal', $item->id);
                }
            }

            foreach ($request->file('general_photos', []) as $photo) {
                $this->storePhoto($report->id, $photo, 'general');
            }

            return $report->load(['rumahSakit', 'ruangan', 'items.photos', 'generalPhotos', 'user']);
        });

        return response()->json([
            'message' => 'Service report berhasil disimpan.',
            'report' => $this->reportPayload($report),
        ], 201);
    }

    public function showReport(Request $request, ServiceReport $report)
    {
        if ($report->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        return response()->json([
            'report' => $this->reportPayload($report->load(['rumahSakit', 'ruangan', 'items.photos', 'generalPhotos', 'user'])),
        ]);
    }

    private function storePhoto(int $reportId, $photo, string $type, ?int $itemId = null): ServiceReportPhoto
    {
        $path = $photo->store('service-photos/' . $reportId, 'public');
        $this->mirrorPublicStorageFile($path);

        return ServiceReportPhoto::create([
            'service_report_id' => $reportId,
            'service_report_item_id' => $itemId,
            'photo_path' => $path,
            'tipe' => $type,
        ]);
    }

    private function reportPayload(ServiceReport $report): array
    {
        return [
            'id' => $report->id,
            'user_id' => $report->user_id,
            'rumah_sakit' => [
                'id' => $report->rumahSakit?->id,
                'nama' => $report->rumahSakit?->nama,
            ],
            'ruangan' => [
                'id' => $report->ruangan?->id,
                'nama' => $report->ruangan?->nama,
            ],
            'gedung' => $report->gedung,
            'merk_ac' => $report->merk_ac,
            'type_ac' => $report->type_ac,
            'tanggal_service' => $report->tanggal_service?->toDateString(),
            'saran' => $report->saran,
            'nama_penerima' => $report->nama_penerima,
            'items' => $report->items->map(fn (ServiceReportItem $item) => [
                'id' => $item->id,
                'nomor' => $item->nomor,
                'nama_pemeriksaan' => $item->nama_pemeriksaan,
                'is_normal' => $item->is_normal,
                'keterangan' => $item->keterangan,
                'photos' => $item->photos->map(fn (ServiceReportPhoto $photo) => $this->photoPayload($photo))->values(),
            ])->values(),
            'general_photos' => $report->generalPhotos->map(fn (ServiceReportPhoto $photo) => $this->photoPayload($photo))->values(),
            'created_at' => $report->created_at?->toISOString(),
            'updated_at' => $report->updated_at?->toISOString(),
        ];
    }

    private function photoPayload(ServiceReportPhoto $photo): array
    {
        return [
            'id' => $photo->id,
            'service_report_item_id' => $photo->service_report_item_id,
            'tipe' => $photo->tipe,
            'photo_path' => $photo->photo_path,
            'photo_url' => asset('storage/' . $photo->photo_path),
        ];
    }

    private function inspectionPayload(array $items): array
    {
        return collect($items)->map(fn ($entry, int $nomor) => [
            'nomor' => $nomor,
            'nama' => is_array($entry) ? $entry['nama'] : $entry,
            'desc' => is_array($entry) ? $entry['desc'] : null,
        ])->values()->all();
    }

    private function mirrorPublicStorageFile(string $path): void
    {
        $publicStorageRoot = public_path('storage');
        if (!is_dir($publicStorageRoot) || is_link($publicStorageRoot)) {
            return;
        }

        $source = storage_path('app/public/' . $path);
        $destination = public_path('storage/' . $path);
        $destinationDir = dirname($destination);

        if (!is_dir($destinationDir)) {
            @mkdir($destinationDir, 0755, true);
        }

        if (is_file($source)) {
            @copy($source, $destination);
        }
    }
}
