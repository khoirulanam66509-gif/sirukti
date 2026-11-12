<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $judul_surat ?? 'Surat Keterangan' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 26mm 10mm 26mm;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 14pt;
            line-height: 1.38;
            color: #000000;
            margin: 0;
            padding: 0;
        }
        body.surat-skck,
        body.surat-panjang {
            line-height: 1.28;
        }
        body.surat-sku {
            font-size: 13.5pt;
            line-height: 1.25;
        }

        /* ===== Kop Surat ===== */
        .kop {
            width: 100%;
            border-collapse: collapse;
        }
        .kop td {
            border: 0;
            padding: 0;
        }
        .kop-logo-cell {
            width: 24mm;
            height: 27mm;
            vertical-align: middle;
            text-align: left;
        }
        .kop-logo {
            width: 19mm;
            height: 26mm;
            display: block;
        }
        .kop-teks {
            text-align: center;
            vertical-align: middle;
            padding-right: 12mm;
        }
        .kop-baris {
            font-weight: bold;
            line-height: 1.08;
        }
        .kop-pemkab {
            font-size: 15pt;
        }
        .kop-kecamatan,
        .kop-kampung {
            font-size: 17pt;
        }
        .kop-alamat,
        .kop-kontak {
            font-size: 11.5pt;
            font-style: italic;
            font-weight: normal;
            line-height: 1.2;
        }
        .garis-ganda {
            border-top: 3pt solid #000000;
            border-bottom: 1pt solid #000000;
            height: 1.5pt;
            margin-top: 2mm;
        }

        /* ===== Judul & Nomor ===== */
        .judul-wrapper {
            text-align: center;
            margin-top: 3.5mm;
            margin-bottom: 2.5mm;
        }
        .surat-skck .judul-wrapper,
        .surat-sku .judul-wrapper {
            margin-top: 2.5mm;
            margin-bottom: 2mm;
        }
        .judul-surat {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            line-height: 1.1;
        }
        .nomor-surat {
            font-size: 14pt;
            font-weight: normal;
            margin-top: 1.5mm;
        }
        .surat-skck .nomor-surat,
        .surat-sku .nomor-surat {
            margin-top: 1mm;
        }

        /* ===== Paragraf ===== */
        p {
            text-align: justify;
            text-indent: 0;
            margin: 0 0 3.5mm 0;
            padding: 0;
        }
        .surat-skck p,
        .surat-sku p,
        .surat-panjang p {
            margin: 0 0 2mm 0;
        }
        p:last-child {
            margin-bottom: 0;
        }
        .teks-tebal {
            font-weight: bold;
        }

        /* ===== Tabel Data (identitas & rincian) ===== */
        .tabel-indent {
            margin-left: 5mm;
        }
        .tabel-data {
            width: 100%;
            border-collapse: collapse;
            border: 0;
            margin: 1.5mm 0 3.5mm 0;
        }
        .surat-skck .tabel-data,
        .surat-sku .tabel-data,
        .surat-panjang .tabel-data {
            margin: 0.8mm 0 1.8mm 0;
        }
        .tabel-data td {
            border: 0;
            padding: 0.4mm 0;
            vertical-align: top;
            font-size: 14pt;
            line-height: 1.32;
        }
        .surat-skck .tabel-data td,
        .surat-panjang .tabel-data td {
            padding: 0.2mm 0;
            line-height: 1.24;
        }
        .surat-sku .tabel-data td {
            font-size: 13.5pt;
            padding: 0.15mm 0;
            line-height: 1.22;
        }
        .tabel-data td.label {
            width: 56mm;
        }
        .tabel-data--ekonomi td.label {
            width: 62mm;
        }
        .tabel-data td.titik-dua {
            width: 4mm;
            text-align: left;
        }

        /* ===== Blok QR + Tanda Tangan ===== */
        .ttd-tabel {
            width: 100%;
            border-collapse: collapse;
            border: 0;
            margin-top: 2.5mm;
            page-break-inside: avoid;
        }
        .surat-panjang .ttd-tabel {
            margin-top: 2mm;
        }
        .surat-sktm .ttd-tabel,
        .surat-dom .ttd-tabel {
            margin-top: 7mm;
        }
        .surat-sktm.surat-panjang .ttd-tabel,
        .surat-dom.surat-panjang .ttd-tabel {
            margin-top: 4mm;
        }
        .ttd-tabel td {
            border: 0;
            vertical-align: top;
        }
        .ttd-kiri {
            vertical-align: top;
            padding: 5.5mm 0 0 0;
            padding-top: 5.5mm;
            text-align: left;
        }
        .ttd-kanan {
            width: 67mm;
            padding: 0;
            vertical-align: top;
            text-align: left;
        }
        .qr-gambar {
            width: 22mm;
            height: 22mm;
            display: block;
            margin: 0;
            border: 0;
        }
        .qr-ruang {
            width: 22mm;
            height: 22mm;
            margin: 0;
        }
        .ttd-tanggal,
        .ttd-jabatan {
            position: relative;
            z-index: 4;
            font-size: 12pt;
            line-height: 1.25;
        }
        .ttd-area {
            position: relative;
            height: 20mm;
            margin: 1mm 0 0 0;
        }
        .ttd-stempel {
            position: absolute;
            left: -9mm;
            top: -5mm;
            width: 30mm;
            height: 30mm;
            z-index: 1;
        }
        .ttd-gambar {
            position: absolute;
            left: 2mm;
            top: -2mm;
            width: 42mm;
            z-index: 2;
        }
        .ttd-nama {
            position: relative;
            z-index: 3;
            font-size: 12pt;
            line-height: 1.25;
            margin-top: 0.5mm;
        }
        .ttd-nama span {
            text-decoration: underline;
        }
        .ttd-nip {
            position: relative;
            z-index: 3;
            font-size: 10.5pt;
            line-height: 1.2;
        }
        .ttd-placeholder {
            position: absolute;
            top: 8mm;
            left: 0;
            width: 67mm;
            text-align: center;
            color: #777777;
            font-style: italic;
            font-size: 10pt;
        }

        /* ===== Footer ===== */
        .footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            text-align: center;
        }
        .footer-garis {
            border-top: 0.5pt solid #6FA0D8;
            width: 100%;
            height: 0;
        }
        .footer-teks {
            font-size: 8pt;
            color: #666666;
            line-height: 1.25;
            margin-top: 1mm;
        }

        /* ===== Penanda Draft ===== */
        .draft-label-wrapper {
            position: absolute;
            top: -5.5mm;
            right: 0;
            z-index: 20;
        }
        .draft-label {
            border: 1pt solid #000000;
            padding: 1pt 5pt;
            font-size: 9pt;
            font-weight: bold;
            letter-spacing: 2pt;
            line-height: 1.1;
        }
    </style>
</head>
@php
    $namaWargaLayout = $warga->pengguna->nama ?? '';
    $alamatWargaLayout = $warga->alamat ?? '';
    $tujuanIsianLayout = $data_isian['tujuan'] ?? ($data_isian['keperluan'] ?? '');
    $kontenPanjang = mb_strlen($namaWargaLayout) > 35
        || mb_strlen($alamatWargaLayout) > 45
        || mb_strlen($tujuanIsianLayout) > 50;
@endphp
<body class="surat-{{ strtolower($layanan->kode ?? '') }}{{ $kontenPanjang ? ' surat-panjang' : '' }}">

@php
    $namaSistem = 'Sistem Informasi Desa Rukti Harjo (SIDESA)';
@endphp

@if(!empty($is_draft))
    <div class="draft-label-wrapper"><span class="draft-label">DRAFT</span></div>
@endif

@include('surat.partials.kop')

<div class="judul-wrapper">
    <div class="judul-surat">{{ $judul_surat }}</div>
    <div class="nomor-surat">
        @if(!empty($is_draft))
            Nomor: (otomatis saat disetujui)
        @else
            Nomor: {{ $no_surat ?? '-' }}
        @endif
    </div>
</div>

@yield('content')

@include('surat.partials.tanda-tangan')
@include('surat.partials.footer')

</body>
</html>

