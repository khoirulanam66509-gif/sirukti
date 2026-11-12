<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi Pelayanan - {{ $namaBulan }} {{ $tahun }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9.5pt;
            line-height: 1.35;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
        }
        .kop-wrapper {
            border-bottom: 2.5px double #1a1a1a;
            padding-bottom: 6px;
            margin-bottom: 14px;
        }
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        .kop-table td {
            border: 0;
            padding: 0;
            vertical-align: middle;
        }
        .kop-logo-cell {
            width: 22mm;
            text-align: left;
        }
        .kop-logo {
            width: 16mm;
            height: 22mm;
            display: block;
        }
        .kop-teks-cell {
            text-align: center;
            padding-right: 12mm;
        }
        .kop-instansi {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            padding: 0;
            line-height: 1.2;
        }
        .kop-kampung {
            font-size: 14pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 2px 0;
            padding: 0;
        }
        .kop-alamat {
            font-size: 8pt;
            font-weight: normal;
            color: #333333;
            margin-top: 3px;
            line-height: 1.25;
        }
        .judul-laporan {
            text-align: center;
            margin-bottom: 14px;
        }
        .judul-laporan h3 {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 3px 0;
            text-decoration: underline;
        }
        .judul-laporan p {
            font-size: 9pt;
            margin: 0;
            color: #444;
        }
        .section-title {
            font-size: 9.5pt;
            font-weight: bold;
            margin: 12px 0 6px 0;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 8.5pt;
        }
        th, td {
            border: 1px solid #777;
            padding: 5px 6px;
            vertical-align: middle;
        }
        th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
            color: #0f172a;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .bg-total {
            background-color: #f8fafc;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 7.5pt;
            font-weight: bold;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .badge-selesai { background-color: #dcfce7; color: #166534; }
        .badge-ditolak { background-color: #fee2e2; color: #991b1b; }
        .badge-proses { background-color: #dbeafe; color: #1e40af; }
        .ttd-container {
            margin-top: 25px;
            width: 100%;
        }
        .ttd-box {
            width: 45%;
            float: right;
            text-align: center;
            font-size: 9pt;
        }
        .ttd-box-left {
            width: 45%;
            float: left;
            text-align: center;
            font-size: 9pt;
        }
        .clear {
            clear: both;
        }
        .ttd-space {
            height: 55px;
        }
    </style>
</head>
<body>

    @php
        $logoSrc = null;
        $kandidatLogo = [
            base_path('resources/surat/logo-lampung-tengah.png'),
            base_path('resources/surat/logo-lampung-tengah.jpg'),
            base_path('resources/surat/logo-lampung-tengah.jpeg'),
            public_path('images/logo-lampung-tengah.png'),
            public_path('images/logo-lampung-tengah.jpg'),
            public_path('images/logo-lampung-tengah.jpeg'),
            public_path('logo-lampung-tengah.png'),
        ];
        foreach ($kandidatLogo as $path) {
            if (file_exists($path)) {
                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                $mime = ($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : 'image/png';
                $logoSrc = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($path));
                break;
            }
        }
    @endphp

    <!-- Kop Surat Resmi -->
    <div class="kop-wrapper">
        <table class="kop-table">
            <tr>
                <td class="kop-logo-cell">
                    @if($logoSrc)
                        <img src="{{ $logoSrc }}" class="kop-logo" alt="Logo Kabupaten">
                    @endif
                </td>
                <td class="kop-teks-cell">
                    <div class="kop-instansi">PEMERINTAH KABUPATEN {{ strtoupper($pengaturan['kabupaten'] ?? 'LAMPUNG TENGAH') }}</div>
                    <div class="kop-instansi">KECAMATAN {{ strtoupper($pengaturan['kecamatan'] ?? 'SEPUTIH RAMAN') }}</div>
                    <div class="kop-kampung">KAMPUNG {{ strtoupper($pengaturan['nama_kampung'] ?? 'RUKTI HARJO') }}</div>
                    <div class="kop-alamat">
                        {{ $pengaturan['alamat'] ?? 'Jl. Merdeka No. 1, Kampung Rukti Harjo' }} Kode Pos {{ $pengaturan['kode_pos'] ?? '34155' }}<br>
                        @if(!empty($pengaturan['telepon'])) Telp: {{ $pengaturan['telepon'] }} @endif
                        @if(!empty($pengaturan['whatsapp'])) | WhatsApp Resmi: {{ $pengaturan['whatsapp'] }} @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Judul Laporan -->
    <div class="judul-laporan">
        <h3>LAPORAN REKAPITULASI PELAYANAN ADMINISTRASI KAMPUNG</h3>
        <p>Periode: <strong>{{ $namaBulan }} {{ $tahun }}</strong></p>
    </div>

    <!-- Tabel I: Rekapitulasi per Layanan (Stored Procedure) -->
    <div class="section-title">I. Ringkasan Pelayanan per Jenis Surat</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">Kode</th>
                <th style="width: 33%;">Nama Layanan</th>
                <th style="width: 10%;">Total</th>
                <th style="width: 10%;">Selesai</th>
                <th style="width: 10%;">Ditolak</th>
                <th style="width: 10%;">Proses</th>
                <th style="width: 10%;">Rata-rata (Jam)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totSemua = 0;
                $totSelesai = 0;
                $totDitolak = 0;
                $totBerjalan = 0;
            @endphp
            @forelse($rekapLayanan as $idx => $r)
                @php
                    $totSemua += $r->total;
                    $totSelesai += $r->selesai;
                    $totDitolak += $r->ditolak;
                    $totBerjalan += $r->berjalan;
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center font-bold">{{ $r->kode }}</td>
                    <td>{{ $r->nama_layanan }}</td>
                    <td class="text-center font-bold">{{ $r->total }}</td>
                    <td class="text-center text-green-700">{{ $r->selesai }}</td>
                    <td class="text-center text-red-700">{{ $r->ditolak }}</td>
                    <td class="text-center">{{ $r->berjalan }}</td>
                    <td class="text-center">{{ $r->rata_jam_selesai ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data layanan pada periode ini.</td>
                </tr>
            @endforelse
            <tr class="bg-total">
                <td colspan="3" class="text-center font-bold">TOTAL KESELURUHAN</td>
                <td class="text-center font-bold">{{ $totSemua }}</td>
                <td class="text-center font-bold">{{ $totSelesai }}</td>
                <td class="text-center font-bold">{{ $totDitolak }}</td>
                <td class="text-center font-bold">{{ $totBerjalan }}</td>
                <td class="text-center font-bold">-</td>
            </tr>
        </tbody>
    </table>

    <!-- Tabel II: Daftar Pengajuan / Dokumen Arsip -->
    <div class="section-title">II. Rincian Permohonan dan Arsip Surat (Total: {{ $pengajuanList->count() }})</div>
    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 14%;">No. Pengajuan</th>
                <th style="width: 18%;">No. Surat Resmi</th>
                <th style="width: 20%;">Pemohon</th>
                <th style="width: 16%;">Layanan</th>
                <th style="width: 14%;">Tgl Pengajuan</th>
                <th style="width: 14%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengajuanList as $index => $p)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $p->no_pengajuan }}</td>
                    <td>{{ $p->surat?->no_surat ?? '-' }}</td>
                    <td>
                        <span class="font-bold">{{ $p->warga?->pengguna?->nama ?? '-' }}</span><br>
                        <span style="font-size: 7.5pt; color: #555;">NIK: {{ $p->warga?->nik ?? '-' }}</span>
                    </td>
                    <td>{{ $p->layanan?->nama_layanan }}</td>
                    <td class="text-center">{{ $p->tgl_pengajuan ? $p->tgl_pengajuan->format('d/m/Y H:i') : '-' }}</td>
                    <td class="text-center">
                        @if($p->status === 'selesai')
                            <span class="badge badge-selesai">Selesai</span>
                        @elseif($p->status === 'ditolak')
                            <span class="badge badge-ditolak">Ditolak</span>
                        @else
                            <span class="badge badge-proses">{{ str_replace('_', ' ', $p->status) }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada permohonan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan / Penutup Laporan -->
    <div class="ttd-container">
        <div class="ttd-box-left">
            <p>Petugas Pelaksana / Pencetak,</p>
            <div class="ttd-space"></div>
            <p class="font-bold"><u>{{ $petugas?->nama ?? 'Petugas Kasi Pelayanan' }}</u><br>
            <span style="font-size: 8pt; color: #444;">{{ $petugas?->role === 'kasi' ? 'Kasi Pelayanan' : 'Aparatur Kampung' }}</span></p>
        </div>

        <div class="ttd-box">
            <p>{{ $pengaturan['nama_kampung'] ?? 'Rukti Harjo' }}, {{ $tglCetak }}<br>Mengetahui,<br><strong>Kepala Kampung {{ $pengaturan['nama_kampung'] ?? 'Rukti Harjo' }}</strong></p>
            <div class="ttd-space"></div>
            <p class="font-bold"><u>{{ $kepala?->nama ?? 'GUNAWAN' }}</u><br>
            <span style="font-size: 8pt; color: #444;">Kepala Kampung</span></p>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>
