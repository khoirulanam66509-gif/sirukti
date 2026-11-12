> **Lokasi file:** `.agents/PRD.md` — dokumen referensi utama untuk AI coding agent, jangan dipindah dari folder `.agents/`.

# Product Requirements Document (PRD)
# SIRUKTI — Sistem Layanan Surat Digital Kampung Rukti Harjo

**Kode proyek:** sirukti
**Versi:** 1.0 (upgrade dan bangun ulang dari sistem skripsi, dibangun dari nol)
**Tanggal:** Oktober 2026
**Konteks:** Tugas project UTS mata kuliah Basis Data Lanjut — memperbaiki sistem hasil skripsi kakak tingkat
**Sumber sistem lama:** skripsi "Rancang Bangun Sistem Layanan Masyarakat Berbasis Web di Balai Desa Rukti Harjo" (I Wayan Warna Widnyana, Universitas Muhammadiyah Metro, 2025)
**Stack:** Laravel 12 (PHP 8.2) + MySQL (XAMPP) + Blade + Alpine.js + Tailwind CSS (Vite)
**Prinsip utama:** **Mobile first** — warga mengakses lewat handphone; tampilan tetap responsif di tablet dan desktop.
**Sumber desain:** proyek Google Stitch **`UTS Dekstop`** (hanya tampilan desktop 1280px) yang dibaca AI agent lewat MCP; versi mobile diturunkan menurut §9.4.

---

## 1. Latar Belakang

Kampung Rukti Harjo (Kecamatan Seputih Raman, Kabupaten Lampung Tengah) berpenduduk sekitar 5.990 jiwa dan 1.780 KK (data September 2025, sumber skripsi). Menurut skripsi, Balai Desa menerima sekitar 4–7 pengajuan surat per hari (SKCK, SKTM, Domisili, SKU). Pencatatan masih memakai buku besar manual, validasi berkas dilakukan manual, warga kerap bolak-balik karena berkas kurang, dan rekap laporan lambat.

Sistem skripsi sudah menjawab sebagian masalah itu (registrasi warga, pengajuan dengan upload KTP/KK, status Menunggu/Diproses/Selesai, arsip dan laporan), tetapi dibatasi oleh batasan masalah. **SIRUKTI** adalah upgrade yang memperbaiki tampilan, responsivitas (mobile first), kemudahan penggunaan, kualitas basis data, dan menambah fitur penting yang tidak sempat dikerjakan skripsi.

### Keterbatasan sistem lama yang diperbaiki

1. Data warga tidak memuat NIK dan identitas yang dibutuhkan surat (form daftar hanya nama, username, password, no HP, alamat).
2. Persyaratan layanan disimpan sebagai satu kolom teks; berkas tidak terhubung ke syarat tertentu; tidak ada penilaian per berkas.
3. Tidak ada riwayat perubahan status dan tidak ada pencatat verifikator.
4. Surat dibuat manual oleh petugas; tanpa template, nomor surat otomatis, atau cara memverifikasi keaslian.
5. Kepala Desa hanya ada di flowchart, tidak menjadi pengguna sistem.
6. Fitur "hapus riwayat" menghapus data permanen.
7. Tampilan dirancang untuk desktop; sidebar dan tabel lebar tidak nyaman di handphone.
8. Statistik di halaman depan tidak berasal dari data sistem.

## 2. Tujuan Produk

1. Warga dapat mengajukan surat kapan saja dari handphone tanpa datang ke kantor.
2. Berkas persyaratan diunggah, diperiksa satu per satu, dan kekurangannya jelas (alasan per berkas) sehingga warga tidak bolak-balik.
3. Surat dibuat otomatis (PDF) dari data warga dan isian pengajuan, lengkap dengan nomor surat otomatis dan QR keaslian.
4. Kepala Kampung menyetujui dan menandatangani surat langsung di sistem.
5. Warga memantau status lewat timeline dan mengunduh surat jadi.
6. Petugas memperoleh arsip dan laporan bulanan/tahunan tanpa buku register fisik.
7. Basis data terstruktur (ternormalisasi, ber-constraint, memiliki trigger, stored procedure, view, dan index) sebagai inti tugas Basis Data Lanjut.

## 3. Target Pengguna (Role)

| Role | Deskripsi | Nilai `role` |
|---|---|---|
| **Warga** | Penduduk Kampung Rukti Harjo; mendaftar sendiri, mengajukan surat, memantau status, mengunduh surat | `warga` |
| **Kasi Pelayanan** | Petugas Balai Desa; memverifikasi berkas, menyiapkan surat, mengelola warga, persyaratan, pengaturan, dan laporan | `kasi` |
| **Kepala Kampung** | Menyetujui dan menandatangani surat secara digital; melihat arsip dan laporan | `kepala` |

> Akun **Kasi** dan **Kepala** dibuat lewat seeder atau perintah Artisan (tidak ada registrasi publik untuk keduanya). Tidak ada role Admin terpisah; Kasi memegang pengelolaan operasional.

## 4. Ruang Lingkup Fitur

### 4.1 Publik dan Autentikasi

| No | Fitur | Deskripsi | Akses |
|---|---|---|---|
| A1 | Landing Page | Hero singkat, 4 langkah cara mengajukan, daftar 4 layanan beserta syaratnya (dari database), statistik pelayanan, jam layanan dan kontak (tautan WhatsApp kantor), FAQ singkat | Publik |
| A2 | Registrasi Warga | Form ringkas: nama lengkap, NIK (16 digit), nomor HP/WhatsApp, password. Data profil lengkap dilengkapi kemudian (W1) | Publik |
| A3 | Login / Logout | Login memakai **NIK (warga) atau username (petugas)** dan password; diarahkan ke beranda sesuai role | Semua |
| A4 | Ubah Password | Pengguna mengganti password sendiri; wajib bila `wajib_ganti_password` aktif | Semua |
| A5 | Verifikasi Keaslian Surat | Halaman publik hasil pindai QR pada surat: nomor, jenis surat, nama pemohon (NIK disamarkan), tanggal terbit, penandatangan, status valid | Publik |

### 4.2 Warga

| No | Fitur | Deskripsi |
|---|---|---|
| W1 | Profil dan Kelengkapan Data | Tempat/tanggal lahir, jenis kelamin, agama, status perkawinan, pekerjaan, alamat, dusun, RT/RW, no. KK. **Wajib lengkap sebelum mengajukan surat pertama** |
| W2 | Ajukan Surat (Stepper) | 4 langkah: pilih layanan → isi data surat → unggah berkas → tinjau dan kirim |
| W3 | Unggah Berkas Persyaratan | Ambil foto langsung dari kamera atau pilih dari galeri; kompresi gambar otomatis di browser; pratinjau; validasi tipe dan ukuran |
| W4 | Status dan Timeline | Timeline status pengajuan, status tiap berkas (menunggu/sesuai/perlu perbaikan) beserta alasan dari petugas |
| W5 | Unggah Ulang dan Kirim Ulang | Mengganti hanya berkas yang perlu perbaikan, lalu mengirim ulang tanpa membuat pengajuan baru |
| W6 | Riwayat dan Unduh Surat | Daftar pengajuan, filter status, unduh PDF surat yang sudah selesai |
| W7 | Beranda Warga | Sapaan, tombol Ajukan Surat, pengajuan aktif beserta progres, riwayat terbaru |

### 4.3 Kasi Pelayanan

| No | Fitur | Deskripsi |
|---|---|---|
| K1 | Beranda dan Antrean | Kartu ringkasan (menunggu verifikasi, kurang berkas, diproses, menunggu TTD, selesai bulan ini) dan antrean pengajuan; pencarian nama/NIK/no. pengajuan; filter status dan layanan |
| K2 | Verifikasi Berkas | Melihat pratinjau tiap berkas, menandai **sesuai** atau **perlu perbaikan** + alasan |
| K3 | Pengelolaan Status | Ubah status: kurang berkas, diproses, ajukan ke Kepala, atau tolak (dengan alasan); mengikuti alur status (bagian 7) |
| K4 | Pratinjau Draft Surat | Pratinjau PDF surat sebelum diajukan ke Kepala (nomor surat belum diberikan) |
| K5 | Kelola Warga | Cari warga, lihat dan koreksi data, reset password (password sementara), nonaktifkan akun |
| K6 | Kelola Persyaratan | Ubah nama, petunjuk, wajib/opsional, urutan, dan status aktif syarat pada 4 layanan. **Tidak ada tambah/hapus jenis layanan** |
| K7 | Pengaturan Kampung | Nama dan alamat kantor, kontak/WhatsApp, jam layanan, statistik kampung, kode kampung untuk nomor surat |
| K8 | Arsip dan Laporan | Rekap bulanan/tahunan per layanan dan status (stored procedure), daftar arsip, cetak laporan PDF, hapus arsip (soft delete) |

### 4.4 Kepala Kampung

| No | Fitur | Deskripsi |
|---|---|---|
| P1 | Daftar Persetujuan | Pengajuan berstatus `menunggu_ttd` |
| P2 | Setujui dan Tandatangani | Memeriksa ringkasan dan pratinjau surat, lalu menyetujui; sistem membuat nomor surat otomatis, menempelkan gambar tanda tangan dan QR, menyimpan PDF final, dan mengubah status menjadi `selesai` |
| P3 | Kembalikan dengan Catatan | Mengembalikan ke Kasi (status `diproses`) disertai catatan perbaikan |
| P4 | Profil dan Tanda Tangan | Mengunggah gambar tanda tangan, mengganti password |
| P5 | Arsip dan Laporan | Melihat arsip dan laporan (hanya baca) |

### 4.5 Dashboard

| No | Fitur | Deskripsi |
|---|---|---|
| D1 | Dashboard Warga | Lihat W7 |
| D2 | Dashboard Kasi | Lihat K1 |
| D3 | Dashboard Kepala | Kartu menunggu TTD, selesai bulan ini, dan daftar persetujuan terbaru |

Dashboard hanya berupa kartu angka dan daftar (tanpa grafik pada v1.0).

## 5. Alur Utama (User Flow)

### 5.1 Pengajuan sampai Surat Jadi
1. Warga membuka SIRUKTI dari handphone, **mendaftar** (nama, NIK, no. HP, password) lalu **login**.
2. Warga **melengkapi profil** (sekali saja) bila belum lengkap.
3. Warga memilih layanan, mengisi data surat (mis. keperluan), mengunggah berkas syarat (kamera/galeri), meninjau, lalu **mengirim**. Status: `menunggu`. Nomor pengajuan `PG-YYYYMMDD-XXXX` dibuat sistem.
4. Kasi membuka antrean, memeriksa tiap berkas:
   - Ada berkas **perlu perbaikan** → status `kurang_berkas` dengan alasan per berkas. Warga mengunggah ulang berkas tersebut dan menekan **Kirim Ulang** → status kembali `menunggu`.
   - Semua berkas **sesuai** → status `diproses`.
5. Kasi memeriksa pratinjau draft surat, lalu **mengajukan ke Kepala** → status `menunggu_ttd`. (Kasi dapat menolak pengajuan dengan alasan → `ditolak`.)
6. Kepala memeriksa dan **menyetujui**: sistem membuat nomor surat, menempelkan tanda tangan digital dan QR, menyimpan PDF final → status `selesai`. Atau **mengembalikan** dengan catatan → `diproses`.
7. Warga melihat status `selesai` dan **mengunduh surat** (PDF). Pihak ketiga dapat memindai QR untuk memverifikasi keaslian (A5).

### 5.2 Arsip dan Laporan
Kasi/Kepala memilih periode (bulan/tahun) dan melihat rekap jumlah pengajuan per layanan dan status, rata-rata waktu penyelesaian, serta mencetak PDF. Kasi dapat memindahkan arsip ke "terhapus" (soft delete).

## 6. Aturan Bisnis

1. NIK terdiri dari 16 digit angka dan **unik**; satu NIK hanya satu akun.
2. Pengajuan hanya dapat dibuat bila profil warga lengkap dan layanan berstatus aktif.
3. Satu warga hanya boleh memiliki **satu pengajuan aktif per jenis layanan** (status selain `selesai` dan `ditolak`).
4. Semua syarat **wajib** harus diunggah sebelum pengajuan dikirim.
5. Berkas hanya boleh JPG, JPEG, PNG, atau PDF dengan ukuran maksimal 2 MB (gambar dikompres di browser sebelum diunggah). Berkas disimpan di penyimpanan **privat** dan hanya dapat dibuka lewat rute terotorisasi.
6. Warga hanya dapat melihat pengajuan dan berkas miliknya; Kasi dan Kepala dapat melihat semua.
7. Status `kurang_berkas` bila minimal satu berkas `perlu_perbaikan`; `diproses` hanya bila semua berkas `sesuai`.
8. Pengajuan ke Kepala hanya dapat dilakukan dari status `diproses`; persetujuan hanya dari `menunggu_ttd`.
9. **Nomor surat dibuat saat Kepala menyetujui**, atomik lewat stored procedure, berurutan per layanan per tahun, dan unik. Format: `{urut 3 digit}/{KODE}/{kode kampung}/{bulan romawi}/{tahun}` (contoh `001/SKCK/RH/X/2026`).
10. Setiap perubahan status tercatat otomatis di `status_log` (trigger) beserta pelakunya.
11. Pengajuan berstatus final (`selesai`, `ditolak`) tidak dapat diubah statusnya (ditegakkan trigger). Koreksi surat yang sudah selesai dilakukan dengan pengajuan baru.
12. "Hapus" arsip adalah **soft delete**; data tidak hilang fisik.
13. Data yang sudah dipakai transaksi tidak dapat dihapus (relasi restrict); akun warga dinonaktifkan, bukan dihapus.
14. Password disimpan ter-hash (bcrypt). Reset password oleh Kasi menghasilkan password sementara dan mewajibkan penggantian saat login berikutnya.
15. Penghasilan (SKTM) dan seluruh isian pengajuan divalidasi server-side sesuai spesifikasi layanan (lihat `schema-database.md` bagian 8).
16. Hanya akun Kepala yang telah mengunggah gambar tanda tangan yang dapat menyetujui surat.
17. Tanda tangan digital pada v1.0 berupa **gambar tanda tangan + QR verifikasi**, bukan tanda tangan elektronik tersertifikasi.

## 7. Status

**Status Pengajuan**

| Status | Warna | Keterangan |
|---|---|---|
| `menunggu` | Amber | Baru dikirim/dikirim ulang, menunggu verifikasi Kasi |
| `kurang_berkas` | Oranye | Ada berkas perlu perbaikan; menunggu warga mengunggah ulang |
| `diproses` | Biru | Berkas lengkap; Kasi menyiapkan surat |
| `menunggu_ttd` | Ungu | Menunggu persetujuan dan tanda tangan Kepala |
| `selesai` | Hijau | Surat terbit dan dapat diunduh |
| `ditolak` | Merah | Pengajuan ditolak dengan alasan |

**Alur perpindahan status yang diizinkan**

| Dari | Ke | Oleh |
|---|---|---|
| (baru) | `menunggu` | Warga |
| `menunggu` | `kurang_berkas`, `diproses`, `ditolak` | Kasi |
| `kurang_berkas` | `menunggu` | Warga (kirim ulang) |
| `kurang_berkas` | `ditolak` | Kasi |
| `diproses` | `menunggu_ttd`, `kurang_berkas`, `ditolak` | Kasi |
| `menunggu_ttd` | `selesai`, `diproses` | Kepala |
| `selesai`, `ditolak` | — (final) | — |

**Status Berkas:** `menunggu` (abu-abu), `sesuai` (hijau), `perlu_perbaikan` (oranye).
**Status Akun:** aktif (hijau) dan nonaktif (abu-abu).

## 8. Perubahan terhadap Sistem Skripsi

| Aspek | Sistem skripsi | SIRUKTI |
|---|---|---|
| Teknologi | PHP native + MySQL | Laravel 12 + MySQL |
| Tabel | 4 (`users`, `layanan`, `pengajuan`, `berkas_pengajuan`) | 11 tabel ternormalisasi (lihat `schema-database.md`) |
| Identitas warga | Nama, username, no HP, alamat | NIK + profil kependudukan terpisah (`warga`), dusun, RT/RW |
| Persyaratan | Kolom teks tunggal | Tabel `syarat_layanan`; berkas terhubung ke syarat |
| Verifikasi berkas | Per pengajuan | Per berkas, dengan alasan |
| Status | 3 (+ Kurang Berkas) tanpa riwayat | 6 status + `status_log` otomatis (trigger) |
| Surat | Dibuat manual, diunggah petugas | PDF otomatis, nomor surat atomik, QR verifikasi |
| Kepala Desa | Di luar sistem | Role `kepala` dengan persetujuan dan tanda tangan digital |
| Hapus riwayat | Hapus permanen | Soft delete |
| Basis data | Tanpa objek terprogram | Trigger, stored procedure, view, index, CHECK, transaction |
| Tampilan | Desktop-oriented | Mobile first, responsif tablet dan desktop |
| Layanan | NA (tidak jelas), SKCK, SKTM, Domisili, SKU | Tetap **4 layanan**: SKCK, Domisili, SKTM, SKU |

## 9. Prinsip Desain (Mobile First)

### Sumber Desain: Google Stitch `UTS Dekstop`

**Sumber kebenaran tampilan adalah proyek Google Stitch bernama `UTS Dekstop`**, dibaca AI coding agent melalui **MCP Stitch**. Ketentuan:

1. Proyek `UTS Dekstop` hanya berisi tampilan **desktop (1280px)**. **Tidak ada layar mobile di Stitch.** Versi mobile (360px) **diturunkan** dari layar desktop sesuai §9.4 dan **dibangun lebih dahulu** (mobile first); tampilan desktop dibangun kemudian mengikuti layar Stitch.
2. **Pembagian wewenang:** Stitch menentukan **tampilan visual** (tata letak desktop, warna, tipografi, jarak, bentuk komponen); **PRD menentukan fitur, isi, aturan bisnis, dan teks**. Bila layar Stitch memuat elemen di luar PRD (mis. grafik, kolom pencarian global, ikon notifikasi, kartu persentase, teks bukan Bahasa Indonesia), elemen itu **tidak diimplementasikan** dan dicatat di `tasklist.md`.
3. **Design token** (warna, font, radius, bayangan, jarak) diambil dari design system proyek Stitch dan diekstrak satu kali ke tema Tailwind (Fase 2). Nilai di §9.2 hanya cadangan bila Stitch tidak menyediakan token tertentu.
4. Bila layar yang dibutuhkan tidak ada di Stitch, atau MCP tidak terhubung atau proyek tidak ditemukan, agent **berhenti dan melapor**; tidak mengarang desain.
5. HTML dari Stitch hanya **acuan visual**; implementasinya ditulis ulang sebagai Blade Component dengan token tema, bukan salinan mentah.

### 9.1 Prinsip
1. **Bangun untuk 360px dahulu**, lalu tambah breakpoint `sm:`/`md:`/`lg:`; tidak boleh ada scroll horizontal pada halaman.
2. Navigasi: **bottom navigation** di layar kecil (maks. 4–5 item, dengan safe-area), berubah menjadi **sidebar kiri** mulai `lg` (bentuk sidebar mengikuti Stitch); pada tablet (`md`) gunakan bottom nav yang diperlebar atau rail ikon.
3. Daftar data tampil sebagai **kartu** di layar kecil dan **tabel** mulai `md`.
4. Target sentuh minimal **44×44 px**, ukuran teks dasar **16px** (mencegah zoom otomatis pada input iOS), kontras minimal WCAG AA.
5. Satu aksi utama per layar; tombol aksi utama berada di area jempol (sticky bottom bar pada form panjang dan halaman detail).
6. Form: label selalu terlihat, `inputmode`/`autocomplete` tepat (`inputmode="numeric"` untuk NIK dan no. HP), pesan galat spesifik berbahasa Indonesia, tidak menghapus isian saat validasi gagal.
7. Bahasa sederhana untuk warga desa (mis. "Kirim Pengajuan", "Foto KTP", bukan istilah teknis); ikon selalu disertai teks.
8. Hemat kuota: gambar dikompres sebelum unggah, tanpa pustaka berat, font di-self-host, pagination pada semua daftar.
9. Warna status konsisten di seluruh sistem (bagian 7).

### 9.2 Design Token Cadangan
Identitas hijau-emas dipertahankan dari sistem skripsi. Nilai berikut adalah brief awal yang dikirim ke Stitch. **Sumber final adalah design system proyek Stitch**; bila berbeda, ikuti Stitch.

| Token | Nilai awal | Pemakaian |
|---|---|---|
| `primary` | hijau tua (acuan `#2F5D3A`) + skala 50–900 | tombol utama, kartu sorotan, menu aktif |
| `accent` | emas (acuan `#E6B73C`) | sorotan, tombol sekunder, badge |
| `surface` | `#FFFFFF`; latar halaman `#F2F5EF` | kartu dan latar |
| `ink` | `#1E2A22` (teks utama), `#66736A` (teks redup) | teks |
| Font | Plus Jakarta Sans (self-host) | seluruh teks |
| Radius | kartu 24px, tombol/input 14px, badge pil | |
| Bayangan | lembut dan rendah | kartu |

Warna status mengikuti bagian 7 (Menunggu amber, Kurang Berkas oranye, Diproses biru, Menunggu TTD ungu, Selesai hijau, Ditolak merah).

### 9.3 Daftar Layar (nama layar di Stitch)

Nama layar di Stitch memakai pola **`{Kode} {Nama} — Desktop`**. Bila nama yang ditemukan lewat MCP berbeda, agent mencocokkan berdasarkan isi layar dan mencatat pemetaannya di bagian "Pemetaan Layar Stitch" pada `tasklist.md`. **Kode layar di sini bukan kode fitur §4.** Kolom kanan hanya memuat komposisi **mobile (turunan, 360px)**; komposisi desktop mengikuti layar Stitch.

| Kode | Layar | Role | Komposisi mobile (turunan) |
|---|---|---|---|
| L1 | Landing | Publik | Hero dengan tombol Masuk/Daftar bertumpuk, kartu statistik, 4 langkah vertikal, kartu layanan vertikal, jam layanan/kontak, FAQ accordion |
| L2 | Daftar | Publik | Header hijau kecil, form 1 kolom (4 field), tombol penuh lebar |
| L3 | Masuk | Publik | Sama seperti L2 |
| L4 | Verifikasi Surat — Valid dan — Tidak Valid | Publik | Kartu hasil penuh lebar dengan rincian |
| W1 | Beranda Warga (+ varian banner profil belum lengkap) | Warga | Sapaan, tombol Ajukan Surat, pengajuan aktif dengan progres, 3 riwayat terakhir, bottom nav |
| W2 | Profil Saya | Warga | Accordion (Data Diri, Alamat), indikator kelengkapan, bar simpan sticky |
| W3 | Ajukan Surat — Langkah 1 Pilih Layanan | Warga | Kartu layanan bertumpuk, stepper ringkas "Langkah n dari 4", bar Lanjut sticky |
| W3 | Ajukan Surat — Langkah 2 Isi Data | Warga | 1 kolom; kartu data diri ringkas di atas form |
| W3 | Ajukan Surat — Langkah 3 Unggah Berkas | Warga | Slot berkas bertumpuk, tombol Ambil Foto/Pilih dari Galeri besar |
| W3 | Ajukan Surat — Langkah 4 Tinjau dan Kirim (+ state terkirim) | Warga | Ringkasan bertumpuk, bar Kirim sticky |
| W4 | Riwayat Pengajuan | Warga | Kartu + filter chip yang dapat digeser |
| W5 | Detail Pengajuan — Kurang Berkas dan — Selesai | Warga | Timeline vertikal lalu daftar berkas; tombol sticky Kirim Ulang atau Unduh Surat |
| U1 | Ubah Password (+ varian wajib ganti) | Semua | 1 kolom |
| K1 | Beranda Kasi | Kasi | Kartu ringkasan grid 2 kolom, antrean terbaru berbentuk kartu |
| K2 | Antrean Pengajuan | Kasi | Kolom cari + filter chip, kartu pengajuan |
| K3 | Detail dan Verifikasi (+ Dialog Perlu Perbaikan) | Kasi | Data pemohon, accordion berkas dengan pratinjau, bar aksi sticky; dialog menjadi bottom sheet |
| K4 | Pratinjau Surat (Draft) | Kasi | Kertas A4 diperkecil sesuai lebar layar (dapat dicubit), tombol sticky |
| K5 | Kelola Warga (+ Dialog Reset Password) | Kasi | Kartu warga dengan menu titik tiga; dialog menjadi bottom sheet |
| K6 | Persyaratan Layanan | Kasi | Accordion per layanan, baris syarat bertumpuk |
| K7 | Pengaturan Kampung | Kasi | Kartu form bertumpuk, bar simpan sticky |
| K8 | Arsip dan Laporan | Kasi, Kepala (hanya baca) | Filter periode, kartu rekap grid 2 kolom, daftar arsip berbentuk kartu |
| P1 | Beranda Kepala | Kepala | Kartu menunggu TTD dan selesai bulan ini, daftar persetujuan berbentuk kartu |
| P2 | Detail Persetujuan (+ Dialog Kembalikan) | Kepala | Ringkasan, berkas, kartu pratinjau surat, bar sticky Setujui/Kembalikan; dialog menjadi bottom sheet |
| P3 | Profil dan Tanda Tangan | Kepala | Kartu bertumpuk (akun, tanda tangan, ubah password) |
| E1 | Halaman Error (404; gaya sama untuk 403/500) | Semua | Konten di tengah tanpa navigasi |

### 9.4 Aturan Menurunkan Layar Desktop Stitch ke Mobile

Karena Stitch hanya memuat desktop, agent menurunkan mobile dengan aturan **tetap** berikut (token visual tetap sama dengan Stitch):

1. **Sidebar → bottom navigation** dengan item per role:
   - Warga (4): Beranda, Ajukan (di tengah, ditonjolkan), Riwayat, Akun (berisi Profil Saya, Ubah Password, Keluar).
   - Kasi (5): Beranda, Antrean, Warga, Arsip, Lainnya (bottom sheet berisi Persyaratan Layanan, Pengaturan Kampung, Ubah Password, Keluar).
   - Kepala (4): Beranda, Persetujuan, Arsip, Akun (berisi Profil dan Tanda Tangan, Ubah Password, Keluar).
2. **Header** desktop (judul + tanggal + avatar) → top bar ringkas (judul halaman + avatar kecil).
3. **Tabel → kartu** (kolom penting menjadi baris kartu, aksi di tombol atau menu titik tiga); toolbar filter → kolom cari + chip yang dapat digeser.
4. **Multi-kolom → 1 kolom**; kolom sekunder (mis. kartu ringkasan) dipindah di bawah atau di atas konten utama sesuai prioritas tugas.
5. **Kartu KPI sebaris → grid 2 kolom**; kartu terakhir penuh lebar bila jumlahnya ganjil.
6. **Modal → bottom sheet**; **stepper horizontal → "Langkah n dari 4" + bar progres**.
7. **Halaman detail dan form panjang**: bottom nav diganti **sticky bottom action bar** berisi aksi utama.
8. **Hero landing**: teks dan kartu statistik bertumpuk; navbar menjadi header ringkas dengan tombol Masuk.
9. **Pratinjau PDF**: kertas A4 diperkecil sesuai lebar layar dengan petunjuk "Cubit untuk memperbesar".
10. **Tablet (`md`)**: kartu 2 kolom, bottom nav diperlebar atau rail ikon; mulai `lg` memakai tata letak desktop Stitch.
11. Hasil turunan mobile **ditinjau pengguna** pada Fase 15; penyesuaian disepakati sebelum dianggap final.

## 10. Non-Functional Requirements

| Aspek | Requirement |
|---|---|
| Mobile first | Seluruh halaman nyaman di 360px; tidak ada scroll horizontal; target sentuh ≥ 44px |
| Performa | Ringan di jaringan seluler dan handphone kelas menengah-bawah; pagination; gambar dikompres sebelum unggah; target Lighthouse mobile Performance dan Accessibility ≥ 85 |
| Keamanan | Password bcrypt, validasi server-side (Form Request), middleware role + Policy kepemilikan, CSRF, rate limiting login, berkas privat, validasi MIME dan ukuran, NIK disamarkan di daftar |
| Integritas Data | DB transaction pada pengajuan dan persetujuan; FK, CHECK, trigger, dan unique constraint |
| Privasi | KTP/KK tidak pernah dapat diakses lewat URL publik; halaman verifikasi hanya menampilkan data minimum |
| Kompatibilitas | PHP 8.2, Laravel 12, MySQL/MariaDB XAMPP; diuji di Chrome (Android dan desktop) |
| Zona waktu | `Asia/Jakarta`; sesi MySQL disetel sama agar `NOW()` pada trigger konsisten |
| Maintainability | Konvensi Laravel (MVC, Eloquent, Form Request, Service, Policy); model memakai primary key kustom |

## 11. Di Luar Ruang Lingkup v1.0 dan Fitur Opsional

**Di luar ruang lingkup:** layanan selain 4 surat; tanda tangan elektronik tersertifikasi (TTE/BSrE); integrasi data kependudukan resmi (Dukcapil); pembayaran; multi-desa; CRUD petugas lewat antarmuka (akun petugas dibuat lewat seeder/Artisan); pembatalan pengajuan oleh warga.

**Fitur opsional (ditunda; dikerjakan setelah sistem v1.0 selesai dan hanya atas perintah pengguna):**

1. Notifikasi WhatsApp otomatis (status berubah, kurang berkas, surat selesai).
2. Lupa password mandiri (OTP/WhatsApp).
3. Ekspor laporan Excel/CSV dan grafik dashboard.
4. Penambahan jenis layanan baru (kelahiran, kematian, kehilangan, pengantar nikah) dengan form dinamis.
5. Landing page: berita/pengumuman dan struktur organisasi.
6. Audit log aktivitas pengguna.
7. Pembatalan pengajuan oleh warga.
8. PWA (dapat dipasang di layar utama handphone) dan notifikasi dalam aplikasi.
9. CRUD akun petugas lewat antarmuka.
10. Tanda tangan elektronik tersertifikasi.

## 12. Asumsi dan Hal yang Perlu Dikonfirmasi

1. **Stack Laravel 12** (bangun ulang, bukan melanjutkan PHP native skripsi) adalah asumsi; dikonfirmasi pengguna.
2. **Format nomor surat** `NNN/KODE/RH/BULAN-ROMAWI/TAHUN` adalah asumsi; format resmi Balai Desa perlu dicocokkan.
3. **Redaksi surat** (4 template) disusun dari pola umum; sebaiknya dicocokkan dengan contoh surat resmi Balai Desa.
4. **Keabsahan surat digital**: institusi tujuan (mis. kepolisian untuk SKCK) belum tentu menerima surat tanpa cap basah; tindakan di luar sistem (mencetak dan membubuhkan cap) tetap dimungkinkan.
5. **Daftar dusun** mengikuti struktur organisasi di skripsi (Dusun I A s.d. IX); daftar final dikonfirmasi pengguna.
6. **Statistik kampung** awal (5.990 jiwa, 1.780 KK) diambil dari skripsi dan dapat diubah lewat Pengaturan Kampung.
7. **Registrasi warga terbuka**: keabsahan NIK diperiksa Kasi saat memverifikasi foto KTP; tidak ada validasi ke sumber resmi.
8. Syarat berkas: SKCK, Domisili, dan SKTM membutuhkan KTP, KK, dan pengantar RT/RW; SKU menambah foto tempat usaha. Disesuaikan bila berbeda dengan ketentuan Balai Desa.
9. **Desain Stitch hanya versi desktop**; versi mobile diturunkan oleh agent menurut §9.4 dan perlu ditinjau pengguna (Fase 15).

## 13. Dokumen Terkait

1. `schema-database.md` — struktur tabel, relasi, trigger, stored procedure, view, dan index
2. `task-instruction.md` — mekanisme tasklist dan konvensi kerja AI coding agent
3. `tasklist.md` — breakdown task eksekusi per fase
4. Desain visual: proyek Google Stitch `UTS Dekstop` (dibaca lewat MCP; lihat §9)
