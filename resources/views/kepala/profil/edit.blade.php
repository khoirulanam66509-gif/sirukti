<x-app-layout>
    <x-slot name="pageTitle">Profil & Tanda Tangan</x-slot>
    <x-slot name="title">Profil & TTD Kepala — SIRUKTI</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-ink tracking-tight font-heading">
                    Profil & Tanda Tangan Digital
                </h1>
                <p class="text-sm text-ink-muted mt-1">
                    Kelola identitas aparatur dan spesimen tanda tangan digital untuk pengesahan surat resmi desa
                </p>
            </div>
            <div class="hidden sm:flex items-center gap-2 text-xs font-semibold px-3 py-1.5 rounded-full bg-primary/10 text-primary border border-primary/20">
                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                <span>Kepala Kampung</span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">

        <!-- Flash Message Profil -->
        @if (session('status_profil'))
            <div class="p-4 rounded-card bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 flex-shrink-0 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <div class="text-sm font-semibold">
                    {{ session('status_profil') }}
                </div>
            </div>
        @endif

        <!-- Flash Message TTD -->
        @if (session('status_ttd'))
            <div class="p-4 rounded-card bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 flex-shrink-0 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <div class="text-sm font-semibold">
                    {{ session('status_ttd') }}
                </div>
            </div>
        @endif

        <!-- Flash Message Stempel -->
        @if (session('status_stempel'))
            <div class="p-4 rounded-card bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 flex-shrink-0 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <div class="text-sm font-semibold">
                    {{ session('status_stempel') }}
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
                    <span>Terdapat kendala pada proses penyimpanan:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-0.5 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- 1. Kartu Pengelolaan Tanda Tangan Digital -->
            <div class="bg-surface rounded-card p-5 border border-border-soft shadow-card space-y-5">
                <div class="flex items-center justify-between border-b border-border-soft pb-3">
                    <h2 class="text-base font-bold text-ink font-heading flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        <span>Tanda Tangan Digital</span>
                    </h2>
                    @if($user->file_ttd)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                            <span>Siap Digunakan</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                            <span>Belum Tersedia</span>
                        </span>
                    @endif
                </div>

                <!-- Pratinjau TTD Tersimpan -->
                @if($user->file_ttd)
                    <div class="space-y-3">
                        <span class="text-xs font-bold text-ink-muted uppercase tracking-wider block">Spesimen Aktif Saat Ini:</span>
                        <div class="p-4 rounded-2xl bg-canvas border border-border-soft flex flex-col items-center justify-center min-h-[160px] relative overflow-hidden">
                            <!-- Background checkerboard pattern untuk transparan -->
                            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#1E2A22_1px,transparent_1px)] [background-size:12px_12px]"></div>

                            <img src="{{ route('kepala.ttd.preview') }}"
                                 alt="Tanda Tangan Kepala Kampung"
                                 class="relative max-h-32 max-w-full object-contain filter drop-shadow-sm">
                        </div>

                        <div class="flex items-center justify-between text-xs text-ink-muted">
                            <span>Tersimpan di penyimpanan privat server</span>
                            <form method="POST" action="{{ route('kepala.ttd.destroy') }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tanda tangan digital ini? Anda tidak dapat menyetujui surat warga sebelum mengunggah kembali.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold hover:underline transition">
                                    Hapus Tanda Tangan
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs space-y-1">
                        <span class="font-bold block text-sm">⚠️ Tanda Tangan Belum Diunggah</span>
                        <p>
                            Surat permohonan warga tidak dapat disetujui dan diterbitkan secara digital sampai Anda mengunggah spesimen tanda tangan di bawah ini.
                        </p>
                    </div>
                @endif

                <!-- Form Unggah TTD Baru -->
                <form method="POST" action="{{ route('kepala.ttd.update') }}" enctype="multipart/form-data" class="space-y-4 pt-2 border-t border-border-soft"
                      x-data="{
                          previewUrl: null,
                          handleFileChange(event) {
                              const file = event.target.files[0];
                              if (file) {
                                  this.previewUrl = URL.createObjectURL(file);
                              } else {
                                  this.previewUrl = null;
                              }
                          }
                      }">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-ink mb-1.5">
                            {{ $user->file_ttd ? 'Unggah Tanda Tangan Pengganti' : 'Unggah Tanda Tangan Baru' }}
                        </label>

                        <!-- Box Drop Area -->
                        <div class="relative border-2 border-dashed border-border-soft hover:border-primary rounded-2xl p-4 text-center cursor-pointer bg-canvas/40 hover:bg-canvas transition group">
                            <input type="file"
                                   name="file_ttd"
                                   accept="image/png,image/jpeg"
                                   @change="handleFileChange($event)"
                                   required
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                            <!-- Tampilan Pratinjau Langsung Alpine.js jika ada file baru dipilih -->
                            <template x-if="previewUrl">
                                <div class="space-y-2 py-2">
                                    <div class="max-h-28 flex items-center justify-center">
                                        <img :src="previewUrl" alt="Pratinjau Baru" class="max-h-24 object-contain">
                                    </div>
                                    <span class="text-xs font-bold text-primary block">Berkas baru siap disimpan</span>
                                </div>
                            </template>

                            <!-- State default sebelum pilih file -->
                            <template x-if="!previewUrl">
                                <div class="space-y-2 py-3">
                                    <div class="w-10 h-10 mx-auto rounded-full bg-primary/10 text-primary flex items-center justify-center group-hover:scale-105 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                    </div>
                                    <div class="text-xs text-ink font-semibold">
                                        Pilih foto tanda tangan atau seret ke sini
                                    </div>
                                    <p class="text-[11px] text-ink-muted">
                                        Format PNG transparan / JPG, maks. 1 MB
                                    </p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold transition shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Simpan Tanda Tangan</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- 2. Kartu Pengelolaan Stempel Resmi Kampung -->
            <div class="bg-surface rounded-card p-5 border border-border-soft shadow-card space-y-5">
                <div class="flex items-center justify-between border-b border-border-soft pb-3">
                    <h2 class="text-base font-bold text-ink font-heading flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span>Stempel Resmi Kampung</span>
                    </h2>
                    @if($user->file_stempel)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                            <span>Siap Digunakan</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                            <span>Belum Tersedia</span>
                        </span>
                    @endif
                </div>

                <!-- Pratinjau Stempel Tersimpan -->
                @if($user->file_stempel)
                    <div class="space-y-3">
                        <span class="text-xs font-bold text-ink-muted uppercase tracking-wider block">Spesimen Cap Aktif:</span>
                        <div class="p-4 rounded-2xl bg-canvas border border-border-soft flex flex-col items-center justify-center min-h-[160px] relative overflow-hidden">
                            <!-- Background checkerboard pattern untuk transparan -->
                            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#1E2A22_1px,transparent_1px)] [background-size:12px_12px]"></div>

                            <img src="{{ route('kepala.stempel.preview') }}"
                                 alt="Stempel Resmi Kampung"
                                 class="relative max-h-32 max-w-full object-contain filter drop-shadow-sm">
                        </div>

                        <div class="flex items-center justify-between text-xs text-ink-muted">
                            <span>Otomatis dibubuhkan bersama TTD di surat</span>
                            <form method="POST" action="{{ route('kepala.stempel.destroy') }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus stempel resmi kampung ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold hover:underline transition">
                                    Hapus Stempel
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="p-4 rounded-xl bg-slate-50 border border-border-soft text-ink-muted text-xs space-y-1">
                        <span class="font-bold text-ink block text-sm">ℹ️ Stempel Belum Diunggah</span>
                        <p>
                            Unggah file cap stempel resmi kampung berformat PNG transparan. Cap akan otomatis menimpa tanda tangan digital pada surat yang diterbitkan.
                        </p>
                    </div>
                @endif

                <!-- Form Unggah Stempel Baru -->
                <form method="POST" action="{{ route('kepala.stempel.update') }}" enctype="multipart/form-data" class="space-y-4 pt-2 border-t border-border-soft"
                      x-data="{
                          previewUrl: null,
                          handleFileChange(event) {
                              const file = event.target.files[0];
                              if (file) {
                                  this.previewUrl = URL.createObjectURL(file);
                              } else {
                                  this.previewUrl = null;
                              }
                          }
                      }">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-ink mb-1.5">
                            {{ $user->file_stempel ? 'Unggah Stempel Pengganti' : 'Unggah Stempel Baru' }}
                        </label>

                        <!-- Box Drop Area -->
                        <div class="relative border-2 border-dashed border-border-soft hover:border-primary rounded-2xl p-4 text-center cursor-pointer bg-canvas/40 hover:bg-canvas transition group">
                            <input type="file"
                                   name="file_stempel"
                                   accept="image/png,image/jpeg"
                                   @change="handleFileChange($event)"
                                   required
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                            <!-- Tampilan Pratinjau Langsung Alpine.js jika ada file baru dipilih -->
                            <template x-if="previewUrl">
                                <div class="space-y-2 py-2">
                                    <div class="max-h-28 flex items-center justify-center">
                                        <img :src="previewUrl" alt="Pratinjau Baru" class="max-h-24 object-contain">
                                    </div>
                                    <span class="text-xs font-bold text-primary block">Berkas stempel siap disimpan</span>
                                </div>
                            </template>

                            <!-- State default sebelum pilih file -->
                            <template x-if="!previewUrl">
                                <div class="space-y-2 py-3">
                                    <div class="w-10 h-10 mx-auto rounded-full bg-primary/10 text-primary flex items-center justify-center group-hover:scale-105 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                    </div>
                                    <div class="text-xs text-ink font-semibold">
                                        Pilih foto stempel atau seret ke sini
                                    </div>
                                    <p class="text-[11px] text-ink-muted">
                                        Disarankan PNG latar transparan, maks. 1 MB
                                    </p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold transition shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Simpan Stempel Kampung</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- 3. Kartu Profil Aparatur Kepala Kampung -->
            <div class="lg:col-span-2 grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                <div class="lg:col-span-2 bg-surface rounded-card p-5 border border-border-soft shadow-card space-y-4">
                    <div class="flex items-center justify-between border-b border-border-soft pb-3">
                        <h2 class="text-base font-bold text-ink font-heading flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Identitas Resmi Penandatangan</span>
                        </h2>
                        <span class="text-xs font-semibold text-ink-muted">Tercetak di Surat</span>
                    </div>

                    <form method="POST" action="{{ route('kepala.profil.update') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs font-bold text-ink mb-1">
                                Nama Lengkap & Gelar <span class="text-rose-600">*</span>
                            </label>
                            <input type="text"
                                   name="nama"
                                   value="{{ old('nama', $user->nama) }}"
                                   required
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-border-soft bg-canvas focus:bg-surface focus:ring-2 focus:ring-primary focus:border-primary text-sm text-ink font-bold">
                            <p class="text-xs text-ink-muted mt-1">
                                Nama ini yang akan tercetak resmi di bawah kolom tanda tangan surat warga.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-ink mb-1">
                                    NIP (Nomor Induk Pegawai)
                                </label>
                                <input type="text"
                                       name="nip"
                                       value="{{ old('nip', $user->nip) }}"
                                       placeholder="Kosongkan bila bukan PNS"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-border-soft bg-canvas focus:bg-surface focus:ring-2 focus:ring-primary focus:border-primary text-sm text-ink font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-ink mb-1">
                                    Jabatan Resmi <span class="text-rose-600">*</span>
                                </label>
                                <input type="text"
                                       name="jabatan"
                                       value="{{ old('jabatan', $user->jabatan ?: 'Kepala Kampung Rukti Harjo') }}"
                                       required
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-border-soft bg-canvas focus:bg-surface focus:ring-2 focus:ring-primary focus:border-primary text-sm text-ink font-medium">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-ink mb-1">
                                Nomor WhatsApp / HP
                            </label>
                            <input type="text"
                                   name="no_hp"
                                   value="{{ old('no_hp', $user->no_hp) }}"
                                   inputmode="tel"
                                   placeholder="0812..."
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-border-soft bg-canvas focus:bg-surface focus:ring-2 focus:ring-primary focus:border-primary text-sm text-ink font-mono">
                        </div>

                        <div class="pt-2 flex items-center justify-end">
                            <button type="submit"
                                    class="min-h-[44px] px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white text-xs font-bold transition shadow-sm">
                                Simpan Profil Aparatur
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Kartu Keamanan Akun / Ganti Password -->
                <div class="bg-surface rounded-card p-5 border border-border-soft shadow-card space-y-3">
                    <div class="flex items-center justify-between border-b border-border-soft pb-2.5">
                        <h3 class="text-sm font-bold text-ink font-heading flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span>Keamanan Akun & Sandi</span>
                        </h3>
                        <span class="text-xs text-ink-muted">Aparatur</span>
                    </div>

                    <p class="text-xs text-ink-muted">
                        Jaga kerahasiaan kata sandi akun Kepala Kampung untuk mencegah penyalahgunaan otorisasi tanda tangan surat.
                    </p>

                    <div class="pt-1">
                        <a href="{{ route('password.change') }}"
                           class="min-h-[44px] inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-border-soft hover:bg-canvas text-xs font-bold text-ink transition shadow-sm">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            <span>Ubah Password Akun</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
