<?php

namespace App\Http\Controllers;

use App\Models\AcUnit;
use App\Models\RumahSakit;
use App\Models\Ruangan;
use App\Models\ServiceReport;
use App\Models\SuratJalan;
use App\Models\SuratJalanItem;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();

        // Summary counts
        $totalReports = ServiceReport::count();
        $totalSuratJalan = SuratJalan::count();
        $totalAcUnits = AcUnit::count();
        $totalRS = RumahSakit::count();
        $totalTeknisi = User::where('role', 'teknisi')->count();

        // This month counts
        $reportsThisMonth = ServiceReport::where('tanggal_service', '>=', $startOfMonth)->count();
        $suratJalanThisMonth = SuratJalan::where('tanggal', '>=', $startOfMonth)->count();

        // Monthly reports for last 6 months (for chart)
        $monthlyReports = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $count = ServiceReport::whereYear('tanggal_service', $month->year)
                ->whereMonth('tanggal_service', $month->month)
                ->count();
            $monthlyReports->push([
                'label' => $month->locale('id')->translatedFormat('M Y'),
                'count' => $count,
            ]);
        }
        $maxMonthly = $monthlyReports->max('count') ?: 1;

        // Reports per Rumah Sakit
        $reportsByRS = RumahSakit::withCount(['serviceReports', 'acUnits'])
            ->having('service_reports_count', '>', 0)
            ->orderByDesc('service_reports_count')
            ->get()
            ->map(function ($rs) {
                $rs->last_service = ServiceReport::where('rumah_sakit_id', $rs->id)
                    ->max('tanggal_service');
                return $rs;
            });

        // Teknisi performance
        $teknisiPerformance = User::where('role', 'teknisi')
            ->withCount(['serviceReports as total_reports'])
            ->withCount(['serviceReports as reports_this_month' => function ($q) use ($startOfMonth) {
                $q->where('tanggal_service', '>=', $startOfMonth);
            }])
            ->orderByDesc('total_reports')
            ->get();

        // Recent reports (5 terbaru)
        $recentReports = ServiceReport::with(['user', 'rumahSakit', 'ruangan'])
            ->latest('tanggal_service')
            ->take(5)
            ->get();

        return view('admin.index', compact(
            'totalReports', 'totalSuratJalan', 'totalAcUnits', 'totalRS', 'totalTeknisi',
            'reportsThisMonth', 'suratJalanThisMonth',
            'monthlyReports', 'maxMonthly',
            'reportsByRS', 'teknisiPerformance', 'recentReports'
        ));
    }

    public function reports(Request $request)
    {
        $query = ServiceReport::with(['user', 'rumahSakit', 'ruangan']);
        $sort = strtolower((string) $request->get('sort', 'desc'));
        $perPage = (int) $request->get('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50], true)) {
            $perPage = 15;
        }
        if (!in_array($sort, ['asc', 'desc'], true)) {
            $sort = 'desc';
        }

        if ($request->filled('rumah_sakit_id')) {
            $query->where('rumah_sakit_id', $request->rumah_sakit_id);
        }
        if ($request->filled('ruangan_id')) {
            $query->where('ruangan_id', $request->ruangan_id);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_service', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_service', '<=', $request->tanggal_sampai);
        }

        $reports = $query->orderBy('tanggal_service', $sort)->orderBy('id', $sort)->paginate($perPage)->withQueryString();
        $rumahSakits = RumahSakit::all();
        $teknisis = User::where('role', 'teknisi')->get();

        return view('admin.reports', compact('reports', 'rumahSakits', 'teknisis'));
    }

    public function showReport(ServiceReport $report)
    {
        $report->load(['rumahSakit', 'ruangan', 'items.photos', 'generalPhotos', 'user']);
        return view('teknisi.show', compact('report'));
    }

    public function exportReportPdf(ServiceReport $report, Request $request)
    {
        $report->load(['rumahSakit', 'ruangan', 'items.photos', 'generalPhotos', 'user']);
        $layout = $request->get('layout', 'intan');

        $pdf = Pdf::loadView('pdf.service-report', compact('report', 'layout'));
        $pdf->setPaper('A4', 'portrait');

        $filename = 'service-report-' . $report->id . '-' . $report->tanggal_service->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    // Surat Jalan
    public function suratJalanIndex(Request $request)
    {
        $query = SuratJalan::with(['rumahSakit', 'items']);
        $sort = strtolower((string) $request->get('sort', 'desc'));
        $perPage = (int) $request->get('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50], true)) {
            $perPage = 15;
        }
        if (!in_array($sort, ['asc', 'desc'], true)) {
            $sort = 'desc';
        }

        if ($request->filled('rumah_sakit_id')) {
            $query->where('rumah_sakit_id', $request->rumah_sakit_id);
        }
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        $suratJalans = $query->orderBy('tanggal', $sort)->orderBy('id', $sort)->paginate($perPage)->withQueryString();
        $rumahSakits = RumahSakit::orderBy('nama')->get();

        return view('admin.surat-jalan.index', compact('suratJalans', 'rumahSakits'));
    }

    public function suratJalanCreate()
    {
        $rumahSakits = RumahSakit::all();
        return view('admin.surat-jalan.create', compact('rumahSakits'));
    }

    public function suratJalanStore(Request $request)
    {
        $request->validate([
            'rumah_sakit_id' => 'required|exists:rumah_sakits,id',
            'departemen' => 'nullable|string|max:255',
            'tanggal' => 'required|date',
            'penerima' => 'nullable|string|max:255',
            'mengetahui' => 'nullable|string|max:255',
            'deskripsi_pekerjaan' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.banyaknya' => 'required|integer|min:1',
            'items.*.nama_ruangan' => 'required|string',
            'items.*.type_ac' => 'nullable|string|max:255',
            'items.*.pk' => 'nullable|string|max:255',
            'items.*.unit_details' => 'nullable|array',
            'items.*.unit_details.*.type_ac' => 'nullable|string|max:100',
            'items.*.unit_details.*.pk' => 'nullable|string|max:50',
        ]);

        $suratJalan = SuratJalan::create([
            'nomor' => $request->nomor,
            'rumah_sakit_id' => $request->rumah_sakit_id,
            'departemen' => $request->departemen,
            'deskripsi_pekerjaan' => $request->deskripsi_pekerjaan,
            'tanggal' => $request->tanggal,
            'penerima' => $request->penerima,
            'mengetahui' => $request->mengetahui,
            'catatan' => $request->catatan,
        ]);

        foreach ($request->items as $item) {
            $qty = max(1, (int) ($item['banyaknya'] ?? 1));
            $unitDetails = collect($item['unit_details'] ?? [])->take($qty)->values();
            $unitSummary = $this->summarizeUnitDetails($unitDetails->all());

            SuratJalanItem::create([
                'surat_jalan_id' => $suratJalan->id,
                'banyaknya' => $qty,
                'nama_ruangan' => $item['nama_ruangan'],
                // Keep legacy columns while storing paired AC detail summary for PDF/readability.
                'type_ac' => $unitSummary ?: ($item['type_ac'] ?? null),
                'pk' => $unitSummary ? null : ($item['pk'] ?? null),
            ]);
        }

        return redirect('/admin/surat-jalan')->with('success', 'Surat jalan berhasil dibuat.');
    }

    private function summarizeUnitDetails(array $details): ?string
    {
        $cleanValues = collect($details)
            ->map(function ($detail) {
                $type = is_array($detail) ? trim((string) ($detail['type_ac'] ?? '')) : '';
                $pk = is_array($detail) ? trim((string) ($detail['pk'] ?? '')) : '';

                if ($type && $pk) {
                    return "{$type} {$pk}";
                }

                return $type ?: $pk;
            })
            ->filter()
            ->values();

        if ($cleanValues->isEmpty()) {
            return null;
        }

        return $cleanValues->implode(', ');
    }

    public function suratJalanShow(SuratJalan $suratJalan)
    {
        $suratJalan->load(['rumahSakit', 'items']);
        return view('admin.surat-jalan.show', compact('suratJalan'));
    }

    public function suratJalanExportPdf(SuratJalan $suratJalan)
    {
        $suratJalan->load(['rumahSakit', 'items']);

        $pdf = Pdf::loadView('pdf.surat-jalan', compact('suratJalan'));
        $pdf->setPaper('A4', 'portrait');

        $filename = 'surat-jalan-' . ($suratJalan->nomor ?: $suratJalan->id) . '.pdf';
        return $pdf->download($filename);
    }

    public function backupIndex()
    {
        return view('admin.backup.index');
    }

    public function backupDownload(Request $request)
    {
        $mode = $request->input('mode', 'months');
        $months = null;

        if ($mode === 'range') {
            $request->validate([
                'tanggal_dari'   => 'required|date',
                'tanggal_sampai' => 'required|date|after_or_equal:tanggal_dari',
            ]);
            $startDate = Carbon::parse($request->tanggal_dari)->startOfDay();
            $endDate   = Carbon::parse($request->tanggal_sampai)->endOfDay();
            $periodeLabel = $startDate->format('d-m-Y') . '_sd_' . $endDate->format('d-m-Y');
        } else {
            $request->validate([
                'months' => 'required|integer|min:1|max:36',
            ]);
            $months    = (int) $request->months;
            $endDate   = Carbon::now()->endOfDay();
            $startDate = Carbon::now()->startOfMonth()->subMonths($months - 1)->startOfDay();
            $periodeLabel = "{$months}bulan";
        }

        $timestamp = Carbon::now()->format('Ymd-His');
        $fileName  = "backup-maintenance-ac-{$periodeLabel}-{$timestamp}.zip";

        $reports = ServiceReport::with(['rumahSakit', 'ruangan', 'user', 'items', 'photos'])
            ->whereBetween('tanggal_service', [$startDate, $endDate])
            ->orderBy('tanggal_service')
            ->get();

        $suratJalans = SuratJalan::with(['rumahSakit', 'items'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal')
            ->get();

        $photoPaths = $reports
            ->flatMap(function ($report) {
                return $report->photos->pluck('photo_path');
            })
            ->filter()
            ->unique()
            ->values();

        $tempDir = storage_path('app/temp-backup');
        $zipPath = $tempDir . DIRECTORY_SEPARATOR . $fileName;

        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return back()->withErrors(['backup' => 'Gagal membuat file backup.']);
        }

        $metadata = [
            'generated_at' => Carbon::now()->toDateTimeString(),
            'period' => [
                'mode' => $mode,
                'months' => $months,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ],
            'summary' => [
                'service_reports_count' => $reports->count(),
                'surat_jalan_count' => $suratJalans->count(),
                'photos_count' => $photoPaths->count(),
            ],
        ];

        $zip->addFromString('metadata.json', json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $zip->addFromString('data/service_reports.json', json_encode($reports->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $zip->addFromString('data/surat_jalans.json', json_encode($suratJalans->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $missingPhotos = [];
        $addedPhotoPaths = [];

        foreach ($reports as $report) {
            $dateFolder = str_replace(' ', '-', strtolower($report->tanggal_service->locale('id')->translatedFormat('j F Y')));
            $teknisiName = trim((string) optional($report->user)->name);
            $teknisiFolder = Str::of($teknisiName !== '' ? $teknisiName : ('teknisi-' . $report->user_id))
                ->ascii()
                ->lower()
                ->replaceMatches('/[^a-z0-9]+/', '-')
                ->trim('-')
                ->value();

            if ($teknisiFolder === '') {
                $teknisiFolder = 'teknisi-' . $report->user_id;
            }

            foreach ($report->photos as $photo) {
                $photoPath = (string) $photo->photo_path;
                if ($photoPath === '' || in_array($photoPath, $addedPhotoPaths, true)) {
                    continue;
                }

                $absolutePath = storage_path('app/public/' . $photoPath);
                if (File::exists($absolutePath)) {
                    $zipEntryName = 'photos/' . $dateFolder . '/' . $teknisiFolder . '/report-' . $report->id . '-' . basename($photoPath);
                    $zip->addFile($absolutePath, $zipEntryName);
                    $addedPhotoPaths[] = $photoPath;
                } else {
                    $missingPhotos[] = [
                        'report_id' => $report->id,
                        'tanggal_service' => optional($report->tanggal_service)->toDateString(),
                        'photo_path' => $photoPath,
                    ];
                }
            }
        }

        if (!empty($missingPhotos)) {
            $zip->addFromString('data/missing_photos.json', json_encode($missingPhotos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        $zip->close();

        return response()->download($zipPath, $fileName)->deleteFileAfterSend(true);
    }

    // Master data - Rumah Sakit
    public function rumahSakitIndex(Request $request)
    {
        $query = RumahSakit::withCount(['ruangans', 'acUnits']);
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }
        $rumahSakits = $query->orderBy('nama')->paginate(15);
        return view('admin.rumah-sakit.index', compact('rumahSakits'));
    }

    public function ruanganIndex(Request $request, RumahSakit $rumahSakit)
    {
        $query = $rumahSakit->ruangans();
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }
        $ruangans = $query->orderBy('nama')->paginate(20);
        return view('admin.rumah-sakit.ruangan', compact('rumahSakit', 'ruangans'));
    }

    public function ruanganUpdate(Request $request, Ruangan $ruangan)
    {
        $request->validate(['nama' => 'required|string|max:255']);
        $ruangan->update($request->only('nama'));
        return back()->with('success', 'Ruangan berhasil diperbarui.');
    }

    // Master data - AC Units
    public function acUnitIndex(Request $request, RumahSakit $rumahSakit)
    {
        $query = $rumahSakit->acUnits();
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('ruangan', 'like', '%' . $request->search . '%')
                  ->orWhere('merk_ac', 'like', '%' . $request->search . '%')
                  ->orWhere('jenis_ac', 'like', '%' . $request->search . '%')
                  ->orWhere('gedung', 'like', '%' . $request->search . '%');
            });
        }
        $acUnits = $query->orderBy('gedung')->orderBy('lantai')->orderBy('ruangan')->paginate(25);
        return view('admin.rumah-sakit.ac-units', compact('rumahSakit', 'acUnits'));
    }

    public function acUnitStore(Request $request, RumahSakit $rumahSakit)
    {
        $request->validate([
            'gedung' => 'nullable|string|max:255',
            'jenis_ac' => 'required|string|max:255',
            'merk_ac' => 'required|string|max:255',
            'kapasitas_pk' => 'required|string|max:255',
            'ruangan' => 'required|string|max:255',
            'lantai' => 'required|string|max:255',
            'frekuensi_cuci' => 'nullable|integer|min:0',
        ]);

        AcUnit::create(array_merge($request->only('gedung', 'jenis_ac', 'merk_ac', 'kapasitas_pk', 'ruangan', 'lantai', 'frekuensi_cuci'), [
            'rumah_sakit_id' => $rumahSakit->id,
        ]));

        // Otomatis daftarkan ruangan ke tabel ruangans jika belum ada
        Ruangan::firstOrCreate(
            ['rumah_sakit_id' => $rumahSakit->id, 'nama' => $request->ruangan]
        );

        return back()->with('success', 'AC Unit berhasil ditambahkan.');
    }

    public function acUnitUpdate(Request $request, AcUnit $acUnit)
    {
        $request->validate([
            'gedung' => 'nullable|string|max:255',
            'jenis_ac' => 'required|string|max:255',
            'merk_ac' => 'required|string|max:255',
            'kapasitas_pk' => 'required|string|max:255',
            'ruangan' => 'required|string|max:255',
            'lantai' => 'required|string|max:255',
            'frekuensi_cuci' => 'nullable|integer|min:0',
        ]);

        $oldRuangan = $acUnit->ruangan;
        $acUnit->update($request->only('gedung', 'jenis_ac', 'merk_ac', 'kapasitas_pk', 'ruangan', 'lantai', 'frekuensi_cuci'));

        // Otomatis daftarkan ruangan baru ke tabel ruangans jika belum ada
        if ($request->ruangan !== $oldRuangan) {
            Ruangan::firstOrCreate(
                ['rumah_sakit_id' => $acUnit->rumah_sakit_id, 'nama' => $request->ruangan]
            );
        }

        return back()->with('success', 'AC Unit berhasil diperbarui.');
    }

    public function acUnitDestroy(AcUnit $acUnit)
    {
        $acUnit->delete();
        return back()->with('success', 'AC Unit berhasil dihapus.');
    }

    public function acUnitBulkDestroy(Request $request)
    {
        $request->validate([
            'rumah_sakit_id' => 'required|exists:rumah_sakits,id',
            'delete_all' => 'nullable|boolean',
            'ids' => 'nullable|array',
            'ids.*' => 'integer|exists:ac_units,id',
        ]);

        $query = AcUnit::where('rumah_sakit_id', $request->rumah_sakit_id);

        if ($request->boolean('delete_all')) {
            $count = (clone $query)->count();
            $query->delete();

            return back()->with('success', "{$count} Unit AC berhasil dihapus.");
        }

        $ids = collect($request->input('ids', []))->filter()->unique()->values();
        if ($ids->isEmpty()) {
            return back()->withErrors(['ids' => 'Pilih minimal satu Unit AC untuk dihapus.']);
        }

        $count = (clone $query)->whereIn('id', $ids)->delete();

        return back()->with('success', "{$count} Unit AC terpilih berhasil dihapus.");
    }

    public function acUnitTemplate(RumahSakit $rumahSakit)
    {
        $rows = [
            ['Gedung', 'Jenis AC', 'Merk AC', 'Kapasitas', 'Ruangan', 'Lantai', 'Frek. Cuci'],
            ['Gedung A', 'Split', 'Daikin', '2 PK', 'Ruang Operasi', '1', '4'],
            ['Gedung B', 'Cassette', 'Panasonic', '1.5 PK', 'Ruang Rawat 201', '2', '3'],
        ];

        $filename = 'template-import-unit-ac-' . Str::slug($rumahSakit->nama) . '.xlsx';

        return $this->downloadSimpleXlsx($filename, $rows);
    }

    public function acUnitImport(Request $request, RumahSakit $rumahSakit)
    {
        $request->validate([
            'file' => 'required|file|max:5120',
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, ['xlsx', 'csv', 'txt'], true)) {
            return back()->withErrors(['file' => 'File harus berformat .xlsx atau .csv.']);
        }

        try {
            $rows = $extension === 'xlsx'
                ? $this->readXlsxRows($file->getRealPath())
                : $this->readCsvRows($file->getRealPath());
        } catch (\Throwable $e) {
            return back()->withErrors(['file' => 'File tidak bisa dibaca. Pastikan memakai template Excel yang tersedia.']);
        }

        [$records, $errors] = $this->mapAcUnitImportRows($rows);

        if (!empty($errors)) {
            return back()->withErrors($errors);
        }

        DB::transaction(function () use ($records, $rumahSakit) {
            foreach ($records as $record) {
                AcUnit::create(array_merge($record, [
                    'rumah_sakit_id' => $rumahSakit->id,
                ]));

                Ruangan::firstOrCreate([
                    'rumah_sakit_id' => $rumahSakit->id,
                    'nama' => $record['ruangan'],
                ]);
            }
        });

        return back()->with('success', count($records) . ' Unit AC berhasil diimport.');
    }

    public function rumahSakitStore(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
        ]);

        RumahSakit::create($request->only('nama', 'alamat'));
        return back()->with('success', 'Rumah Sakit berhasil ditambahkan.');
    }

    public function rumahSakitUpdate(Request $request, RumahSakit $rumahSakit)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
        ]);

        $rumahSakit->update($request->only('nama', 'alamat'));
        return back()->with('success', 'Rumah Sakit berhasil diperbarui.');
    }

    public function rumahSakitDestroy(RumahSakit $rumahSakit)
    {
        $rumahSakit->delete();
        return back()->with('success', 'Rumah Sakit berhasil dihapus.');
    }

    public function rumahSakitBulkDestroy(Request $request)
    {
        $request->validate([
            'delete_all' => 'nullable|boolean',
            'ids' => 'nullable|array',
            'ids.*' => 'integer|exists:rumah_sakits,id',
        ]);

        if ($request->boolean('delete_all')) {
            $count = RumahSakit::count();
            RumahSakit::query()->delete();

            return back()->with('success', "{$count} Rumah Sakit berhasil dihapus beserta data terkait.");
        }

        $ids = collect($request->input('ids', []))->filter()->unique()->values();
        if ($ids->isEmpty()) {
            return back()->withErrors(['ids' => 'Pilih minimal satu Rumah Sakit untuk dihapus.']);
        }

        $count = RumahSakit::whereIn('id', $ids)->delete();

        return back()->with('success', "{$count} Rumah Sakit terpilih berhasil dihapus beserta data terkait.");
    }

    public function koordinatorRsIndex()
    {
        $rumahSakits = RumahSakit::orderBy('nama')->get();
        return view('admin.koordinator-rs.index', compact('rumahSakits'));
    }

    public function koordinatorRsUpdate(Request $request, RumahSakit $rumahSakit)
    {
        $request->validate([
            'koordinator_lapangan' => 'nullable|string|max:255',
        ]);

        $rumahSakit->update([
            'koordinator_lapangan' => $request->koordinator_lapangan,
        ]);

        return back()->with('success', 'Koordinator lapangan berhasil diperbarui.');
    }

    public function koordinatorSuratJalanIndex()
    {
        $rumahSakits = RumahSakit::orderBy('nama')->get();
        return view('admin.koordinator-surat-jalan.index', compact('rumahSakits'));
    }

    public function koordinatorSuratJalanUpdate(Request $request, RumahSakit $rumahSakit)
    {
        $request->validate([
            'mengetahui_surat_jalan' => 'nullable|string|max:255',
        ]);

        $rumahSakit->update([
            'mengetahui_surat_jalan' => $request->mengetahui_surat_jalan,
        ]);

        return back()->with('success', 'Mengetahui surat jalan berhasil diperbarui.');
    }

    public function ruanganStore(Request $request, RumahSakit $rumahSakit)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        Ruangan::create([
            'rumah_sakit_id' => $rumahSakit->id,
            'nama' => $request->nama,
        ]);

        return back()->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function ruanganDestroy(Ruangan $ruangan)
    {
        $ruangan->delete();
        return back()->with('success', 'Ruangan berhasil dihapus.');
    }

    // Master data - Teknisi
    public function teknisiIndex(Request $request)
    {
        $query = User::where('role', 'teknisi');
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('username', 'like', '%' . $request->search . '%');
            });
        }
        $teknisis = $query->orderBy('name')->get();
        return view('admin.teknisi.index', compact('teknisis'));
    }

    public function teknisiStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:3',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'role' => 'teknisi',
        ]);

        return back()->with('success', 'Teknisi berhasil ditambahkan.');
    }

    public function teknisiUpdate(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:3',
        ]);

        $data = $request->only('name', 'username');
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);

        return back()->with('success', 'Teknisi berhasil diperbarui.');
    }

    public function teknisiDestroy(User $user)
    {
        if ($user->signature_path) {
            Storage::disk('public')->delete($user->signature_path);

            $publicStoragePath = public_path('storage/' . $user->signature_path);
            if (is_file($publicStoragePath)) {
                @unlink($publicStoragePath);
            }
        }
        $user->delete();
        return back()->with('success', 'Teknisi berhasil dihapus.');
    }

    public function teknisiUpdateSignature(Request $request, User $user)
    {
        $request->validate([
            'signature' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($user->signature_path) {
            Storage::disk('public')->delete($user->signature_path);

            $oldPublicStoragePath = public_path('storage/' . $user->signature_path);
            if (is_file($oldPublicStoragePath)) {
                @unlink($oldPublicStoragePath);
            }
        }

        $path = $request->file('signature')->store('signatures', 'public');

        // Shared hosting fallback: when public/storage is a normal directory (not symlink),
        // mirror uploaded files so /storage/... URLs still work.
        $publicStorageRoot = public_path('storage');
        if (is_dir($publicStorageRoot) && !is_link($publicStorageRoot)) {
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

        $user->update(['signature_path' => $path]);

        return back()->with('success', 'Tanda tangan berhasil diupload.');
    }

    private function downloadSimpleXlsx(string $filename, array $rows)
    {
        $tempDir = storage_path('app/temp-export');
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        $path = $tempDir . DIRECTORY_SEPARATOR . Str::uuid() . '.xlsx';
        $zip = new \ZipArchive();

        if ($zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return back()->withErrors(['template' => 'Gagal membuat template Excel.']);
        }

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
    <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
</Types>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>');
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <sheets><sheet name="Unit AC" sheetId="1" r:id="rId1"/></sheets>
</workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
</Relationships>');
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->buildXlsxSheetXml($rows));
        $zip->close();

        return response()->download($path, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private function buildXlsxSheetXml(array $rows): string
    {
        $sheetRows = '';

        foreach ($rows as $rowIndex => $row) {
            $excelRow = $rowIndex + 1;
            $cells = '';

            foreach ($row as $columnIndex => $value) {
                $cellRef = $this->excelColumnName($columnIndex + 1) . $excelRow;
                $value = htmlspecialchars((string) $value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
                $cells .= '<c r="' . $cellRef . '" t="inlineStr"><is><t>' . $value . '</t></is></c>';
            }

            $sheetRows .= '<row r="' . $excelRow . '">' . $cells . '</row>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
    <sheetData>' . $sheetRows . '</sheetData>
</worksheet>';
    }

    private function excelColumnName(int $column): string
    {
        $name = '';

        while ($column > 0) {
            $column--;
            $name = chr(65 + ($column % 26)) . $name;
            $column = intdiv($column, 26);
        }

        return $name;
    }

    private function readCsvRows(string $path): array
    {
        $sample = file_get_contents($path, false, null, 0, 4096) ?: '';
        $firstLine = strtok($sample, "\r\n") ?: '';
        $delimiter = ',';
        $maxColumns = 0;

        foreach ([',', ';', "\t"] as $candidate) {
            $columns = count(str_getcsv($firstLine, $candidate));
            if ($columns > $maxColumns) {
                $maxColumns = $columns;
                $delimiter = $candidate;
            }
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            return [];
        }

        $rows = [];
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (isset($row[0])) {
                $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', $row[0]);
            }
            $rows[] = array_map(fn ($value) => $this->cleanImportCell($value), $row);
        }

        fclose($handle);

        return $rows;
    }

    private function readXlsxRows(string $path): array
    {
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            return [];
        }

        $sharedStrings = $this->readXlsxSharedStrings($zip);
        $sheetXml = $this->readFirstXlsxSheetXml($zip);
        $zip->close();

        if ($sheetXml === null) {
            return [];
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $loaded = $dom->loadXML($sheetXml);
        libxml_clear_errors();

        if (!$loaded) {
            return [];
        }

        $rows = [];
        foreach ($dom->getElementsByTagName('row') as $rowNode) {
            $row = [];

            foreach ($rowNode->getElementsByTagName('c') as $cellNode) {
                $reference = $cellNode->getAttribute('r');
                $columnIndex = $reference
                    ? $this->xlsxColumnReferenceToIndex($reference)
                    : count($row);

                $row[$columnIndex] = $this->readXlsxCellValue($cellNode, $sharedStrings);
            }

            if (!empty($row)) {
                ksort($row);
                $maxIndex = max(array_keys($row));
                $normalized = [];
                for ($i = 0; $i <= $maxIndex; $i++) {
                    $normalized[] = $row[$i] ?? '';
                }
                $rows[] = $normalized;
            }
        }

        return $rows;
    }

    private function readXlsxSharedStrings(\ZipArchive $zip): array
    {
        $xml = $zip->getFromName('xl/sharedStrings.xml');
        if ($xml === false) {
            return [];
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $loaded = $dom->loadXML($xml);
        libxml_clear_errors();

        if (!$loaded) {
            return [];
        }

        $strings = [];
        foreach ($dom->getElementsByTagName('si') as $item) {
            $text = '';
            foreach ($item->getElementsByTagName('t') as $textNode) {
                $text .= $textNode->nodeValue;
            }
            $strings[] = $this->cleanImportCell($text);
        }

        return $strings;
    }

    private function readFirstXlsxSheetXml(\ZipArchive $zip): ?string
    {
        $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheet !== false) {
            return $sheet;
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if ($name && Str::startsWith($name, 'xl/worksheets/') && Str::endsWith($name, '.xml')) {
                $sheet = $zip->getFromName($name);
                return $sheet === false ? null : $sheet;
            }
        }

        return null;
    }

    private function readXlsxCellValue(\DOMElement $cellNode, array $sharedStrings): string
    {
        $type = $cellNode->getAttribute('t');

        if ($type === 'inlineStr') {
            $text = '';
            foreach ($cellNode->getElementsByTagName('t') as $textNode) {
                $text .= $textNode->nodeValue;
            }
            return $this->cleanImportCell($text);
        }

        $valueNode = $cellNode->getElementsByTagName('v')->item(0);
        $value = $valueNode ? $valueNode->nodeValue : '';

        if ($type === 's') {
            return $this->cleanImportCell($sharedStrings[(int) $value] ?? '');
        }

        return $this->cleanImportCell($value);
    }

    private function xlsxColumnReferenceToIndex(string $reference): int
    {
        preg_match('/^([A-Z]+)/i', $reference, $matches);
        $letters = strtoupper($matches[1] ?? 'A');
        $index = 0;

        for ($i = 0; $i < strlen($letters); $i++) {
            $index = ($index * 26) + (ord($letters[$i]) - 64);
        }

        return max(0, $index - 1);
    }

    private function mapAcUnitImportRows(array $rows): array
    {
        $rows = collect($rows)
            ->filter(fn ($row) => collect($row)->filter(fn ($value) => trim((string) $value) !== '')->isNotEmpty())
            ->values();

        if ($rows->isEmpty()) {
            return [[], ['File import kosong.']];
        }

        $headerRow = $rows->first();
        $headerMap = [];

        foreach ($headerRow as $index => $header) {
            $field = $this->mapAcUnitHeader($header);
            if ($field) {
                $headerMap[$field] = $index;
            }
        }

        $requiredHeaders = [
            'jenis_ac' => 'Jenis AC',
            'merk_ac' => 'Merk AC',
            'kapasitas_pk' => 'Kapasitas',
            'ruangan' => 'Ruangan',
            'lantai' => 'Lantai',
        ];

        $errors = [];
        foreach ($requiredHeaders as $field => $label) {
            if (!array_key_exists($field, $headerMap)) {
                $errors[] = "Kolom {$label} wajib ada di file import.";
            }
        }

        if (!empty($errors)) {
            return [[], $errors];
        }

        $records = [];

        foreach ($rows->slice(1)->values() as $index => $row) {
            $lineNumber = $index + 2;
            $record = [
                'gedung' => $this->getImportValue($row, $headerMap, 'gedung') ?: null,
                'jenis_ac' => $this->getImportValue($row, $headerMap, 'jenis_ac'),
                'merk_ac' => $this->getImportValue($row, $headerMap, 'merk_ac'),
                'kapasitas_pk' => $this->getImportValue($row, $headerMap, 'kapasitas_pk'),
                'ruangan' => $this->getImportValue($row, $headerMap, 'ruangan'),
                'lantai' => $this->getImportValue($row, $headerMap, 'lantai'),
                'frekuensi_cuci' => $this->parseFrekuensiCuci($this->getImportValue($row, $headerMap, 'frekuensi_cuci')),
            ];

            foreach (['jenis_ac' => 'Jenis AC', 'merk_ac' => 'Merk AC', 'kapasitas_pk' => 'Kapasitas', 'ruangan' => 'Ruangan', 'lantai' => 'Lantai'] as $field => $label) {
                if ($record[$field] === '') {
                    $errors[] = "Baris {$lineNumber}: {$label} wajib diisi.";
                } elseif (strlen($record[$field]) > 255) {
                    $errors[] = "Baris {$lineNumber}: {$label} maksimal 255 karakter.";
                }
            }

            if ($record['gedung'] !== null && strlen($record['gedung']) > 255) {
                $errors[] = "Baris {$lineNumber}: Gedung maksimal 255 karakter.";
            }

            if ($record['frekuensi_cuci'] < 0) {
                $errors[] = "Baris {$lineNumber}: Frek. Cuci tidak valid.";
            }

            $records[] = $record;

            if (count($errors) >= 15) {
                $errors[] = 'Masih ada error lain. Perbaiki file lalu import ulang.';
                break;
            }
        }

        if (empty($records) && empty($errors)) {
            $errors[] = 'File import belum berisi data Unit AC.';
        }

        return [$records, $errors];
    }

    private function mapAcUnitHeader(?string $header): ?string
    {
        $normalized = preg_replace('/[^a-z0-9]/', '', strtolower((string) $header));

        return [
            'gedung' => 'gedung',
            'jenis' => 'jenis_ac',
            'jenisac' => 'jenis_ac',
            'typeac' => 'jenis_ac',
            'tipeac' => 'jenis_ac',
            'merk' => 'merk_ac',
            'merek' => 'merk_ac',
            'merkac' => 'merk_ac',
            'merekac' => 'merk_ac',
            'kapasitas' => 'kapasitas_pk',
            'kapasitaspk' => 'kapasitas_pk',
            'pk' => 'kapasitas_pk',
            'ruang' => 'ruangan',
            'ruangan' => 'ruangan',
            'lantai' => 'lantai',
            'frekcuci' => 'frekuensi_cuci',
            'frekuensicuci' => 'frekuensi_cuci',
            'frekcucithn' => 'frekuensi_cuci',
            'frekuensicucithn' => 'frekuensi_cuci',
        ][$normalized] ?? null;
    }

    private function getImportValue(array $row, array $headerMap, string $field): string
    {
        if (!array_key_exists($field, $headerMap)) {
            return '';
        }

        return $this->cleanImportCell($row[$headerMap[$field]] ?? '');
    }

    private function parseFrekuensiCuci(string $value): int
    {
        if ($value === '') {
            return 0;
        }

        preg_match('/\d+/', $value, $matches);

        return isset($matches[0]) ? (int) $matches[0] : -1;
    }

    private function cleanImportCell($value): string
    {
        return trim(preg_replace('/^\xEF\xBB\xBF/', '', (string) $value));
    }
}
