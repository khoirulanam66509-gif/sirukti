<x-app-layout :hide-bottom-nav="true">
    <x-slot name="pageTitle">Edit Data Warga</x-slot>
    <x-slot name="title">Edit Warga — SIRUKTI</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs font-semibold text-ink-muted mb-1.5">
                    <a href="{{ route('kasi.warga.index') }}" class="hover:text-primary transition">Kelola Warga</a>
                    <span>/</span>
                    <span class="text-ink">Koreksi Data</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-extrabold text-ink tracking-tight font-heading">
                    Koreksi Data: {{ $warga->pengguna?->nama ?? 'Warga' }}
                </h1>
                <p class="text-xs sm:text-sm text-ink-muted mt-0.5">
                    Perbarui dan sesuaikan informasi kependudukan warga secara resmi
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('kasi.warga.index') }}" class="min-h-[44px] px-4 rounded-btn bg-canvas text-ink font-semibold text-xs border border-border-soft hover:bg-border-soft transition inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Daftar</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        @if ($errors->any())
            <div class="p-4 rounded-card bg-rose-50 border border-rose-200 text-rose-800 space-y-1 shadow-sm">
                <div class="flex items-center gap-2 font-bold text-sm">
                    <svg class="w-5 h-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Terdapat kesalahan pengisian data:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-0.5 pl-6">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('kasi.warga.update', $warga) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- KARTU 1: IDENTITAS & AKUN -->
            <div class="rounded-card bg-surface shadow-card border border-border-soft p-5 sm:p-7 space-y-5">
                <div class="flex items-center gap-3 pb-3 border-b border-border-soft">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-ink">Identitas Akun &amp; KTP</h2>
                        <p class="text-xs text-ink-muted">Data utama kependudukan warga</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- NIK (Read Only) -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-ink">
                            Nomor Induk Kependudukan (NIK)
                        </label>
                        <input
                            type="text"
                            value="{{ $warga->nik }}"
                            disabled
                            class="w-full h-11 px-4 bg-canvas/80 text-ink-muted rounded-btn border-border-soft text-sm font-mono cursor-not-allowed"
                        >
                        <p class="text-[11px] text-ink-muted">NIK bersifat permanen dan tidak dapat diubah.</p>
                    </div>

                    <!-- Username Akun Warga -->
                    <div class="space-y-1.5">
                        <label for="username" class="block text-xs font-semibold text-ink">
                            Username Akun Login
                        </label>
                        <input
                            type="text"
                            name="username"
                            id="username"
                            maxlength="30"
                            value="{{ old('username', $warga->pengguna?->username) }}"
                            placeholder="Huruf, angka, underscore"
                            class="w-full h-11 px-4 bg-canvas rounded-btn border-border-soft text-sm text-ink focus:border-primary focus:ring-primary transition font-mono lowercase"
                        >
                        <p class="text-[11px] text-ink-muted">Warga dapat login memakai username ini atau NIK.</p>
                    </div>

                    <!-- No KK -->
                    <div class="space-y-1.5">
                        <label for="no_kk" class="block text-xs font-semibold text-ink">
                            Nomor Kartu Keluarga (KK)
                        </label>
                        <input
                            type="text"
                            name="no_kk"
                            id="no_kk"
                            maxlength="16"
                            value="{{ old('no_kk', $warga->no_kk) }}"
                            placeholder="16 digit nomor KK"
                            class="w-full h-11 px-4 bg-canvas rounded-btn border-border-soft text-sm text-ink focus:border-primary focus:ring-primary transition font-mono"
                        >
                    </div>

                    <!-- Nama Lengkap -->
                    <div class="space-y-1.5">
                        <label for="nama" class="block text-xs font-semibold text-ink">
                            Nama Lengkap Warga <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="nama"
                            id="nama"
                            required
                            value="{{ old('nama', $warga->pengguna?->nama) }}"
                            placeholder="Sesuai KTP"
                            class="w-full h-11 px-4 bg-canvas rounded-btn border-border-soft text-sm text-ink focus:border-primary focus:ring-primary transition"
                        >
                    </div>

                    <!-- No WhatsApp / HP -->
                    <div class="space-y-1.5">
                        <label for="no_hp" class="block text-xs font-semibold text-ink">
                            Nomor WhatsApp / HP
                        </label>
                        <input
                            type="tel"
                            name="no_hp"
                            id="no_hp"
                            value="{{ old('no_hp', $warga->pengguna?->no_hp) }}"
                            placeholder="Contoh: 081234567890"
                            class="w-full h-11 px-4 bg-canvas rounded-btn border-border-soft text-sm text-ink focus:border-primary focus:ring-primary transition"
                        >
                    </div>
                </div>
            </div>

            <!-- KARTU 2: DATA KELAHIRAN & STATUS -->
            <div class="rounded-card bg-surface shadow-card border border-border-soft p-5 sm:p-7 space-y-5">
                <div class="flex items-center gap-3 pb-3 border-b border-border-soft">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-ink">Data Pribadi &amp; Kelahiran</h2>
                        <p class="text-xs text-ink-muted">Informasi tanggal lahir, jenis kelamin, dan pekerjaan</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Tempat Lahir -->
                    <div class="space-y-1.5">
                        <label for="tempat_lahir" class="block text-xs font-semibold text-ink">
                            Tempat Lahir
                        </label>
                        <input
                            type="text"
                            name="tempat_lahir"
                            id="tempat_lahir"
                            value="{{ old('tempat_lahir', $warga->tempat_lahir) }}"
                            placeholder="Kota/Kabupaten"
                            class="w-full h-11 px-4 bg-canvas rounded-btn border-border-soft text-sm text-ink focus:border-primary focus:ring-primary transition"
                        >
                    </div>

                    <!-- Tanggal Lahir -->
                    <div class="space-y-1.5">
                        <label for="tgl_lahir" class="block text-xs font-semibold text-ink">
                            Tanggal Lahir
                        </label>
                        <input
                            type="date"
                            name="tgl_lahir"
                            id="tgl_lahir"
                            value="{{ old('tgl_lahir', $warga->tgl_lahir?->format('Y-m-d')) }}"
                            class="w-full h-11 px-4 bg-canvas rounded-btn border-border-soft text-sm text-ink focus:border-primary focus:ring-primary transition"
                        >
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="space-y-1.5">
                        <label for="jenis_kelamin" class="block text-xs font-semibold text-ink">
                            Jenis Kelamin
                        </label>
                        <select
                            name="jenis_kelamin"
                            id="jenis_kelamin"
                            class="w-full h-11 px-4 bg-canvas rounded-btn border-border-soft text-sm text-ink focus:border-primary focus:ring-primary transition cursor-pointer"
                        >
                            <option value="">-- Pilih --</option>
                            <option value="L" @selected(old('jenis_kelamin', $warga->jenis_kelamin) === 'L')>Laki-laki (L)</option>
                            <option value="P" @selected(old('jenis_kelamin', $warga->jenis_kelamin) === 'P')>Perempuan (P)</option>
                        </select>
                    </div>

                    <!-- Agama -->
                    <div class="space-y-1.5">
                        <label for="agama" class="block text-xs font-semibold text-ink">
                            Agama
                        </label>
                        <select
                            name="agama"
                            id="agama"
                            class="w-full h-11 px-4 bg-canvas rounded-btn border-border-soft text-sm text-ink focus:border-primary focus:ring-primary transition cursor-pointer"
                        >
                            <option value="">-- Pilih --</option>
                            @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agm)
                                <option value="{{ $agm }}" @selected(old('agama', $warga->agama) === $agm)>{{ $agm }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Perkawinan -->
                    <div class="space-y-1.5">
                        <label for="status_perkawinan" class="block text-xs font-semibold text-ink">
                            Status Perkawinan
                        </label>
                        <select
                            name="status_perkawinan"
                            id="status_perkawinan"
                            class="w-full h-11 px-4 bg-canvas rounded-btn border-border-soft text-sm text-ink focus:border-primary focus:ring-primary transition cursor-pointer"
                        >
                            <option value="">-- Pilih --</option>
                            <option value="belum_kawin" @selected(old('status_perkawinan', $warga->status_perkawinan) === 'belum_kawin')>Belum Kawin</option>
                            <option value="kawin" @selected(old('status_perkawinan', $warga->status_perkawinan) === 'kawin')>Kawin</option>
                            <option value="cerai_hidup" @selected(old('status_perkawinan', $warga->status_perkawinan) === 'cerai_hidup')>Cerai Hidup</option>
                            <option value="cerai_mati" @selected(old('status_perkawinan', $warga->status_perkawinan) === 'cerai_mati')>Cerai Mati</option>
                        </select>
                    </div>

                    <!-- Pekerjaan -->
                    <div class="space-y-1.5">
                        <label for="pekerjaan" class="block text-xs font-semibold text-ink">
                            Pekerjaan
                        </label>
                        <input
                            type="text"
                            name="pekerjaan"
                            id="pekerjaan"
                            value="{{ old('pekerjaan', $warga->pekerjaan) }}"
                            placeholder="Contoh: Petani, Wiraswasta"
                            class="w-full h-11 px-4 bg-canvas rounded-btn border-border-soft text-sm text-ink focus:border-primary focus:ring-primary transition"
                        >
                    </div>
                </div>
            </div>

            <!-- KARTU 3: ALAMAT & WILAYAH -->
            <div class="rounded-card bg-surface shadow-card border border-border-soft p-5 sm:p-7 space-y-5">
                <div class="flex items-center gap-3 pb-3 border-b border-border-soft">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-ink">Alamat Tempat Tinggal</h2>
                        <p class="text-xs text-ink-muted">Wilayah dusun dan RT/RW di Kampung Rukti Harjo</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-3 space-y-1.5">
                        <label for="alamat" class="block text-xs font-semibold text-ink">
                            Alamat Jalan / Gang / Blok
                        </label>
                        <input
                            type="text"
                            name="alamat"
                            id="alamat"
                            value="{{ old('alamat', $warga->alamat) }}"
                            placeholder="Contoh: Jl. Merdeka No. 12"
                            class="w-full h-11 px-4 bg-canvas rounded-btn border-border-soft text-sm text-ink focus:border-primary focus:ring-primary transition"
                        >
                    </div>

                    <!-- Dusun -->
                    <div class="space-y-1.5">
                        <label for="id_dusun" class="block text-xs font-semibold text-ink">
                            Dusun
                        </label>
                        <select
                            name="id_dusun"
                            id="id_dusun"
                            class="w-full h-11 px-4 bg-canvas rounded-btn border-border-soft text-sm text-ink focus:border-primary focus:ring-primary transition cursor-pointer"
                        >
                            <option value="">-- Pilih Dusun --</option>
                            @foreach ($daftarDusun as $dusun)
                                <option value="{{ $dusun->id_dusun }}" @selected(old('id_dusun', $warga->id_dusun) == $dusun->id_dusun)>
                                    {{ $dusun->nama_dusun }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- RT -->
                    <div class="space-y-1.5">
                        <label for="rt" class="block text-xs font-semibold text-ink">
                            RT (Rukun Tetangga)
                        </label>
                        <input
                            type="text"
                            name="rt"
                            id="rt"
                            maxlength="3"
                            value="{{ old('rt', $warga->rt) }}"
                            placeholder="Contoh: 003"
                            class="w-full h-11 px-4 bg-canvas rounded-btn border-border-soft text-sm text-ink focus:border-primary focus:ring-primary transition font-mono"
                        >
                    </div>

                    <!-- RW -->
                    <div class="space-y-1.5">
                        <label for="rw" class="block text-xs font-semibold text-ink">
                            RW (Rukun Warga)
                        </label>
                        <input
                            type="text"
                            name="rw"
                            id="rw"
                            maxlength="3"
                            value="{{ old('rw', $warga->rw) }}"
                            placeholder="Contoh: 001"
                            class="w-full h-11 px-4 bg-canvas rounded-btn border-border-soft text-sm text-ink focus:border-primary focus:ring-primary transition font-mono"
                        >
                    </div>
                </div>
            </div>

            <!-- Sticky Bottom Action Bar -->
            <div class="fixed bottom-0 inset-x-0 lg:left-64 z-40 bg-surface/95 backdrop-blur-md border-t border-border-soft py-3 px-4 sm:px-8 shadow-modal safe-bottom">
                <div class="max-w-7xl mx-auto flex items-center justify-end gap-2.5 sm:gap-3">
                    <a href="{{ route('kasi.warga.index') }}" class="flex-1 sm:flex-none min-h-[44px] px-5 rounded-btn bg-canvas text-ink font-semibold text-sm hover:bg-border-soft transition border border-border-soft inline-flex items-center justify-center">
                        Batal
                    </a>
                    <button type="submit" class="flex-1 sm:flex-none min-h-[44px] px-6 rounded-btn bg-primary text-white font-bold text-sm hover:bg-primary-hover shadow-sm transition inline-flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan</span>
                    </button>
                </div>
            </div>

        </form>

    </div>
</x-app-layout>
