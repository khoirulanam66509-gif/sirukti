# Tasklist SIRUKTI

> Log progres proyek. Centang `[x]` hanya setelah task terverifikasi. Jangan hapus riwayat. Aturan lengkap: `.agents/task-instruction.md` §0.
> Status awal (Oktober 2026): belum ada fase yang dikerjakan. Kerjakan satu fase pada satu waktu dan tunggu perintah pengguna sebelum fase berikutnya.
> QA responsif selalu diuji di **360px, 768px, dan 1280px** (tanpa scroll horizontal, target sentuh ≥ 44px).
> Sumber desain: proyek Google Stitch **`UTS Dekstop`** (hanya desktop) via MCP. Mobile diturunkan dari desktop menurut `PRD.md` §9.4 dan dibangun lebih dahulu. Pemetaan layar ada pada bagian "Pemetaan Layar Stitch" di bawah. Elemen Stitch di luar PRD tidak diimplementasikan dan dicatat di fase terkait.

## Pemetaan Layar Stitch

Isi/koreksi pada Fase 0 setelah daftar layar proyek `UTS Dekstop` dibaca lewat MCP. Kolom "Nama layar Stitch" berisi nama yang diharapkan (`{Kode} {Nama} — Desktop`); ganti dengan nama sebenarnya bila berbeda, dan beri tanda `[x]` bila layar ditemukan.

| Layar PRD (§9.3) | Nama layar Stitch (diharapkan) | Ada? |
|---|---|---|
| L1 Landing | L1 Landing — Desktop | [x] |
| L2 Daftar | L2 Daftar — Desktop | [x] |
| L3 Masuk | L3 Masuk — Desktop | [x] |
| L4 Verifikasi Surat (valid) | L4 Verifikasi Surat — Valid — Desktop | [x] |
| L4 Verifikasi Surat (tidak valid) | L4 Verifikasi Surat — Tidak Valid — Desktop | [x] |
| W1 Beranda Warga | W1 Beranda Warga — Desktop | [x] |
| W2 Profil Saya | W2 Profil Saya — Desktop | [x] |
| W3 Ajukan Surat langkah 1 | W3 Ajukan Surat — Langkah 1 Pilih Layanan — Desktop | [x] |
| W3 Ajukan Surat langkah 2 | W3 Ajukan Surat — Langkah 2 Isi Data — Desktop | [x] |
| W3 Ajukan Surat langkah 3 | W3 Ajukan Surat — Langkah 3 Unggah Berkas — Desktop | [x] |
| W3 Ajukan Surat langkah 4 | W3 Ajukan Surat — Langkah 4 Tinjau dan Kirim — Desktop | [x] |
| W4 Riwayat Pengajuan | W4 Riwayat Pengajuan — Desktop | [x] |
| W5 Detail (kurang berkas) | W5 Detail Pengajuan — Kurang Berkas — Desktop | [x] |
| W5 Detail (selesai) | W5 Detail Pengajuan — Selesai — Desktop | [x] |
| U1 Ubah Password | U1 Ubah Password — Desktop | [x] |
| K1 Beranda Kasi | K1 Beranda Kasi — Desktop | [x] |
| K2 Antrean Pengajuan | K2 Antrean Pengajuan — Desktop | [x] |
| K3 Detail dan Verifikasi | K3 Detail dan Verifikasi — Desktop | [x] |
| K3 Dialog Perlu Perbaikan | K3 Detail dan Verifikasi — Dialog Perlu Perbaikan — Desktop | [x] |
| K4 Pratinjau Surat (Draft) | K4 Pratinjau Surat (Draft) — Desktop | [x] |
| K5 Kelola Warga | K5 Kelola Warga — Desktop | [x] |
| K5 Dialog Reset Password | K5 Kelola Warga — Dialog Reset Password — Desktop | [x] |
| K6 Persyaratan Layanan | K6 Persyaratan Layanan — Desktop | [x] |
| K7 Pengaturan Kampung | K7 Pengaturan Kampung — Desktop | [x] |
| K8 Arsip dan Laporan | K8 Arsip dan Laporan — Desktop | [x] |
| P1 Beranda Kepala | P1 Beranda Kepala — Desktop | [x] |
| P2 Detail Persetujuan | P2 Detail Persetujuan — Desktop | [x] |
| P2 Dialog Kembalikan | P2 Detail Persetujuan — Dialog Kembalikan — Desktop | [x] |
| P3 Profil dan Tanda Tangan | P3 Profil dan Tanda Tangan — Desktop | [x] |
| E1 Halaman Error | E1 Halaman Error 404 — Desktop | [x] |

Catatan temuan (elemen Stitch di luar PRD, layar hilang, selisih nama):
- Ditemukan 30 dari 30 layar PRD (100% lengkap, tanpa ada layar yang hilang atau perbedaan nama).
- Layar tambahan di Stitch di luar 30 PRD: `KP-01 Beranda — Desktop` (draft alternatif beranda desa), serta 2 aset gambar (`image.png` dan foto panorama sawah `Panoramic lush green rice fields in rural Indonesia at sunrise...`).
- Elemen di luar PRD pada Stitch: beberapa dashboard memuat widget metrik/tren persentase sekunder, search bar global, dan ikon lonceng notifikasi. Mengikuti PRD §4.5 dan §9.2, elemen di luar PRD tidak diimplementasikan (v1.0 fokus kartu angka dan daftar tanpa grafik).
- Versi Tailwind terpasang: Tailwind CSS v3.4.19 (memakai `tailwind.config.js` dengan `@tailwind base; @tailwind components; @tailwind utilities;`).

## Fase 0 — Setup Project
- [x] Buat project Laravel 12 (`composer create-project laravel/laravel:^12.0`), pastikan PHP 8.2 XAMPP terpakai; catat versi terpasang
- [x] Konfigurasi `.env`: koneksi MySQL XAMPP ke database yang sudah dibuat pengguna, `SESSION_DRIVER=file`, `CACHE_STORE=file`, `QUEUE_CONNECTION=sync`, locale `id`, timezone `Asia/Jakarta`
- [x] Setel `'timezone' => '+07:00'` pada koneksi mysql di `config/database.php`
- [x] Siapkan database terpisah untuk test dan `.env.testing` (MySQL/MariaDB, bukan SQLite, karena trigger dan stored procedure)
- [x] Verifikasi koneksi database (`php artisan migrate:status`)
- [x] Install Laravel Breeze (stack Blade + Alpine.js)
- [x] Periksa versi Tailwind yang terpasang dan catat di tasklist
- [x] Install `barryvdh/laravel-dompdf` dan pustaka QR code; pastikan ekstensi `gd` dan `mbstring` aktif di `php.ini` XAMPP
- [x] Setup font Plus Jakarta Sans (self-hosted via Vite), ukuran dasar 16px
- [x] Verifikasi MCP Stitch terhubung dan proyek `UTS Dekstop` ditemukan (bila tidak, berhenti dan laporkan)
- [x] Baca daftar layar proyek, isi tabel "Pemetaan Layar Stitch", dan catat layar yang hilang atau elemen di luar PRD; laporkan ke pengguna sebelum Fase 1
- [x] Salin dokumen ke `.agents/` (PRD, schema-database, task-instruction, tasklist)
- [x] Update `.agents/tasklist.md`

> ✅ Fase 0 selesai — 19 Oktober 2026

## Fase 1 — Database
- [x] Hapus migration bawaan yang tidak dipakai (`users`, `cache`, `jobs`)
- [x] Buat 11 migration sesuai urutan `schema-database.md` §9, termasuk FK, PK komposit (`counter_surat`), UNIQUE, dan index (§7.4)
- [x] Tambah constraint CHECK (NIK 16 digit, `no_kk`, `ukuran_kb > 0`, `nomor_terakhir >= 0`)
- [x] Migration trigger `trg_pengajuan_ai`, `trg_pengajuan_bu`, `trg_pengajuan_au` (dengan `down()`)
- [x] Migration stored procedure `sp_ambil_nomor_surat` dan `sp_rekap_periode` (dengan `down()`)
- [x] Migration view `v_antrean_pengajuan` dan `v_rekap_bulanan` (dengan `down()`)
- [x] Buat model + relationship (`$table`, `$primaryKey`); `Pengguna` sebagai Authenticatable; `Pengajuan` memakai SoftDeletes; `StatusLog` tanpa `updated_at`
- [x] Method `Warga::profilLengkap()`
- [x] Buat seeder: `DusunSeeder`, `LayananSeeder` (4 layanan + syarat), `PengaturanSeeder`, `PenggunaSeeder`, `PengajuanSeeder`
- [x] Jalankan `php artisan migrate:fresh --seed`, verifikasi data
- [x] Test PHPUnit (MySQL): struktur tabel, relasi, CHECK NIK, FK restrict, trigger mencatat `status_log`, trigger menolak ubah status final, trigger mengisi `tgl_diproses`/`tgl_selesai`
- [x] Test PHPUnit: `sp_ambil_nomor_surat` (berurutan, tahun baru mulai dari 1, tidak ada nomor ganda), `sp_rekap_periode`, kedua view
- [x] Update `.agents/tasklist.md`

> ✅ Fase 1 selesai — 19 Oktober 2026

## Fase 2 — Desain dan Layout
- [x] Ambil design system/token dari proyek Stitch `UTS Dekstop` via MCP (warna, font, radius, bayangan, jarak) dan ekstrak satu kali ke konfigurasi tema Tailwind; bila token tidak tersedia pakai nilai cadangan PRD §9.2 dan catat
- [x] Komponen Blade: button, card, input (label + galat), select, textarea, badge status pengajuan, badge status berkas, daftar-responsif (kartu di mobile, tabel mulai `md`), pagination, modal konfirmasi, toast, empty state, stepper, timeline vertikal, ikon, kartu statistik
- [x] Layout guest/publik (navbar sederhana + footer)
- [x] Layout aplikasi: **bottom navigation** di layar kecil (safe-area; item per role sesuai PRD §9.4), sidebar kiri mulai `lg` mengikuti layar Stitch Desktop, topbar; menu menyesuaikan role (warga, kasi, kepala)
- [x] Helper: `tgl_indo()`, `rupiah()`, `samarkan_nik()`
- [x] Halaman preview komponen dev-only (`/design-system`, hanya saat `APP_DEBUG=true`)
- [x] QA responsif: layout, bottom nav, dan komponen di 360px, 768px, 1280px; cocokkan 1280px dengan layar Stitch Desktop dan 360px dengan PRD §9.4
- [x] Update `.agents/tasklist.md`

> ✅ Fase 2 selesai — 19 Oktober 2026

## Fase 3 — Landing Page Publik (A1)
- [x] Layar (Stitch → mobile PRD §9.4): L1 Landing (hero, 4 langkah, 4 layanan + syarat dari database, statistik dari `pengaturan`, jam layanan, kontak dengan tautan WhatsApp kantor, FAQ accordion)
- [x] Tombol Masuk dan Daftar; tautan menuju beranda sesuai role bila sudah login
- [x] Meta tag dan judul halaman bahasa Indonesia
- [x] Test: halaman memuat 4 layanan dan syaratnya, statistik berasal dari `pengaturan`
- [x] QA responsif: landing di 360px, 768px, 1280px; cocokkan 1280px dengan layar Stitch Desktop dan 360px dengan PRD §9.4
- [x] Update `.agents/tasklist.md`

> ✅ Fase 3 selesai — 20 Oktober 2026

## Fase 4 — Autentikasi, Registrasi Warga, dan Role (A2–A4)
- [x] Ubah provider auth ke model `Pengguna`; login memakai `username` (warga: NIK); nonaktifkan "ingat saya"
- [x] Layar (Stitch → mobile PRD §9.4): L2 Daftar (nama, NIK 16 digit, no. HP/WhatsApp, password); membuat `pengguna` (role warga, username = NIK) dan `warga` dalam satu transaksi
- [x] Layar (Stitch → mobile PRD §9.4): L3 Masuk; akun nonaktif ditolak; rate limiting login
- [x] Middleware `role` (warga, kasi, kepala) + alias di `bootstrap/app.php`; redirect setelah login sesuai role
- [x] Hapus rute lupa password publik bawaan Breeze
- [x] Layar (Stitch → mobile PRD §9.4): U1 Ubah Password; paksa ubah password bila `wajib_ganti_password`
- [x] Perintah Artisan untuk membuat akun `kasi`/`kepala`
- [x] Halaman error 403/404/419/500 berbahasa Indonesia (E1)
- [x] Test: registrasi (NIK ganda/invalid ditolak), login benar/salah, akses per role, wajib ganti password, akun nonaktif
- [x] QA responsif: Daftar, Masuk, Ubah Password di 360px, 768px, 1280px; cocokkan 1280px dengan layar Stitch Desktop dan 360px dengan PRD §9.4
- [x] Update `.agents/tasklist.md`

> ✅ Fase 4 selesai — 20 Oktober 2026

## Fase 5 — Profil Warga (W1)
- [x] Layar (Stitch → mobile PRD §9.4): W2 Profil Saya (data diri, alamat, dusun, RT/RW, no. KK); form bertahap/accordion di mobile
- [x] Indikator kelengkapan profil; arahkan ke profil bila warga mencoba mengajukan sebelum lengkap
- [x] NIK tidak dapat diubah warga (koreksi lewat Kasi)
- [x] Test: validasi profil, kelengkapan, warga tidak dapat mengubah NIK/profil warga lain
- [x] QA responsif: halaman profil di 360px, 768px, 1280px; cocokkan 1280px dengan layar Stitch Desktop dan 360px dengan PRD §9.4
- [x] Update `.agents/tasklist.md`

> ✅ Fase 5 selesai — 20 Oktober 2026

## Fase 6 — Pengajuan Surat oleh Warga (W2, W3)
- [x] Layar (Stitch → mobile PRD §9.4): W3 Ajukan Surat (stepper 4 langkah: pilih layanan → isi data → unggah berkas → tinjau dan kirim)
- [x] `config/layanan.php` + Form Request validasi `data_isian` per layanan (lihat `schema-database.md` §8); isian awal otomatis dari profil bila relevan
- [x] `BerkasService`: validasi MIME/ukuran, simpan ke disk privat, hapus berkas bila transaksi gagal
- [x] Unggah di browser: ambil foto (kamera) atau pilih galeri, kompresi gambar (Canvas), pratinjau, tombol ganti/hapus
- [x] `PengajuanService::buat()`: transaksi DB, nomor `PG-YYYYMMDD-XXXX`, semua syarat wajib terisi, aturan satu pengajuan aktif per layanan, `id_pengubah` terisi
- [x] Rute pengunduhan/pratinjau berkas lewat controller + Policy (tanpa URL publik)
- [x] Test: syarat wajib kurang ditolak, tipe/ukuran salah ditolak, pengajuan aktif ganda ditolak, profil belum lengkap ditolak, berkas tidak bisa dibuka warga lain, `status_log` tercatat, rollback penuh bila gagal
- [x] QA responsif: stepper dan unggah berkas di 360px, 768px, 1280px; cocokkan 1280px dengan layar Stitch Desktop dan 360px dengan PRD §9.4
- [x] Update `.agents/tasklist.md`

> ✅ Fase 6 selesai — 20 Oktober 2026

## Fase 7 — Status, Riwayat, dan Unggah Ulang (W4–W6)
- [x] Layar (Stitch → mobile PRD §9.4): W4 Riwayat (kartu di mobile, tabel `md`+, filter chip status, pagination)
- [x] Layar (Stitch → mobile PRD §9.4): W5 Detail Pengajuan (timeline dari `status_log`, daftar berkas + status + alasan, data isian)
- [x] Unggah ulang: ganti hanya berkas `perlu_perbaikan`; status berkas kembali `menunggu`
- [x] Tombol Kirim Ulang (aktif bila semua berkas yang perlu perbaikan sudah diganti): `kurang_berkas` → `menunggu`
- [x] Unduh surat PDF bila status `selesai` (Policy kepemilikan)
- [x] Test: unggah ulang hanya untuk berkas `perlu_perbaikan`, kirim ulang mengubah status, riwayat hanya milik sendiri, unduh surat hanya pemilik
- [x] QA responsif: riwayat dan detail di 360px, 768px, 1280px; cocokkan 1280px dengan layar Stitch Desktop dan 360px dengan PRD §9.4
- [x] Update `.agents/tasklist.md`

> ✅ Fase 7 selesai — 20 Oktober 2026

## Fase 8 — Verifikasi dan Pengelolaan Pengajuan (Kasi) (K1–K3)
- [x] Layar (Stitch → mobile PRD §9.4): K2 Antrean (memakai view `v_antrean_pengajuan`; cari nama/NIK/no. pengajuan; filter status dan layanan; pagination)
- [x] Layar (Stitch → mobile PRD §9.4): K3 Detail dan Verifikasi (data pemohon, accordion berkas + pratinjau, tombol Sesuai/Perlu Perbaikan + alasan, bar aksi sticky)
- [x] `PengajuanService::ubahStatus()` dengan matriks perpindahan (PRD §7), `id_pengubah`, `catatan_petugas`, penanganan galat trigger
- [x] Aksi Kasi: kurang berkas (otomatis bila ada berkas perlu perbaikan), diproses (semua sesuai), ajukan ke Kepala, tolak + alasan wajib
- [x] Hak akses: hanya `kasi`; warga dan kepala 403 pada rute Kasi
- [x] Test: matriks perpindahan (yang tidak valid ditolak), diproses hanya bila semua berkas sesuai, tolak wajib alasan, `status_log` mencatat pelaku, status final tidak berubah
- [x] QA responsif: antrean dan detail verifikasi di 360px, 768px, 1280px; cocokkan 1280px dengan layar Stitch Desktop dan 360px dengan PRD §9.4
- [x] Bug fix: perbaikan modal tolak pengajuan (resolusi stacking context, teleport dialog ke body, scroll lock, auto-focus textarea, validasi alasan wajib dan error state old input)
- [x] Update `.agents/tasklist.md`

> ✅ Fase 8 selesai — 20 Oktober 2026

## Fase 9 — Pengaturan Kampung dan Tanda Tangan (K7, P4)
- [x] Layar (Stitch → mobile PRD §9.4): K7 Pengaturan Kampung (profil/kop, kontak dan WhatsApp, jam layanan, statistik, kode kampung untuk nomor surat); disimpan di tabel `pengaturan` lewat `Pengaturan::ambil/simpan`
- [x] Layar (Stitch → mobile PRD §9.4): P3 Profil dan TTD (Kepala mengunggah gambar tanda tangan, pratinjau, validasi PNG/JPG; disimpan privat; ubah password)
- [x] Hak akses: Pengaturan hanya Kasi; unggah TTD hanya Kepala (untuk akunnya sendiri)
- [x] Test: validasi pengaturan, akses per role, TTD tersimpan privat dan tidak dapat dibuka akun lain
- [x] QA responsif: pengaturan dan profil TTD di 360px, 768px, 1280px; cocokkan 1280px dengan layar Stitch Desktop dan 360px dengan PRD §9.4
- [x] Update `.agents/tasklist.md`

> ✅ Fase 9 selesai — 20 Oktober 2026

## Fase 10 — Surat PDF dan Persetujuan Kepala (K4, P1–P3)
- [x] `SuratService`: render template Blade → PDF (DomPDF) untuk 4 layanan (kop dari `pengaturan`, data pemohon dari `warga`/`pengguna`, isian dari `data_isian`, A4)
- [x] 4 template surat: `skck`, `domisili`, `sktm`, `sku` (redaksi dicocokkan dengan contoh resmi Balai Desa bila tersedia)
- [x] Layar (Stitch → mobile PRD §9.4): K4 Pratinjau Surat (Draft) — pratinjau draft untuk Kasi (tanpa nomor surat; tidak disimpan)
- [x] Layar (Stitch → mobile PRD §9.4): P1 Beranda Kepala (daftar `menunggu_ttd`) dan P2 Detail Persetujuan (ringkasan, pratinjau PDF, bar sticky Setujui/Kembalikan)
- [x] Aksi **Setujui**: satu `DB::transaction` yang memanggil `sp_ambil_nomor_surat`, membentuk `no_surat`, membuat `kode_verifikasi`, menyisipkan gambar TTD dan QR (PNG via `gd`), menyimpan PDF final ke disk privat, menyimpan baris `surat`, mengubah status menjadi `selesai`
- [x] Aksi **Kembalikan** dengan catatan wajib → `diproses`
- [x] Setujui ditolak bila Kepala belum mengunggah TTD
- [x] Hak akses: hanya `kepala` untuk P1–P3; Kasi untuk pratinjau draft
- [x] Test: nomor surat unik dan berurutan per layanan per tahun, setujui hanya dari `menunggu_ttd`, rollback penuh bila PDF gagal (tidak ada baris `surat`, status tetap), kembalikan wajib catatan, TTD wajib
- [x] QA responsif: halaman persetujuan dan pratinjau PDF di 360px, 768px, 1280px; cek hasil PDF terbaca; cocokkan 1280px dengan layar Stitch Desktop dan 360px dengan PRD §9.4
- [x] Update `.agents/tasklist.md`
- [x] Revisi tampilan template surat sesuai gambar referensi

> ✅ Fase 10 selesai — 20 Oktober 2026

## Fase 11 — Verifikasi Keaslian Surat (Publik) (A5)
- [x] Layar (Stitch → mobile PRD §9.4): L4 Verifikasi Surat pada `/verifikasi/{kode}` (nomor surat, jenis, nama pemohon, NIK disamarkan, tanggal terbit, penandatangan, status valid); kode tidak ditemukan → tampilan "tidak valid"
- [x] QR pada surat mengarah ke URL verifikasi; batasi laju permintaan untuk mencegah penebakan kode
- [x] Test: kode valid, kode tidak valid, NIK tidak tampil utuh
- [x] QA responsif: halaman verifikasi di 360px, 768px, 1280px; cocokkan 1280px dengan layar Stitch Desktop dan 360px dengan PRD §9.4
- [x] Update `.agents/tasklist.md`

> ✅ Fase 11 selesai — 20 Oktober 2026

## Fase 12 — Kelola Warga dan Persyaratan Layanan (Kasi) (K5, K6)
- [x] Layar (Stitch → mobile PRD §9.4): K5 Kelola Warga (cari NIK/nama; kartu di mobile, tabel `md`+; lihat/koreksi data; reset password sementara dengan `wajib_ganti_password`; nonaktifkan/aktifkan akun)
- [x] Layar (Stitch → mobile PRD §9.4): K6 Persyaratan Layanan (per layanan: ubah nama, petunjuk, wajib, urutan, status aktif; tidak ada tambah/hapus layanan)
- [x] Syarat yang sudah dipakai berkas hanya dapat dinonaktifkan, tidak dihapus
- [x] Hak akses: hanya `kasi`
- [x] Test: reset password memaksa ganti, akun nonaktif tidak dapat login, syarat terpakai tidak dapat dihapus, akses per role
- [x] QA responsif: kedua halaman di 360px, 768px, 1280px; cocokkan 1280px dengan layar Stitch Desktop dan 360px dengan PRD §9.4
- [x] Update `.agents/tasklist.md`

> ✅ Fase 12 selesai — 20 Oktober 2026

## Fase 13 — Dashboard (D1–D3, W7, K1)
- [x] Layar (Stitch → mobile PRD §9.4): W1 Beranda Warga (sapaan, tombol Ajukan Surat, pengajuan aktif dengan progres, 3 riwayat terakhir)
- [x] Layar (Stitch → mobile PRD §9.4): K1 Beranda Kasi (kartu: menunggu verifikasi, kurang berkas, diproses, menunggu TTD, selesai bulan ini; antrean terbaru)
- [x] Layar (Stitch → mobile PRD §9.4): P1 Beranda Kepala — kartu menunggu TTD dan selesai bulan ini
- [x] Query ringkas (agregasi database, eager loading, tanpa N+1)
- [x] Test: angka kartu cocok dengan data seeder, akses per role
- [x] QA responsif: ketiga dashboard di 360px, 768px, 1280px; cocokkan 1280px dengan layar Stitch Desktop dan 360px dengan PRD §9.4
- [x] Update `.agents/tasklist.md`

> ✅ Fase 13 selesai — 20 Oktober 2026

## Fase 14 — Arsip dan Laporan (K8, P5)
- [x] Layar (Stitch → mobile PRD §9.4): K8 Arsip dan Laporan (filter bulan/tahun dan layanan; rekap dari `sp_rekap_periode`/`v_rekap_bulanan`: total, selesai, ditolak, berjalan, rata-rata waktu selesai)
- [x] Cetak laporan PDF (DomPDF) dengan kop dari `pengaturan`
- [x] Hapus arsip = soft delete (hanya Kasi); data tersembunyi dari daftar dan rekap
- [x] Kepala: akses hanya baca
- [x] Test: total laporan sama dengan data, soft delete tidak menghapus fisik dan keluar dari rekap, akses per role
- [x] QA responsif: halaman arsip/laporan di 360px, 768px, 1280px; cek PDF terbaca; cocokkan 1280px dengan layar Stitch Desktop dan 360px dengan PRD §9.4
- [x] Update `.agents/tasklist.md`

> ✅ Fase 14 selesai — 20 Oktober 2026

## Fase 15 — Polish, Responsif, dan Aksesibilitas
- [x] Audit seluruh halaman terhadap layar Stitch Desktop (1280px) dan PRD §9.3–§9.4 (turunan mobile 360px); catat dan perbaiki selisih
- [x] Tinjau hasil turunan mobile bersama pengguna (Stitch hanya memuat desktop) dan catat keputusan perubahan
- [x] Tidak ada scroll horizontal di semua halaman pada 360px; target sentuh ≥ 44px
- [x] Empty state, loading, dan pesan galat konsisten
- [x] Optimasi query (N+1, index pada kolom filter) dan ukuran aset
- [x] Aksesibilitas dasar: label form, kontras AA, fokus keyboard, `inputmode`/`autocomplete`
- [x] Ukur Lighthouse mobile (target Performance dan Accessibility ≥ 85) dan catat hasil
- [x] Update `.agents/tasklist.md`

> ✅ Fase 15 selesai — 20 Oktober 2026

## Fase 16 — Testing dan QA
- [x] Jalankan seluruh test: `php artisan test`
- [x] `vendor\bin\pint --test` dan `npm run build` bersih
- [x] Skenario manual per role (warga → kasi → kepala) dari registrasi sampai surat terbit dan diunduh, termasuk jalur kurang berkas dan tolak
- [x] Uji dari handphone sungguhan (Chrome Android): daftar, unggah foto KTP dari kamera, unduh surat
- [x] Cek keamanan: akses antar-role dan antar-warga via URL, berkas privat, CSRF, mass assignment, rate limiting, NIK tidak bocor di log atau halaman publik
- [x] Cek `storage/logs/laravel.log` bersih
- [x] Update `.agents/tasklist.md`

> ✅ Fase 16 selesai — 20 Oktober 2026

## Fase 17 — Dokumentasi Akhir
- [x] README: persyaratan (XAMPP, PHP 8.2, Composer, Node.js), langkah instalasi, perintah migrate dan seed, pembuatan akun petugas
- [x] Daftar akun demo per role dan catatan mengganti password awal
- [x] ERD akhir dan penjelasan objek basis data (trigger, stored procedure, view, index) untuk laporan tugas
- [x] Dokumentasi visual & diagram arsitektur sistem di folder `.agents/` untuk laporan tugas
- [x] Sinkronkan dokumen `.agents/` dengan kondisi akhir sistem
- [x] Update `.agents/tasklist.md`

> ✅ Fase 17 selesai — 12 November 2026

## Perbaikan
- [x] **Perbaikan 1 — Bug Fungsional, Sinkronisasi Data Isian, & Konfigurasi Tailwind:**
  - [x] Sinkronisasi pembacaan key JSON `data_isian` (`lama_tinggal_tahun`, `keperluan`, `tujuan`, `penghasilan_per_bulan`, `jumlah_tanggungan`, `nama_usaha`, `jenis_usaha`, `alamat_usaha`, `lama_usaha_tahun`) beserta fallback data lama pada `resources/views/kasi/pengajuan/show.blade.php`, `resources/views/kepala/persetujuan/show.blade.php`, dan ke-4 template cetak PDF (`skck`, `domisili`, `sktm`, `sku`)
  - [x] Perbaiki pemanggilan kolom `$warga->tgl_lahir` (sebelumnya tertulis `tanggal_lahir`) pada ke-4 template cetak PDF, halaman Verifikasi Kasi (`kasi/pengajuan/show.blade.php`), dan halaman Persetujuan Kepala (`kepala/persetujuan/show.blade.php`)
  - [x] Perbaiki pemanggilan `$warga->dusun?->nama_dusun` pada `resources/views/verifikasi/show.blade.php` dan pengecekan kode layanan `'DOM'` pada `resources/views/landing.blade.php`
  - [x] Tambahkan alias warna semantik yang belum terdaftar di `tailwind.config.js` (`danger`, `danger-soft`, `success`, `success-soft`, `primary-soft`, `error`) dan rapikan class warna serta masking `samarkan_nik()` pada `resources/views/arsip/index.blade.php` & `resources/views/profile/edit.blade.php`
- [x] **Perbaikan 2 — Desain Footer Lebih Gelap (Dark Footer) & Tata Letak Navigasi Bawah Mobile:**
  - [x] Ubah tampilan footer menjadi tema gelap (*Dark Forest Emerald* `#0D2616` dengan aksen emas dan teks kontras nyaman) pada `resources/views/landing.blade.php`, `resources/views/layouts/guest.blade.php`, `resources/views/verifikasi/show.blade.php`, serta tambahkan footer gelap ringkas pada `resources/views/layouts/app.blade.php`
  - [x] Tambahkan dukungan prop `hideBottomNav` pada `resources/views/layouts/app.blade.php` agar halaman detail/form yang memiliki *Sticky Action Bar* tidak bertumpuk (double bottom bar) dengan `bottom-nav` di layar mobile
  - [x] Ringkas tampilan mobile halaman Masuk (`auth/login.blade.php`) dan Daftar (`auth/register.blade.php`) dengan mengganti blok marketing panjang di layar `< lg` menjadi *Compact Brand Header* agar pengguna langsung melihat form tanpa scroll panjang
  - [x] Rapikan tata letak tombol pada *Sticky Action Bar* mobile di `kasi/pengajuan/show.blade.php`, `kasi/pengajuan/draft.blade.php`, `kasi/warga/edit.blade.php`, `kasi/pengaturan/edit.blade.php`, `kepala/persetujuan/show.blade.php`, dan `profile/edit.blade.php` agar proporsional di layar 360px
- [x] **Perbaikan 3 — Redesign UI Kartu Interaktif & Penyederhanaan Halaman Warga (Anti-Monoton & Anti-Scroll Panjang):**
  - [x] Terapkan identitas visual warna & ikon per jenis layanan (`SKCK` Emerald, `DOM` Biru, `SKTM` Amber, `SKU` Ungu) secara konsisten di seluruh kartu layanan dan daftar pengajuan
  - [x] Ubah tampilan daftar pengajuan pada `dashboard/warga.blade.php` dan `pengajuan/index.blade.php` dari list baris biasa menjadi *Interactive Elevated Cards* yang user-friendly dengan tombol aksi cepat (Unduh Surat PDF langsung / Perbaiki Berkas)
  - [x] Sederhanakan halaman Detail Pengajuan Warga (`pengajuan/show.blade.php`) di mobile menggunakan navigasi *Segmented Tabs* (`Rincian & Berkas` | `Data Pemohon` | `Riwayat Status`) untuk memangkas panjang scroll vertikal
- [x] **Perbaikan 4 — Redesign UI & Optimasi Mobile Halaman Kasi & Kepala Kampung:**
  - [x] Ubah tampilan daftar pada `dashboard/kasi.blade.php`, `kasi/pengajuan/index.blade.php`, `kasi/warga/index.blade.php`, `dashboard/kepala.blade.php`, dan `kepala/persetujuan/index.blade.php` menjadi kartu interaktif yang bersih, informatif, dan tidak memaksakan banyak teks di layar mobile
  - [x] Terapkan sistem *Segmented Tabs* / *Accordion* pada halaman Verifikasi Kasi (`kasi/pengajuan/show.blade.php`) agar pemeriksaan data pemohon, berkas syarat, dan log status tidak memerlukan scroll terlalu panjang di HP
  - [x] Ubah pemilih layanan di `kasi/layanan/index.blade.php` pada layar mobile menjadi *Horizontal Pill Tabs* dan sederhanakan `kasi/pengaturan/edit.blade.php` dengan sistem Tab per kategori (`Profil Kampung`, `Penomoran & TTD`, `Kontak`, `Alur & Jam`)
  - [x] Sederhanakan halaman Arsip & Laporan (`arsip/index.blade.php`) dengan *Collapsible Filter* di mobile serta pemisahan Tab (`Daftar Arsip Surat` | `Rekapitulasi Bulanan`)
  - [x] Optimasi pratinjau PDF di layar mobile pada `kasi/pengajuan/draft.blade.php` dan `kepala/persetujuan/show.blade.php` (kartu ringkasan dokumen + tombol buka PDF layar penuh & opsi toggle pratinjau) agar scroll layar tidak terjebak di dalam `iframe`
- [x] **Perbaikan 5 — Penambahan Fitur Pendukung User-Friendly & Pengujian Akhir:**
  - [x] Tambahkan fitur **Lacak Cepat Pengajuan** berdasarkan Nomor Pengajuan di `landing.blade.php` untuk memudahkan warga mengecek progres permohonan dari halaman depan
  - [x] Tambahkan tombol **Salin Nomor (1-Klik Copy dengan Toast)** untuk Nomor Pengajuan & Nomor Surat, serta tombol **Hubungi Pemohon via WhatsApp** (dengan template pesan otomatis) pada halaman Verifikasi Kasi (`kasi/pengajuan/show.blade.php`)
  - [x] Jalankan pengujian otomatis (`php artisan test`), format kode (`vendor\bin\pint`), build aset (`npm run build`), dan verifikasi responsif (360px, 768px, 1280px)
  - [x] Update `.agents/tasklist.md`
- [x] **Perbaikan 6 — Penyederhanaan Halaman Publik Mobile (Landing, Lacak, Verifikasi):**
  - [x] Ubah section **Tata Cara** di `landing.blade.php` menjadi *Timeline Vertikal Ringkas* di mobile (satu kartu, 4 langkah: angka + judul pendek + 1 kalimat; hapus angka ganda di judul dan baris ikon/label bawah); desktop tetap 4 kolom, konten dari satu sumber data `@php $langkah`
  - [x] Ubah section **Layanan Surat** menjadi kartu ringkas di mobile (ikon + nama + deskripsi 1 baris) dengan daftar syarat dilipat (tombol "Lihat syarat (n)", Alpine `x-collapse`) dan tombol Ajukan tetap terlihat; desktop tetap grid 4 kolom
  - [x] Ringkas **Hero** di mobile: statistik 3 angka sebaris (`grid-cols-3`), kurangi tinggi/padding hero; perbaiki widget **Lacak Cepat** (hapus `-mt` negatif di mobile agar tidak tumpang tindih, persingkat placeholder agar tidak terpotong di 360px)
  - [x] Opsi **Sticky CTA Bawah** mobile: sempat dipasang, kemudian dihilangkan sesuai permintaan pengguna agar layar mobile lebih leluasa dan bersih
  - [x] Pangkas **FAQ** (padding & ukuran teks mobile) dan **Footer** mobile (deskripsi dipendekkan, alamat tidak diulang, link inline, sembunyikan kolom Layanan Mandiri); sinkronkan jam footer dengan `$jamLayanan` (sumber tunggal, hilangkan kontradiksi Jumat 08.00-11.30)
  - [x] Tambahkan tautan silang `/lacak` ↔ `/verifikasi` dan sesuaikan placeholder input `/lacak` untuk layar 360px
  - [x] Tambah/perbarui test landing (4 langkah tata cara, 4 layanan + syarat, jam footer sama dengan `$jamLayanan`); jalankan `php artisan test`, `vendor\bin\pint --test`, `npm run build`, dan verifikasi responsif (360px, 768px, 1280px; tanpa scroll horizontal, target sentuh ≥ 44px, target panjang scroll landing mobile ≤ 6 layar)
  - [x] Update `.agents/tasklist.md`

> ✅ Perbaikan 6 selesai — 20 Oktober 2026

### Rencana Perbaikan 7–12 — Penyesuaian Halaman Warga (Mobile-First) & Bug Header Desktop
> Sumber: analisis 20 Oktober 2026 (lihat artifact `implementation_plan.md`). Kerjakan satu perbaikan pada satu waktu dan tunggu perintah pengguna. Keputusan bawaan (pengguna belum memilih; ubah bila perlu): bottom nav warga 4 tab dengan Profil langsung ke `/profil`; Ubah Password & Keluar dipindah ke halaman Profil; kartu "Foto Salinan KTP & KK" dihapus; daftar syarat di card jenis surat dilipat; tab aktif memakai pil hijau penuh seperti desktop; tombol Ajukan hijau muda saat tidak aktif.

- [x] **Perbaikan 7 — Layout Global & Bug Header Desktop (semua role: warga, kasi, kepala):**
  - [x] Perbaiki bug topbar desktop pada `layouts/app.blade.php`: slot `$header` tidak lagi dirender di dalam `<h1>` topbar `h-16`; topbar hanya berisi judul singkat satu baris + tanggal + avatar; slot `$header` dirender sebagai blok *page header* di area konten (`<div>`, tumbuh sesuai isi) untuk desktop dan mobile
  - [x] Rapikan 17 halaman yang memakai `<x-slot name="header">` (dashboard warga/kasi/kepala, `arsip/index`, `auth/ubah-password`, `kasi/*`, `kepala/*`) agar tidak ada judul ganda dan tidak ada `<h1>` bersarang
  - [x] Topbar mobile: logo kecil + judul halaman + avatar
  - [x] Hapus padding bawah ganda (`main` vs `pb-24/pb-28` di halaman); satu sumber padding di layout; sembunyikan footer gelap di mobile
  - [x] Definisikan class yang dipakai tetapi tidak terdaftar: ganti `w-13 h-13` → `w-14 h-14`; utilitas `safe-bottom` (`env(safe-area-inset-bottom)`) di `resources/css/app.css`; `boxShadow.xs` di `tailwind.config.js`; hapus/definisikan `font-heading`
  - [x] Perbaiki pola menu aktif pada `layouts/sidebar-desktop.blade.php`: Riwayat aktif juga untuk `pengajuan/*` selain `pengajuan/buat*`
  - [x] QA responsif topbar/header di 360px, 768px, 1280px untuk 3 role (tanpa tumpang tindih, tanpa scroll horizontal); `npm run build`, `php artisan test`, `vendor\bin\pint --test`
  - [x] Update `.agents/tasklist.md`

> ✅ Perbaikan 7 selesai — 20 Oktober 2026
- [x] **Perbaikan 8 — Navigasi Bawah Mobile & Beranda Warga:**
  - [x] Bottom nav (`layouts/bottom-nav.blade.php`): satukan logika aktif dalam satu `@php` (array `$nav`); gaya tab aktif pil hijau (`bg-primary text-white`) + label tebal, tidak aktif `text-ink-muted`; transisi warna; `aria-current="page"`; target sentuh ≥ 44px
  - [x] Tombol **Ajukan**: hijau muda saat tidak aktif, hijau penuh + cincin hanya di `pengajuan/buat*` (tidak lagi selalu aktif)
  - [x] Pola aktif: Riwayat = `riwayat*` + `pengajuan/*` selain `pengajuan/buat*`; Akun/Profil = `profil*` + `ubah-password*`; Kasi/Kepala: tab "Lainnya/Akun" aktif untuk rute yang hanya ada di sheet (`kasi/persyaratan*`, `kasi/pengaturan*`, `kepala/profil-ttd*`, `ubah-password*`)
  - [x] Bottom nav warga: 4 tab Beranda · Ajukan · Riwayat · Profil (tanpa bottom sheet untuk warga); bottom nav tetap tampil di halaman Profil
  - [x] Beranda warga (`dashboard/warga.blade.php`) mobile: kartu sapaan ringkas + tombol Ajukan Surat + status pengajuan aktif di layar pertama; ringkas hero (1 kalimat, hapus 3 chip); banner profil belum lengkap menjadi strip satu baris dengan progres
  - [x] Kartu pengajuan aktif: di mobile ganti stepper 5 langkah dengan satu baris status + progress bar (stepper penuh mulai `sm`); tombol aksi `min-h-[44px]`; teks informasi penting minimum 12px
  - [x] Ganti slab "Keaslian Dokumen QR" dan kartu "Pusat Layanan" dengan tombol **Hubungi Balai (WhatsApp)** dari data Pengaturan Kampung
  - [x] Test: bottom nav menandai tepat satu tab aktif pada `/dashboard`, `/pengajuan/buat`, `/riwayat`, `/pengajuan/{id}`, `/profil`; QA 360px/768px/1280px
  - [x] Update `.agents/tasklist.md`

> ✅ Perbaikan 8 selesai — 20 Oktober 2026
- [x] **Perbaikan 9 — Wizard Ajukan Surat Mobile (`pengajuan/create.blade.php`):**
  - [x] Ringkas header halaman (judul 1 baris + subjudul pendek); hapus pill "Proses 100% Digital" dan badge panjang
  - [x] Stepper mobile: baris `1/4 · Pilih Layanan` + progress 4 segmen; hapus chip "Estimasi: 3 Menit"
  - [x] Card jenis surat: ringkas (ikon, nama, deskripsi 1–2 baris); daftar syarat dilipat ("Lihat syarat (n)"); badge "Sedang Diajukan" tidak lagi `absolute` (ditaruh di baris sendiri agar tidak menimpa judul)
  - [x] Bar tombol bawah **sticky** di mobile untuk langkah 1–4 (Kembali ringkas + Lanjut/Kirim Surat lebar penuh); perpendek teks tombol; `:hide-bottom-nav="true"` sehingga fokus tanpa tab bar dan muat di 360px
  - [x] Ganti seluruh `alert()` (±15 tempat) dengan pesan error inline di bawah kolom + scroll/fokus ke kolom yang salah
  - [x] Langkah 2: ringkas "Data Pemohon" menjadi satu baris (nama, NIK) + tautan "Ubah profil" + dropdown rincian; Langkah 3: tombol unggah `h-11`, tersusun grid 2 kolom di `< sm`, petunjuk 2 baris; Langkah 4: ringkasan ringkas, pernyataan dipersingkat
  - [x] QA alur lengkap 4 langkah di 360px/768px/1280px; test alur kirim pengajuan tetap lulus (134 passed, 694 assertions)
  - [x] Update `.agents/tasklist.md`

> ✅ Perbaikan 9 selesai — 20 Oktober 2026
- [x] **Perbaikan 10 — Profil Saya Disederhanakan (`profile/edit.blade.php`):**
  - [x] Satu kartu kelengkapan ringkas (persentase + progress + daftar kolom kosong); hapus paragraf panjang dan banner Kerahasiaan (diganti satu catatan kecil di bawah form)
  - [x] Hapus kartu placeholder "Foto Salinan KTP & KK" (tidak berfungsi)
  - [x] Bar sticky mobile hanya tombol **Simpan** (lebar penuh), bottom nav tetap tampil; samakan tinggi input Jenis Kelamin dengan input lain; NIK tampil sebagai teks terkunci
  - [x] Seksi Identitas & Alamat: tampil langsung berurutan di mobile; hanya seksi yang sudah lengkap boleh dilipat
  - [x] Tambah kartu "Keamanan Akun" (tautan Ubah Password) dan tombol **Keluar** di bagian bawah halaman Profil
  - [x] Pastikan toast sukses simpan tidak tertutup bottom nav; QA 360px/768px/1280px; test update profil tetap lulus
  - [x] Update `.agents/tasklist.md`

> ✅ Perbaikan 10 selesai — 21 Oktober 2026
- [x] **Perbaikan 11 — Polesan Riwayat, Detail Pengajuan, & Ubah Password (warga):**
  - [x] `pengajuan/index.blade.php`: pertahankan 4 kartu statistik sesuai konfirmasi user, pertahankan filter chip dan pencarian, naikkan target sentuh aksi mobile ke min-h-[44px]
  - [x] `pengajuan/show.blade.php`: sesuaikan offset tab sticky `top-16` dengan topbar baru; pangkas teks banner; perbaiki ternary identik sehingga tab default = Berkas bila kurang berkas dan Riwayat bila normal; tombol aksi/unggah ulang ≥ 44px; tombol Hubungi Balai (WhatsApp) di header dan kartu bantuan
  - [x] `auth/ubah-password.blade.php`: hapus judul ganda, padding kartu `p-5` di mobile, target sentuh tombol batal & simpan min-h-[48px]
  - [x] QA 360px/768px/1280px; `php artisan test`, `vendor\bin\pint --test`, `npm run build`
  - [x] Update `.agents/tasklist.md`

> ✅ Perbaikan 11 selesai — 21 Oktober 2026
- [x] **Perbaikan 12 — Fitur Tambahan Kenyamanan Warga:**
  - [x] Pembatalan pengajuan oleh warga pada status `menunggu` (terkait backlog O7; policy, controller, route, trigger log, konfirmasi modal)
  - [x] Simpan draf isian teks wizard di `localStorage` per user ID agar tidak hilang saat halaman tertutup/refresh, plus tombol pulihkan & bersihkan draf
  - [x] Pembaruan status otomatis (polling ringan 30 detik via JSON endpoint) pada Detail Pengajuan dengan toast banner & reload otomatis
  - [x] Update `.agents/tasklist.md`

> ✅ Perbaikan 12 selesai — 21 Oktober 2026
- [x] **Perbaikan 13 — Penyempurnaan Tampilan & Fitur Kasi dan Kepala Desa:**
  - [x] **Fitur Utama (Sesuai Permintaan Pengguna):**
    - [x] **Stempel Resmi Kampung (Alur Sama dengan TTD):**
      - [x] Migrasi kolom `file_stempel` pada pengguna role kepala / tabel pengaturan kampung
      - [x] Form unggah, pratinjau transparan, dan hapus stempel di menu Kepala (`/kepala/profil-ttd`) dengan alur dan validasi serupa TTD
      - [x] Render cap stempel di dokumen PDF surat resmi ([`surat/layout.blade.php`](file:///D:/Folder%20Anam/Semester%205/Basis%20Data%20Lanjut/Praktek/SIPD_UTS/sirukti/resources/views/surat/layout.blade.php)), menimpa/menyentuh sisi kiri tanda tangan secara proporsional dan elegan
    - [x] **Penyederhanaan QR Code Surat:**
      - [x] Hapus kotak border putus-putus (`.qr-wrapper`), tabel, dan teks panjang deskripsi di kiri bawah surat
      - [x] Tampilkan gambar QR Code secara mandiri, bersih, dan ringkas tanpa teks berlebih
  - [x] **Peningkatan Halaman Kasi Pelayanan:**
    - [x] Tambahkan tombol pintas "Hubungi Pemohon (WhatsApp)" di halaman verifikasi ([`kasi/pengajuan/show.blade.php`](file:///D:/Folder%20Anam/Semester%205/Basis%20Data%20Lanjut/Praktek/SIPD_UTS/sirukti/resources/views/kasi/pengajuan/show.blade.php))
    - [x] Tambahkan opsi preset / saran cepat alasan perbaikan berkas (misal: "KTP buram", "KK terpotong", dll.)
    - [x] Fitur Reset Password Akun Warga oleh Kasi di manajemen warga ([`kasi/warga`](file:///D:/Folder%20Anam/Semester%205/Basis%20Data%20Lanjut/Praktek/SIPD_UTS/sirukti/resources/views/kasi/warga/index.blade.php)) untuk membantu warga yang lupa akses
    - [x] Segmented tab status cepat di antrean Kasi (Menunggu Verifikasi, Kurang Berkas, Diproses, Dikembalikan)
  - [x] **Peningkatan Halaman Kepala Kampung:**
    - [x] Tab status di antrean Kepala ([`kepala/persetujuan`](file:///D:/Folder%20Anam/Semester%205/Basis%20Data%20Lanjut/Praktek/SIPD_UTS/sirukti/resources/views/kepala/persetujuan/index.blade.php)): "Menunggu TTD" dan "Riwayat Disetujui"
    - [x] Preset alasan pengembalian cepat ke Kasi saat revisi draf naskah
    - [x] Penataan kartu stempel berdampingan dengan tanda tangan di profil Kepala
  - [x] **Testing, Linting, & QA:**
    - [x] Unit & Feature tests untuk alur stempel, render PDF, dan penyederhanaan QR
    - [x] `php artisan test` (147 tests lulus), `vendor\bin\pint --test`, `npm run build`
    - [x] Update `.agents/tasklist.md`

> ✅ Perbaikan 13 selesai — 21 Oktober 2026
- [x] **Perbaikan 14 — Audit Menyeluruh, Spasi & Posisi TTD/Stempel Surat PDF, Menu Profil Mobile Warga & Optimasi Mobile-First:**
  - [x] **14.1 Format Surat PDF (Spasi Paragraf & Posisi Stempel/TTD):**
    - [x] Perlega jarak antar paragraf (`p` margin bawah `3.5mm`, `line-height: 1.38`) dan jarak tabel biodata di [`resources/views/surat/layout.blade.php`](file:///D:/Folder%20Anam/Semester%205/Basis%20Data%20Lanjut/Praktek/SIPD_UTS/sirukti/resources/views/surat/layout.blade.php) dengan penyesuaian `margin-top` blok tanda tangan agar seluruh 4 jenis surat (`SKCK`, `DOM`, `SKTM`, `SKU`) dijamin tetap 1 lembar A4
    - [x] Pertahankan layout stempel & tanda tangan saat ini dan geser posisinya sedikit ke kiri (`.ttd-stempel` ke `left: -9mm` dan `.ttd-gambar` ke `left: 2mm`) agar tampak lebih natural seperti surat fisik asli
  - [x] **14.2 Popup / Bottom-Sheet Menu Profil Warga di Mobile & Topbar:**
    - [x] Ubah tab "Profil" pada [`resources/views/layouts/bottom-nav.blade.php`](file:///D:/Folder%20Anam/Semester%205/Basis%20Data%20Lanjut/Praktek/SIPD_UTS/sirukti/resources/views/layouts/bottom-nav.blade.php) untuk role `warga` menjadi pemicu Bottom Sheet Menu yang menampilkan identitas singkat warga, menu **Pengaturan Profil**, **Ubah Kata Sandi**, dan tombol **Keluar dari Akun**
    - [x] Jadikan avatar pengguna di pojok kanan atas Topbar ([`resources/views/layouts/app.blade.php`](file:///D:/Folder%20Anam/Semester%205/Basis%20Data%20Lanjut/Praktek/SIPD_UTS/sirukti/resources/views/layouts/app.blade.php)) interaktif (dropdown menu Pengaturan Profil, Ubah Kata Sandi, dan Keluar)
  - [x] **14.3 Perbaikan Halaman Publik & Komponen Lacak Mobile-First:**
    - [x] Perbaiki logika tombol detail saat `@auth` pada [`resources/views/components/hasil-lacak-pengajuan.blade.php`](file:///D:/Folder%20Anam/Semester%205/Basis%20Data%20Lanjut/Praktek/SIPD_UTS/sirukti/resources/views/components/hasil-lacak-pengajuan.blade.php) sesuai role/kepemilikan (mencegah 403) dan rapikan stepper 4 kolom di layar 360px
    - [x] Hapus padding bawah berlebih (`pb-20`) di `<main>` [`resources/views/landing.blade.php`](file:///D:/Folder%20Anam/Semester%205/Basis%20Data%20Lanjut/Praktek/SIPD_UTS/sirukti/resources/views/landing.blade.php), arahkan tombol "Ajukan Surat" pada kartu layanan mobile langsung ke form pengajuan bagi warga yang login, dan pastikan tinggi tombol `min-h-[44px]`
    - [x] Tambahkan `<x-toast />` dan tautan pintas Lacak Surat tanpa login pada halaman [`resources/views/auth/login.blade.php`](file:///D:/Folder%20Anam/Semester%205/Basis%20Data%20Lanjut/Praktek/SIPD_UTS/sirukti/resources/views/auth/login.blade.php) & [`resources/views/auth/register.blade.php`](file:///D:/Folder%20Anam/Semester%205/Basis%20Data%20Lanjut/Praktek/SIPD_UTS/sirukti/resources/views/auth/register.blade.php)
  - [x] **14.4 Optimasi Mobile-First Halaman Warga, Kasi & Kepala Kampung:**
    - [x] Rapikan padding bawah dan z-index sticky save bar pada [`resources/views/profile/edit.blade.php`](file:///D:/Folder%20Anam/Semester%205/Basis%20Data%20Lanjut/Praktek/SIPD_UTS/sirukti/resources/views/profile/edit.blade.php), gunakan jam layanan dinamis di [`resources/views/dashboard/warga.blade.php`](file:///D:/Folder%20Anam/Semester%205/Basis%20Data%20Lanjut/Praktek/SIPD_UTS/sirukti/resources/views/dashboard/warga.blade.php), dan pastikan seluruh tombol aksi mobile memiliki *touch target* `min-h-[44px]`
    - [x] Seimbangkan tata letak kartu Identitas & Keamanan Akun (`lg:col-span-2`) pada [`resources/views/kepala/profil/edit.blade.php`](file:///D:/Folder%20Anam/Semester%205/Basis%20Data%20Lanjut/Praktek/SIPD_UTS/sirukti/resources/views/kepala/profil/edit.blade.php) serta standarkan *touch target* `min-h-[44px]` pada kartu antrean mobile Kasi & Kepala
  - [x] **14.5 Pengujian Otomatis & Verifikasi Regresi:**
    - [x] Tambahkan/perbarui pengujian fitur pada `tests/Feature` untuk memverifikasi perubahan PDF surat, menu profil mobile warga, dan halaman publik, lalu jalankan `php artisan test`, `vendor\bin\pint --test`, serta `npm run build` hingga 100% lulus
    - [x] Update `.agents/tasklist.md`

> ✅ Perbaikan 14 selesai — 26 Oktober 2026

## Revisi Pasca-v1.0 — Batch 1 (2 November 2026)
> Urutan eksekusi: **R6 → R5 → R2 → R4 + R1 (mekanisme bersama) → R3 → R7**.

- [x] **R1 Pencarian Nomor Surat AJAX pada Halaman Riwayat Warga**
  - Keputusan pengguna: Cari pada `pengajuan.no_pengajuan`, `surat.no_surat`, serta `layanan.nama_layanan`/`kode`; pencarian otomatis saat mengetik (debounce 300 ms, `AbortController`), kosong = tampilkan semua, filter chip status & pagination berjalan bersamaan via fragmen HTML partial (`aria-live="polite"`, `history.replaceState`).
  - Temuan/akar masalah: `PengajuanController::index()` baru mencari `no_pengajuan` dan `layanan` (belum `surat.no_surat`), karakter wildcard LIKE belum di-escape, dan `resources/views/pengajuan/index.blade.php` masih memakai form GET penuh tanpa partial daftar terpisah.
  - Rencana file:
    - `app/Http/Controllers/PengajuanController.php` (escape wildcard LIKE, tambah pencarian `orWhereHas('surat', ...)` pada `no_surat`, kembalikan partial view saat request AJAX/`X-Requested-With`)
    - `resources/views/pengajuan/index.blade.php` (integrasi Alpine AJAX search + filter chip + pagination + indikator loading + `aria-live="polite"`)
    - `resources/views/pengajuan/partials/daftar-riwayat.blade.php` *(baru — ekstraksi daftar kartu mobile, tabel desktop, empty state, dan pagination)*
    - `routes/web.php` (throttle ringan pada rute pencarian)
    - `tests/Feature/RiwayatPencarianAjaxTest.php` *(baru)*
  - [x] Implementasi
  - [x] Test (PHPUnit) dan/atau uji manual (9 test lulus, 29 assertions)
  - [x] QA responsif 360px, 768px, 1280px

- [x] **R2 Login Warga Memakai Username Unik (Huruf, Angka, Underscore)**
  - Keputusan pengguna: Registrasi tetap 4 field (default `username` = NIK) dan warga dapat mengatur/mengubah `username` di halaman **Profil Saya** (serta Kasi di Kelola Warga); login menerima `username` ATAU `nik` (16 digit); panjang 4–30 karakter, regex `^[A-Za-z0-9_]+$`, disimpan huruf kecil (lowercase), unik case-insensitive pada `pengguna.username`, tolak 16 digit angka kecuali NIK milik sendiri, tolak kata terlarang (`admin`, `kasi`, `kepala`, `root`, `system`, `petugas`, `operator`, `sirukti`, `kampung`, `desa`). Tidak memerlukan migration tabel karena `pengguna.username` sudah `varchar(50) UNIQUE`.
  - Temuan/akar masalah: `LoginRequest` sebelumnya hanya mencocokkan kolom `pengguna.username` sehingga jika warga mengganti username, NIK tidak lagi bisa dipakai login; halaman Profil Warga (`profile/edit.blade.php` & `ProfileUpdateRequest`) belum memiliki input `username`.
  - Rencana file:
    - `app/Http/Requests/Auth/LoginRequest.php` (normalisasi input, dukung autentikasi via `pengguna.username` atau `warga.nik`, pesan galat generik, rate limiting per identifier+IP)
    - `app/Http/Requests/ProfileUpdateRequest.php` & `app/Http/Controllers/ProfileController.php` (normalisasi lowercase, validasi `username` unik, regex, panjang 4–30, proteksi NIK 16 digit orang lain & kata terlarang)
    - `app/Http/Requests/Kasi/UpdateWargaRequest.php` & `app/Http/Controllers/Kasi/WargaController.php` (dukung koreksi `username` warga oleh Kasi dengan aturan yang sama)
    - `resources/views/profile/edit.blade.php`, `resources/views/kasi/warga/edit.blade.php`, `resources/views/auth/login.blade.php` (input `username` dengan helper text `"Huruf, angka, dan _ saja"`, `autocomplete="username"`, galat inline)
    - `tests/Feature/Auth/UsernameWargaTest.php` *(baru)*
  - [x] Implementasi
  - [x] Test (PHPUnit) dan/atau uji manual (12 test baru lulus, 58 assertions; 27 total test auth lulus, 110 assertions)
  - [x] QA responsif 360px, 768px, 1280px

- [x] **R3 Ikon WhatsApp Berwarna Hijau pada Halaman Kelola Warga (Kasi)**
  - Keputusan pengguna: Ikon WhatsApp menggunakan SVG inline berwarna hijau `#25D366` pada kolom Nomor WhatsApp baik di tabel desktop maupun kartu mobile, sejajar dengan teks nomor.
  - Temuan/akar masalah: Pada `resources/views/kasi/warga/index.blade.php`, ikon SVG di tabel desktop sebelumnya memakai `text-primary`, sedangkan di kartu mobile belum ada ikon SVG WhatsApp.
  - Rencana file:
    - `resources/views/kasi/warga/index.blade.php` & `resources/views/kasi/warga/partials/daftar-warga.blade.php`
  - [x] Implementasi
  - [x] Test (PHPUnit) dan/atau uji manual (terverifikasi via `tests/Feature/Kasi/PencarianAjaxKasiTest.php`)
  - [x] QA responsif 360px, 768px, 1280px

- [x] **R4 Pencarian AJAX pada Halaman Kasi: Kelola Warga dan Antrean Verifikasi**
  - Keputusan pengguna: Satu mekanisme bersama R1 (Alpine.js + `fetch` + `AbortController` + debounce 300 ms + `history.replaceState` + fragmen partial HTML); filter dusun/status/layanan dan pagination tetap bekerja bersamaan.
  - Temuan/akar masalah: `Kasi\PengajuanController::index()` dan `Kasi\WargaController::index()` sebelumnya masih merender halaman penuh dan belum meng-escape wildcard LIKE; daftar pada `kasi/pengajuan/index.blade.php` dan `kasi/warga/index.blade.php` belum dipisah menjadi partial.
  - Rencana file:
    - `app/Http/Controllers/Kasi/PengajuanController.php` & `app/Http/Controllers/Kasi/WargaController.php` (escape wildcard LIKE, dukung respons partial saat request AJAX)
    - `resources/views/kasi/pengajuan/index.blade.php` & `resources/views/kasi/pengajuan/partials/daftar-antrean.blade.php` *(baru)*
    - `resources/views/kasi/warga/index.blade.php` & `resources/views/kasi/warga/partials/daftar-warga.blade.php` *(baru)*
    - `routes/web.php` (throttle ringan)
    - `tests/Feature/Kasi/PencarianAjaxKasiTest.php` *(baru)*
  - [x] Implementasi
  - [x] Test (PHPUnit) dan/atau uji manual (6 test baru lulus, 31 assertions; 9 test verifikasi eksisting lulus, 53 assertions; 9 test warga eksisting lulus, 41 assertions)
  - [x] QA responsif 360px, 768px, 1280px

- [x] **R5 Reset Nomor Surat ke 1 Setiap Periode Tahun**
  - Keputusan pengguna: Pertahankan reset ke 1 per jenis layanan per tahun (`counter_surat` PK `id_layanan + tahun`); tahun dan bulan Romawi diambil dari tanggal surat (`now('Asia/Jakarta')`) saat Kepala menyetujui; pertahankan format `{urut 3 digit}/{kode kampung}/{kode layanan}/{bulan romawi}/{tahun}` (`002/RH/SKTM/X/2026`).
  - Temuan/akar masalah: `sp_ambil_nomor_surat` sudah mereset ke 1 saat parameter `p_tahun` berganti, tetapi `SuratService::setujui()` memakai `date('Y')` dan `SuratService::buatNomorSurat()` memakai `date('n')` alih-alih waktu Carbon `now('Asia/Jakarta')`, sehingga tidak mengikuti waktu aplikasi/pengujian `Carbon::setTestNow()`.
  - Rencana file:
    - `app/Services/SuratService.php` (gunakan instance Carbon `now('Asia/Jakarta')` untuk `tahun`, `bulanRomawi`, dan `tgl_surat` secara konsisten)
    - `tests/Feature/NomorSuratResetTahunanTest.php` *(baru — uji transisi 31 Des 23:59 WIB → 1 Jan 00:00 WIB dengan `Carbon::setTestNow()`)*
  - [x] Implementasi
  - [x] Test (PHPUnit) dan/atau uji manual (2 test baru lulus, 13 assertions; 9 test persetujuan kepala lulus, 54 assertions)

- [x] **R6 Perbaikan Error `validation.required_with` Saat Klik "Minta Perbaikan" (Verifikasi Pengajuan Kasi)**
  - Keputusan pengguna: Sinkronkan input `status_verifikasi` setiap berkas di form, wajibkan minimal 1 berkas ditandai `perlu_perbaikan` beserta alasannya saat klik "Minta Perbaikan", tampilkan pesan Bahasa Indonesia yang ramah per nama berkas, dan lengkapi `lang/id/validation.php` (+ `auth.php`, `pagination.php`, `passwords.php`).
  - Temuan/akar masalah: (1) Berkas berstatus awal `menunggu` tidak memiliki input hidden `status_verifikasi` yang terkirim saat form submit, sedangkan input teks `berkas[{id}][catatan]` selalu terkirim sehingga memicu gagalnya `berkas.*.status_verifikasi => required_with:berkas` sebanyak 3 kali; (2) Folder `lang/id/` belum ada sehingga kunci mentah `validation.required_with` tampil langsung ke pengguna.
  - Rencana file:
    - `resources/views/kasi/pengajuan/show.blade.php` (tambahkan `<input type="hidden" name="berkas[{{ $b->id_berkas }}][status_verifikasi]" :value="statusVerifikasi">`, kontrol tombol verifikasi berkas sinkron, galat inline per berkas, pertahankan isian lama `old()`)
    - `app/Http/Requests/Kasi/VerifikasiPengajuanRequest.php` (aturan validasi konsisten: saat `aksi=minta_perbaikan` wajib minimal 1 berkas `perlu_perbaikan`, saat `status_verifikasi=perlu_perbaikan` wajib `catatan` terisi, pesan & atribut dinamis sesuai nama berkas)
    - `lang/id/validation.php`, `lang/id/auth.php`, `lang/id/pagination.php`, `lang/id/passwords.php` *(baru)*
    - `tests/Feature/Kasi/VerifikasiMintaPerbaikanTest.php` *(baru)*
  - [x] Implementasi
  - [x] Test (PHPUnit) dan/atau uji manual (5 test baru lulus, 31 assertions; 9 test verifikasi eksisting lulus, 53 assertions)
  - [x] QA responsif 360px, 768px, 1280px

- [x] **R7 Penyesuaian Posisi QR Code Sejajar Tanda Tangan pada Template Surat PDF**
  - Keputusan pengguna: Naikkan posisi vertikal QR code pada layout bersama agar sejajar horizontal dengan area tanda tangan/stempel (`.ttd-area`) untuk keempat template surat (`SKCK`, `DOM`, `SKTM`, `SKU`), ukuran QR tetap (`22mm × 22mm`), rata kiri pada margin teks, dan tetap 1 halaman A4.
  - Temuan/akar masalah: Pada `resources/views/surat/layout.blade.php`, `.ttd-tabel td` sebelumnya memakai `vertical-align: bottom` sehingga dasar QR sejajar dengan baris paling bawah NIP penandatangan (`.ttd-nip`), bukan dengan area gambar tanda tangan/stempel (`.ttd-area`).
  - Rencana file:
    - `resources/views/surat/layout.blade.php` (atur posisi vertikal `.ttd-tabel td { vertical-align: top; }` dan `.ttd-kiri { vertical-align: top; padding-top: 11mm; }` agar QR code sejajar horizontal dengan `.ttd-area`)
    - `tests/Feature/SuratPdfQrLayoutTest.php` *(baru)*
  - [x] Implementasi
  - [x] Test (PHPUnit) dan/atau uji manual (2 test baru lulus, 11 assertions; uji render 4 template surat SKCK, DOM, SKTM, SKU draft & final tetap 1 halaman A4)

> ✅ Revisi Pasca-v1.0 — Batch 1 selesai (7 dari 7 item: R1–R7 tuntas dan terverifikasi) — 2 November 2026

## Revisi Pasca-v1.0 — Batch 2: Fitur Backlog Opsional Prioritas (O3, O6, O8)
> Status: Selesai & Terverifikasi (3 November 2026). Referensi teknis Laravel 12 via Context7 (`/laravel/docs/__branch__12.x`: `response()->streamDownload()`, Eloquent & Service audit).
> Keputusan pengguna (via popup): (1) Kerjakan ketiga rekomendasi (`O3`, `O6`, `O8`); (2) **O3** berupa Ekspor CSV ramah Excel (UTF-8 BOM) + Bar visual ringkas hanya di tab Rekapitulasi Arsip agar Beranda tetap bersih; (3) **O6** ditampilkan sebagai Tab ke-3 (`'Log Aktivitas'`) di halaman Arsip & Laporan agar tidak menambah menu navigasi baru; (4) **O8** fokus pada fitur *Install ke Layar Utama HP* (`manifest.webmanifest` + Service Worker ringan + Ikon aplikasi) tanpa lonceng notifikasi di topbar.

- [x] **O3 — Ekspor Laporan CSV/Excel pada Halaman Arsip & Rekapitulasi**
  - Tujuan: Memudahkan Kasi Pelayanan dan Kepala Kampung mengunduh data arsip surat & rekapitulasi bulanan dalam format `.csv` (kompatibel penuh dengan Microsoft Excel menggunakan UTF-8 BOM) menggunakan `response()->streamDownload()` bawaan Laravel 12 tanpa pustaka berat tambahan, ditambah bar proporsi visual pada tab Rekapitulasi Bulanan.
  - File yang diimplementasikan:
    - `app/Http/Controllers/ArsipLaporanController.php` (method `eksporCsv(Request $request)` dengan `response()->streamDownload()`, mengikuti filter `bulan`, `tahun`, `id_layanan`, `status`, dan `q`; NIK tetap disamarkan via `samarkan_nik()`)
    - `routes/web.php` (rute `GET /arsip-laporan/ekspor-csv` bernama `arsip.ekspor-csv`)
    - `resources/views/arsip/index.blade.php` (tombol **Ekspor Excel/CSV** di samping **Cetak PDF** + kartu **Visualisasi Proporsi Pengajuan per Layanan** di tab Rekapitulasi Bulanan)
  - [x] Implementasi & Verifikasi (`tests/Feature/BacklogOpsionalPrioritasTest.php`)

- [x] **O6 — Audit Log Aktivitas Petugas (Keamanan & Jejak Basis Data)**
  - Tujuan: Mencatat jejak audit otomatis di belakang layar untuk aksi penting petugas (koreksi profil/username warga, reset password warga, nonaktifkan/aktifkan akun warga, hapus arsip *soft delete*, tambah/ubah/hapus/toggle persyaratan layanan, ubah pengaturan kampung, dan unggah/hapus TTD & stempel Kepala) tanpa menambah langkah kerja petugas.
  - File yang diimplementasikan:
    - `database/migrations/2026_11_03_000001_create_audit_log_table.php` *(baru — tabel `audit_log` dengan `id_audit`, `id_pengguna` FK, `aksi`, `modul`, `target_label`, `detail_perubahan` JSON, `ip_address`, `created_at` + index)*
    - `app/Models/AuditLog.php` & `app/Services/AuditLogService.php` *(baru — pencatatan terpusat dengan sanitasi otomatis agar password dan NIK utuh tidak pernah bocor ke log)*
    - Integrasi pencatatan pada `Kasi\WargaController`, `Kasi\PersyaratanLayananController`, `Kasi\PengaturanController`, `Kepala\ProfilTtdController`, dan `ArsipLaporanController::destroy`
    - `resources/views/arsip/index.blade.php` & `app/Http/Controllers/ArsipLaporanController.php` (tampil sebagai Tab ke-3 **"Log Aktivitas"** responsif: kartu di mobile `< md`, tabel di desktop `md+`)
  - [x] Implementasi & Verifikasi (`tests/Feature/BacklogOpsionalPrioritasTest.php`)

- [x] **O8 — PWA Dasar (Installable Web App ke Layar Utama HP)**
  - Tujuan: Memungkinkan warga dan petugas meng-install **SIRUKTI** ke layar utama (*Home Screen*) HP Android/iOS layaknya aplikasi native tanpa memperumit tampilan UI.
  - File yang diimplementasikan:
    - `public/manifest.webmanifest` *(baru — konfigurasi `name`, `short_name: SIRUKTI`, `start_url: /`, `display: standalone`, `background_color: #F2F5EF`, `theme_color: #2F5D3A`, dan daftar ikon)*
    - `public/sw.js` *(baru — Service Worker ringan dengan strategi *network-first* khusus GET agar token CSRF dan status surat selalu segar)*
    - `public/icons/icon-192.png`, `public/icons/icon-512.png`, & `public/icons/icon.svg` *(baru — ikon identitas hijau-emas SIRUKTI)*
    - `resources/views/components/pwa-install-banner.blade.php` *(baru — komponen Alpine.js pemicu 1-klik `beforeinstallprompt` + modal panduan ringkas Android/iOS/Desktop)*
    - `resources/views/layouts/app.blade.php`, `resources/views/layouts/guest.blade.php`, `resources/views/landing.blade.php`, `resources/views/auth/login.blade.php`, & `resources/views/auth/register.blade.php` (tag `<link rel="manifest">`, `<meta name="theme-color">`, `<link rel="apple-touch-icon">`, tombol ringkas **Instal** khusus di halaman publik desktop & mobile, dan registrasi `navigator.serviceWorker`)
  - [x] Implementasi & Verifikasi (`tests/Feature/BacklogOpsionalPrioritasTest.php` — 5 test lulus, 46 assertions)

> ✅ Revisi Pasca-v1.0 — Batch 2 selesai (O3, O6, O8 tuntas dan terverifikasi: 5 test baru lulus, 46 assertions) — 3 November 2026

---

# BACKLOG OPSIONAL — JANGAN dikerjakan sebelum pengguna memerintahkan

Ditambahkan setelah sistem v1.0 selesai (lihat PRD §11):
- [ ] O1 Notifikasi WhatsApp otomatis
- [ ] O2 Lupa password mandiri (OTP/WhatsApp)
- [x] O3 Ekspor Excel/CSV dan grafik dashboard (Selesai pada Revisi Batch 2 — Ekspor CSV ramah Excel + Bar Visual Rekapitulasi)
- [ ] O4 Penambahan jenis layanan baru dengan form dinamis
- [ ] O5 Landing page: berita/pengumuman dan struktur organisasi
- [x] O6 Audit log aktivitas pengguna (Selesai pada Revisi Batch 2 — Tabel `audit_log`, `AuditLogService`, & Tab Log Aktivitas)
- [x] O7 Pembatalan pengajuan oleh warga (Selesai pada Perbaikan 12)
- [x] O8 PWA dan notifikasi dalam aplikasi (Selesai pada Revisi Batch 2 — PWA Installable Web Manifest, Service Worker & Ikon)
- [ ] O9 CRUD akun petugas lewat antarmuka
- [ ] O10 Tanda tangan elektronik tersertifikasi
