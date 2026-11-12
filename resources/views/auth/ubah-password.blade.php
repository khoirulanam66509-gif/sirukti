<x-app-layout>
    <x-slot name="pageTitle">Ubah Password</x-slot>
    <x-slot name="title">Ubah Kata Sandi — SIRUKTI</x-slot>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-primary-light text-primary flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-ink tracking-tight">Ubah Kata Sandi Akun</h1>
                <p class="text-xs sm:text-sm text-ink-muted">Perbarui kata sandi akun Anda secara berkala demi keamanan data</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto py-2 sm:py-4" x-data="{ showOld: false, showNew: false, showConfirm: false }">

        @if ($wajibGanti)
            <div class="mb-6 p-4 rounded-card bg-status-kurang_berkas-bg text-status-kurang_berkas-text border border-status-kurang_berkas-dot/30 shadow-sm flex items-start gap-3">
                <svg class="w-6 h-6 text-status-kurang_berkas-dot shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="flex flex-col">
                    <span class="font-bold text-sm">Wajib Mengganti Kata Sandi</span>
                    <span class="text-xs mt-0.5 leading-relaxed">
                        Akun Anda baru saja di-reset oleh Kasi Pelayanan atau merupakan sandi sementara. Demi keamanan data Anda, silakan buat kata sandi baru sebelum melanjutkan aktivitas di SIRUKTI.
                    </span>
                </div>
            </div>
        @endif

        <x-card class="shadow-card border border-border-soft p-5 sm:p-7">

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <!-- 1. Kata Sandi Saat Ini -->
                <div>
                    <div class="flex items-center justify-between">
                        <x-input-label for="current_password" value="Kata Sandi Saat Ini" />
                        @if ($wajibGanti)
                            <span class="text-[11px] text-ink-muted">Masukkan sandi sementara</span>
                        @endif
                    </div>
                    <div class="relative mt-1">
                        <input :type="showOld ? 'text' : 'password'"
                               id="current_password"
                               name="current_password"
                               required
                               autocomplete="current-password"
                               placeholder="Ketik kata sandi saat ini"
                               class="w-full h-[50px] pl-4 pr-12 rounded-btn bg-canvas text-ink border border-border-soft text-sm focus:bg-surface focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition" />
                        <button type="button"
                                @click="showOld = !showOld"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-ink-muted hover:text-ink focus:outline-none p-1"
                                aria-label="Lihat kata sandi">
                            <svg x-show="!showOld" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showOld" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('current_password')" class="mt-1" />
                </div>

                <!-- 2. Kata Sandi Baru -->
                <div>
                    <x-input-label for="password" value="Kata Sandi Baru" />
                    <div class="relative mt-1">
                        <input :type="showNew ? 'text' : 'password'"
                               id="password"
                               name="password"
                               required
                               autocomplete="new-password"
                               placeholder="Minimal 8 karakter baru"
                               class="w-full h-[50px] pl-4 pr-12 rounded-btn bg-canvas text-ink border border-border-soft text-sm focus:bg-surface focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition" />
                        <button type="button"
                                @click="showNew = !showNew"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-ink-muted hover:text-ink focus:outline-none p-1"
                                aria-label="Lihat kata sandi">
                            <svg x-show="!showNew" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showNew" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <!-- 3. Konfirmasi Kata Sandi Baru -->
                <div>
                    <x-input-label for="password_confirmation" value="Konfirmasi Kata Sandi Baru" />
                    <div class="relative mt-1">
                        <input :type="showConfirm ? 'text' : 'password'"
                               id="password_confirmation"
                               name="password_confirmation"
                               required
                               autocomplete="new-password"
                               placeholder="Ketik ulang kata sandi baru"
                               class="w-full h-[50px] pl-4 pr-12 rounded-btn bg-canvas text-ink border border-border-soft text-sm focus:bg-surface focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition" />
                        <button type="button"
                                @click="showConfirm = !showConfirm"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-ink-muted hover:text-ink focus:outline-none p-1"
                                aria-label="Lihat kata sandi">
                            <svg x-show="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showConfirm" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>

                <!-- Keamanan Info -->
                <div class="bg-canvas rounded-xl p-3.5 border border-border-soft flex items-start gap-2.5 text-xs text-ink-muted">
                    <svg class="w-4 h-4 text-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Kata sandi harus terdiri dari minimal 8 karakter dan berbeda dari kata sandi sebelumnya.</span>
                </div>

                <!-- Tombol Aksi -->
                <div class="pt-3 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                    @if (! $wajibGanti)
                        <a href="{{ url()->previous() ?: route('profile.edit') }}" class="w-full sm:w-auto min-h-[48px] px-6 rounded-btn border border-border-soft bg-surface hover:bg-canvas text-ink font-semibold text-sm inline-flex items-center justify-center transition">
                            Batal
                        </a>
                    @endif

                    <x-primary-button class="w-full sm:w-auto min-h-[48px]">
                        Simpan Kata Sandi Baru
                    </x-primary-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
