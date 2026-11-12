<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#2F5D3A">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-lampung-tengah.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo-lampung-tengah.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-lampung-tengah.png') }}">
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="description" content="Pendaftaran Akun Warga SIRUKTI — Pelayanan Surat Digital Kampung Rukti Harjo">

    <title>Daftar Akun Baru — SIRUKTI</title>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-canvas text-ink min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8 selection:bg-primary-light selection:text-primary"
      x-data="{ showPass: false, showPassConfirm: false }">

    <div class="w-full max-w-5xl my-auto flex flex-col lg:flex-row items-stretch rounded-card overflow-hidden shadow-card border border-border-soft bg-surface">

        <!-- Sisi Kiri: Panel Pengenalan & Identitas Desa -->
        <div class="relative w-full lg:w-[48%] bg-gradient-to-b from-[#0c2414] via-[#143d21] to-[#0e2c17] p-5 sm:p-8 lg:p-12 flex flex-col justify-between overflow-hidden text-white">
            <!-- Subtle Gate Silhouette Watermark -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none select-none opacity-[0.08] mix-blend-luminosity flex items-end justify-center">
                <img src="{{ asset('images/Gerbang Balai Kampung Rukti Harjo Transparan.png') }}" alt="" class="w-[160%] max-w-none transform translate-y-6 scale-110 object-cover filter contrast-125" loading="lazy">
            </div>

            <!-- Decorative Bloom -->
            <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full bg-primary/30 blur-3xl pointer-events-none"></div>
            <div class="absolute -left-16 top-10 w-60 h-60 rounded-full bg-accent/15 blur-2xl pointer-events-none"></div>

            <!-- Bagian Atas: Brand & Lambang Resmi -->
            <div class="relative z-10 flex items-center justify-between lg:flex-col lg:items-start gap-3">
                <a href="/" class="focus:outline-none focus:ring-2 focus:ring-accent rounded-xl transition">
                    <x-application-logo :dark="true" size="md" />
                </a>

                <div class="inline-flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3 py-1 rounded-full bg-white/10 backdrop-blur-sm text-[11px] sm:text-xs font-semibold text-white/90 border border-white/10">
                    <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-accent animate-pulse"></span>
                    <span>Daftar Warga Baru</span>
                </div>
            </div>

            <!-- Bagian Tengah: Judul & Narasi Pelayanan (Desktop >= lg) -->
            <div class="relative z-10 my-8 lg:my-12 hidden lg:flex flex-col gap-3">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white leading-tight">
                    Pelayanan Digital Desa
                </h1>
                <p class="text-sm sm:text-base text-white/90 leading-relaxed max-w-md">
                    Layanan persuratan mandiri warga Kampung Rukti Harjo, Seputih Raman, Lampung Tengah. Cepat, transparan, dan resmi dari balai kampung.
                </p>

                <!-- Poin Keunggulan Layanan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4 pt-4 border-t border-white/15">
                    <div class="flex items-start gap-2.5">
                        <svg class="w-5 h-5 text-accent shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-white">Tanda Tangan Digital</span>
                            <span class="text-[11px] text-white/80">Disahkan resmi Kepala Kampung</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <svg class="w-5 h-5 text-accent shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-white">Pantau Lewat HP</span>
                            <span class="text-[11px] text-white/80">Riwayat status 24 jam</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bagian Bawah: Informasi Balai (Desktop >= lg) -->
            <div class="relative z-10 pt-4 border-t border-white/15 hidden lg:flex items-center justify-between text-xs text-white/70">
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-accent shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    </svg>
                    <span>Kecamatan Seputih Raman</span>
                </div>
                <span>Lampung Tengah</span>
            </div>
        </div>

        <!-- Sisi Kanan: Formulir Pendaftaran Warga -->
        <div class="w-full lg:w-[52%] bg-surface p-6 sm:p-8 lg:p-12 flex flex-col justify-center">
            <div class="w-full max-w-md mx-auto flex flex-col">

                <!-- Header Form -->
                <div class="flex flex-col mb-6">
                    <div class="inline-flex items-center gap-1.5 text-primary text-xs font-bold mb-1.5 tracking-wider uppercase">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                        </svg>
                        <span>Pendaftaran Warga Baru</span>
                    </div>
                    <h2 class="text-2xl font-bold text-ink tracking-tight">Buat Akun SIRUKTI</h2>
                    <p class="text-xs sm:text-sm text-ink-muted mt-1">Daftar mudah dengan NIK KTP Anda untuk mengakses seluruh layanan surat.</p>
                </div>

                <!-- Formulir -->
                <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-4">
                    @csrf

                    <!-- 1. Nama Lengkap -->
                    <div>
                        <x-input-label for="nama" value="Nama Lengkap" />
                        <x-text-input id="nama"
                                      type="text"
                                      name="nama"
                                      :value="old('nama')"
                                      required
                                      autofocus
                                      placeholder="Sesuai KTP, misal: Sri Wahyuni"
                                      class="mt-1" />
                        <x-input-error :messages="$errors->get('nama')" class="mt-1" />
                    </div>

                    <!-- 2. NIK (16 Digit) -->
                    <div>
                        <div class="flex items-center justify-between">
                            <x-input-label for="nik" value="Nomor Induk Kependudukan (NIK)" />
                            <span class="text-[11px] text-ink-muted">16 Angka KTP</span>
                        </div>
                        <x-text-input id="nik"
                                      type="text"
                                      name="nik"
                                      :value="old('nik')"
                                      required
                                      inputmode="numeric"
                                      maxlength="16"
                                      pattern="[0-9]*"
                                      placeholder="16 digit NIK tertera di KTP"
                                      class="mt-1 font-mono tracking-wider" />
                        <x-input-error :messages="$errors->get('nik')" class="mt-1" />
                    </div>

                    <!-- 3. Nomor WhatsApp / HP -->
                    <div>
                        <div class="flex items-center justify-between">
                            <x-input-label for="no_hp" value="Nomor WhatsApp / HP" />
                            <span class="text-[11px] text-ink-muted">Aktif</span>
                        </div>
                        <x-text-input id="no_hp"
                                      type="tel"
                                      name="no_hp"
                                      :value="old('no_hp')"
                                      required
                                      inputmode="tel"
                                      placeholder="081234567890"
                                      class="mt-1" />
                        <x-input-error :messages="$errors->get('no_hp')" class="mt-1" />
                    </div>

                    <!-- 4. Kata Sandi -->
                    <div>
                        <x-input-label for="password" value="Kata Sandi Baru" />
                        <div class="relative mt-1">
                            <input :type="showPass ? 'text' : 'password'"
                                   id="password"
                                   name="password"
                                   required
                                   autocomplete="new-password"
                                   placeholder="Minimal 8 karakter"
                                   class="w-full h-[50px] pl-4 pr-12 rounded-btn bg-canvas text-ink border border-border-soft text-sm focus:bg-surface focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition" />
                            <button type="button"
                                    @click="showPass = !showPass"
                                    class="absolute right-3.5 top-1/2 -translate-y-1/2 text-ink-muted hover:text-ink focus:outline-none p-1"
                                    aria-label="Lihat kata sandi">
                                <svg x-show="!showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPass" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <!-- 5. Konfirmasi Kata Sandi -->
                    <div>
                        <x-input-label for="password_confirmation" value="Konfirmasi Kata Sandi" />
                        <div class="relative mt-1">
                            <input :type="showPassConfirm ? 'text' : 'password'"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   required
                                   autocomplete="new-password"
                                   placeholder="Ketik ulang kata sandi baru"
                                   class="w-full h-[50px] pl-4 pr-12 rounded-btn bg-canvas text-ink border border-border-soft text-sm focus:bg-surface focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition" />
                            <button type="button"
                                    @click="showPassConfirm = !showPassConfirm"
                                    class="absolute right-3.5 top-1/2 -translate-y-1/2 text-ink-muted hover:text-ink focus:outline-none p-1"
                                    aria-label="Lihat konfirmasi kata sandi">
                                <svg x-show="!showPassConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassConfirm" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="pt-3 flex flex-col gap-3">
                        <x-primary-button class="w-full">
                            Daftar Akun Sekarang
                        </x-primary-button>

                        <div class="text-center text-xs text-ink-muted">
                            Sudah memiliki akun warga?
                            <a href="{{ route('login') }}" class="font-bold text-primary hover:underline">
                                Masuk ke akun Anda
                            </a>
                        </div>

                        <div class="pt-2 flex flex-wrap items-center justify-center gap-4 border-t border-border-soft/60 mt-1">
                            <a href="{{ route('lacak') }}" class="text-xs font-semibold text-primary hover:underline transition inline-flex items-center gap-1 min-h-[36px]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Lacak Pengajuan
                            </a>
                            <span class="text-border-soft">•</span>
                            <button
                                type="button"
                                @click="$dispatch('pwa-install-request')"
                                class="text-xs font-semibold text-ink-muted hover:text-primary transition inline-flex items-center gap-1 min-h-[36px]"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                Instal
                            </button>
                            <span class="text-border-soft">•</span>
                            <a href="/" class="text-xs font-semibold text-ink-muted hover:text-primary transition inline-flex items-center gap-1 min-h-[36px]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <x-toast />
    <x-pwa-install-banner />
</body>
</html>
