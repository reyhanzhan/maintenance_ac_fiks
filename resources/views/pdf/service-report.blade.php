<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Calibri, DejaVu Sans, sans-serif; font-size: 11px; padding: 15px; }

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
        .check-table th { background: #dcdcdc; }
        .check-table td.text-left { text-align: left; }
        .check-mark { font-size: 15px; font-weight: bold; line-height: 1; }

        .signature-area { margin-top: 20px; }
        .signature-area table { width: 100%; }
        .signature-area td { text-align: center; vertical-align: bottom; padding: 5px; }

        .saran-box { border: 1px solid #aaa; padding: 6px; min-height: 40px; margin-bottom: 10px; }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header header-{{ $layout }}" style="display: flex; align-items: center; margin-bottom: 10px;">
        @if($layout === 'intan')
            <div style="display: flex; align-items: center; width: 100%; gap: 15px;">
                <img src="{{ public_path('images/logo_intan-removebg-preview.png') }}" style="width:300px; height:auto; object-fit:contain;">
                <div style="color: #000; font-family: DejaVu Sans, sans-serif; font-weight: 700; line-height: 1.05;">
                    <div class="subtitle" style="font-weight: 700; color: #000; font-size: 16px;">General Supplier &amp; Maintenance</div>
                    <div class="subtitle" style="font-weight: 700; color: #000; font-size: 12px;">Jl. Simo Pomahan Baru III No. 21 Tlp. (031) 7481628</div>
                    <div class="subtitle" style="font-weight: 700; color: #000; font-size: 14px; letter-spacing: 4px;">SURABAYA.</div>
                </div>
            </div>
        @else
            <div style="width: 100%; text-align: center;">
                <img src="{{ public_path('images/logo_kemilau-removebg-preview.png') }}" style="width:200px; height:auto; object-fit:contain; margin-bottom: 6px;">
                <div style="color: #000; font-family: DejaVu Sans, sans-serif; line-height: 1.1; text-align: center;">
                    <div class="company-name" style="color: #000; font-size: 16px; font-weight: 700;">CV. KEMILAU MAS SEJAHTERA</div>
                    <div class="subtitle" style="color: #000; font-size: 11px; font-weight: 700;">Jalan Raya Sawo No.129 Surabaya</div>
                </div>
            </div>
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
                <strong>Date:</strong> {{ $report->tanggal_service->locale('id')->translatedFormat('l, d F Y') }}
            </td>
        </tr>
    </table>

    {{-- AC Info --}}
    <table class="info-table" style="border: 1px solid #333; border-collapse: collapse;">
        <tr>
            <td class="label" style="border:1px solid #333; width:100px;">Nama Alat</td>
            <td style="border:1px solid #333; width:50%;">AIR CONDITIONER</td>
            <td rowspan="3" style="border:1px solid #333; border-bottom:none; vertical-align:top; padding: 4px 6px; white-space:nowrap; width:30%;">
                <strong>RUANGAN</strong> {{ $report->ruangan->nama }}
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
            <td style="border:1px solid #333;">{{ $report->tanggal_service->locale('id')->translatedFormat('l, d F Y') }}</td>
            <td style="border:1px solid #333; border-top:none; vertical-align:bottom; padding: 4px 6px; white-space:nowrap;">
                <strong>PETUGAS RUANGAN</strong>
            </td>
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
                <td>{{ rtrim((string) $item->nomor, '.') }}.</td>
                <td class="text-left">{{ $item->nama_pemeriksaan }}</td>
                <td>{!! $item->is_normal ? '<span class="check-mark">&#10003;</span>' : '' !!}</td>
                <td>{!! !$item->is_normal ? '<span class="check-mark">&#10003;</span>' : '' !!}</td>
                <td class="text-left">{{ $item->keterangan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Uraian Pekerjaan --}}
    <p style="font-weight:bold; margin-bottom:3px;">Uraian Pekerjaan</p>
    <div class="saran-box">{{ $report->saran }}</div>

    {{-- TTD --}}
    <div class="signature-area">
        <table>
            <tr>
                <td style="width:50%">
                    <strong>Mengetahui<br>{{ $report->rumahSakit->nama }}</strong>
                </td>
                <td style="width:50%">
                    <strong>TEKNISI</strong>
                </td>
            </tr>
            <tr>
                <td style="height:60px;"></td>
                <td style="height:60px;">
                    @if($report->user->signature_path)
                        <img src="{{ public_path('storage/' . $report->user->signature_path) }}" style="width:120px; height:50px; object-fit:contain;">
                    @endif
                </td>
            </tr>
            <tr>
                <td style="padding-top: 0; vertical-align: top;">
                    Koordinator Lapangan<br>
                    <strong>({{ $report->rumahSakit->koordinator_lapangan ?: 'M. Choiruddin' }})</strong>
                </td>
                <td style="padding-top: 0; vertical-align: top;">{{ $report->user->name }}</td>
            </tr>
        </table>
    </div>

    {{-- Foto AC (general photos) --}}
    @php
        $generalPhotos = $report->generalPhotos->values();
        $photoChunks = $generalPhotos->chunk(4);
    @endphp
    @if($generalPhotos->count() > 0)
        @foreach($photoChunks as $chunk)
        <div style="page-break-before: always; padding: 15px;">
            <p style="font-weight: bold; font-size: 13px; margin-bottom: 12px; text-align: center;">Lampiran Foto AC</p>
            <table style="width: 100%; border-collapse: collapse;">
                @foreach($chunk->chunk(2) as $row)
                <tr>
                    @foreach($row as $photo)
                    @php
                        $imgPath = public_path('storage/' . $photo->photo_path);
                        $imgSize = @getimagesize($imgPath);
                        $imgW = $imgSize ? $imgSize[0] : 1;
                        $imgH = $imgSize ? $imgSize[1] : 1;
                        $isLandscape = $imgW >= $imgH;
                        $imgStyle = $isLandscape
                            ? 'width: 260px; height: auto;'
                            : 'width: auto; height: 220px;';
                    @endphp
                    <td style="width: 50%; padding: 6px; text-align: center; vertical-align: middle;">
                        <img src="{{ $imgPath }}"
                             style="{{ $imgStyle }} border: 1px solid #ccc; max-width: 260px; max-height: 220px;">
                    </td>
                    @endforeach
                    @if($row->count() < 2)
                    <td style="width: 50%;"></td>
                    @endif
                </tr>
                @endforeach
            </table>
        </div>
        @endforeach
    @endif
</body>
</html>
