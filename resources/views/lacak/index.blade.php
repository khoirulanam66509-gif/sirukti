<!DOCTYPE html>
<html lang="id" class="scroll-smooth h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Lacak Cepat Pengajuan Surat Digital Kampung Rukti Harjo. Cek status permohonan secara real-time hanya dengan nomor pengajuan.">
    <meta name="author" content="Pemerintah Kampung Rukti Harjo">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-lampung-tengah.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo-lampung-tengah.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-lampung-tengah.png') }}">

    <title>Lacak Status Pengajuan Surat — SIRUKTI</title>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-canvas text-ink min-h-screen flex flex-col selection:bg-primary-light selection:text-primary">

    <!-- HEADER / NAVBAR -->
    <header class="fixed top-0 inset-x-0 z-50 bg-surface/90 backdrop-blur-xl border-b border-border-soft shadow-sm transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
            <!-- Brand Logo -->
            <a href="{{ route('landing') }}" class="focus:outline-none focus:ring-2 focus:ring-primary rounded-xl transition">
                <x-application-logo />
            </a>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 sm:gap-3">
                <a href="{{ route('landing') }}" class="min-h-[44px] px-4 inline-flex items-center justify-center rounded-btn text-ink font-semibold text-xs sm:text-sm hover:bg-canvas hover:text-primary transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Beranda</span>
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="min-h-[44px] px-4 sm:px-5 inline-flex items-center justify-center rounded-btn bg-primary text-white font-bold text-xs sm:text-sm hover:bg-primary-hover shadow-sm transition">
                        Beranda Saya
                    </a>
                @else
                    <a href="{{ route('login') }}" class="min-h-[44px] px-4 sm:px-5 inline-flex items-center justify-center rounded-btn bg-primary text-white font-bold text-xs sm:text-sm hover:bg-primary-hover shadow-sm transition">
                        Masuk
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="flex-1 pt-24 pb-16 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto w-full space-y-8">

        <!-- Header Title Section -->
        <div class="text-center space-y-3 pt-4 sm:pt-6">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-primary/10 text-primary text-xs font-bold border border-primary/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span>Pelacakan Cepat Surat Mandiri</span>
            </div>
            <h1 class="text-2xl sm:text-4xl font-extrabold text-ink tracking-tight font-heading">
                Lacak Status Permohonan Surat
            </h1>
            <p class="text-xs sm:text-sm text-ink-muted max-w-xl mx-auto leading-relaxed">
                Ketikkan nomor registrasi pengajuan permohonan Anda (contoh: <span class="font-mono text-primary font-bold">PG-20261020-0001</span>) untuk memantau progres secara langsung.
            </p>
        </div>

        <!-- Search Form Card -->
        <div class="bg-surface rounded-card p-5 sm:p-7 border border-border-soft shadow-featured">
            <form method="GET" action="{{ route('lacak') }}" class="flex flex-col sm:flex-row items-stretch gap-3">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-ink-muted">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        name="no_pengajuan"
                        value="{{ $nomor }}"
                        required
                        placeholder="Masukkan Nomor Pengajuan (contoh: PG-20261020-0001)"
                        class="w-full pl-11 pr-4 py-3 sm:py-3.5 rounded-xl border border-border-soft bg-canvas focus:bg-surface focus:ring-2 focus:ring-primary focus:border-primary text-xs sm:text-sm font-mono text-ink placeholder:font-sans placeholder:text-ink-muted uppercase transition"
                    >
                </div>
                <button
                    type="submit"
                    class="btn-primary min-h-[46px] px-6 py-3 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs sm:text-sm font-bold flex items-center justify-center gap-2 active:scale-95 transition shadow-sm"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>Lacak Pengajuan</span>
                </button>
            </form>

            <div class="mt-3.5 pt-3 border-t border-border-soft flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[11px] sm:text-xs text-ink-muted">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Lupa nomor registrasi? Cek WhatsApp notifikasi pengajuan Anda atau masuk ke akun.</span>
                </span>
                <span class="inline-flex items-center gap-1.5 text-ink">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>Keaslian surat cetak diverifikasi via pindaian QR Code resmi.</span>
                </span>
            </div>
        </div>

        <!-- Result Section -->
        @if($nomor !== '')
            @if($ditemukan && $pengajuan)
                <div class="space-y-4">
                    <div class="flex items-center gap-2 text-xs font-bold text-ink-muted uppercase tracking-wider">
                        <svg class="w-4 h-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Hasil Pelacakan Dokumen Ditemukan</span>
                    </div>

                    <x-hasil-lacak-pengajuan :pengajuan="$pengajuan" />
                </div>
            @else
                <div class="bg-surface rounded-card p-8 border border-border-soft text-center space-y-4 shadow-card">
                    <div class="w-14 h-14 rounded-full bg-rose-50 text-rose-600 mx-auto flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-ink">Nomor Pengajuan Tidak Ditemukan</h3>
                        <p class="text-xs sm:text-sm text-ink-muted mt-1 max-w-md mx-auto">
                            Nomor pengajuan <strong class="text-ink font-mono">{{ $nomor }}</strong> tidak terdaftar dalam sistem basis data kami. Harap periksa kembali format nomor pengajuan pada bukti tanda terima Anda.
                        </p>
                    </div>
                    <div class="pt-2">
                        <a href="https://wa.me/{{ $profilKampung['whatsapp'] ?? '6281234567890' }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-xs font-bold text-primary hover:underline">
                            <span>Butuh bantuan? Hubungi WhatsApp Kantor Kampung</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            @endif
        @endif

    </main>

    <!-- FOOTER DARK THEME -->
    <footer class="mt-auto bg-[#0D2616] text-[#E8ECE9] border-t border-emerald-950/60 pt-12 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs text-white/70">
            <p>&copy; {{ date('Y') }} Pemerintah Kampung {{ $profilKampung['nama_kampung'] ?? 'Rukti Harjo' }}. Hak Cipta Dilindungi.</p>
        </div>
    </footer>

</body>
</html>
