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
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function index()
    {
        $totalReports = ServiceReport::count();
        $totalRS = RumahSakit::count();
        $totalTeknisi = User::where('role', 'teknisi')->count();
        $recentReports = ServiceReport::with(['user', 'rumahSakit', 'ruangan'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.index', compact('totalReports', 'totalRS', 'totalTeknisi', 'recentReports'));
    }

    public function reports(Request $request)
    {
        $query = ServiceReport::with(['user', 'rumahSakit', 'ruangan']);

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

        $reports = $query->latest()->paginate(15);
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
    public function suratJalanIndex()
    {
        $suratJalans = SuratJalan::with(['rumahSakit', 'items'])->latest()->paginate(15);
        return view('admin.surat-jalan.index', compact('suratJalans'));
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
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.banyaknya' => 'required|integer|min:1',
            'items.*.nama_barang' => 'required|string',
        ]);

        $suratJalan = SuratJalan::create([
            'nomor' => $request->nomor,
            'rumah_sakit_id' => $request->rumah_sakit_id,
            'departemen' => $request->departemen,
            'tanggal' => $request->tanggal,
            'penerima' => $request->penerima,
            'mengetahui' => $request->mengetahui,
            'catatan' => $request->catatan,
        ]);

        foreach ($request->items as $item) {
            SuratJalanItem::create([
                'surat_jalan_id' => $suratJalan->id,
                'banyaknya' => $item['banyaknya'],
                'nama_barang' => $item['nama_barang'],
            ]);
        }

        return redirect('/admin/surat-jalan')->with('success', 'Surat jalan berhasil dibuat.');
    }

    public function suratJalanShow(SuratJalan $suratJalan)
    {
        $suratJalan->load(['rumahSakit', 'items']);
        return view('admin.surat-jalan.show', compact('suratJalan'));
    }

    public function suratJalanExportPdf(SuratJalan $suratJalan, Request $request)
    {
        $suratJalan->load(['rumahSakit', 'items']);
        $layout = $request->get('layout', 'intan');

        $pdf = Pdf::loadView('pdf.surat-jalan', compact('suratJalan', 'layout'));
        $pdf->setPaper('A4', 'portrait');

        $filename = 'surat-jalan-' . ($suratJalan->nomor ?: $suratJalan->id) . '.pdf';
        return $pdf->download($filename);
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

        $acUnit->update($request->only('gedung', 'jenis_ac', 'merk_ac', 'kapasitas_pk', 'ruangan', 'lantai', 'frekuensi_cuci'));
        return back()->with('success', 'AC Unit berhasil diperbarui.');
    }

    public function acUnitDestroy(AcUnit $acUnit)
    {
        $acUnit->delete();
        return back()->with('success', 'AC Unit berhasil dihapus.');
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
        }
        $user->delete();
        return back()->with('success', 'Teknisi berhasil dihapus.');
    }

    public function teknisiUpdateSignature(Request $request, User $user)
    {
        $request->validate([
            'signature' => 'required|image|max:2048',
        ]);

        if ($user->signature_path) {
            Storage::disk('public')->delete($user->signature_path);
        }

        $path = $request->file('signature')->store('signatures', 'public');
        $user->update(['signature_path' => $path]);

        return back()->with('success', 'Tanda tangan berhasil diupload.');
    }
}
