<!DOCTYPE html>
<html lang="id" class="scroll-smooth h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#2F5D3A">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-lampung-tengah.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo-lampung-tengah.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-lampung-tengah.png') }}">
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="description" content="SIRUKTI — Sistem Informasi Pelayanan Kampung Terpadu Rukti Harjo, Kecamatan Seputih Raman, Kabupaten Lampung Tengah. Layanan pengajuan surat mandiri, cepat, dan transparan.">
    <meta name="author" content="Pemerintah Kampung Rukti Harjo">

    <title>SIRUKTI — Pelayanan Surat Digital Kampung Rukti Harjo</title>

    <!-- Scripts & Styles (Plus Jakarta Sans self-hosted via Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-canvas text-ink min-h-screen flex flex-col selection:bg-primary-light selection:text-primary" x-data="{ mobileMenuOpen: false }">

    <!-- HEADER / NAVBAR -->
    <header class="fixed top-0 inset-x-0 z-50 bg-surface/90 backdrop-blur-xl border-b border-border-soft shadow-sm transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
            <!-- Brand Logo -->
            <a href="/" class="focus:outline-none focus:ring-2 focus:ring-primary rounded-xl transition">
                <x-application-logo />
            </a>

            <!-- Desktop Nav Links -->
            <nav class="hidden lg:flex items-center gap-1 font-semibold text-sm text-ink-muted">
                <a href="#hero" class="px-3.5 py-2 rounded-xl text-ink hover:text-primary hover:bg-canvas transition">Beranda</a>
                <a href="#lacak" class="px-3.5 py-2 rounded-xl hover:text-primary hover:bg-canvas transition">Lacak Surat</a>
                <a href="#tata-cara" class="px-3.5 py-2 rounded-xl hover:text-primary hover:bg-canvas transition">Tata Cara</a>
                <a href="#layanan" class="px-3.5 py-2 rounded-xl hover:text-primary hover:bg-canvas transition">Layanan Surat</a>
                <a href="#kontak" class="px-3.5 py-2 rounded-xl hover:text-primary hover:bg-canvas transition">Jam Layanan</a>
                <a href="#faq" class="px-3.5 py-2 rounded-xl hover:text-primary hover:bg-canvas transition">FAQ</a>
            </nav>

            <!-- Auth Action Buttons (Desktop & Tablet) -->
            <div class="hidden sm:flex items-center gap-2">
                <button
                    type="button"
                    @click="$dispatch('pwa-install-request')"
                    class="min-h-[44px] px-3 inline-flex items-center justify-center gap-1.5 rounded-xl text-ink-muted hover:text-primary hover:bg-canvas font-semibold text-sm transition"
                    title="Instal Aplikasi SIRUKTI"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span>Instal</span>
                </button>
                @auth
                    <a href="{{ route('dashboard') }}" class="min-h-[44px] px-5 inline-flex items-center justify-center rounded-btn bg-primary text-white font-bold text-sm hover:bg-primary-hover shadow-sm transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Buka Beranda
                    </a>
                @else
                    <a href="{{ route('login') }}" class="min-h-[44px] px-4 inline-flex items-center justify-center rounded-btn text-ink font-semibold text-sm hover:bg-canvas hover:text-primary transition">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="min-h-[44px] px-5 inline-flex items-center justify-center rounded-btn bg-primary text-white font-bold text-sm hover:bg-primary-hover shadow-sm transition">
                        Daftar Akun
                    </a>
                @endauth
            </div>

            <!-- Mobile Menu Toggle Button (< sm) -->
            <div class="flex items-center gap-2 sm:hidden">
                @auth
                    <a href="{{ route('dashboard') }}" class="min-h-[44px] px-3.5 inline-flex items-center justify-center rounded-btn bg-primary text-white font-bold text-xs shadow-sm">
                        Beranda
                    </a>
                @else
                    <a href="{{ route('login') }}" class="min-h-[44px] px-3.5 inline-flex items-center justify-center rounded-btn bg-primary text-white font-bold text-xs shadow-sm">
                        Masuk
                    </a>
                @endauth

                <button @click="mobileMenuOpen = !mobileMenuOpen"
                        type="button"
                        class="min-h-[44px] min-w-[44px] inline-flex items-center justify-center rounded-xl text-ink-muted hover:text-ink hover:bg-canvas focus:outline-none focus:ring-2 focus:ring-primary"
                        aria-label="Buka menu navigasi"
                        :aria-expanded="mobileMenuOpen.toString()">
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div x-show="mobileMenuOpen"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             @click.away="mobileMenuOpen = false"
             class="sm:hidden border-b border-border-soft bg-surface px-4 pt-2 pb-6 space-y-2 shadow-lg">
            <a @click="mobileMenuOpen = false" href="#hero" class="block px-3 py-2.5 rounded-xl font-semibold text-ink hover:bg-canvas hover:text-primary">Beranda</a>
            <a @click="mobileMenuOpen = false" href="#lacak" class="block px-3 py-2.5 rounded-xl font-semibold text-ink hover:bg-canvas hover:text-primary">Lacak Status Surat</a>
            <a @click="mobileMenuOpen = false" href="#tata-cara" class="block px-3 py-2.5 rounded-xl font-semibold text-ink hover:bg-canvas hover:text-primary">Tata Cara Pengajuan</a>
            <a @click="mobileMenuOpen = false" href="#layanan" class="block px-3 py-2.5 rounded-xl font-semibold text-ink hover:bg-canvas hover:text-primary">Layanan Surat</a>
            <a @click="mobileMenuOpen = false" href="#kontak" class="block px-3 py-2.5 rounded-xl font-semibold text-ink hover:bg-canvas hover:text-primary">Jam Layanan & Kontak</a>
            <a @click="mobileMenuOpen = false" href="#faq" class="block px-3 py-2.5 rounded-xl font-semibold text-ink hover:bg-canvas hover:text-primary">Pertanyaan Umum (FAQ)</a>
            <button
                type="button"
                @click="mobileMenuOpen = false; $dispatch('pwa-install-request')"
                class="w-full flex items-center gap-2 px-3 py-2.5 rounded-xl font-semibold text-primary hover:bg-canvas transition text-left"
            >
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <span>Instal Aplikasi</span>
            </button>

            <div class="pt-4 border-t border-border-soft flex flex-col gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="min-h-[48px] w-full inline-flex items-center justify-center rounded-btn bg-primary text-white font-bold text-sm shadow">
                        Buka Beranda Saya
                    </a>
                @else
                    <a href="{{ route('login') }}" class="min-h-[48px] w-full inline-flex items-center justify-center rounded-btn border border-border-soft bg-surface text-ink font-bold text-sm hover:bg-canvas">
                        Masuk ke Akun
                    </a>
                    <a href="{{ route('register') }}" class="min-h-[48px] w-full inline-flex items-center justify-center rounded-btn bg-primary text-white font-bold text-sm shadow">
                        Daftar Akun Baru
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="flex-1 pt-20">

        <!-- 1. HERO SECTION -->
        <section id="hero" class="relative overflow-hidden bg-gradient-to-b from-[#0c2414] via-[#143d21] to-[#0e2c17] text-white py-10 sm:py-16 lg:py-20">
            <!-- Subtle Architectural Gate Silhouette Watermark (Atmospheric Layer) -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none select-none opacity-[0.08] mix-blend-luminosity flex items-center justify-center">
                <img
                    src="{{ asset('images/Gerbang Balai Kampung Rukti Harjo Transparan.png') }}"
                    alt=""
                    class="w-[140%] max-w-none transform -translate-y-8 scale-110 object-cover filter contrast-125"
                    loading="lazy"
                >
            </div>

            <!-- Ambient Golden Radiance & Forest Glow -->
            <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-primary/40 blur-3xl pointer-events-none"></div>
            <div class="absolute top-1/4 right-10 w-[500px] h-[500px] rounded-full bg-accent/15 blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-0 right-0 w-80 h-80 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">

                    <!-- Left Column: Typography & CTAs (Col 7) -->
                    <div class="lg:col-span-7 flex flex-col items-start gap-4 sm:gap-5">
                        
                        <!-- Official Hierarchy Badge -->
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/15 shadow-xs">
                            <img src="{{ asset('images/logo-lampung-tengah.png') }}" alt="Logo Kabupaten Lampung Tengah" class="w-4 h-auto shrink-0 drop-shadow">
                            <span class="text-[11px] sm:text-xs font-bold tracking-wide text-accent uppercase">Pemerintah Kampung Rukti Harjo</span>
                            <span class="hidden sm:inline text-white/40">•</span>
                            <span class="hidden sm:inline text-[11px] font-semibold text-emerald-200">Seputih Raman, Lampung Tengah</span>
                        </div>

                        <!-- Main Title -->
                        <h1 class="text-2xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white leading-tight font-heading">
                            Pelayanan Surat Digital <br class="hidden sm:inline">
                            <span class="text-accent underline decoration-accent/40 underline-offset-8">Kampung Rukti Harjo</span>
                        </h1>

                        <!-- Local Motto & Spirit -->
                        <div class="inline-flex items-center gap-2 text-xs sm:text-sm text-emerald-200/95 font-medium italic">
                            <span class="w-1.5 h-1.5 rounded-full bg-accent animate-pulse"></span>
                            <span>&ldquo;Beguwai Jejama Wawai&rdquo; — Gotong Royong Melayani Warga</span>
                        </div>

                        <!-- Hero Description -->
                        <p class="text-sm sm:text-base lg:text-lg text-white/90 max-w-xl leading-relaxed">
                            Sistem resmi pengurusan administrasi kependudukan warga secara daring. Ajukan surat dari rumah tanpa perlu antre di balai kampung, pantau verifikasi berkas secara transparan, dan unduh surat resmi bertanda tangan digital.
                        </p>

                        <!-- Action Buttons Group -->
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-1 sm:pt-2 w-full sm:w-auto">
                            @auth
                                <a href="{{ route('dashboard') }}" class="min-h-[46px] sm:min-h-[50px] px-6 rounded-btn bg-accent text-ink font-bold hover:bg-accent-hover transition shadow-featured inline-flex items-center justify-center text-sm sm:text-base">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    Buka Beranda Saya
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="min-h-[46px] sm:min-h-[50px] px-6 rounded-btn bg-surface text-primary font-bold hover:bg-canvas transition shadow-md inline-flex items-center justify-center text-sm sm:text-base">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                    </svg>
                                    Masuk Akun
                                </a>
                                <a href="{{ route('register') }}" class="min-h-[46px] sm:min-h-[50px] px-6 rounded-btn bg-accent text-ink font-bold hover:bg-accent-hover transition shadow-featured inline-flex items-center justify-center text-sm sm:text-base">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                    Daftar Akun Baru
                                </a>
                            @endauth
                        </div>

                        <!-- Trust Credentials / Maklumat Layanan -->
                        <div class="flex flex-wrap items-center gap-3 pt-2 text-xs text-emerald-100/80">
                            <div class="inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <span>100% Bebas Pungli</span>
                            </div>
                            <span class="text-white/30">•</span>
                            <div class="inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span>Tanda Tangan Elektronik Sah</span>
                            </div>
                            <span class="text-white/30">•</span>
                            <div class="inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                                <span>Verifikasi QR Code Valid</span>
                            </div>
                        </div>

                        <!-- Helper WhatsApp Link -->
                        <div class="pt-1">
                            <a href="https://wa.me/{{ $profilKampung['whatsapp'] ?? '6281234567890' }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-xs sm:text-sm text-white/90 hover:text-accent transition group">
                                <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-white/20 flex items-center justify-center text-[#25D366] group-hover:bg-white transition">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 fill-current" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.301-.15-1.782-.88-2.058-.98-.276-.1-.476-.15-.676.15-.2.301-.776.98-.952 1.18-.175.201-.351.226-.652.075-.301-.15-1.27-.468-2.42-1.493-.895-.798-1.5-1.784-1.675-2.085-.175-.3-.019-.463.131-.613.136-.135.301-.351.451-.526.15-.176.201-.301.301-.502.1-.201.05-.376-.025-.526-.075-.15-.676-1.631-.926-2.235-.244-.589-.493-.509-.676-.519-.175-.01-.376-.01-.577-.01-.2 0-.526.075-.802.376-.276.301-1.053 1.029-1.053 2.509 0 1.48 1.078 2.909 1.229 3.11.15.2 2.122 3.24 5.14 4.542.718.31 1.279.495 1.716.634.721.23 1.378.197 1.9.12.58-.088 1.782-.728 2.033-1.432.25-.703.25-1.305.175-1.431-.075-.126-.275-.201-.576-.351zM12.04 2C6.544 2 2.08 6.464 2.08 11.96c0 1.84.502 3.564 1.374 5.045L2 22l5.142-1.35c1.42.776 3.04 1.218 4.76 1.218 5.496 0 9.96-4.464 9.96-9.96C21.862 6.464 17.436 2 12.04 2zm0 18.234c-1.58 0-3.07-.42-4.364-1.155l-.313-.18-3.044.798.813-2.966-.2-.317a8.214 8.214 0 01-1.258-4.454c0-4.562 3.712-8.274 8.274-8.274 4.562 0 8.274 3.712 8.274 8.274 0 4.562-3.712 8.274-8.274 8.274z" />
                                    </svg>
                                </div>
                                <span class="underline underline-offset-4 decoration-white/40 group-hover:decoration-accent">Konsultasi Pamong Desa via WhatsApp</span>
                            </a>
                        </div>
                    </div>

                    <!-- Right Column: Showcase Gapura Balai Kampung & Bento Statistik (Col 5) -->
                    <div class="lg:col-span-5 flex flex-col items-center gap-5 w-full">
                        <!-- Hero Gate Visual Showcase Container -->
                        <div class="relative w-full max-w-lg lg:max-w-none group">
                            <!-- Golden Sunbeam & Radiance Behind Gate -->
                            <div class="absolute -inset-4 bg-gradient-to-tr from-accent/25 via-emerald-400/15 to-transparent rounded-3xl blur-2xl pointer-events-none transform transition-transform duration-700 group-hover:scale-105"></div>
                            
                            <!-- Frame & Gate Image -->
                            <div class="relative rounded-3xl p-3 sm:p-5 bg-gradient-to-b from-white/10 to-white/5 backdrop-blur-md border border-white/20 shadow-2xl overflow-hidden">
                                <div class="relative flex items-center justify-center pt-2 pb-1">
                                    <img
                                        src="{{ asset('images/Gerbang Balai Kampung Rukti Harjo Transparan.png') }}"
                                        alt="Gerbang Kantor Balai Kampung Rukti Harjo"
                                        class="w-full h-auto max-h-[300px] sm:max-h-[340px] object-contain drop-shadow-[0_16px_24px_rgba(0,0,0,0.55)] filter contrast-105 transition-transform duration-500 group-hover:scale-[1.02]"
                                        loading="eager"
                                    >
                                </div>
                                
                                <!-- Realistic Ground Shadow Ellipse -->
                                <div class="w-4/5 h-3.5 mx-auto bg-black/40 blur-sm rounded-full -mt-2 pointer-events-none"></div>

                                <!-- Floating Glassmorphic Identity Tag -->
                                <div class="mt-4 p-3.5 rounded-2xl bg-black/35 backdrop-blur-xl border border-white/15 flex items-center justify-between gap-3 text-white">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-xl bg-accent/20 border border-accent/40 flex items-center justify-center text-accent shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <span class="text-xs sm:text-sm font-extrabold text-white tracking-tight truncate">Balai Kampung Rukti Harjo</span>
                                            <span class="text-[10px] sm:text-[11px] text-emerald-200/90 font-medium truncate">Pusat Pelayanan Terpadu Seputih Raman</span>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-[10px] sm:text-xs font-bold shrink-0">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        <span>Aktif Melayani</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Integrated Statistics Bento (11 Dusun Synchronized) -->
                        <div class="grid grid-cols-3 gap-2.5 w-full">
                            <!-- Bento 1: Jiwa -->
                            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 sm:p-3.5 border border-white/15 text-center flex flex-col justify-center transition hover:bg-white/15">
                                <span class="text-base sm:text-xl font-extrabold text-accent leading-none font-heading">
                                    {{ number_format($statistik['jiwa'] ?? 5990, 0, ',', '.') }} Jiwa
                                </span>
                                <span class="text-[10px] sm:text-xs text-white/80 font-medium mt-1">Total Penduduk</span>
                            </div>

                            <!-- Bento 2: KK & 11 Dusun -->
                            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 sm:p-3.5 border border-white/15 text-center flex flex-col justify-center transition hover:bg-white/15">
                                <span class="text-base sm:text-xl font-extrabold text-accent leading-none font-heading">
                                    {{ number_format($statistik['kk'] ?? 1780, 0, ',', '.') }} Kepala Keluarga
                                </span>
                                <span class="text-[10px] sm:text-xs text-white/80 font-medium mt-1">Tersebar di 11 Dusun</span>
                            </div>

                            <!-- Bento 3: Surat Terbit -->
                            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 sm:p-3.5 border border-white/15 text-center flex flex-col justify-center transition hover:bg-white/15">
                                <span class="text-base sm:text-xl font-extrabold text-accent leading-none font-heading">
                                    {{ $statistik['surat_terbit'] ?? 128 }} Surat Terbit
                                </span>
                                <span class="text-[10px] sm:text-xs text-white/80 font-medium mt-1">Pelayanan Resmi</span>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <!-- Authentic Lampung Traditional Tapis / Geometric Accent Ribbon -->
            <div class="absolute bottom-0 inset-x-0 h-1.5 bg-gradient-to-r from-emerald-800 via-accent to-emerald-800 opacity-90"></div>
        </section>

        <!-- LACAK CEPAT PENGAJUAN SECTION -->
        <section id="lacak" class="relative mt-6 lg:-mt-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 z-20">
            <div class="bg-surface rounded-card p-5 sm:p-7 border border-border-soft shadow-featured space-y-4 sm:space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 sm:gap-2">
                    <div>
                        <div class="inline-flex items-center gap-1.5 text-xs font-bold text-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <span>Lacak Cepat Pengajuan</span>
                        </div>
                        <h2 class="text-base sm:text-xl font-extrabold text-ink tracking-tight font-heading mt-0.5">
                            Cek Progres Surat Anda Secara Real-time
                        </h2>
                    </div>
                    <span class="text-[11px] sm:text-xs text-ink-muted">Tanpa harus login ke sistem</span>
                </div>

                <form method="GET" action="{{ route('landing') }}#lacak" class="flex flex-col sm:flex-row items-stretch gap-2.5 sm:gap-3">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3.5 sm:pl-4 flex items-center pointer-events-none text-ink-muted">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            name="no_pengajuan"
                            value="{{ $nomorLacak }}"
                            required
                            placeholder="Contoh: PG-20261020-0001"
                            class="w-full pl-10 sm:pl-11 pr-4 py-2.5 sm:py-3.5 rounded-xl border border-border-soft bg-canvas focus:bg-surface focus:ring-2 focus:ring-primary focus:border-primary text-xs sm:text-sm font-mono text-ink placeholder:font-sans placeholder:text-ink-muted uppercase transition"
                        >
                    </div>
                    <button
                        type="submit"
                        class="btn-primary min-h-[44px] sm:min-h-[46px] px-5 sm:px-6 py-2.5 sm:py-3 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs sm:text-sm font-bold flex items-center justify-center gap-2 active:scale-95 transition shadow-sm"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span>Lacak Surat</span>
                    </button>
                </form>

                <!-- Tampilan Hasil Pelacakan di Landing Page -->
                @if($nomorLacak !== '')
                    <div class="pt-2 border-t border-border-soft/60">
                        @if($lacakDitemukan && $hasilLacak)
                            <div class="space-y-3">
                                <div class="flex items-center gap-2 text-xs font-bold text-success">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Data Permohonan Surat Ditemukan</span>
                                </div>
                                <x-hasil-lacak-pengajuan :pengajuan="$hasilLacak" />
                            </div>
                        @else
                            <div class="p-4 sm:p-5 rounded-2xl bg-rose-50 border border-rose-200 text-center space-y-2">
                                <p class="text-sm font-bold text-rose-900">
                                    Nomor Pengajuan <span class="font-mono">{{ $nomorLacak }}</span> Tidak Ditemukan
                                </p>
                                <p class="text-xs text-rose-800 max-w-lg mx-auto">
                                    Mohon periksa kembali nomor pengajuan yang Anda masukkan. Pastikan nomor diawali dengan <span class="font-mono font-bold">PG-</span> sesuai yang tertera pada tanda terima permohonan Anda.
                                </p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </section>

        @php
            $langkahTataCara = [
                [
                    'no' => 1,
                    'judul' => 'Daftar Akun Warga',
                    'deskripsi' => 'Siapkan NIK dan nomor handphone aktif untuk membuat akun warga SIRUKTI.',
                    'badge' => 'Verifikasi KTP Warga',
                    'icon' => 'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2',
                ],
                [
                    'no' => 2,
                    'judul' => 'Ajukan & Unggah Berkas',
                    'deskripsi' => 'Pilih jenis surat dan foto dokumen asli via kamera HP dengan jelas tanpa fotokopi.',
                    'badge' => 'Unggah Dokumen Asli',
                    'icon' => 'M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z M15 13a3 3 0 11-6 0 3 3 0 016 0z',
                ],
                [
                    'no' => 3,
                    'judul' => 'Pantau Status Real-time',
                    'deskripsi' => 'Notifikasi verifikasi berkas oleh Kasi dan pengesahan Kepala Kampung terpantau langsung.',
                    'badge' => 'Pelacakan Waktu Nyata',
                    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                ],
                [
                    'no' => 4,
                    'judul' => 'Unduh Surat Resmi',
                    'deskripsi' => 'Surat bertanda tangan digital dan kode QR resmi siap diunduh dalam format PDF.',
                    'badge' => 'Sah & Tanda Tangan Digital',
                    'icon' => 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z',
                ],
            ];
        @endphp

        <!-- 2. CARA MENGAJUKAN SECTION -->
        <section id="tata-cara" class="py-10 sm:py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center text-center max-w-2xl mx-auto mb-8 sm:mb-12">
                <span class="text-xs uppercase tracking-wider text-primary font-bold bg-primary-light px-3.5 py-1 rounded-full mb-2.5">
                    Tata Cara Layanan
                </span>
                <h2 class="text-xl sm:text-3xl lg:text-4xl font-extrabold text-ink tracking-tight font-heading">
                    Cara Mengajukan Surat
                </h2>
                <p class="text-xs sm:text-base text-ink-muted mt-1.5 sm:mt-2">
                    Empat langkah mudah pengurusan administrasi warga secara mandiri dan transparan.
                </p>
            </div>

            <!-- Tampilan Mobile (< md): Timeline Vertikal Ringkas dalam 1 Kartu -->
            <div class="block md:hidden bg-surface rounded-card p-5 border border-border-soft shadow-card">
                <div class="relative pl-7 space-y-6 before:absolute before:left-3 before:top-2 before:bottom-2 before:w-0.5 before:bg-border-soft">
                    @foreach($langkahTataCara as $l)
                        <div class="relative">
                            <div class="absolute -left-7 top-0 w-6 h-6 rounded-full bg-primary text-white font-bold text-xs flex items-center justify-center ring-4 ring-primary-soft">
                                {{ $l['no'] }}
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-ink leading-tight">{{ $l['judul'] }}</h3>
                                <p class="text-xs text-ink-muted mt-1 leading-relaxed">{{ $l['deskripsi'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Tampilan Desktop (md+): Grid 4 Kolom Penuh -->
            <div class="hidden md:grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($langkahTataCara as $l)
                    <div class="bg-surface rounded-card p-6 shadow-card border border-border-soft hover:shadow-md transition flex flex-col justify-between group">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-canvas flex items-center justify-center text-primary text-xl font-extrabold mb-5 group-hover:bg-primary group-hover:text-white transition">
                                {{ $l['no'] }}
                            </div>
                            <h3 class="text-lg font-bold text-ink mb-2">{{ $l['no'] }}. {{ $l['judul'] }}</h3>
                            <p class="text-sm text-ink-muted leading-relaxed">
                                {{ $l['deskripsi'] }}
                            </p>
                        </div>
                        <div class="pt-6 flex items-center text-primary text-xs font-semibold">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $l['icon'] }}" />
                            </svg>
                            <span>{{ $l['badge'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- 3. LAYANAN SURAT SECTION (FROM DATABASE) -->
        <section id="layanan" class="py-10 sm:py-20 bg-surface/50 border-y border-border-soft">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 sm:mb-12 gap-3 sm:gap-4">
                    <div class="max-w-xl">
                        <span class="text-xs uppercase tracking-wider text-primary font-bold bg-primary-light px-3.5 py-1 rounded-full mb-2 sm:mb-3 inline-block">
                            Administrasi Terpadu
                        </span>
                        <h2 class="text-xl sm:text-3xl lg:text-4xl font-extrabold text-ink tracking-tight font-heading">
                            Layanan Surat Kami
                        </h2>
                        <p class="text-xs sm:text-base text-ink-muted mt-1.5 sm:mt-2">
                            Daftar permohonan surat keterangan yang dapat diurus secara daring dari Kampung Rukti Harjo.
                        </p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-primary bg-canvas px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl border border-border-soft">
                            {{ $layanan->count() }} Jenis Layanan Aktif
                        </span>
                    </div>
                </div>

                <!-- Tampilan Mobile (< md): Kartu Ringkas dengan Syarat Dilipat -->
                <div class="block md:hidden space-y-3">
                    @forelse ($layanan as $item)
                        <div x-data="{ openSyarat: false }" class="bg-surface rounded-2xl p-4 border border-border-soft shadow-card space-y-3">
                            <div class="flex items-start gap-3">
                                <x-badge-layanan :kode="$item->kode" variant="icon" size="md" class="shrink-0" />
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-sm font-bold text-ink leading-tight">{{ $item->nama_layanan }}</h3>
                                    <p class="text-xs text-ink-muted line-clamp-1 mt-0.5">{{ $item->deskripsi }}</p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between gap-2 pt-2 border-t border-border-soft/60">
                                <button
                                    type="button"
                                    @click="openSyarat = !openSyarat"
                                    class="inline-flex items-center gap-1 text-[11px] font-bold text-primary hover:underline active:scale-95 transition"
                                >
                                    <span x-text="openSyarat ? 'Tutup Syarat' : 'Lihat Syarat ({{ $item->syaratAktif->count() }})'">Lihat Syarat ({{ $item->syaratAktif->count() }})</span>
                                    <svg class="w-3.5 h-3.5 transform transition-transform" :class="openSyarat ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                @auth
                                    <a href="{{ auth()->user()?->role === 'warga' ? route('pengajuan.buat', ['layanan' => $item->kode]) : route('dashboard') }}" class="min-h-[44px] px-4 rounded-xl bg-primary text-white text-xs font-bold flex items-center justify-center hover:bg-primary-hover shadow-sm transition active:scale-95">
                                        Ajukan Surat
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="min-h-[44px] px-4 rounded-xl bg-primary text-white text-xs font-bold flex items-center justify-center hover:bg-primary-hover shadow-sm transition active:scale-95">
                                        Ajukan Surat
                                    </a>
                                @endauth
                            </div>

                            <!-- Accordion Syarat Layanan Mobile -->
                            <div x-show="openSyarat" x-cloak x-collapse class="pt-2 border-t border-border-soft/60">
                                <span class="text-[10px] font-bold text-ink-muted uppercase tracking-wider block mb-1.5">
                                    Dokumen Persyaratan:
                                </span>
                                <ul class="text-xs text-ink space-y-1.5 bg-canvas p-3 rounded-xl border border-border-soft/60">
                                    @forelse ($item->syaratAktif as $syarat)
                                        <li class="flex items-start gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-primary shrink-0 mt-1.5"></span>
                                            <span>{{ $syarat->nama_syarat }}</span>
                                        </li>
                                    @empty
                                        <li class="text-ink-muted italic">Tidak ada syarat khusus</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-ink-muted text-xs">
                            Belum ada layanan aktif saat ini.
                        </div>
                    @endforelse
                </div>

                <!-- Tampilan Desktop (md+): Grid 4 Kolom Penuh -->
                <div class="hidden md:grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @forelse ($layanan as $item)
                        <div class="bg-surface rounded-card p-6 shadow-card border border-border-soft hover:shadow-md transition flex flex-col justify-between">
                            <div>
                                <!-- Icon Container -->
                                <x-badge-layanan :kode="$item->kode" variant="icon" size="lg" class="mb-4" />

                                <h3 class="text-lg font-bold text-ink mb-1.5">{{ $item->nama_layanan }}</h3>
                                <p class="text-xs sm:text-sm text-ink-muted mb-5 leading-relaxed">
                                    {{ $item->deskripsi }}
                                </p>
                            </div>

                            <!-- Requirements Box -->
                            <div class="bg-canvas rounded-xl p-4 border border-border-soft">
                                <span class="text-[11px] font-bold text-ink-muted uppercase tracking-wider block mb-2">
                                    Berkas Syarat:
                                </span>
                                <ul class="text-xs text-ink space-y-1.5 mb-4">
                                    @forelse ($item->syaratAktif as $syarat)
                                        <li class="flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-primary shrink-0"></span>
                                            <span class="truncate">{{ $syarat->nama_syarat }}</span>
                                        </li>
                                    @empty
                                        <li class="text-ink-muted italic">Tidak ada syarat khusus</li>
                                    @endforelse
                                </ul>

                                @auth
                                    <a href="{{ route('dashboard') }}" class="w-full min-h-[44px] rounded-xl bg-primary text-white text-xs sm:text-sm font-bold flex items-center justify-center hover:bg-primary-hover shadow-sm transition">
                                        Ajukan Surat
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="w-full min-h-[44px] rounded-xl bg-primary text-white text-xs sm:text-sm font-bold flex items-center justify-center hover:bg-primary-hover shadow-sm transition">
                                        Ajukan Surat
                                    </a>
                                @endauth
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-8 text-ink-muted">
                            Belum ada layanan aktif saat ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- 4. JAM LAYANAN DAN KONTAK SECTION -->
        <section id="kontak" class="py-10 sm:py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-surface rounded-card p-5 sm:p-8 lg:p-12 shadow-card border border-border-soft">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-center">

                    <!-- Left: Info Jam Layanan & Lokasi -->
                    <div class="lg:col-span-7 flex flex-col gap-3 sm:gap-4">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-light text-primary w-fit text-xs font-bold">
                            <span class="w-2 h-2 rounded-full bg-primary"></span>
                            Pelayanan Kantor Terbuka
                        </div>

                        <h2 class="text-xl sm:text-3xl font-extrabold text-ink tracking-tight font-heading">
                            Jam Layanan dan Kontak
                        </h2>

                        <p class="text-xs sm:text-base text-ink-muted leading-relaxed">
                            Pemerintah Kampung Rukti Harjo siap melayani keperluan administrasi warga baik secara tatap muka maupun koordinasi daring melalui operator desa.
                        </p>

                        <!-- Jam Operasional Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mt-1 sm:mt-2">
                            <div class="bg-canvas p-3.5 sm:p-4 rounded-2xl border border-border-soft flex flex-col">
                                <span class="text-[11px] text-ink-muted uppercase font-bold tracking-wider">Senin – Kamis</span>
                                <span class="text-base sm:text-lg font-bold text-primary mt-0.5 sm:mt-1">{{ $jamLayanan['senin_kamis'] ?? '08.00 - 15.00 WIB' }}</span>
                                <span class="text-[11px] sm:text-xs text-ink-muted mt-0.5 sm:mt-1">Istirahat: 12.00 – 13.00 WIB</span>
                            </div>
                            <div class="bg-canvas p-3.5 sm:p-4 rounded-2xl border border-border-soft flex flex-col">
                                <span class="text-[11px] text-ink-muted uppercase font-bold tracking-wider">Jumat</span>
                                <span class="text-base sm:text-lg font-bold text-primary mt-0.5 sm:mt-1">{{ $jamLayanan['jumat'] ?? '08.00 - 11.30 WIB' }}</span>
                                <span class="text-[11px] sm:text-xs text-ink-muted mt-0.5 sm:mt-1">Pelayanan pagi hari</span>
                            </div>
                        </div>

                        <!-- Alamat Kantor -->
                        <div class="flex items-start gap-2.5 text-ink-muted text-xs sm:text-sm pt-1 sm:pt-2">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Balai Kampung Rukti Harjo, {{ $profilKampung['alamat'] ?? 'Jl. Merdeka No. 1' }}, Kecamatan {{ $profilKampung['kecamatan'] ?? 'Seputih Raman' }}, Kabupaten {{ $profilKampung['kabupaten'] ?? 'Lampung Tengah' }} (Kode Pos {{ $profilKampung['kode_pos'] ?? '34155' }})</span>
                        </div>
                    </div>

                    <!-- Right: Callout WhatsApp Card -->
                    <div class="lg:col-span-5 flex flex-col items-center justify-center p-6 sm:p-8 bg-canvas rounded-card text-center border border-border-soft">
                        <div class="w-16 h-16 rounded-full bg-[#25D366]/20 flex items-center justify-center text-[#1E7E34] mb-4">
                            <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.301-.15-1.782-.88-2.058-.98-.276-.1-.476-.15-.676.15-.2.301-.776.98-.952 1.18-.175.201-.351.226-.652.075-.301-.15-1.27-.468-2.42-1.493-.895-.798-1.5-1.784-1.675-2.085-.175-.3-.019-.463.131-.613.136-.135.301-.351.451-.526.15-.176.201-.301.301-.502.1-.201.05-.376-.025-.526-.075-.15-.676-1.631-.926-2.235-.244-.589-.493-.509-.676-.519-.175-.01-.376-.01-.577-.01-.2 0-.526.075-.802.376-.276.301-1.053 1.029-1.053 2.509 0 1.48 1.078 2.909 1.229 3.11.15.2 2.122 3.24 5.14 4.542.718.31 1.279.495 1.716.634.721.23 1.378.197 1.9.12.58-.088 1.782-.728 2.033-1.432.25-.703.25-1.305.175-1.431-.075-.126-.275-.201-.576-.351zM12.04 2C6.544 2 2.08 6.464 2.08 11.96c0 1.84.502 3.564 1.374 5.045L2 22l5.142-1.35c1.42.776 3.04 1.218 4.76 1.218 5.496 0 9.96-4.464 9.96-9.96C21.862 6.464 17.436 2 12.04 2zm0 18.234c-1.58 0-3.07-.42-4.364-1.155l-.313-.18-3.044.798.813-2.966-.2-.317a8.214 8.214 0 01-1.258-4.454c0-4.562 3.712-8.274 8.274-8.274 4.562 0 8.274 3.712 8.274 8.274 0 4.562-3.712 8.274-8.274 8.274z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-ink">Ada Kendala atau Pertanyaan?</h3>
                        <p class="text-xs sm:text-sm text-ink-muted mt-1.5 mb-5 max-w-sm">
                            Hubungi operator pamong desa untuk verifikasi NIK atau panduan pengisian berkas secara langsung.
                        </p>
                        <a href="https://wa.me/{{ $profilKampung['whatsapp'] ?? '6281234567890' }}" target="_blank" rel="noopener noreferrer" class="min-h-[48px] px-6 rounded-xl font-bold text-sm bg-[#25D366] text-white hover:bg-[#1EBE5D] transition shadow inline-flex items-center justify-center w-full sm:w-auto">
                            <svg class="w-5 h-5 mr-2 fill-current" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.301-.15-1.782-.88-2.058-.98-.276-.1-.476-.15-.676.15-.2.301-.776.98-.952 1.18-.175.201-.351.226-.652.075-.301-.15-1.27-.468-2.42-1.493-.895-.798-1.5-1.784-1.675-2.085-.175-.3-.019-.463.131-.613.136-.135.301-.351.451-.526.15-.176.201-.301.301-.502.1-.201.05-.376-.025-.526-.075-.15-.676-1.631-.926-2.235-.244-.589-.493-.509-.676-.519-.175-.01-.376-.01-.577-.01-.2 0-.526.075-.802.376-.276.301-1.053 1.029-1.053 2.509 0 1.48 1.078 2.909 1.229 3.11.15.2 2.122 3.24 5.14 4.542.718.31 1.279.495 1.716.634.721.23 1.378.197 1.9.12.58-.088 1.782-.728 2.033-1.432.25-.703.25-1.305.175-1.431-.075-.126-.275-.201-.576-.351zM12.04 2C6.544 2 2.08 6.464 2.08 11.96c0 1.84.502 3.564 1.374 5.045L2 22l5.142-1.35c1.42.776 3.04 1.218 4.76 1.218 5.496 0 9.96-4.464 9.96-9.96C21.862 6.464 17.436 2 12.04 2zm0 18.234c-1.58 0-3.07-.42-4.364-1.155l-.313-.18-3.044.798.813-2.966-.2-.317a8.214 8.214 0 01-1.258-4.454c0-4.562 3.712-8.274 8.274-8.274 4.562 0 8.274 3.712 8.274 8.274 0 4.562-3.712 8.274-8.274 8.274z" />
                            </svg>
                            Hubungi Petugas via WhatsApp
                        </a>
                    </div>

                </div>
            </div>
        </section>

        <!-- 5. FAQ SECTION (ACCORDION) -->
        <section id="faq" class="py-10 sm:py-16 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ activeFaq: null }">
            <div class="flex flex-col items-center text-center mb-6 sm:mb-10">
                <span class="text-[11px] sm:text-xs uppercase tracking-wider text-primary font-bold bg-primary-light px-3 py-1 rounded-full mb-2 sm:mb-3">
                    Pusat Tanya Jawab
                </span>
                <h2 class="text-xl sm:text-3xl lg:text-4xl font-extrabold text-ink tracking-tight font-heading">
                    Pertanyaan yang Sering Diajukan
                </h2>
                <p class="text-xs sm:text-base text-ink-muted mt-1.5 max-w-xl">
                    Jawaban cepat atas pertanyaan seputar mekanisme pengurusan surat digital di Kampung Rukti Harjo.
                </p>
            </div>

            <div class="space-y-2.5 sm:space-y-3.5">
                <!-- FAQ 1 -->
                <div class="bg-surface rounded-2xl p-4 sm:p-6 shadow-sm border border-border-soft transition">
                    <button type="button"
                            @click="activeFaq = (activeFaq === 1 ? null : 1)"
                            class="w-full flex items-center justify-between gap-3 text-left font-bold text-sm sm:text-lg text-ink focus:outline-none min-h-[44px]"
                            :aria-expanded="(activeFaq === 1).toString()">
                        <span>Berkas apa saja yang perlu disiapkan?</span>
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-primary shrink-0 transition-transform duration-300"
                             :class="{ 'rotate-180': activeFaq === 1 }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="activeFaq === 1"
                         x-cloak
                         x-collapse
                         class="pt-3 text-xs sm:text-sm text-ink-muted leading-relaxed border-t border-border-soft mt-3">
                        KTP, Kartu Keluarga, dan Surat Pengantar RT/RW setempat. Pastikan foto dokumen terbaca jelas, tidak terpotong, dan diambil dengan pencahayaan yang cukup langsung melalui kamera handphone Anda.
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="bg-surface rounded-2xl p-4 sm:p-6 shadow-sm border border-border-soft transition">
                    <button type="button"
                            @click="activeFaq = (activeFaq === 2 ? null : 2)"
                            class="w-full flex items-center justify-between gap-3 text-left font-bold text-sm sm:text-lg text-ink focus:outline-none min-h-[44px]"
                            :aria-expanded="(activeFaq === 2).toString()">
                        <span>Berapa lama surat selesai?</span>
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-primary shrink-0 transition-transform duration-300"
                             :class="{ 'rotate-180': activeFaq === 2 }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="activeFaq === 2"
                         x-cloak
                         x-collapse
                         class="pt-3 text-xs sm:text-sm text-ink-muted leading-relaxed border-t border-border-soft mt-3">
                        Rata-rata 1x24 jam kerja setelah berkas dinyatakan lengkap dan diverifikasi oleh Kasi Pelayanan serta ditandatangani secara digital oleh Kepala Kampung.
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="bg-surface rounded-2xl p-4 sm:p-6 shadow-sm border border-border-soft transition">
                    <button type="button"
                            @click="activeFaq = (activeFaq === 3 ? null : 3)"
                            class="w-full flex items-center justify-between gap-3 text-left font-bold text-sm sm:text-lg text-ink focus:outline-none min-h-[44px]"
                            :aria-expanded="(activeFaq === 3).toString()">
                        <span>Bagaimana jika berkas saya kurang?</span>
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-primary shrink-0 transition-transform duration-300"
                             :class="{ 'rotate-180': activeFaq === 3 }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="activeFaq === 3"
                         x-cloak
                         x-collapse
                         class="pt-3 text-xs sm:text-sm text-ink-muted leading-relaxed border-t border-border-soft mt-3">
                        Petugas akan memberikan catatan perbaikan dan status pengajuan berubah menjadi "Kurang Berkas". Anda cukup mengunggah ulang hanya berkas yang perlu diperbaiki tanpa mengulang pendaftaran dari awal.
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="bg-surface rounded-2xl p-4 sm:p-6 shadow-sm border border-border-soft transition">
                    <button type="button"
                            @click="activeFaq = (activeFaq === 4 ? null : 4)"
                            class="w-full flex items-center justify-between gap-3 text-left font-bold text-sm sm:text-lg text-ink focus:outline-none min-h-[44px]"
                            :aria-expanded="(activeFaq === 4).toString()">
                        <span>Bagaimana cara memastikan surat asli?</span>
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-primary shrink-0 transition-transform duration-300"
                             :class="{ 'rotate-180': activeFaq === 4 }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="activeFaq === 4"
                         x-cloak
                         x-collapse
                         class="pt-3 text-xs sm:text-sm text-ink-muted leading-relaxed border-t border-border-soft mt-3">
                        Setiap surat dilengkapi QR Code verifikasi resmi. Saat dipindai dengan kamera handphone, tautan akan langsung mengarah ke halaman verifikasi resmi SIRUKTI yang membuktikan keabsahan dokumen secara digital.
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- FOOTER (DARK FOREST EMERALD) -->
    <footer class="w-full bg-[#0D2616] text-white border-t border-emerald-900/50 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 lg:py-16">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 lg:gap-12">

                <!-- Col 1: Brand & Profile (md:col-span-6) -->
                <div class="md:col-span-6 flex flex-col gap-3 sm:gap-4">
                    <x-application-logo :dark="true" />
                    <p class="text-xs sm:text-sm text-emerald-100/75 max-w-xl leading-relaxed">
                        Pusat digitalisasi administrasi kependudukan dan surat menyurat resmi Pemerintah Kampung Rukti Harjo demi tata kelola kampung yang transparan, santun, dan melayani.
                    </p>
                    <div class="flex flex-col gap-2 text-xs text-emerald-200/70 pt-1">
                        <div class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-accent shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Balai Kampung Rukti Harjo, Kecamatan Seputih Raman, Kabupaten Lampung Tengah, Lampung 34155</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-accent shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Jam Pelayanan: Senin–Kamis ({{ $jamLayanan['senin_kamis'] ?? '08.00 - 15.00 WIB' }}) & Jumat ({{ $jamLayanan['jumat'] ?? '08.00 - 11.30 WIB' }})</span>
                        </div>
                        <div class="flex items-center gap-2 text-accent font-mono text-[11px] pt-1">
                            <span class="px-2 py-0.5 rounded bg-emerald-950/90 border border-emerald-800/80">Kode Wilayah Kemendagri: 18.02.13.2001</span>
                            <span class="text-emerald-300/70 hidden sm:inline">•</span>
                            <span class="text-emerald-300/80 font-sans hidden sm:inline">&ldquo;Beguwai Jejama Wawai&rdquo;</span>
                        </div>
                    </div>
                </div>

                <!-- Col 2: Layanan Mandiri (md:col-span-3, hidden on mobile for conciseness) -->
                <div class="hidden md:flex md:col-span-3 flex-col gap-3">
                    <span class="text-sm font-bold text-white tracking-wide">Layanan Mandiri</span>
                    <div class="flex flex-col gap-2 text-xs text-emerald-200/70">
                        @foreach ($layanan as $l)
                            <a href="#layanan" class="hover:text-accent transition">{{ $l->nama_layanan }}</a>
                        @endforeach
                    </div>
                </div>

                <!-- Col 3: Akses Cepat (md:col-span-3) -->
                <div class="md:col-span-3 flex flex-col gap-2.5 sm:gap-3">
                    <span class="text-xs sm:text-sm font-bold text-white tracking-wide uppercase sm:normal-case">Akses Cepat</span>
                    <div class="flex flex-col gap-2 text-xs text-emerald-200/70">
                        <a href="#tata-cara" class="hover:text-accent transition">Panduan Pengurusan Surat</a>
                        <a href="#layanan" class="hover:text-accent transition">Daftar Layanan Surat</a>
                        <a href="#kontak" class="hover:text-accent transition">Kontak & WhatsApp Desa</a>
                        <a href="#faq" class="hover:text-accent transition">Pertanyaan yang Sering Diajukan</a>
                        <a href="{{ route('lacak') }}" class="hover:text-accent transition">Lacak Status Pengajuan</a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="hover:text-white transition font-bold text-accent">Beranda Saya</a>
                        @else
                            <a href="{{ route('login') }}" class="hover:text-accent transition">Masuk Akun</a>
                            <a href="{{ route('register') }}" class="hover:text-accent transition">Daftar Warga Baru</a>
                        @endauth
                    </div>
                </div>

            </div>

            <!-- Bottom Copyright & Status -->
            <div class="mt-8 sm:mt-12 pt-5 sm:pt-6 border-t border-emerald-900/60 flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4 text-center sm:text-left text-[11px] sm:text-xs text-emerald-200/60">
                <p>© {{ date('Y') }} Pemerintah Kampung Rukti Harjo, Kecamatan Seputih Raman. Seluruh hak cipta dilindungi.</p>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-950/80 border border-emerald-800/60 text-emerald-200 text-[10px] sm:text-[11px]">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Layanan Daring Aktif
                    </span>
                    <span class="text-emerald-300/60 hidden sm:inline">Tata Naskah Dinas Desa</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Modal Panduan & Pemicu Shortcut PWA (O8) -->
    <x-pwa-install-banner />

    <!-- Registrasi Service Worker PWA (O8) -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }
    </script>
</body>
</html>
