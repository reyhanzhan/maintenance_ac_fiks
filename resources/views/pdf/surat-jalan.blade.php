<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Calibri, DejaVu Sans, sans-serif; font-size: 14px; padding: 20px; }

        .header { text-align: center; margin-bottom: 20px; }
        .header .company-name { color: #e67e22; font-size: 18px; font-weight: bold; }
        .header .subtitle { font-size: 11px; color: #555; }

        .title { font-size: 20px; font-weight: bold; text-decoration: underline; }

        .items-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .items-table th, .items-table td { border: 1px solid #333; padding: 5px 8px; }
        .items-table th { background: #f0f0f0; }

        .signature-area { margin-top: 40px; }
        .signature-area table { width: 100%; }
        .signature-area td { text-align: center; vertical-align: top; padding: 10px; }
    </style>
</head>
<body>


    <table style="width:100%; margin-bottom:10px;">
        <tr>
            <td style="vertical-align:top;">
                <span class="title">SURAT JALAN</span>
            </td>
            <td style="text-align:right; vertical-align:top;">
                @if($suratJalan->departemen)
                    <strong>{{ $suratJalan->rumahSakit->nama }}</strong><br>
                    Ruangan: {{ $suratJalan->departemen }}
                @else
                    <strong>{{ $suratJalan->rumahSakit->nama }}</strong>
                @endif
            </td>
        </tr>
    </table>

    @php
        $totalUnit = $suratJalan->items->sum('banyaknya');
        $penandaMengetahui = $suratJalan->mengetahui
            ?: ($suratJalan->rumahSakit->mengetahui_surat_jalan
                ?: ($suratJalan->rumahSakit->koordinator_lapangan ?: 'M. Choiruddin'));
        $showKoordinatorTtd = stripos($suratJalan->rumahSakit->nama, 'RS Haji') !== false;
    @endphp

    <table class="items-table">
        <thead>
            <tr>
                <th style="width:80px">Banyaknya</th>
                <th style="width:80px">Rincian</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            {{-- Deskripsi pekerjaan as first row with total --}}
            <tr>
                <td style="text-align:center; font-weight:bold;">{{ $totalUnit }}</td>
                <td>&nbsp;</td>
                <td>{{ $suratJalan->deskripsi_pekerjaan ?: 'Servis rutin ac split' }}</td>
            </tr>
            @foreach($suratJalan->items as $item)
            @php
                $typeText = trim((string) ($item->type_ac ?? ''));
                $pkText = trim((string) ($item->pk ?? ''));
                $detailText = $typeText;

                if ($detailText === '' && $pkText !== '') {
                    $detailText = $pkText;
                } elseif ($detailText !== '' && $pkText !== '' && stripos($detailText, $pkText) === false && stripos($detailText, 'PK') === false) {
                    $detailText = $detailText . ' ' . $pkText;
                }

                $detailText = rtrim($detailText, ', ');
            @endphp
            <tr>
                <td>&nbsp;</td>
                <td style="text-align:center;">{{ $item->banyaknya }}</td>
                <td>{{ $item->nama_ruangan }}@if($detailText) – {{ $detailText }}@endif</td>
            </tr>
            @endforeach
            {{-- Empty rows for aesthetics --}}
            @for($i = 0; $i < 3; $i++)
            <tr>
                <td>&nbsp;</td>
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
                <td style="width:{{ $showKoordinatorTtd ? '33%' : '50%' }};">
                    <p>Penerima,</p>
                </td>
                @if($showKoordinatorTtd)
                <td style="width:33%;">
                    <p>Mengetahui Koordinator Lapangan</p>
                </td>
                @endif
                <td style="width:{{ $showKoordinatorTtd ? '33%' : '50%' }};">
                    <p>Hormat Kami,</p>
                </td>
            </tr>
            <tr>
                <td style="height:60px;"></td>
                @if($showKoordinatorTtd)
                <td style="height:60px;"></td>
                @endif
                <td style="height:60px;"></td>
            </tr>
            <tr>
                <td style="vertical-align:top;"><u>{{ $suratJalan->penerima }}</u></td>
                @if($showKoordinatorTtd)
                <td style="vertical-align:top;">
                    <u>{{ $penandaMengetahui }}</u>
                </td>
                @endif
                <td></td>
            </tr>
        </table>
    </div>
</body>
</html>
