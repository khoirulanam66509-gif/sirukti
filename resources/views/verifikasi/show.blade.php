<!DOCTYPE html>
<html lang="id" class="scroll-smooth h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Verifikasi Keaslian Surat Digital Pemerintah Kampung {{ $kop['nama_kampung'] ?? 'Rukti Harjo' }}">
    <meta name="author" content="Pemerintah Kampung {{ $kop['nama_kampung'] ?? 'Rukti Harjo' }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-lampung-tengah.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo-lampung-tengah.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-lampung-tengah.png') }}">

    <title>
        {{ $isValid ? 'Surat Terverifikasi — Sah & Aktif' : 'Surat Tidak Valid / Tidak Ditemukan' }} — SIRUKTI
    </title>

    <!-- Scripts & Styles (Plus Jakarta Sans self-hosted via Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @media print {
            header, footer, .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
            }
            .print-card {
                box-shadow: none !important;
                border: 1px solid #ccc !important;
                margin: 0 !important;
                padding: 1.5rem !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body class="font-sans antialiased bg-canvas text-ink min-h-screen flex flex-col selection:bg-primary-light selection:text-primary">

    <!-- HEADER / NAVBAR -->
    <header class="fixed top-0 inset-x-0 z-50 bg-surface/90 backdrop-blur-xl border-b border-border-soft shadow-sm transition-all no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
            <!-- Brand Logo -->
            <a href="{{ route('landing') }}" class="focus:outline-none focus:ring-2 focus:ring-primary rounded-xl transition">
                <x-application-logo />
            </a>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 sm:gap-3">
                <a href="{{ route('landing') }}" class="min-h-[44px] px-4 inline-flex items-center justify-center rounded-btn text-ink font-semibold text-sm hover:bg-canvas hover:text-primary transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Beranda
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="min-h-[44px] px-4 inline-flex items-center justify-center rounded-btn bg-primary text-white font-bold text-sm hover:bg-primary-hover shadow-sm transition">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="min-h-[44px] px-4 inline-flex items-center justify-center rounded-btn bg-primary text-white font-bold text-sm hover:bg-primary-hover shadow-sm transition">
                        Masuk
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="w-full pt-24 sm:pt-28 pb-12 flex-1 flex flex-col items-center justify-center relative overflow-hidden px-4 sm:px-6">

        <!-- Background Ambient Blobs -->
        <div class="absolute -top-32 -left-24 w-96 h-96 rounded-full bg-primary-light/40 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-28 -right-20 w-80 h-80 rounded-full bg-accent-light/30 blur-3xl pointer-events-none"></div>

        <div class="w-full max-w-2xl flex flex-col items-center relative z-10">

            @if ($isValid)
                <!-- ========================================== -->
                <!-- LAYAR L4: VERIFIKASI SURAT - VALID -->
                <!-- ========================================== -->

                <!-- Header Lembaga Desa -->
                <div class="flex items-center gap-3 mb-6 bg-surface/90 backdrop-blur-md px-4 py-2 rounded-full shadow-sm border border-border-soft no-print">
                    <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div class="flex flex-col text-left">
                        <span class="text-xs font-bold text-ink tracking-tight leading-none">Pemerintah Kampung {{ $kop['nama_kampung'] ?? 'Rukti Harjo' }}</span>
                        <span class="text-[11px] text-ink-muted leading-tight mt-0.5">Sistem Verifikasi Dokumen Resmi (QR Code Valid)</span>
                    </div>
                </div>

                <!-- Kartu Status Dokumen Valid -->
                <div class="w-full bg-surface rounded-card shadow-card border border-border-soft p-6 sm:p-8 flex flex-col items-center text-center print-card">

                    <!-- Badge Icon Valid -->
                    <div class="relative mb-4">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-[#DCFCE7] flex items-center justify-center shadow-inner">
                            <svg class="w-12 h-12 sm:w-14 sm:h-14 text-[#15803D]" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="absolute -bottom-1 -right-1 bg-surface rounded-full p-1 shadow-sm">
                            <div class="w-6 h-6 rounded-full bg-primary flex items-center justify-center text-white">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Lencana Sah & Aktif -->
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#DCFCE7] text-[#15803D] mb-2">
                        <span class="w-2 h-2 rounded-full bg-[#16A34A] animate-pulse"></span>
                        <span class="text-xs font-bold">Sah &amp; Aktif</span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight mt-1">
                        Surat Terverifikasi
                    </h1>

                    <p class="text-sm text-ink-muted max-w-lg mt-2 leading-relaxed">
                        Dokumen ini sah diterbitkan oleh <strong class="text-ink font-semibold">Pemerintah Kampung {{ $kop['nama_kampung'] ?? 'Rukti Harjo' }}</strong>, Kecamatan {{ $kop['kecamatan'] ?? 'Seputih Raman' }}, Kabupaten {{ $kop['kabupaten'] ?? 'Lampung Tengah' }}.
                    </p>

                    <!-- Divider -->
                    <div class="w-full my-6 flex items-center gap-2">
                        <div class="h-0.5 bg-border-soft flex-1 rounded-full"></div>
                        <div class="flex items-center gap-1.5 px-2 text-ink-muted">
                            <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="text-xs uppercase tracking-wider font-bold">Rincian Dokumen Resmi</span>
                        </div>
                        <div class="h-0.5 bg-border-soft flex-1 rounded-full"></div>
                    </div>

                    <!-- Rincian Data Dokumen -->
                    <div class="w-full bg-canvas/70 rounded-xl p-3 sm:p-4 flex flex-col gap-2.5 text-left mb-6 border border-border-soft">

                        <!-- Nomor Surat -->
                        <div class="grid grid-cols-1 sm:grid-cols-12 py-2 px-3 bg-surface rounded-lg items-center border border-border-soft/60">
                            <div class="sm:col-span-5 text-sm text-ink-muted font-medium flex items-center gap-2">
                                <svg class="w-4 h-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                </svg>
                                Nomor Surat
                            </div>
                            <div class="sm:col-span-7 text-sm font-bold text-ink tracking-tight mt-1 sm:mt-0 font-mono">
                                {{ $surat->no_surat }}
                            </div>
                        </div>

                        <!-- Jenis Surat -->
                        <div class="grid grid-cols-1 sm:grid-cols-12 py-2 px-3 bg-surface rounded-lg items-center border border-border-soft/60">
                            <div class="sm:col-span-5 text-sm text-ink-muted font-medium flex items-center gap-2">
                                <svg class="w-4 h-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Jenis Surat
                            </div>
                            <div class="sm:col-span-7 text-sm font-semibold text-ink mt-1 sm:mt-0">
                                {{ $layanan?->nama_layanan ?? $layanan?->nama ?? '-' }}
                            </div>
                        </div>

                        <!-- Nama Pemohon -->
                        <div class="grid grid-cols-1 sm:grid-cols-12 py-2 px-3 bg-surface rounded-lg items-center border border-border-soft/60">
                            <div class="sm:col-span-5 text-sm text-ink-muted font-medium flex items-center gap-2">
                                <svg class="w-4 h-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Nama Pemohon
                            </div>
                            <div class="sm:col-span-7 text-sm font-bold text-ink mt-1 sm:mt-0">
                                {{ $warga?->pengguna?->nama ?? $warga?->pengguna?->nama_lengkap ?? '-' }}
                            </div>
                        </div>

                        <!-- NIK Pemohon (Disamarkan) -->
                        <div class="grid grid-cols-1 sm:grid-cols-12 py-2 px-3 bg-surface rounded-lg items-center border border-border-soft/60">
                            <div class="sm:col-span-5 text-sm text-ink-muted font-medium flex items-center gap-2">
                                <svg class="w-4 h-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                </svg>
                                NIK Pemohon
                            </div>
                            <div class="sm:col-span-7 text-sm font-semibold text-ink font-mono tracking-wider mt-1 sm:mt-0">
                                {{ samarkan_nik($warga?->nik) }}
                            </div>
                        </div>

                        <!-- Wilayah / Dusun -->
                        @if ($warga)
                            <div class="grid grid-cols-1 sm:grid-cols-12 py-2 px-3 bg-surface rounded-lg items-center border border-border-soft/60">
                                <div class="sm:col-span-5 text-sm text-ink-muted font-medium flex items-center gap-2">
                                    <svg class="w-4 h-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Wilayah / Dusun
                                </div>
                                <div class="sm:col-span-7 text-sm font-medium text-ink mt-1 sm:mt-0">
                                    {{ $warga->dusun?->nama_dusun ?? 'Kampung Rukti Harjo' }}
                                    @if ($warga->rt || $warga->rw)
                                        , RT {{ str_pad($warga->rt ?? 1, 3, '0', STR_PAD_LEFT) }} / RW {{ str_pad($warga->rw ?? 1, 3, '0', STR_PAD_LEFT) }}
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Tanggal Terbit -->
                        <div class="grid grid-cols-1 sm:grid-cols-12 py-2 px-3 bg-surface rounded-lg items-center border border-border-soft/60">
                            <div class="sm:col-span-5 text-sm text-ink-muted font-medium flex items-center gap-2">
                                <svg class="w-4 h-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Tanggal Terbit
                            </div>
                            <div class="sm:col-span-7 text-sm font-medium text-ink mt-1 sm:mt-0">
                                {{ tgl_indo($surat->tgl_surat) }}
                            </div>
                        </div>

                        <!-- Penandatangan -->
                        <div class="grid grid-cols-1 sm:grid-cols-12 py-2 px-3 bg-surface rounded-lg items-start border border-border-soft/60">
                            <div class="sm:col-span-5 text-sm text-ink-muted font-medium flex items-center gap-2 pt-0.5">
                                <svg class="w-4 h-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                Penandatangan
                            </div>
                            <div class="sm:col-span-7 flex flex-col mt-1 sm:mt-0">
                                <span class="text-sm font-bold text-ink">
                                    {{ $penandatangan?->nama ?? $penandatangan?->nama_lengkap ?? 'Kepala Kampung' }}
                                </span>
                                <span class="text-xs text-ink-muted">
                                    {{ $suratCfg['jabatan_penandatangan'] ?? 'Kepala Kampung Rukti Harjo' }}
                                </span>
                                <span class="inline-flex items-center gap-1 text-[#15803D] text-[11px] font-semibold mt-1">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Tanda Tangan Digital Resmi Terdaftar
                                </span>
                            </div>
                        </div>

                        <!-- Status Dokumen -->
                        <div class="grid grid-cols-1 sm:grid-cols-12 py-2 px-3 bg-surface rounded-lg items-center border border-border-soft/60">
                            <div class="sm:col-span-5 text-sm text-ink-muted font-medium flex items-center gap-2">
                                <svg class="w-4 h-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Status Dokumen
                            </div>
                            <div class="sm:col-span-7 mt-1 sm:mt-0">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#DCFCE7] text-[#15803D] text-xs font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#16A34A]"></span>
                                    Sah &amp; Aktif
                                </span>
                            </div>
                        </div>

                    </div>

                    <!-- Info Box -->
                    <div class="w-full bg-canvas rounded-xl p-3.5 sm:p-4 flex items-start gap-2.5 text-left mb-6 border border-border-soft">
                        <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-xs text-ink-muted leading-relaxed">
                            Keabsahan dokumen ini dapat dipertanggungjawabkan sesuai data register arsip surat digital Kampung {{ $kop['nama_kampung'] ?? 'Rukti Harjo' }} tahun {{ $surat->tgl_surat ? $surat->tgl_surat->format('Y') : date('Y') }}. Data di atas dicocokkan secara waktu-nyata dengan basis data administrasi desa.
                        </p>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-2.5 sm:gap-3 w-full no-print">
                        <a href="{{ route('landing') }}" class="w-full sm:w-auto min-h-[46px] px-5 rounded-btn bg-primary text-white font-bold text-xs sm:text-sm hover:bg-primary-hover shadow-sm transition inline-flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span>Beranda</span>
                        </a>
                        <a href="{{ route('lacak') }}" class="w-full sm:w-auto min-h-[46px] px-5 rounded-btn bg-canvas text-ink font-semibold text-xs sm:text-sm hover:bg-border-soft hover:text-primary transition inline-flex items-center justify-center gap-2 border border-border-soft">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <span>Lacak Permohonan</span>
                        </a>
                        <button type="button" onclick="window.print()" class="w-full sm:w-auto min-h-[46px] px-5 rounded-btn bg-canvas text-ink font-semibold text-xs sm:text-sm hover:bg-border-soft hover:text-primary transition inline-flex items-center justify-center gap-2 border border-border-soft">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <span>Cetak Bukti</span>
                        </button>
                    </div>

                </div>

                <!-- Footer Keamanan Dokumen -->
                <div class="mt-6 flex flex-col items-center gap-1 text-center no-print">
                    <div class="flex items-center gap-2 text-ink-muted">
                        <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span class="text-xs font-semibold">Integritas Dokumen Digital Terlindungi</span>
                    </div>
                    <p class="text-xs text-ink-muted">
                        ID Verifikasi: <span class="font-mono font-medium">{{ $surat->kode_verifikasi }}</span>
                    </p>
                </div>

            @else
                <!-- ========================================== -->
                <!-- LAYAR L4: VERIFIKASI SURAT - TIDAK VALID -->
                <!-- ========================================== -->

                <!-- Header Lembaga Desa -->
                <div class="flex flex-col items-center text-center mb-6">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-surface border border-border-soft text-ink-muted text-xs font-medium mb-3 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                        <span>Pemeriksaan Keaslian Dokumen Desa</span>
                    </div>
                    <div class="flex items-center justify-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-white shadow-sm">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <h2 class="text-lg font-bold text-ink tracking-tight leading-snug">
                                Pemerintah Kampung {{ $kop['nama_kampung'] ?? 'Rukti Harjo' }}
                            </h2>
                            <p class="text-xs text-ink-muted">
                                Sistem Verifikasi Dokumen Digital (SIRUKTI)
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Kartu Status Dokumen Tidak Valid -->
                <div class="w-full bg-surface rounded-card shadow-card border border-border-soft p-6 sm:p-8 flex flex-col items-center text-center relative overflow-hidden">

                    <!-- Background Accents -->
                    <div class="absolute -top-16 -right-16 w-48 h-48 bg-red-100/60 rounded-full blur-2xl pointer-events-none"></div>

                    <!-- Ikon Silang Merah -->
                    <div class="relative w-20 h-20 rounded-full bg-red-100 flex items-center justify-center mb-4 text-red-600 shadow-inner">
                        <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>

                    <!-- Lencana Status -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#FEE2E2] text-[#B91C1C] text-xs font-bold mb-3">
                        <span class="w-2 h-2 rounded-full bg-[#DC2626]"></span>
                        <span>Tidak Valid / Tidak Ditemukan</span>
                    </div>

                    <!-- Judul & Penjelasan -->
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight">
                        Surat Tidak Ditemukan
                    </h1>

                    <p class="text-sm text-ink-muted max-w-md mt-2 mb-6 leading-relaxed">
                        Kode verifikasi atau tanda tangan digital ini tidak cocok dengan pangkalan data arsip resmi desa. Dokumen tidak terdaftar atau belum diterbitkan.
                    </p>

                    <!-- Panel Rincian Pemindaian Gagal -->
                    <div class="w-full bg-canvas/70 rounded-xl p-4 text-left mb-6 border border-border-soft flex flex-col gap-2.5">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between text-xs text-ink-muted gap-1 border-b border-border-soft/60 pb-2">
                            <span>Kode yang Dipindai</span>
                            <span class="font-mono font-bold text-ink break-all">{{ $kode }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-ink-muted py-0.5">
                            <span>Waktu Pengecekan</span>
                            <span class="font-semibold text-ink" id="live-timestamp">{{ tgl_indo(now(), true) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-ink-muted pt-1 border-t border-border-soft/60">
                            <span>Status Integritas TTE</span>
                            <span class="inline-flex items-center gap-1 font-bold text-red-600">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Tidak Terverifikasi
                            </span>
                        </div>
                    </div>

                    <!-- Kotak Peringatan Edukasi Keabsahan -->
                    <div class="w-full bg-red-50/70 border border-red-200/60 rounded-xl p-4 flex items-start gap-3 text-left mb-6">
                        <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs font-bold text-ink">Perhatian Keamanan Arsip</span>
                            <p class="text-xs text-ink-muted leading-relaxed">
                                Pastikan Anda memindai kode QR langsung dari dokumen cetak asli yang diterbitkan oleh Pemerintah Kampung {{ $kop['nama_kampung'] ?? 'Rukti Harjo' }}, Kecamatan {{ $kop['kecamatan'] ?? 'Seputih Raman' }}, Kabupaten {{ $kop['kabupaten'] ?? 'Lampung Tengah' }}.
                            </p>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="w-full flex flex-col gap-2.5 mb-4">
                        @php
                            $rawWa = preg_replace('/[^0-9]/', '', $kop['whatsapp'] ?? '6281234567890');
                            if (str_starts_with($rawWa, '0')) {
                                $rawWa = '62' . substr($rawWa, 1);
                            }
                            $waText = urlencode("Halo Petugas Balai Kampung " . ($kop['nama_kampung'] ?? 'Rukti Harjo') . ", saya ingin mengonfirmasi dokumen dengan kode verifikasi: " . $kode);
                        @endphp
                        <a href="https://wa.me/{{ $rawWa }}?text={{ $waText }}" target="_blank" rel="noopener noreferrer" class="w-full min-h-[48px] px-6 py-3 rounded-btn bg-primary text-white hover:bg-primary-hover font-bold text-sm inline-flex items-center justify-center gap-2 transition shadow-sm">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z" />
                            </svg>
                            <span>Hubungi Petugas via WhatsApp</span>
                        </a>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 w-full">
                            <button type="button" onclick="window.location.reload();" class="min-h-[44px] px-4 rounded-btn bg-canvas hover:bg-border-soft text-ink font-semibold text-xs inline-flex items-center justify-center gap-1.5 transition border border-border-soft">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span>Pindai Ulang</span>
                            </button>
                            <a href="{{ route('landing') }}" class="min-h-[44px] px-4 rounded-btn bg-canvas hover:bg-border-soft text-ink font-semibold text-xs inline-flex items-center justify-center gap-1.5 transition border border-border-soft">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                <span>Kembali ke Beranda</span>
                            </a>
                        </div>
                    </div>

                    <!-- Keterangan Kontak Pelayanan Desa -->
                    <div class="w-full pt-4 border-t border-border-soft flex flex-col items-center gap-1 text-xs text-ink-muted">
                        <div class="inline-flex items-center gap-1.5 font-semibold text-ink">
                            <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>Pusat Layanan Warga &amp; Validasi Fisik</span>
                        </div>
                        <p class="text-[11px] text-ink-muted max-w-sm">
                            Balai Kampung: <span class="font-semibold text-ink">{{ $kop['telepon'] ?? '0812-3456-7890' }}</span> (Senin - Jumat, Jam 08.00 - 15.00 WIB)
                        </p>
                    </div>

                </div>

                <!-- Blok Transparansi Pelayanan Publik -->
                <div class="mt-6 text-center max-w-md">
                    <p class="text-[11px] text-ink-muted leading-relaxed">
                        Pemerintah Kampung {{ $kop['nama_kampung'] ?? 'Rukti Harjo' }} menerapkan Tanda Tangan Digital resmi untuk menjamin keabsahan dokumen kependudukan. Pemalsuan dokumen dan stempel desa merupakan tindakan melawan hukum.
                    </p>
                </div>

            @endif

        </div>

    </main>

    <!-- FOOTER (DARK FOREST EMERALD) -->
    <footer class="w-full bg-[#0D2616] text-white border-t border-emerald-900/50 py-6 text-center text-xs no-print">
        <div class="max-w-7xl mx-auto px-4 space-y-1">
            <p class="font-bold text-white tracking-wide">SIRUKTI <span class="text-accent">—</span> Sistem Informasi Pelayanan Kampung Terpadu</p>
            <p class="text-emerald-200/70">Pemerintah Kampung {{ $kop['nama_kampung'] ?? 'Rukti Harjo' }}, Kecamatan {{ $kop['kecamatan'] ?? 'Seputih Raman' }}, Kabupaten {{ $kop['kabupaten'] ?? 'Lampung Tengah' }}</p>
        </div>
    </footer>

    <!-- Toast Notifications -->
    <x-toast />

</body>
</html>
