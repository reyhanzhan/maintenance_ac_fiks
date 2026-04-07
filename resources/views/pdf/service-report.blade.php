<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; padding: 15px; }

        .header { text-align: center; margin-bottom: 10px; }
        .header-intan .company-name { color: #e67e22; font-size: 16px; font-weight: bold; }
        .header-kemilau .company-name { color: #2980b9; font-size: 16px; font-weight: bold; }
        .header .subtitle { font-size: 10px; color: #555; }

        .info-table { width: 100%; margin-bottom: 10px; }
        .info-table td { padding: 2px 4px; vertical-align: top; }
        .info-table .label { font-weight: bold; width: 110px; }

        .report-box { border: 1px solid #aaa; padding: 6px; margin-bottom: 8px; }

        .check-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .check-table th, .check-table td { border: 1px solid #333; padding: 2px 4px; text-align: center; font-size: 10px; }
        .check-table th { background: #f0f0f0; }
        .check-table td.text-left { text-align: left; }

        .signature-area { margin-top: 20px; }
        .signature-area table { width: 100%; }
        .signature-area td { text-align: center; vertical-align: bottom; padding: 5px; }

        .saran-box { border: 1px solid #aaa; padding: 6px; min-height: 40px; margin-bottom: 10px; }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header header-{{ $layout }}">
        @if($layout === 'intan')
            <div class="company-name">CV. INTAN CITRA PERKASA</div>
            <div class="subtitle">General Supplier & Maintenance</div>
            <div class="subtitle">Jl. Simo Pomahan Baru III No. 21 Tlp. (031) 7481628</div>
            <div class="subtitle">SURABAYA</div>
        @else
            <div class="company-name">CV. KEMILAU MAS SEJAHTERA</div>
            <div class="subtitle">Jl. Dukuh Sawo No.70 Surabaya</div>
        @endif
    </div>

    <hr style="border: 1px solid #333; margin-bottom: 10px;">

    {{-- Customer & Date --}}
    <table class="info-table">
        <tr>
            <td style="width:60%">
                <strong>Customer:</strong> {{ $report->rumahSakit->nama }}<br>
                <strong>Addres:</strong> {{ $report->rumahSakit->alamat }}
            </td>
            <td>
                <strong>Date:</strong> {{ $report->tanggal_service->format('d/m/Y') }}
            </td>
        </tr>
    </table>

    {{-- AC Info --}}
    <table class="info-table" style="border: 1px solid #333; border-collapse: collapse;">
        <tr>
            <td class="label" style="border:1px solid #333;">Nama Alat</td>
            <td style="border:1px solid #333;">AIR CONDITIONER</td>
            <td rowspan="4" style="border:1px solid #333; text-align:center; vertical-align:middle;">
                <strong>RUANGAN</strong><br>{{ $report->ruangan->nama }}
            </td>
        </tr>
        <tr>
            <td class="label" style="border:1px solid #333;">Merk</td>
            <td style="border:1px solid #333;">{{ $report->merk_ac }}</td>
        </tr>
        <tr>
            <td class="label" style="border:1px solid #333;">Type</td>
            <td style="border:1px solid #333;">{{ $report->type_ac }}</td>
        </tr>
        <tr>
            <td class="label" style="border:1px solid #333;">Tanggal servise</td>
            <td style="border:1px solid #333;">{{ $report->tanggal_service->format('d-m-Y') }}</td>
        </tr>
    </table>

    {{-- Pemeriksaan --}}
    <p style="margin: 10px 0 5px; font-weight:bold;">Pelaksanaan Pekerjaan Meliputi</p>
    <table class="check-table">
        <thead>
            <tr>
                <th rowspan="2" style="width:25px">No</th>
                <th rowspan="2">PEMERIKSAAN</th>
                <th colspan="2" style="width:120px">KONDISI</th>
                <th rowspan="2">Keterangan</th>
            </tr>
            <tr>
                <th style="width:55px">Normal</th>
                <th style="width:65px">Tidak Normal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report->items as $item)
            <tr>
                <td>{{ $item->nomor }}</td>
                <td class="text-left">{{ $item->nama_pemeriksaan }}</td>
                <td>{!! $item->is_normal ? '&#10003;' : '' !!}</td>
                <td>{!! !$item->is_normal ? '&#10003;' : '' !!}</td>
                <td class="text-left">{{ $item->keterangan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Saran --}}
    <p style="font-weight:bold; margin-bottom:3px;">saran-saran</p>
    <div class="saran-box">{{ $report->saran }}</div>

    {{-- TTD --}}
    <div class="signature-area">
        <table>
            <tr>
                <td style="width:50%">
                    <strong>{{ $report->rumahSakit->nama }}</strong>
                </td>
                <td>
                    <strong>TEKNISI</strong>
                </td>
            </tr>
            <tr>
                <td style="height:40px;"></td>
                <td>
                    @if($report->user->signature_path)
                        <img src="{{ public_path('storage/' . $report->user->signature_path) }}" style="max-height:50px;">
                    @endif
                </td>
            </tr>
            <tr>
                <td>{{ $report->nama_penerima }}</td>
                <td>{{ $report->user->name }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
