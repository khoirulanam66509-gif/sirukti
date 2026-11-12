<x-app-layout>
    <x-slot name="pageTitle">Profil Saya</x-slot>
    <x-slot name="title">Profil Saya — SIRUKTI</x-slot>

    @php
        $kolomBelum = $warga ? $warga->kolomBelumLengkap() : \App\Models\Warga::$daftarFieldWajib;
        $persentase = $warga ? $warga->persentaseKelengkapan() : 0;
        $isLengkap = $warga ? $warga->profilLengkap() : false;

        $adaKosongIdentitas = isset($kolomBelum['no_kk'])
            || isset($kolomBelum['tempat_lahir'])
            || isset($kolomBelum['tgl_lahir'])
            || isset($kolomBelum['jenis_kelamin'])
            || isset($kolomBelum['agama'])
            || isset($kolomBelum['status_perkawinan'])
            || isset($kolomBelum['pekerjaan']);

        $adaKosongDomisili = isset($kolomBelum['alamat'])
            || isset($kolomBelum['id_dusun'])
            || isset($kolomBelum['rt'])
            || isset($kolomBelum['rw']);

        // Sesuai aturan: hanya seksi yang sudah lengkap boleh dilipat
        $identitasBolehLipat = ! $adaKosongIdentitas;
        $domisiliBolehLipat = ! $adaKosongDomisili;

        $jenisKelaminVal = old('jenis_kelamin', $warga?->jenis_kelamin);
        $agamaVal = old('agama', $warga?->agama);
        $statusKawinVal = old('status_perkawinan', $warga?->status_perkawinan);
        $dusunVal = old('id_dusun', $warga?->id_dusun);
    @endphp

    <div class="space-y-6 pb-20 lg:pb-0" x-data="{ openIdentitas: true, openDomisili: {{ $adaKosongDomisili || ! $identitasBolehLipat ? 'true' : ($isLengkap ? 'true' : 'false') }} }">
        <!-- 1. Header Contextual Area -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-accent"></span>
                    <span class="text-xs uppercase tracking-wider text-ink-muted font-bold">Kependudukan & Arsip Warga</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight">Profil Saya</h1>
                <p class="text-sm text-ink-muted max-w-2xl leading-relaxed">
                    Kelola kelengkapan data kependudukan Anda untuk kemudahan permohonan surat administrasi resmi di Kampung Rukti Harjo.
                </p>
            </div>

            <div class="inline-flex items-center gap-2 bg-surface px-4 py-2 rounded-full self-start md:self-auto border border-border-soft shadow-xs">
                <svg class="w-4 h-4 text-primary flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                </svg>
                <span class="text-xs sm:text-sm text-primary font-bold">
                    @if($warga && $warga->rt && $warga->rw && $warga->dusun)
                        Terdaftar: RT {{ $warga->rt }} / RW {{ $warga->rw }} {{ $warga->dusun->nama_dusun }}
                    @else
                        Status: Domisili Belum Lengkap
                    @endif
                </span>
            </div>
        </div>

        <!-- 2. Completion Tracker Card (Ringkas & Informatif) -->
        <div class="bg-surface rounded-card p-5 sm:p-6 border border-border-soft shadow-card">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl {{ $isLengkap ? 'bg-primary-light text-primary' : 'bg-amber-100 text-amber-700' }} flex items-center justify-center shrink-0">
                        @if($isLengkap)
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
                            </svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        @endif
                    </div>

                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-lg font-bold text-ink">
                                {{ $isLengkap ? 'Profil Anda 100% Lengkap' : 'Profil Anda ' . $persentase . '% Lengkap' }}
                            </h2>
                            @if($isLengkap)
                                <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-800 border border-emerald-200 px-2.5 py-0.5 rounded-full text-xs font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Siap Mengajukan Surat
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-800 border border-amber-200 px-2.5 py-0.5 rounded-full text-xs font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    {{ count($kolomBelum) }} Kolom Belum Terisi
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-ink-muted mt-0.5">
                            {{ $isLengkap ? 'Data kependudukan terverifikasi untuk permohonan surat administrasi.' : 'Lengkapi data bertanda bintang (*) agar siap mengajukan surat secara online.' }}
                        </p>
                    </div>
                </div>

                <!-- Linear Progress Gauge -->
                <div class="flex flex-col sm:items-end w-full sm:w-56 shrink-0 space-y-1.5 pt-2 sm:pt-0 border-t sm:border-t-0 border-border-soft">
                    <div class="flex items-center justify-between w-full text-xs font-semibold">
                        <span class="text-ink-muted">Tingkat Pengisian</span>
                        <span class="text-lg font-extrabold text-primary leading-none">{{ $persentase }}%</span>
                    </div>
                    <div class="w-full h-2.5 rounded-full bg-canvas border border-border-soft overflow-hidden p-0.5">
                        <div class="h-full {{ $isLengkap ? 'bg-primary' : 'bg-accent' }} rounded-full transition-all duration-700 ease-out" style="width: {{ $persentase }}%;"></div>
                    </div>
                </div>
            </div>

            @if(!$isLengkap && count($kolomBelum) > 0)
                <!-- Daftar Kolom Kosong Ringkas -->
                <div class="mt-4 pt-3.5 border-t border-border-soft flex flex-wrap items-center gap-1.5">
                    <span class="text-xs font-semibold text-ink-muted mr-1">Perlu diisi:</span>
                    @foreach($kolomBelum as $fieldKey => $labelKolom)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-amber-50 text-amber-900 border border-amber-200">
                            {{ $labelKolom }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- 3. Main Form Slab -->
        <form id="profil-form" method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('patch')

            <div class="bg-surface rounded-card p-5 sm:p-6 md:p-8 border border-border-soft shadow-card space-y-6 md:space-y-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">

                    <!-- SEKSI 1: DATA IDENTITAS KEPENDUDUKAN -->
                    <div class="space-y-4 rounded-2xl border border-border-soft p-4 sm:p-5 lg:border-0 lg:p-0">
                        <!-- Header Seksi (Accordion hanya jika Lengkap) -->
                        <div class="w-full flex items-center justify-between pb-1 text-left">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-primary-light text-primary flex items-center justify-center font-bold shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base sm:text-lg font-bold text-ink flex items-center gap-2">
                                        Identitas Kependudukan
                                        @if($adaKosongIdentitas)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                Belum Lengkap
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                Lengkap
                                            </span>
                                        @endif
                                    </h3>
                                    <p class="text-xs text-ink-muted">Data diri sesuai dokumen resmi KTP-el</p>
                                </div>
                            </div>

                            @if($identitasBolehLipat)
                                <!-- Tombol Lipat (Hanya jika seksi lengkap) -->
                                <button
                                    type="button"
                                    @click="openIdentitas = !openIdentitas"
                                    class="lg:hidden p-1.5 rounded-lg text-ink-muted hover:text-ink hover:bg-canvas transition"
                                    title="Lipat/Buka Seksi"
                                >
                                    <svg class="w-5 h-5 transition-transform duration-200" :class="{ 'rotate-180': openIdentitas }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            @endif
                        </div>

                        <!-- Form Fields -->
                        <div x-show="openIdentitas" class="space-y-4 pt-1 lg:!block">
                            <!-- Nama Lengkap -->
                            <div class="space-y-1.5">
                                <x-input-label for="nama">
                                    Nama Lengkap <span class="text-error font-bold">*</span>
                                </x-input-label>
                                <x-text-input
                                    id="nama"
                                    name="nama"
                                    type="text"
                                    :value="old('nama', $user->nama)"
                                    required
                                    autocomplete="name"
                                    placeholder="Masukkan nama lengkap sesuai KTP"
                                />
                                <p class="text-xs text-ink-muted">Pastikan ejaan huruf persis sesuai KTP-el.</p>
                                <x-input-error :messages="$errors->get('nama')" />
                            </div>

                            <!-- NIK (Read-only System Locked - Teks Terkunci Rapi) -->
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <x-input-label value="Nomor Induk Kependudukan (NIK)" />
                                    <span class="text-[11px] text-ink-muted bg-canvas border border-border-soft px-2.5 py-0.5 rounded-full flex items-center gap-1 font-semibold">
                                        <svg class="w-3.5 h-3.5 text-ink-muted" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                                        </svg>
                                        Terkunci Sistem
                                    </span>
                                </div>
                                <div class="h-[50px] min-h-[50px] px-4 py-2 bg-canvas/80 text-ink font-mono text-base font-bold rounded-btn border border-border-soft flex items-center justify-between select-none">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-ink-muted shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        <span class="tracking-wider">{{ $warga?->nik ?? $user->username }}</span>
                                    </div>
                                    <span class="text-[11px] text-ink-muted font-normal hidden sm:inline">Identitas Utama</span>
                                </div>
                                <p class="text-xs text-ink-muted">
                                    NIK terkunci dan tidak dapat diubah mandiri. Hubungi perangkat balai desa jika terdapat kesalahan NIK.
                                </p>
                            </div>

                            <!-- Username / Nama Pengguna untuk Login -->
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <x-input-label for="username">
                                        Username Akun <span class="text-error font-bold">*</span>
                                    </x-input-label>
                                    <span class="text-[11px] text-ink-muted">Dapat digunakan untuk masuk/login</span>
                                </div>
                                <x-text-input
                                    id="username"
                                    name="username"
                                    type="text"
                                    :value="old('username', $user->username)"
                                    required
                                    autocomplete="username"
                                    placeholder="Contoh: budi_santoso"
                                    class="font-mono lowercase"
                                />
                                <p class="text-xs text-ink-muted">
                                    Gunakan 4–30 karakter (hanya huruf, angka, dan garis bawah _). Login tetap dapat memakai NIK Anda.
                                </p>
                                <x-input-error :messages="$errors->get('username')" />
                            </div>

                            <!-- Nomor KK -->
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <x-input-label for="no_kk">
                                        Nomor Kartu Keluarga (KK) <span class="text-error font-bold">*</span>
                                    </x-input-label>
                                    @if(empty($warga?->no_kk))
                                        <span class="text-[11px] text-amber-700 font-bold bg-amber-100/70 border border-amber-200 px-2 py-0.5 rounded-full">Belum Diisi</span>
                                    @else
                                        <span class="text-[11px] text-emerald-700 font-bold bg-emerald-100/70 border border-emerald-200 px-2 py-0.5 rounded-full">Terisi</span>
                                    @endif
                                </div>
                                <x-text-input
                                    id="no_kk"
                                    name="no_kk"
                                    type="text"
                                    maxlength="16"
                                    inputmode="numeric"
                                    placeholder="180201XXXXXXXXXX"
                                    :value="old('no_kk', $warga?->no_kk)"
                                    class="font-mono"
                                />
                                <p class="text-xs text-ink-muted">Wajib 16 digit angka untuk pembuatan surat pengantar & administrasi.</p>
                                <x-input-error :messages="$errors->get('no_kk')" />
                            </div>

                            <!-- Tempat & Tanggal Lahir -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <x-input-label for="tempat_lahir" value="Tempat Lahir" />
                                    <x-text-input
                                        id="tempat_lahir"
                                        name="tempat_lahir"
                                        type="text"
                                        placeholder="Contoh: Lampung Tengah"
                                        :value="old('tempat_lahir', $warga?->tempat_lahir)"
                                    />
                                    <x-input-error :messages="$errors->get('tempat_lahir')" />
                                </div>
                                <div class="space-y-1.5">
                                    <x-input-label for="tgl_lahir" value="Tanggal Lahir" />
                                    <x-text-input
                                        id="tgl_lahir"
                                        name="tgl_lahir"
                                        type="date"
                                        :value="old('tgl_lahir', $warga?->tgl_lahir?->format('Y-m-d'))"
                                    />
                                    <x-input-error :messages="$errors->get('tgl_lahir')" />
                                </div>
                            </div>

                            <!-- Jenis Kelamin & Agama (Tinggi Input Seragam h-[50px]) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <x-input-label value="Jenis Kelamin" />
                                    <div class="grid grid-cols-2 gap-2 h-[50px] min-h-[50px] p-1 bg-canvas border border-border-soft rounded-btn">
                                        <label class="h-full flex items-center justify-center font-semibold text-sm rounded-lg cursor-pointer transition select-none has-[:checked]:bg-primary has-[:checked]:text-white has-[:checked]:shadow-xs text-ink-muted hover:text-ink">
                                            <input type="radio" name="jenis_kelamin" value="L" class="sr-only" {{ $jenisKelaminVal === 'L' ? 'checked' : '' }}>
                                            <span>Laki-laki</span>
                                        </label>
                                        <label class="h-full flex items-center justify-center font-semibold text-sm rounded-lg cursor-pointer transition select-none has-[:checked]:bg-primary has-[:checked]:text-white has-[:checked]:shadow-xs text-ink-muted hover:text-ink">
                                            <input type="radio" name="jenis_kelamin" value="P" class="sr-only" {{ $jenisKelaminVal === 'P' ? 'checked' : '' }}>
                                            <span>Perempuan</span>
                                        </label>
                                    </div>
                                    <x-input-error :messages="$errors->get('jenis_kelamin')" />
                                </div>

                                <div class="space-y-1.5">
                                    <x-input-label for="agama" value="Agama" />
                                    <x-select-input id="agama" name="agama">
                                        <option value="">-- Pilih Agama --</option>
                                        @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu'] as $agm)
                                            <option value="{{ $agm }}" {{ $agamaVal === $agm ? 'selected' : '' }}>{{ $agm }}</option>
                                        @endforeach
                                    </x-select-input>
                                    <x-input-error :messages="$errors->get('agama')" />
                                </div>
                            </div>

                            <!-- Status Perkawinan & Pekerjaan -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <x-input-label for="status_perkawinan" value="Status Perkawinan" />
                                    <x-select-input id="status_perkawinan" name="status_perkawinan">
                                        <option value="">-- Pilih Status --</option>
                                        <option value="belum_kawin" {{ $statusKawinVal === 'belum_kawin' ? 'selected' : '' }}>Belum Kawin</option>
                                        <option value="kawin" {{ $statusKawinVal === 'kawin' ? 'selected' : '' }}>Kawin</option>
                                        <option value="cerai_hidup" {{ $statusKawinVal === 'cerai_hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                                        <option value="cerai_mati" {{ $statusKawinVal === 'cerai_mati' ? 'selected' : '' }}>Cerai Mati</option>
                                    </x-select-input>
                                    <x-input-error :messages="$errors->get('status_perkawinan')" />
                                </div>

                                <div class="space-y-1.5">
                                    <x-input-label for="pekerjaan" value="Pekerjaan" />
                                    <x-text-input
                                        id="pekerjaan"
                                        name="pekerjaan"
                                        type="text"
                                        placeholder="Contoh: Petani/Pekebun, Wiraswasta"
                                        :value="old('pekerjaan', $warga?->pekerjaan)"
                                    />
                                    <x-input-error :messages="$errors->get('pekerjaan')" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEKSI 2: KONTAK & WILAYAH DOMISILI -->
                    <div class="space-y-4 rounded-2xl border border-border-soft p-4 sm:p-5 lg:border-0 lg:p-0">
                        <!-- Header Seksi (Accordion hanya jika Lengkap) -->
                        <div class="w-full flex items-center justify-between pb-1 text-left">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-primary-light text-primary flex items-center justify-center font-bold shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base sm:text-lg font-bold text-ink flex items-center gap-2">
                                        Kontak & Domisili
                                        @if($adaKosongDomisili)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                Belum Lengkap
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                Lengkap
                                            </span>
                                        @endif
                                    </h3>
                                    <p class="text-xs text-ink-muted">Tujuan verifikasi & notifikasi surat</p>
                                </div>
                            </div>

                            @if($domisiliBolehLipat)
                                <!-- Tombol Lipat (Hanya jika seksi lengkap) -->
                                <button
                                    type="button"
                                    @click="openDomisili = !openDomisili"
                                    class="lg:hidden p-1.5 rounded-lg text-ink-muted hover:text-ink hover:bg-canvas transition"
                                    title="Lipat/Buka Seksi"
                                >
                                    <svg class="w-5 h-5 transition-transform duration-200" :class="{ 'rotate-180': openDomisili }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            @endif
                        </div>

                        <!-- Form Fields -->
                        <div x-show="openDomisili" class="space-y-4 pt-1 lg:!block">
                            <!-- Nomor WhatsApp / Seluler -->
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <x-input-label for="no_hp">
                                        Nomor WhatsApp / Seluler <span class="text-error font-bold">*</span>
                                    </x-input-label>
                                    <span class="text-[11px] text-primary font-bold bg-primary-light border border-primary/20 px-2 py-0.5 rounded-full flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                        Notifikasi Surat
                                    </span>
                                </div>
                                <div class="relative">
                                    <x-text-input
                                        id="no_hp"
                                        name="no_hp"
                                        type="tel"
                                        inputmode="numeric"
                                        placeholder="0812XXXXXXXX"
                                        :value="old('no_hp', $user->no_hp)"
                                        class="pl-11 font-mono"
                                        required
                                    />
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-primary">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-xs text-ink-muted">Pemberitahuan perubahan status permohonan surat dikirim ke nomor ini.</p>
                                <x-input-error :messages="$errors->get('no_hp')" />
                            </div>

                            <!-- Alamat Sesuai KTP -->
                            <div class="space-y-1.5">
                                <x-input-label for="alamat">
                                    Alamat Sesuai KTP <span class="text-error font-bold">*</span>
                                </x-input-label>
                                <x-text-input
                                    id="alamat"
                                    name="alamat"
                                    type="text"
                                    placeholder="Nama jalan, gang, atau nomor rumah"
                                    :value="old('alamat', $warga?->alamat)"
                                />
                                <p class="text-xs text-ink-muted">Tuliskan nama jalan, gang, atau nomor rumah tempat tinggal Anda.</p>
                                <x-input-error :messages="$errors->get('alamat')" />
                            </div>

                            <!-- Dusun / RT / RW -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div class="space-y-1.5 sm:col-span-1">
                                    <x-input-label for="id_dusun">
                                        Dusun <span class="text-error font-bold">*</span>
                                    </x-input-label>
                                    <x-select-input id="id_dusun" name="id_dusun">
                                        <option value="">-- Pilih Dusun --</option>
                                        @foreach($daftarDusun as $dusun)
                                            <option value="{{ $dusun->id_dusun }}" {{ (string) $dusunVal === (string) $dusun->id_dusun ? 'selected' : '' }}>
                                                {{ $dusun->nama_dusun }}
                                            </option>
                                        @endforeach
                                    </x-select-input>
                                    <x-input-error :messages="$errors->get('id_dusun')" />
                                </div>

                                <div class="space-y-1.5 sm:col-span-1">
                                    <x-input-label for="rt">
                                        RT <span class="text-error font-bold">*</span>
                                    </x-input-label>
                                    <x-text-input
                                        id="rt"
                                        name="rt"
                                        type="text"
                                        maxlength="3"
                                        inputmode="numeric"
                                        placeholder="003"
                                        :value="old('rt', $warga?->rt)"
                                        class="text-center font-mono"
                                    />
                                    <x-input-error :messages="$errors->get('rt')" />
                                </div>

                                <div class="space-y-1.5 sm:col-span-1">
                                    <x-input-label for="rw">
                                        RW <span class="text-error font-bold">*</span>
                                    </x-input-label>
                                    <x-text-input
                                        id="rw"
                                        name="rw"
                                        type="text"
                                        maxlength="3"
                                        inputmode="numeric"
                                        placeholder="001"
                                        :value="old('rw', $warga?->rw)"
                                        class="text-center font-mono"
                                    />
                                    <x-input-error :messages="$errors->get('rw')" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Desktop Action Bar (Bawah Card Form) -->
                <div class="pt-6 border-t border-border-soft hidden lg:flex items-center justify-between gap-4">
                    <div class="flex items-center gap-2 text-ink-muted text-xs">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Terakhir diperbarui: {{ $warga?->updated_at ? tgl_indo($warga->updated_at, true) : 'Belum pernah diperbarui' }}</span>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="/dashboard" class="px-6 h-12 rounded-btn bg-canvas text-ink hover:bg-gray-200 text-sm font-bold transition flex items-center justify-center gap-2 border border-border-soft">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Batal / Kembali</span>
                        </a>
                        <x-primary-button type="submit" class="h-12 px-8 shadow-featured text-sm font-bold">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            <span>Simpan Profil</span>
                        </x-primary-button>
                    </div>
                </div>
            </div>
        </form>

        <!-- 4. Keamanan Akun & Sesi Masuk -->
        <div class="bg-surface rounded-card p-5 sm:p-6 border border-border-soft shadow-card space-y-4">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-ink flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Keamanan Akun
                </h3>
                <p class="text-xs text-ink-muted">Kelola kata sandi akun dan sesi masuk aplikasi Anda.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-border-soft">
                <!-- Tautan Ubah Password -->
                <a
                    href="{{ route('password.change') }}"
                    class="flex items-center justify-between p-3.5 rounded-xl border border-border-soft bg-canvas/60 hover:bg-canvas hover:border-primary/40 transition group"
                >
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-surface border border-border-soft flex items-center justify-center text-primary group-hover:bg-primary-light transition shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-ink group-hover:text-primary transition">Ubah Kata Sandi</p>
                            <p class="text-xs text-ink-muted">Ganti kata sandi secara berkala</p>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-ink-muted group-hover:text-primary group-hover:translate-x-0.5 transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                <!-- Tombol Keluar dari Akun -->
                <form
                    method="POST"
                    action="{{ route('logout') }}"
                    onsubmit="return confirm('Apakah Anda yakin ingin keluar dari akun?');"
                    class="m-0"
                >
                    @csrf
                    <button
                        type="submit"
                        class="w-full flex items-center justify-between p-3.5 rounded-xl border border-red-200/80 bg-red-50/40 hover:bg-red-50 hover:border-red-300 transition text-left group"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-surface border border-red-200 flex items-center justify-center text-red-600 group-hover:bg-red-100/60 transition shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-red-700">Keluar dari Akun</p>
                                <p class="text-xs text-red-600/80">Akhiri sesi di perangkat ini</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-red-400 group-hover:translate-x-0.5 transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- 5. Catatan Ringkas Kerahasiaan Data -->
        <p class="text-xs text-ink-muted text-center flex items-center justify-center gap-1.5 pt-1">
            <svg class="w-3.5 h-3.5 text-primary shrink-0" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
            </svg>
            <span>Data kependudukan Anda dilindungi dan hanya digunakan untuk pelayanan administrasi resmi Kampung Rukti Harjo.</span>
        </p>

        <!-- 6. Mobile Sticky Bottom Action Bar (Melayang Bersih di Atas Bottom Nav) -->
        <div class="lg:hidden fixed bottom-[84px] sticky-above-nav inset-x-0 z-30 bg-surface/95 backdrop-blur-md border-t border-b border-border-soft p-3 shadow-modal">
            <button
                type="submit"
                form="profil-form"
                class="w-full h-12 rounded-btn bg-primary text-white font-bold text-sm shadow-featured hover:bg-primary-hover active:scale-[0.99] transition flex items-center justify-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                <span>Simpan Profil</span>
            </button>
        </div>
    </div>
</x-app-layout>
