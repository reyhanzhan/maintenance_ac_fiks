<?php

namespace App\Http\Controllers;

use App\Models\RumahSakit;
use App\Models\ServiceReport;
use App\Models\ServiceReportItem;
use App\Models\ServiceReportPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function index()
    {
        $reports = ServiceReport::with(['rumahSakit', 'ruangan'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('teknisi.index', compact('reports'));
    }

    public function create()
    {
        $rumahSakits = RumahSakit::with('ruangans')->get();
        $pemeriksaans = self::PEMERIKSAAN_LIST;
        $pemeriksaansSiloamBaru = self::PEMERIKSAAN_SILOAM_BARU;
        return view('teknisi.create', compact('rumahSakits', 'pemeriksaans', 'pemeriksaansSiloamBaru'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rumah_sakit_id' => 'required|exists:rumah_sakits,id',
            'ruangan_id' => 'required|exists:ruangans,id',
            'gedung' => 'nullable|string|in:Baru,Lama',
            'merk_ac' => 'required|string|max:255',
            'type_ac' => 'required|string|max:255',
            'tanggal_service' => 'required|date',
            'saran' => 'nullable|string',
            'nama_penerima' => 'nullable|string|max:255',
            'items' => 'required|array',
            'items.*.is_normal' => 'required|boolean',
            'items.*.keterangan' => 'nullable|string',
            'item_photos.*' => 'nullable|array',
            'item_photos.*.*' => 'nullable|image|max:5120',
            'general_photos' => 'nullable|array',
            'general_photos.*' => 'nullable|image|max:5120',
        ]);

        $rs = RumahSakit::find($request->rumah_sakit_id);
        $isSiloamBaru = str_contains(strtolower($rs->nama), 'siloam') && $request->gedung === 'Baru';

        $report = ServiceReport::create([
            'user_id' => Auth::id(),
            'rumah_sakit_id' => $request->rumah_sakit_id,
            'ruangan_id' => $request->ruangan_id,
            'gedung' => $request->gedung,
            'merk_ac' => $request->merk_ac,
            'type_ac' => $request->type_ac,
            'tanggal_service' => $request->tanggal_service,
            'saran' => $request->saran,
            'nama_penerima' => $request->nama_penerima,
        ]);

        $pemeriksaanList = $isSiloamBaru ? self::PEMERIKSAAN_SILOAM_BARU : self::PEMERIKSAAN_LIST;

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

            if ($request->hasFile("item_photos.{$nomor}")) {
                foreach ($request->file("item_photos.{$nomor}") as $photo) {
                    $path = $photo->store('service-photos/' . $report->id, 'public');
                    $this->mirrorPublicStorageFile($path);

                    ServiceReportPhoto::create([
                        'service_report_id' => $report->id,
                        'service_report_item_id' => $item->id,
                        'photo_path' => $path,
                        'tipe' => 'tidak_normal',
                    ]);
                }
            }
        }

        if ($request->hasFile('general_photos')) {
            foreach ($request->file('general_photos') as $photo) {
                $path = $photo->store('service-photos/' . $report->id, 'public');
                $this->mirrorPublicStorageFile($path);

                ServiceReportPhoto::create([
                    'service_report_id' => $report->id,
                    'photo_path' => $path,
                    'tipe' => 'general',
                ]);
            }
        }

        return redirect('/teknisi')->with('success', 'Service report berhasil disimpan.');
    }

    public function show(ServiceReport $report)
    {
        if ($report->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $report->load(['rumahSakit', 'ruangan', 'items.photos', 'generalPhotos', 'user']);
        return view('teknisi.show', compact('report'));
    }

    public function getRuangan(RumahSakit $rumahSakit)
    {
        return response()->json($rumahSakit->ruangans);
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
