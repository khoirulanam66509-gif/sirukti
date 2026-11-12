<x-app-layout>
    <x-slot name="pageTitle">Sistem Desain</x-slot>
    <x-slot name="title">Sistem Desain — SIRUKTI</x-slot>
    <x-slot name="header">
        <h1 class="text-xl sm:text-2xl font-extrabold text-ink tracking-tight">Pratinjau Sistem Desain & Komponen (Dev-Only)</h1>
        <p class="text-xs sm:text-sm text-ink-muted mt-1">Dokumentasi komponen UI, tombol, lencana status, dan token warna sistem</p>
    </x-slot>

    <div class="space-y-10">
        <!-- 1. Color Palette & Badges -->
        <section class="space-y-4">
            <h2 class="text-lg font-bold text-ink">1. Lencana Status Pengajuan & Berkas</h2>
            <x-card class="space-y-6">
                <div>
                    <h3 class="text-sm font-semibold text-ink-muted mb-3 uppercase tracking-wider">Status Pengajuan (6 Status)</h3>
                    <div class="flex flex-wrap gap-2.5">
                        <x-badge-status-pengajuan status="menunggu" />
                        <x-badge-status-pengajuan status="kurang_berkas" />
                        <x-badge-status-pengajuan status="diproses" />
                        <x-badge-status-pengajuan status="menunggu_ttd" />
                        <x-badge-status-pengajuan status="selesai" />
                        <x-badge-status-pengajuan status="ditolak" />
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-ink-muted mb-3 uppercase tracking-wider">Status Berkas Persyaratan (3 Status)</h3>
                    <div class="flex flex-wrap gap-2.5">
                        <x-badge-status-berkas status="menunggu" />
                        <x-badge-status-berkas status="sesuai" />
                        <x-badge-status-berkas status="perlu_perbaikan" />
                    </div>
                </div>
            </x-card>
        </section>

        <!-- 2. Buttons -->
        <section class="space-y-4">
            <h2 class="text-lg font-bold text-ink">2. Tombol Aksi (Tinggi Sentuh &ge; 48px)</h2>
            <x-card>
                <div class="flex flex-wrap gap-3">
                    <x-primary-button>Tombol Utama (Primary)</x-primary-button>
                    <x-secondary-button>Tombol Sekunder (Secondary)</x-secondary-button>
                    <x-accent-button>Tombol Aksen Emas (CTA)</x-accent-button>
                    <x-secondary-button disabled>Nonaktif (Disabled)</x-secondary-button>
                </div>
            </x-card>
        </section>

        <!-- 3. Stat Cards / KPI -->
        <section class="space-y-4">
            <h2 class="text-lg font-bold text-ink">3. Kartu Statistik & KPI</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-stat-card
                    title="Total Pengajuan"
                    value="15"
                    description="Seluruh pengajuan aktif dan arsip"
                >
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card
                    title="Menunggu TTD"
                    value="2"
                    description="Menunggu tandatangan Kepala Kampung"
                >
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card
                    title="Surat Terbit (Selesai)"
                    value="3"
                    featured="true"
                    description="Bulan Oktober 2026"
                >
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card
                    title="Warga Terdaftar"
                    value="8"
                    description="Penduduk memiliki akun SIRUKTI"
                >
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </x-slot>
                </x-stat-card>
            </div>
        </section>

        <!-- 4. Form Controls -->
        <section class="space-y-4">
            <h2 class="text-lg font-bold text-ink">4. Elemen Formulir (Tinggi Input 50px)</h2>
            <x-card class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="nama" value="Nama Lengkap Warga" />
                        <x-text-input id="nama" placeholder="Contoh: Ahmad Fauzi" />
                    </div>

                    <div>
                        <x-input-label for="nik" value="Nomor Induk Kependudukan (NIK)" />
                        <x-text-input id="nik" inputmode="numeric" placeholder="16 digit sesuai KTP" />
                        <x-input-error :messages="['NIK harus terdiri dari 16 digit angka.']" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="dusun" value="Pilih Dusun" />
                        <x-select-input id="dusun">
                            <option value="">-- Pilih Dusun Domisili --</option>
                            <option value="1">Dusun I A</option>
                            <option value="2">Dusun I B</option>
                            <option value="3">Dusun II</option>
                        </x-select-input>
                    </div>

                    <div>
                        <x-input-label for="keperluan" value="Keperluan Surat" />
                        <x-textarea-input id="keperluan" rows="2" placeholder="Tuliskan keperluan Anda..." />
                    </div>
                </div>
            </x-card>
        </section>

        <!-- 5. Stepper Component -->
        <section class="space-y-4">
            <h2 class="text-lg font-bold text-ink">5. Komponen Stepper Pengajuan (Mobile: Progres Bar, Desktop: Horizontal)</h2>
            <x-stepper :currentStep="2" />
        </section>

        <!-- 6. Responsive Table -->
        <section class="space-y-4">
            <h2 class="text-lg font-bold text-ink">6. Tabel Responsif (Kartu di Mobile &lt; md, Tabel di Desktop &ge; md)</h2>
            <x-responsive-table :headers="['No. Pengajuan', 'Nama Pemohon', 'Layanan', 'Tanggal', 'Status', 'Aksi']">
                <!-- Mobile Slot -->
                <x-slot name="mobile">
                    <x-card class="space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono font-bold text-primary">PG-20261010-0001</span>
                            <x-badge-status-pengajuan status="menunggu" />
                        </div>
                        <div>
                            <h4 class="font-bold text-base text-ink">Ahmad Fauzi</h4>
                            <p class="text-xs text-ink-muted">Pengantar SKCK &bull; 10 Oktober 2026</p>
                        </div>
                        <div class="pt-2 border-t border-border-soft flex justify-end">
                            <a href="#" class="text-sm font-bold text-primary hover:underline">Lihat Detail &rarr;</a>
                        </div>
                    </x-card>

                    <x-card class="space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono font-bold text-primary">PG-20260925-0011</span>
                            <x-badge-status-pengajuan status="selesai" />
                        </div>
                        <div>
                            <h4 class="font-bold text-base text-ink">I Made Suwarta</h4>
                            <p class="text-xs text-ink-muted">Pengantar SKCK &bull; 25 September 2026</p>
                        </div>
                        <div class="pt-2 border-t border-border-soft flex justify-end">
                            <a href="#" class="text-sm font-bold text-primary hover:underline">Unduh Surat &rarr;</a>
                        </div>
                    </x-card>
                </x-slot>

                <!-- Desktop Slot -->
                <x-slot name="desktop">
                    <tr class="hover:bg-canvas/60 transition">
                        <td class="py-3.5 px-4 first:pl-6 font-mono font-bold text-primary text-xs">PG-20261010-0001</td>
                        <td class="py-3.5 px-4 font-semibold text-ink">Ahmad Fauzi</td>
                        <td class="py-3.5 px-4 text-ink-muted">Pengantar SKCK</td>
                        <td class="py-3.5 px-4 text-xs text-ink-muted">10 Okt 2026</td>
                        <td class="py-3.5 px-4"><x-badge-status-pengajuan status="menunggu" /></td>
                        <td class="py-3.5 px-4 last:pr-6"><a href="#" class="text-xs font-bold text-primary hover:underline">Detail</a></td>
                    </tr>
                    <tr class="hover:bg-canvas/60 transition">
                        <td class="py-3.5 px-4 first:pl-6 font-mono font-bold text-primary text-xs">PG-20260925-0011</td>
                        <td class="py-3.5 px-4 font-semibold text-ink">I Made Suwarta</td>
                        <td class="py-3.5 px-4 text-ink-muted">Pengantar SKCK</td>
                        <td class="py-3.5 px-4 text-xs text-ink-muted">25 Sep 2026</td>
                        <td class="py-3.5 px-4"><x-badge-status-pengajuan status="selesai" /></td>
                        <td class="py-3.5 px-4 last:pr-6"><a href="#" class="text-xs font-bold text-primary hover:underline">Unduh</a></td>
                    </tr>
                </x-slot>
            </x-responsive-table>
        </section>

        <!-- 7. Timeline Tracker -->
        <section class="space-y-4">
            <h2 class="text-lg font-bold text-ink">7. Pelacak Riwayat Status (Timeline Tracker)</h2>
            <x-card>
                @php
                    $dummyLogs = [
                        (object)[
                            'status_baru' => 'menunggu',
                            'catatan' => 'Pengajuan dibuat oleh warga dengan 3 berkas terlampir.',
                            'created_at' => now()->subDays(2),
                            'pengguna' => (object)['nama' => 'Ahmad Fauzi', 'role' => 'warga']
                        ],
                        (object)[
                            'status_baru' => 'kurang_berkas',
                            'catatan' => 'Foto KTP buram dan terpotong pada bagian NIK. Mohon foto ulang dengan jelas.',
                            'created_at' => now()->subDay(),
                            'pengguna' => (object)['nama' => 'Budi Santoso, S.IP', 'role' => 'kasi']
                        ],
                        (object)[
                            'status_baru' => 'menunggu',
                            'catatan' => 'Warga telah mengunggah ulang Foto KTP dan mengirimkan kembali permohonan.',
                            'created_at' => now()->subHours(4),
                            'pengguna' => (object)['nama' => 'Ahmad Fauzi', 'role' => 'warga']
                        ],
                    ];
                @endphp
                <x-timeline-tracker :logs="$dummyLogs" />
            </x-card>
        </section>

        <!-- 8. Modal / Bottom Sheet Trigger -->
        <section class="space-y-4">
            <h2 class="text-lg font-bold text-ink">8. Dialog Modal (Desktop) / Bottom Sheet (Mobile)</h2>
            <x-card>
                <div x-data>
                    <x-primary-button @click="$dispatch('open-modal', 'demo-modal')">
                        Buka Contoh Dialog Modal
                    </x-primary-button>

                    <x-modal-dialog name="demo-modal" title="Konfirmasi Pengembalian Berkas">
                        <div class="space-y-4">
                            <p class="text-sm text-ink-muted leading-relaxed">
                                Apakah Anda yakin ingin mengembalikan pengajuan ini kepada pemohon untuk perbaikan berkas?
                            </p>
                            <div>
                                <x-input-label for="alasan_demo" value="Catatan Perbaikan" />
                                <x-textarea-input id="alasan_demo" placeholder="Tuliskan alasan kekurangan berkas..." />
                            </div>
                            <div class="flex justify-end gap-3 pt-3 border-t border-border-soft">
                                <x-secondary-button @click="$dispatch('close-modal', 'demo-modal')">
                                    Batal
                                </x-secondary-button>
                                <x-primary-button @click="$dispatch('close-modal', 'demo-modal')">
                                    Kirim Catatan
                                </x-primary-button>
                            </div>
                        </div>
                    </x-modal-dialog>
                </div>
            </x-card>
        </section>

        <!-- 9. Empty State -->
        <section class="space-y-4">
            <h2 class="text-lg font-bold text-ink">9. Keadaan Kosong (Empty State)</h2>
            <x-empty-state
                title="Belum Ada Surat Diajukan"
                description="Anda belum memiliki pengajuan surat aktif saat ini. Sentuh tombol di bawah untuk membuat pengajuan baru."
            >
                <x-slot name="action">
                    <x-primary-button>Ajukan Surat Pertama</x-primary-button>
                </x-slot>
            </x-empty-state>
        </section>

        <!-- 10. Helper Functions Display -->
        <section class="space-y-4">
            <h2 class="text-lg font-bold text-ink">10. Fungsi Helper Format Data</h2>
            <x-card class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-3 bg-canvas rounded-xl">
                    <span class="text-xs text-ink-muted block mb-1">samarkan_nik('1802012345670001')</span>
                    <span class="font-mono font-bold text-primary">{{ samarkan_nik('1802012345670001') }}</span>
                </div>
                <div class="p-3 bg-canvas rounded-xl">
                    <span class="text-xs text-ink-muted block mb-1">rupiah(250000) &amp; rupiah(0, true)</span>
                    <span class="font-bold text-ink">{{ rupiah(250000) }} / {{ rupiah(0, true) }}</span>
                </div>
                <div class="p-3 bg-canvas rounded-xl">
                    <span class="text-xs text-ink-muted block mb-1">tgl_indo(now(), true)</span>
                    <span class="font-semibold text-ink text-xs">{{ tgl_indo(now(), true) }}</span>
                </div>
            </x-card>
        </section>
    </div>
</x-app-layout>
