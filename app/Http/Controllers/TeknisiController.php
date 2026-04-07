<?php

namespace App\Http\Controllers;

use App\Models\RumahSakit;
use App\Models\Ruangan;
use App\Models\ServiceReport;
use App\Models\ServiceReportItem;
use App\Models\ServiceReportPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        return view('teknisi.create', compact('rumahSakits', 'pemeriksaans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rumah_sakit_id' => 'required|exists:rumah_sakits,id',
            'ruangan_id' => 'required|exists:ruangans,id',
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

        $report = ServiceReport::create([
            'user_id' => Auth::id(),
            'rumah_sakit_id' => $request->rumah_sakit_id,
            'ruangan_id' => $request->ruangan_id,
            'merk_ac' => $request->merk_ac,
            'type_ac' => $request->type_ac,
            'tanggal_service' => $request->tanggal_service,
            'saran' => $request->saran,
            'nama_penerima' => $request->nama_penerima,
        ]);

        foreach (self::PEMERIKSAAN_LIST as $nomor => $nama) {
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
}
