<x-app-layout :hide-bottom-nav="true">
    <x-slot name="pageTitle">Pengaturan Balai Kampung</x-slot>
    <x-slot name="title">Pengaturan Kampung — SIRUKTI</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-ink tracking-tight font-heading">
                    Pengaturan Kampung
                </h1>
                <p class="text-sm text-ink-muted mt-1">
                    Kelola identitas resmi, kop surat, kontak layanan, dan konfigurasi administrasi Kampung Rukti Harjo
                </p>
            </div>
            <div class="hidden sm:flex items-center gap-2 text-xs font-semibold px-3 py-1.5 rounded-full bg-primary/10 text-primary border border-primary/20">
                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                <span>Konfigurasi Sistem</span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        <!-- Flash Message Status -->
        @if (session('status'))
            <div class="p-4 rounded-card bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 flex-shrink-0 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <div class="text-sm font-semibold">
                    {{ session('status') }}
                </div>
            </div>
        @endif

        <!-- Error Message Alert -->
        @if ($errors->any())
            <div class="p-4 rounded-card bg-rose-50 border border-rose-200 text-rose-800 space-y-1 shadow-sm">
                <div class="flex items-center gap-2 font-bold text-sm">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Terdapat kendala pada isian pengaturan:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-0.5 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="form-pengaturan" method="POST" action="{{ route('kasi.pengaturan.update') }}" class="space-y-6" x-data="{ activeCategory: 'profil' }">
            @csrf

            <!-- Mobile Category Tabs (< lg) -->
            <div class="lg:hidden flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                <button type="button" @click="activeCategory = 'profil'" :class="activeCategory === 'profil' ? 'bg-primary text-white shadow-sm' : 'bg-surface text-ink-muted border border-border-soft'" class="px-3.5 py-2 rounded-full text-xs font-bold whitespace-nowrap transition">
                    Profil Kampung
                </button>
                <button type="button" @click="activeCategory = 'penomoran'" :class="activeCategory === 'penomoran' ? 'bg-primary text-white shadow-sm' : 'bg-surface text-ink-muted border border-border-soft'" class="px-3.5 py-2 rounded-full text-xs font-bold whitespace-nowrap transition">
                    Penomoran &amp; TTD
                </button>
                <button type="button" @click="activeCategory = 'kontak'" :class="activeCategory === 'kontak' ? 'bg-primary text-white shadow-sm' : 'bg-surface text-ink-muted border border-border-soft'" class="px-3.5 py-2 rounded-full text-xs font-bold whitespace-nowrap transition">
                    Kontak Layanan
                </button>
                <button type="button" @click="activeCategory = 'jam'" :class="activeCategory === 'jam' ? 'bg-primary text-white shadow-sm' : 'bg-surface text-ink-muted border border-border-soft'" class="px-3.5 py-2 rounded-full text-xs font-bold whitespace-nowrap transition">
                    Alur &amp; Jam Layanan
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- 1. Identitas Pemerintahan & Kop Surat -->
                <div class="bg-surface rounded-card p-5 border border-border-soft shadow-card space-y-4" :class="activeCategory === 'profil' ? 'block' : 'hidden lg:block'">
                    <div class="flex items-center justify-between border-b border-border-soft pb-3">
                        <h2 class="text-base font-bold text-ink font-heading flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>Kop &amp; Identitas Pemerintahan</span>
                        </h2>
                        <span class="text-xs font-semibold text-ink-muted">Format Resmi</span>
                    </div>

                    <div class="space-y-3.5">
                        <div>
                            <label class="block text-xs font-bold text-ink mb-1">
                                Nama Kampung / Desa <span class="text-rose-600">*</span>
                            </label>
                            <input type="text"
                                   name="nama_kampung"
                                   value="{{ old('nama_kampung', $profil['nama_kampung'] ?? 'Rukti Harjo') }}"
                                   required
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-border-soft bg-canvas focus:bg-surface focus:ring-2 focus:ring-primary focus:border-primary text-sm text-ink font-semibold">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-ink mb-1">
                                    Kecamatan <span class="text-rose-600">*</span>
                                </label>
                                <input type="text"
                                       name="kecamatan"
                                       value="{{ old('kecamatan', $profil['kecamatan'] ?? 'Seputih Raman') }}"
                                       required
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-border-soft bg-canvas focus:bg-surface focus:ring-2 focus:ring-primary focus:border-primary text-sm text-ink">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-ink mb-1">
                                    Kabupaten <span class="text-rose-600">*</span>
                                </label>
                                <input type="text"
                                       name="kabupaten"
                                       value="{{ old('kabupaten', $profil['kabupaten'] ?? 'Lampung Tengah') }}"
                                       required
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-border-soft bg-canvas focus:bg-surface focus:ring-2 focus:ring-primary focus:border-primary text-sm text-ink">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-ink mb-1">
                                Alamat Kantor Kampung <span class="text-rose-600">*</span>
                            </label>
                            <input type="text"
                                   name="alamat"
                                   value="{{ old('alamat', $profil['alamat'] ?? '') }}"
                                   required
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-border-soft bg-canvas focus:bg-surface focus:ring-2 focus:ring-primary focus:border-primary text-sm text-ink">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-ink mb-1">
                                Kode Pos <span class="text-rose-600">*</span>
                            </label>
                            <input type="text"
                                   name="kode_pos"
                                   value="{{ old('kode_pos', $profil['kode_pos'] ?? '') }}"
                                   required
                                   inputmode="numeric"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-border-soft bg-canvas focus:bg-surface focus:ring-2 focus:ring-primary focus:border-primary text-sm text-ink font-mono">
                        </div>
                    </div>
                </div>

                <!-- 2. Format Penomoran Surat Administrasi -->
                <div class="bg-surface rounded-card p-5 border border-border-soft shadow-card space-y-4" :class="activeCategory === 'penomoran' ? 'block' : 'hidden lg:block'">
                    <div class="flex items-center justify-between border-b border-border-soft pb-3">
                        <h2 class="text-base font-bold text-ink font-heading flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Format Penomoran Surat Resmi</span>
                        </h2>
                        <span class="text-xs font-semibold text-ink-muted">Tata Naskah</span>
                    </div>

                    <div class="space-y-3.5">
                        <div x-data="{ kode: '{{ old('kode_kampung', $surat['kode_kampung'] ?? 'RH') }}' }">
                            <label class="block text-xs font-bold text-ink mb-1">
                                Kode Singkatan Kampung <span class="text-rose-600">*</span>
                            </label>
                            <input type="text"
                                   name="kode_kampung"
                                   x-model="kode"
                                   required
                                   maxlength="10"
                                   placeholder="Contoh: RH"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-border-soft bg-canvas focus:bg-surface focus:ring-2 focus:ring-primary focus:border-primary text-sm text-ink uppercase font-bold font-mono">
                            <p class="text-xs text-ink-muted mt-1">
                                Digunakan pada struktur nomor surat: <span class="font-mono font-bold text-primary">001/<span x-text="kode.toUpperCase() || 'KODE'"></span>/SKCK/X/{{ date('Y') }}</span>
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-ink mb-1">
                                Sebutan Jabatan Penandatangan <span class="text-rose-600">*</span>
                            </label>
                            <input type="text"
                                   name="jabatan_penandatangan"
                                   value="{{ old('jabatan_penandatangan', $surat['jabatan_penandatangan'] ?? 'Kepala Kampung') }}"
                                   required
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-border-soft bg-canvas focus:bg-surface focus:ring-2 focus:ring-primary focus:border-primary text-sm text-ink">
                            <p class="text-xs text-ink-muted mt-1">
                                Dicantumkan di bagian bawah kolom tanda tangan surat (contoh: Kepala Kampung Rukti Harjo).
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 3. Kontak Pelayanan Kampung -->
                <div class="bg-surface rounded-card p-5 border border-border-soft shadow-card space-y-4" :class="activeCategory === 'kontak' ? 'block' : 'hidden lg:block'">
                    <div class="flex items-center justify-between border-b border-border-soft pb-3">
                        <h2 class="text-base font-bold text-ink font-heading flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span>Kontak Layanan Warga</span>
                        </h2>
                        <span class="text-xs font-semibold text-ink-muted">Komunikasi Publik</span>
                    </div>

                    <div class="space-y-3.5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-ink mb-1">
                                    No. Telepon Kantor
                                </label>
                                <input type="text"
                                       name="telepon"
                                       value="{{ old('telepon', $profil['telepon'] ?? '') }}"
                                       inputmode="tel"
                                       placeholder="Contoh: 081234567890"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-border-soft bg-canvas focus:bg-surface focus:ring-2 focus:ring-primary focus:border-primary text-sm text-ink">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-ink mb-1">
                                    WhatsApp Resmi Pelayanan
                                </label>
                                <input type="text"
                                       name="whatsapp"
                                       value="{{ old('whatsapp', $profil['whatsapp'] ?? '') }}"
                                       inputmode="tel"
                                       placeholder="Contoh: 6281234567890"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-border-soft bg-canvas focus:bg-surface focus:ring-2 focus:ring-primary focus:border-primary text-sm text-ink font-mono">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-ink mb-1">
                                Catatan Pelayanan Daring / Tatap Muka
                            </label>
                            <textarea name="catatan_layanan"
                                      rows="3"
                                      placeholder="Contoh: Pelayanan tatap muka dilayani pada jam kantor, permohonan daring aktif 24 jam."
                                      class="w-full px-3.5 py-2 rounded-xl border border-border-soft bg-canvas focus:bg-surface focus:ring-2 focus:ring-primary focus:border-primary text-xs text-ink">{{ old('catatan_layanan', $jamLayanan['catatan'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- 4. Jam Pelayanan & Statistik Wilayah -->
                <div class="bg-surface rounded-card p-5 border border-border-soft shadow-card space-y-4" :class="activeCategory === 'jam' ? 'block' : 'hidden lg:block'">
                    <div class="flex items-center justify-between border-b border-border-soft pb-3">
                        <h2 class="text-base font-bold text-ink font-heading flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Jam Layanan &amp; Statistik Wilayah</span>
                        </h2>
                        <span class="text-xs font-semibold text-ink-muted">Publik</span>
                    </div>

                    <div class="space-y-3.5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-ink mb-1">
                                    Jam Pelayanan (Senin - Kamis) <span class="text-rose-600">*</span>
                                </label>
                                <input type="text"
                                       name="jam_senin_kamis"
                                       value="{{ old('jam_senin_kamis', $jamLayanan['senin_kamis'] ?? '08.00 - 15.00 WIB') }}"
                                       required
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-border-soft bg-canvas focus:bg-surface focus:ring-2 focus:ring-primary focus:border-primary text-sm text-ink">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-ink mb-1">
                                    Jam Pelayanan (Jumat) <span class="text-rose-600">*</span>
                                </label>
                                <input type="text"
                                       name="jam_jumat"
                                       value="{{ old('jam_jumat', $jamLayanan['jumat'] ?? '08.00 - 11.30 WIB') }}"
                                       required
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-border-soft bg-canvas focus:bg-surface focus:ring-2 focus:ring-primary focus:border-primary text-sm text-ink">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1 border-t border-border-soft/60">
                            <div>
                                <label class="block text-xs font-bold text-ink mb-1">
                                    Total Jiwa Penduduk
                                </label>
                                <input type="number"
                                       name="jumlah_jiwa"
                                       value="{{ old('jumlah_jiwa', $statistik['jiwa'] ?? 5990) }}"
                                       min="0"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-border-soft bg-canvas focus:bg-surface focus:ring-2 focus:ring-primary focus:border-primary text-sm text-ink font-bold">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-ink mb-1">
                                    Total Kepala Keluarga (KK)
                                </label>
                                <input type="number"
                                       name="jumlah_kk"
                                       value="{{ old('jumlah_kk', $statistik['kk'] ?? 1780) }}"
                                       min="0"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-border-soft bg-canvas focus:bg-surface focus:ring-2 focus:ring-primary focus:border-primary text-sm text-ink font-bold">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-ink mb-1">
                                Sumber &amp; Periode Data Statistik
                            </label>
                            <input type="text"
                                   name="sumber_statistik"
                                   value="{{ old('sumber_statistik', $statistik['sumber'] ?? '') }}"
                                   placeholder="Contoh: Laporan Bulanan Kependudukan September 2025"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-border-soft bg-canvas focus:bg-surface focus:ring-2 focus:ring-primary focus:border-primary text-xs text-ink">
                        </div>
                    </div>
                </div>

            </div>
        </form>

        <!-- Sticky Bottom Action Bar -->
        <div class="fixed bottom-0 inset-x-0 lg:left-64 bg-surface/95 backdrop-blur-md border-t border-border-soft p-4 z-40 shadow-2xl safe-bottom">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs text-ink-muted">
                    Pastikan seluruh data identitas pemerintahan dan format surat sudah sesuai sebelum menyimpan.
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                    <button type="button"
                            onclick="document.getElementById('form-pengaturan').submit()"
                            class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold transition shadow-featured flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan Pengaturan</span>
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
