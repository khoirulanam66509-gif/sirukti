> **Lokasi file:** `.agents/task-instruction.md` — dokumen referensi utama untuk AI coding agent, jangan dipindah dari folder `.agents/`.
> Dokumen ini bekerja berdampingan dengan `.agents/PRD.md`, `.agents/schema-database.md`, dan `.agents/tasklist.md`.

# Task Instruction — SIRUKTI
**Tujuan:** Breakdown task eksekusi development untuk AI coding agent
**Basis:** `.agents/PRD.md`, `.agents/schema-database.md`
**Stack:** Laravel 12 (PHP 8.2) + MySQL (XAMPP) + Blade + Alpine.js + Tailwind CSS
**Prinsip:** **Mobile first** (warga memakai handphone), tetap responsif di tablet dan desktop
**Sumber desain:** proyek Google Stitch **`UTS Dekstop`** (hanya desktop) via MCP; versi mobile diturunkan menurut `PRD.md` §9.4

---

## 0. Mekanisme Tasklist (WAJIB dibaca sebelum mulai)

Sebelum mengerjakan Fase 0, AI agent **wajib** memastikan `.agents/tasklist.md` berisi checklist seluruh fase, format checkbox markdown.

**Aturan penggunaan tasklist:**
1. Jika `.agents/tasklist.md` **sudah ada dan berisi progres**, baca dulu untuk tahu posisi terakhir; jangan membuat ulang dari nol.
2. Setiap task selesai dan terverifikasi (kode berjalan tanpa error), ubah `- [ ]` menjadi `- [x]`.
3. Setelah satu fase 100% selesai, tambahkan baris ringkasan: `> ✅ Fase N selesai — tanggal`.
4. Jangan hapus riwayat checklist yang sudah tercentang; tasklist.md adalah log progres.
5. Task baru yang muncul di tengah jalan (bug fix tak terduga) ditambahkan sebagai sub-item di bawah fase terkait.
6. **Checklist QA responsif wajib ada di setiap fase yang menghasilkan halaman baru**, diuji di **360px, 768px, dan 1280px** sebelum lanjut ke fase berikutnya (tidak boleh ada scroll horizontal; target sentuh ≥ 44px). Pada 1280px bandingkan hasil dengan layar Stitch Desktop yang bersangkutan; pada 360px bandingkan dengan aturan `PRD.md` §9.4.
7. **Kerjakan satu fase pada satu waktu.** Setelah fase selesai, berhenti, laporkan ringkasan, dan tunggu perintah pengguna untuk fase berikutnya.
8. **Fitur opsional** (PRD §11) dan Backlog Opsional di `tasklist.md` **tidak boleh dikerjakan** sebelum pengguna memerintahkan secara eksplisit.

---

## 1. Sumber Desain: Google Stitch `UTS Dekstop` via MCP

1. Sebelum membangun halaman apa pun, ambil layar yang bersangkutan dari proyek Stitch bernama **`UTS Dekstop`** (cocokkan persis ejaan itu) melalui **MCP Stitch**: pilih proyek, daftar layar, lalu ambil screenshot dan HTML/kode layar yang sedang dikerjakan saja (jangan memuat semua layar sekaligus). Ikuti tata letak desktop, warna, tipografi, jarak, dan komponennya.
2. **Proyek Stitch hanya berisi desktop (1280px). Tidak ada layar mobile.** Bangun **mobile lebih dahulu** (360px) dengan menurunkan layar desktop memakai aturan tetap `PRD.md` §9.4 dan komposisi mobile di `PRD.md` §9.3; setelah itu tambahkan tata letak `lg:` yang mengikuti layar Stitch Desktop. Jangan menyalin layout desktop mentah ke mobile dan jangan mengarang pola mobile di luar §9.4.
3. Pada Fase 2, ambil **design system/token** dari proyek Stitch (warna, font, radius, bayangan, jarak) dan ekstrak **satu kali** ke konfigurasi tema Tailwind. Halaman berikutnya memakai token tersebut, bukan nilai hex yang ditulis ulang. Nilai di `PRD.md` §9.2 hanya cadangan bila Stitch tidak menyediakan token tertentu; bila berbeda, **Stitch menang**.
4. **Pembagian wewenang:** Stitch menang untuk **tampilan visual**; `PRD.md` menang untuk **fitur, isi, aturan bisnis, dan teks**. Elemen di layar Stitch yang berada di luar PRD (grafik, kolom pencarian global, ikon notifikasi, kartu persentase, "Upgrade", teks bukan Bahasa Indonesia, data contoh yang tidak sesuai) **tidak diimplementasikan**; catat di `tasklist.md` pada fase terkait.
5. Jangan mengarang desain. Jika layar yang dibutuhkan tidak ada di Stitch (termasuk varian dan dialog pada `PRD.md` §9.3), **berhenti dan tanyakan** ke pengguna.
6. Jika MCP Stitch tidak terhubung atau proyek `UTS Dekstop` tidak ditemukan, **berhenti dan laporkan**; jangan melanjutkan dengan desain karangan sendiri.
7. Nama layar Stitch memakai pola `{Kode} {Nama} — Desktop`. Pada Fase 0, buat pemetaan nama layar Stitch ↔ kode layar PRD di bagian "Pemetaan Layar Stitch" pada `tasklist.md`, lalu catat layar Stitch yang dipakai pada setiap task halaman (contoh: `Layar Stitch: "W3 Ajukan Surat — Langkah 1 Pilih Layanan — Desktop"`).
8. Teks antarmuka seluruhnya Bahasa Indonesia sederhana yang mudah dipahami warga desa.

## 2. Konvensi Umum

- Ikuti konvensi penamaan Laravel; **pengecualian:** nama tabel dan kolom mengikuti `schema-database.md` (bahasa Indonesia, PK `id_<tabel>`).
- Gunakan Form Request untuk validasi form; gunakan Policy untuk kepemilikan data.
- Gunakan Blade Component untuk elemen berulang (badge status, kartu, tabel/daftar responsif, modal, toast, stepper, timeline, bottom nav).
- Logika bisnis ditaruh di class Service (`PengajuanService`, `BerkasService`, `SuratService`), bukan di controller.
- Operasi yang melibatkan lebih dari satu tabel (kirim pengajuan + berkas, persetujuan + nomor surat + surat) **wajib** dalam `DB::transaction()`.
- Responsif: bangun untuk 360px dulu (turunan dari desktop Stitch menurut `PRD.md` §9.4), lalu tambah breakpoint `sm:`/`md:`/`lg:`; layar Stitch Desktop menjadi acuan tampilan mulai `lg:`.
- Pesan validasi, flash message, dan halaman error (403/404/419/500) berbahasa Indonesia.
- **Selalu update `.agents/tasklist.md` di akhir setiap fase.**

### Konvensi Teknis Wajib

1. **Model custom key.** Setiap model mendeklarasikan `$table` dan `$primaryKey` sesuai `schema-database.md` §4. `CounterSurat` memakai PK komposit (lihat catatan di skema).
2. **Tailwind:** periksa versi yang terpasang dari starter Laravel 12 sebelum menulis konfigurasi (v4 memakai blok `@theme` di CSS; v3 memakai `tailwind.config.js`). Jangan mencampur keduanya.
3. **Class Tailwind ditulis literal.** Class yang dirangkai string (`'bg-' . $warna`) tidak ter-generate. Untuk warna dinamis (badge status), pakai array berisi string class lengkap.
4. **Sesi dan cache:** `SESSION_DRIVER=file`, `CACHE_STORE=file`, `QUEUE_CONNECTION=sync` (tidak ada tabel sesi/cache/jobs).
5. **Autentikasi** memakai model `Pengguna` dan kolom `username` (warga: NIK). Registrasi publik hanya membuat role `warga`; akun `kasi` dan `kepala` dibuat lewat seeder atau perintah Artisan. Akun `status_aktif = false` ditolak. Terapkan rate limiting pada login.
6. **Hak akses:** pembatasan role lewat middleware `role:warga`, `role:kasi`, `role:kepala` dan Policy untuk kepemilikan (warga hanya melihat pengajuan/berkasnya sendiri). Rute di luar hak akses mengembalikan 403; jangan hanya menyembunyikan menu.
7. **Berkas privat.** Simpan di disk privat (`storage/app/private`), **bukan** `public`. Berkas dan PDF surat hanya dapat dibuka lewat controller yang memeriksa Policy (mis. `response()->file()`/`Storage::download`). Jangan pernah membuat URL publik untuk KTP/KK.
8. **Upload:** validasi MIME dan ukuran di server (JPG/PNG/PDF, ≤ 2 MB). Kompresi gambar dilakukan di browser (Canvas) sebelum unggah; server tetap memvalidasi. Bila transaksi DB gagal, hapus berkas yang sempat tersimpan.
9. **Objek basis data terprogram** (trigger, stored procedure, view, CHECK) dibuat lewat migration memakai `DB::unprepared()`/`DB::statement()` dengan `down()` yang benar (lihat `schema-database.md` §7). Aplikasi **tidak menulis langsung** ke `status_log` dan `counter_surat`.
10. **Perubahan status** selalu lewat `PengajuanService::ubahStatus()` yang memeriksa matriks perpindahan (PRD §7), menyetel `id_pengubah` dan `catatan_petugas` pada pernyataan UPDATE yang sama, dan menangani galat trigger (`SQLSTATE 45000`) menjadi pesan Indonesia.
11. **Nomor otomatis:** `no_pengajuan` = `PG-YYYYMMDD-XXXX` (dibuat di Service, unik); `no_surat` memakai stored procedure `sp_ambil_nomor_surat` dalam transaksi persetujuan.
12. **Mobile first:** `inputmode`/`autocomplete` tepat pada form; unggah foto memakai `accept="image/*"` dengan opsi `capture`; bottom navigation memperhatikan safe-area; tabel menjadi kartu di layar kecil; tidak memakai pustaka UI berat.
13. **Font** di-self-host lewat Vite; jangan menambahkan `<link>` CDN font manual.
14. **Zona waktu** `Asia/Jakarta` pada aplikasi dan koneksi MySQL (`'timezone' => '+07:00'`).
15. **Privasi:** NIK disamarkan di daftar/tampilan publik (helper `samarkan_nik()`); log aplikasi tidak boleh memuat NIK, password, atau isi berkas.
16. **Verifikasi sebelum mencentang task:** `npm run build`, `php artisan test`, `vendor\bin\pint --test`, lalu cek halaman terkait tanpa error di `storage/logs/laravel.log`.

17. **HTML dari Stitch hanya acuan visual.** Tulis ulang sebagai Blade Component dan class Tailwind berbasis token tema; jangan menempelkan HTML mentah, hex hardcode, atau gambar placeholder dari Stitch ke aplikasi.

## 3. Ringkasan Fase (detail di `.agents/tasklist.md`)

| Fase | Fokus |
|---|---|
| 0 | Setup Project (termasuk verifikasi MCP Stitch dan pemetaan layar) |
| 1 | Database (11 tabel, trigger, stored procedure, view, model, seeder) |
| 2 | Desain dan Layout (token dari Stitch, komponen UI, layout mobile first) |
| 3 | Landing Page Publik |
| 4 | Autentikasi, Registrasi Warga, dan Role |
| 5 | Profil Warga |
| 6 | Pengajuan Surat oleh Warga |
| 7 | Status, Riwayat, dan Unggah Ulang (Warga) |
| 8 | Verifikasi dan Pengelolaan Pengajuan (Kasi) |
| 9 | Pengaturan Kampung dan Tanda Tangan |
| 10 | Surat PDF dan Persetujuan Kepala |
| 11 | Verifikasi Keaslian Surat (Publik) |
| 12 | Kelola Warga dan Persyaratan Layanan (Kasi) |
| 13 | Dashboard (Warga, Kasi, Kepala) |
| 14 | Arsip dan Laporan |
| 15 | Polish, Responsif, dan Aksesibilitas |
| 16 | Testing dan QA |
| 17 | Dokumentasi Akhir |

## 4. Catatan Khusus

**Fase 6, 8, dan 10 (alur pengajuan sampai surat terbit)** adalah inti sistem. Wajib ada test untuk: validasi berkas (tipe/ukuran/syarat wajib), satu pengajuan aktif per layanan per warga, warga tidak dapat membuka pengajuan atau berkas warga lain, berkas privat tidak dapat diakses tanpa login, matriks perpindahan status (yang tidak valid ditolak), `status_log` tercatat otomatis, status final tidak dapat diubah (trigger), unggah ulang mengembalikan status berkas ke `menunggu`, persetujuan membuat nomor surat unik dan berurutan, dan rollback penuh bila pembuatan PDF gagal.

**Fase 1 (database):** test wajib menggunakan MySQL/MariaDB sungguhan (trigger dan stored procedure tidak tersedia di SQLite). Uji: trigger `trg_pengajuan_*`, `sp_ambil_nomor_surat` (urutan, tahun baru mulai dari 1, dua permintaan berurutan tidak menghasilkan nomor sama), `sp_rekap_periode`, view, CHECK NIK, dan constraint FK restrict.

**Fase 10 (surat PDF)** memakai `barryvdh/laravel-dompdf` dan pustaka QR (PNG via ekstensi `gd`); pastikan ekstensi `gd` dan `mbstring` aktif di `php.ini` XAMPP. Redaksi 4 template surat dicocokkan dengan contoh resmi Balai Desa bila tersedia.

**Data dummy** (`PengajuanSeeder`) dibuat agar dashboard dan arsip dapat diverifikasi; seluruh NIK dummy bukan NIK asli.

**Desain Stitch:** karena proyek `UTS Dekstop` hanya memuat desktop, risiko terbesar adalah tampilan mobile yang melenceng. Setiap fase halaman wajib menutup dengan perbandingan dua arah: (a) 1280px dibandingkan dengan screenshot layar Stitch, (b) 360px diperiksa terhadap aturan `PRD.md` §9.4. Selisih dicatat di `tasklist.md`; hasil turunan mobile ditinjau pengguna pada Fase 15.
