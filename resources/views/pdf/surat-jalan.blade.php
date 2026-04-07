<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; padding: 20px; }

        .header { text-align: center; margin-bottom: 20px; }
        .header-intan .company-name { color: #e67e22; font-size: 18px; font-weight: bold; }
        .header-kemilau .company-name { color: #2980b9; font-size: 18px; font-weight: bold; }
        .header .subtitle { font-size: 11px; color: #555; }

        .title { font-size: 16px; font-weight: bold; text-decoration: underline; margin-bottom: 10px; }
        .dest-info { float: right; margin-top: -40px; text-align: right; }

        .items-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .items-table th, .items-table td { border: 1px solid #333; padding: 5px 8px; }
        .items-table th { background: #f0f0f0; }

        .signature-area { margin-top: 40px; }
        .signature-area table { width: 100%; }
        .signature-area td { text-align: center; vertical-align: bottom; padding: 10px; width: 33%; }
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

    <hr style="border: 1px solid #333; margin-bottom: 15px;">

    <div>
        <span class="title">SURAT JALAN</span>
        <span style="margin-left:10px;">No. {{ $suratJalan->nomor }}</span>
    </div>

    <div style="text-align:right; margin-top:-20px;">
        @if($suratJalan->departemen)
            <strong>{{ $suratJalan->departemen }}</strong><br>
        @endif
        <span>Ruangan ...............................</span>
    </div>

    <p style="margin: 15px 0 5px;">Kami kirimkan barang-barang tersebut di bawah ini dengan kendaraan ........................... No ............</p>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width:80px">Banyaknya</th>
                <th>Nama Barang</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suratJalan->items as $item)
            <tr>
                <td style="text-align:center">{{ $item->banyaknya }}</td>
                <td>{{ $item->nama_barang }}</td>
            </tr>
            @endforeach
            {{-- Empty rows for aesthetics --}}
            @for($i = count($suratJalan->items); $i < 10; $i++)
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            @endfor
        </tbody>
    </table>

    {{-- Signature --}}
    <div class="signature-area">
        <table>
            <tr>
                <td>
                    <p>{{ $suratJalan->tanggal->format('d/m/Y') }}</p>
                    <p style="margin-top:5px;">Penerima,</p>
                </td>
                <td>
                    <p>Mengetahui</p>
                </td>
                <td>
                    <p>Hormat Kami,</p>
                </td>
            </tr>
            <tr>
                <td style="height:60px;"></td>
                <td style="height:60px;"></td>
                <td style="height:60px;"></td>
            </tr>
            <tr>
                <td>{{ $suratJalan->penerima }}</td>
                <td>{{ $suratJalan->mengetahui }}</td>
                <td></td>
            </tr>
        </table>
    </div>
</body>
</html>
