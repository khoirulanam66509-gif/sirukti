> **Lokasi file:** `.agents/schema-database.md` — dokumen referensi utama untuk AI coding agent, jangan dipindah dari folder `.agents/`.

# Schema Database — SIRUKTI
**Database:** MySQL/MariaDB (XAMPP)
**ORM:** Laravel Eloquent
**Versi:** 1.0
**Sumber:** Perancangan ulang dari 4 tabel sistem skripsi (`users`, `layanan`, `pengajuan`, `berkas_pengajuan`) menjadi 11 tabel ternormalisasi.

> **Catatan:** Skema ini adalah rancangan baru (bukan DDL resmi dari sumber lain). Bila pengguna mengubah rancangan, dokumen ini diperbarui terlebih dahulu sebelum migration disesuaikan.

---

## 1. Ringkasan Tabel

| Tabel | Fungsi |
|---|---|
| `dusun` | Master dusun (untuk alamat warga dan surat) |
| `pengguna` | Akun login semua role (warga, kasi, kepala) |
| `warga` | Profil kependudukan warga (NIK, data diri, alamat); 1 baris per akun warga |
| `layanan` | Master jenis surat (tetap 4: SKCK, DOM, SKTM, SKU) |
| `syarat_layanan` | Daftar berkas persyaratan per layanan |
| `pengajuan` | Header pengajuan surat oleh warga (status, data isian) |
| `berkas_pengajuan` | Berkas yang diunggah per syarat, beserta status verifikasi |
| `status_log` | Riwayat perubahan status pengajuan (diisi trigger) |
| `counter_surat` | Penghitung nomor urut surat per layanan per tahun |
| `surat` | Surat final yang diterbitkan (nomor, PDF, QR, penandatangan) |
| `pengaturan` | Konfigurasi kampung (profil, jam layanan, statistik, kode nomor surat) |

Tidak ada tabel `sessions`, `cache`, atau `jobs`. Sesi dan cache memakai driver `file`. Tidak ada tabel `password_reset_tokens` (lupa password mandiri adalah fitur opsional).

Objek basis data terprogram (lihat bagian 7): **3 trigger**, **2 stored procedure**, **2 view**.

---

## 2. Detail Tabel

Konvensi: nama tabel dan kolom memakai bahasa Indonesia `snake_case`; primary key bernama `id_<tabel>`. Tabel bertanda **(timestamps)** memiliki `created_at` dan `updated_at`.

### 2.1 `dusun`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_dusun | smallint unsigned, PK, auto increment | |
| nama_dusun | varchar(30), unique, not null | contoh `Dusun I A` |

### 2.2 `pengguna` (timestamps)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_pengguna | int unsigned, PK, auto increment | |
| nama | varchar(100), not null | nama lengkap |
| username | varchar(50), unique, not null | untuk warga berisi NIK; untuk petugas berisi username |
| password | varchar(255), not null | bcrypt |
| no_hp | varchar(20), nullable | nomor HP/WhatsApp |
| role | enum('warga','kasi','kepala'), not null, default 'warga' | |
| jabatan | varchar(60), nullable | untuk petugas (mis. "Kasi Pelayanan", "Kepala Kampung") |
| nip | varchar(25), nullable | opsional, untuk dicantumkan pada surat |
| file_ttd | varchar(255), nullable | path gambar tanda tangan (khusus `kepala`, penyimpanan privat) |
| wajib_ganti_password | boolean, not null, default false | true setelah reset password oleh Kasi |
| status_aktif | boolean, not null, default true | akun nonaktif tidak dapat login |

### 2.3 `warga` (timestamps)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_warga | int unsigned, PK, auto increment | |
| id_pengguna | int unsigned, FK → pengguna.id_pengguna, **unique**, not null | `cascadeOnDelete` |
| nik | char(16), unique, not null | CHECK hanya angka 16 digit |
| no_kk | char(16), nullable | CHECK hanya angka 16 digit bila terisi |
| tempat_lahir | varchar(50), nullable | |
| tgl_lahir | date, nullable | |
| jenis_kelamin | enum('L','P'), nullable | |
| agama | varchar(20), nullable | divalidasi pada daftar: Islam, Kristen, Katolik, Hindu, Buddha, Konghucu |
| status_perkawinan | enum('belum_kawin','kawin','cerai_hidup','cerai_mati'), nullable | |
| pekerjaan | varchar(60), nullable | |
| alamat | varchar(255), nullable | nama jalan/gang/nomor |
| id_dusun | smallint unsigned, FK → dusun.id_dusun, nullable | `restrictOnDelete` |
| rt | varchar(3), nullable | |
| rw | varchar(3), nullable | |

> Kolom profil dibuat nullable karena registrasi hanya meminta nama, NIK, no. HP, dan password. **Kelengkapan profil dicek di aplikasi** (method `Warga::profilLengkap()`): `tempat_lahir`, `tgl_lahir`, `jenis_kelamin`, `agama`, `status_perkawinan`, `pekerjaan`, `alamat`, `id_dusun`, `rt`, `rw`, `no_kk` wajib terisi sebelum pengajuan.

### 2.4 `layanan`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_layanan | smallint unsigned, PK, auto increment | |
| kode | varchar(6), unique, not null | `SKCK`, `DOM`, `SKTM`, `SKU` |
| nama_layanan | varchar(100), not null | |
| deskripsi | varchar(255), nullable | penjelasan singkat untuk warga |
| status_aktif | boolean, not null, default true | |
| urutan | tinyint unsigned, not null, default 1 | urutan tampil |

Tepat **4 baris** (diisi seeder). Tidak ada fitur tambah/hapus layanan pada v1.0.

### 2.5 `syarat_layanan`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_syarat | int unsigned, PK, auto increment | |
| id_layanan | smallint unsigned, FK → layanan.id_layanan, not null | `cascadeOnDelete` |
| nama_syarat | varchar(100), not null | contoh `Foto KTP` |
| petunjuk | varchar(255), nullable | teks bantuan pada form (contoh: "Foto KTP asli, tulisan terbaca jelas") |
| wajib | boolean, not null, default true | |
| urutan | tinyint unsigned, not null, default 1 | |
| status_aktif | boolean, not null, default true | nonaktifkan, jangan hapus, bila sudah dipakai berkas |

UNIQUE (`id_layanan`, `nama_syarat`).

### 2.6 `pengajuan` (timestamps, soft delete)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_pengajuan | int unsigned, PK, auto increment | |
| no_pengajuan | varchar(20), unique, not null | format `PG-YYYYMMDD-XXXX`, dibuat di Service |
| id_warga | int unsigned, FK → warga.id_warga, not null | `restrictOnDelete` |
| id_layanan | smallint unsigned, FK → layanan.id_layanan, not null | `restrictOnDelete` |
| tgl_pengajuan | datetime, not null, default current_timestamp | |
| status | enum('menunggu','kurang_berkas','diproses','menunggu_ttd','selesai','ditolak'), not null, default 'menunggu' | |
| data_isian | json, not null | isian khusus layanan (bagian 8) |
| catatan_petugas | text, nullable | catatan/alasan untuk perubahan status terakhir |
| id_verifikator | int unsigned, FK → pengguna.id_pengguna, nullable | `nullOnDelete`; Kasi yang menangani |
| id_pengubah | int unsigned, FK → pengguna.id_pengguna, nullable | `nullOnDelete`; pelaku perubahan terakhir, dibaca trigger untuk `status_log` |
| tgl_diproses | datetime, nullable | diisi trigger saat status pertama kali `diproses` |
| tgl_selesai | datetime, nullable | diisi trigger saat status `selesai` |
| deleted_at | timestamp, nullable | soft delete (arsip dihapus) |

### 2.7 `berkas_pengajuan` (timestamps)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_berkas | int unsigned, PK, auto increment | |
| id_pengajuan | int unsigned, FK → pengajuan.id_pengajuan, not null | `cascadeOnDelete` |
| id_syarat | int unsigned, FK → syarat_layanan.id_syarat, not null | `restrictOnDelete` |
| nama_file_asli | varchar(150), not null | |
| path_file | varchar(255), not null | penyimpanan privat |
| mime | varchar(50), not null | `image/jpeg`, `image/png`, `application/pdf` |
| ukuran_kb | int unsigned, not null | CHECK `> 0` |
| status_verifikasi | enum('menunggu','sesuai','perlu_perbaikan'), not null, default 'menunggu' | |
| catatan | varchar(255), nullable | alasan bila `perlu_perbaikan` |

UNIQUE (`id_pengajuan`, `id_syarat`) — satu berkas per syarat; unggah ulang **mengganti** berkas lama dan mengembalikan `status_verifikasi` ke `menunggu`.

### 2.8 `status_log`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_log | bigint unsigned, PK, auto increment | |
| id_pengajuan | int unsigned, FK → pengajuan.id_pengajuan, not null | `cascadeOnDelete` |
| status_lama | enum (sama dengan `pengajuan.status`), nullable | null saat pengajuan dibuat |
| status_baru | enum (sama dengan `pengajuan.status`), not null | |
| id_pengguna | int unsigned, FK → pengguna.id_pengguna, nullable | `nullOnDelete`; pelaku |
| catatan | text, nullable | |
| created_at | timestamp, not null, default current_timestamp | tidak ada `updated_at` (log tidak diubah) |

Diisi **hanya oleh trigger** (bagian 7.1); aplikasi tidak menulis ke tabel ini langsung.

### 2.9 `counter_surat`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_layanan | smallint unsigned, FK → layanan.id_layanan | PK komposit; `restrictOnDelete` |
| tahun | smallint unsigned | PK komposit |
| nomor_terakhir | int unsigned, not null, default 0 | CHECK `>= 0` |

PK: (`id_layanan`, `tahun`). Dikelola **hanya oleh** stored procedure `sp_ambil_nomor_surat`.

### 2.10 `surat` (timestamps)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_surat | int unsigned, PK, auto increment | |
| id_pengajuan | int unsigned, FK → pengajuan.id_pengajuan, **unique**, not null | `restrictOnDelete` |
| no_surat | varchar(50), unique, not null | contoh `001/SKCK/RH/X/2026` |
| tgl_surat | date, not null | |
| kode_verifikasi | char(32), unique, not null | token acak untuk QR/halaman verifikasi |
| file_pdf | varchar(255), not null | path PDF final (penyimpanan privat) |
| id_penandatangan | int unsigned, FK → pengguna.id_pengguna, not null | `restrictOnDelete` |
| ditandatangani_at | datetime, not null | |

Hanya surat **final** yang disimpan; draft untuk pratinjau dibuat sesaat (tidak disimpan).

### 2.11 `pengaturan` (timestamps)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_pengaturan | bigint unsigned, PK, auto increment | |
| kunci | varchar(64), unique, not null | |
| nilai | json, not null | |
| id_pengguna | int unsigned, FK → pengguna.id_pengguna, nullable | `nullOnDelete`; terakhir mengubah |

Kunci yang dipakai sistem:
- `profil_kampung`: `{"nama_kampung":"Rukti Harjo","kecamatan":"Seputih Raman","kabupaten":"Lampung Tengah","alamat":"...","kode_pos":"...","telepon":"...","whatsapp":"62..."}`
- `jam_layanan`: `{"senin_kamis":"08.00-15.00","jumat":"08.00-11.30","catatan":"..."}` (isi awal contoh; dikonfirmasi pengguna)
- `statistik_kampung`: `{"jiwa":5990,"kk":1780,"sumber":"Laporan Bulanan Kependudukan September 2025"}`
- `surat`: `{"kode_kampung":"RH","jabatan_penandatangan":"Kepala Kampung"}`

---

## 3. Relasi Antar Tabel

```
dusun        1 ──── N warga
pengguna     1 ──── 0..1 warga                (warga.id_pengguna unique)
pengguna     0..1 ── N pengajuan              (id_verifikator, id_pengubah)
pengguna     0..1 ── N status_log
pengguna     1 ──── N surat                   (id_penandatangan)
pengguna     0..1 ── N pengaturan
warga        1 ──── N pengajuan
layanan      1 ──── N pengajuan
layanan      1 ──── N syarat_layanan
layanan      1 ──── N counter_surat
pengajuan    1 ──── N berkas_pengajuan
pengajuan    1 ──── N status_log
pengajuan    1 ──── 0..1 surat
syarat_layanan 1 ── N berkas_pengajuan
```

Relasi `pengajuan` ↔ `syarat_layanan` adalah **many-to-many** melalui `berkas_pengajuan`.

---

## 4. Catatan Implementasi Eloquent

Nama kolom mengikuti skema ini, bukan konvensi Laravel, sehingga **setiap model wajib** mendeklarasikan `$table` dan `$primaryKey`:

```php
// Pengguna.php — extends Authenticatable
protected $table = 'pengguna';
protected $primaryKey = 'id_pengguna';
protected $hidden = ['password'];
protected $casts = ['password' => 'hashed', 'status_aktif' => 'boolean', 'wajib_ganti_password' => 'boolean'];
public function warga() { return $this->hasOne(Warga::class, 'id_pengguna', 'id_pengguna'); }

// Warga.php
protected $table = 'warga';
protected $primaryKey = 'id_warga';
public function pengguna() { return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna'); }
public function dusun() { return $this->belongsTo(Dusun::class, 'id_dusun', 'id_dusun'); }
public function pengajuan() { return $this->hasMany(Pengajuan::class, 'id_warga', 'id_warga'); }

// Pengajuan.php — use SoftDeletes
protected $table = 'pengajuan';
protected $primaryKey = 'id_pengajuan';
protected $casts = ['data_isian' => 'array', 'tgl_pengajuan' => 'datetime', 'tgl_diproses' => 'datetime', 'tgl_selesai' => 'datetime'];
public function warga() { return $this->belongsTo(Warga::class, 'id_warga', 'id_warga'); }
public function layanan() { return $this->belongsTo(Layanan::class, 'id_layanan', 'id_layanan'); }
public function berkas() { return $this->hasMany(BerkasPengajuan::class, 'id_pengajuan', 'id_pengajuan'); }
public function log() { return $this->hasMany(StatusLog::class, 'id_pengajuan', 'id_pengajuan')->orderBy('id_log'); }
public function surat() { return $this->hasOne(Surat::class, 'id_pengajuan', 'id_pengajuan'); }

// StatusLog.php — hanya dibaca aplikasi
protected $table = 'status_log';
protected $primaryKey = 'id_log';
const UPDATED_AT = null;
```

**Model `CounterSurat`** memakai `$incrementing = false`, `$primaryKey = null`, dan hanya diakses lewat stored procedure (jangan memakai `save()`/`delete()`).

**Casting:** `penghasilan_per_bulan` pada `data_isian` disimpan sebagai bilangan bulat rupiah; tampilkan dengan helper `rupiah()`.

**Autentikasi:** `config/auth.php` → provider `eloquent` dengan model `App\Models\Pengguna`; login memakai kolom `username` (warga: NIK). Akun `status_aktif = false` ditolak saat login. Fitur "ingat saya" dinonaktifkan; tidak ada kolom `remember_token`.

**Pengajuan dan trigger:** setiap `UPDATE` yang mengubah `status` wajib menyetel `id_pengubah` dan `catatan_petugas` pada pernyataan yang sama (dibaca trigger `trg_pengajuan_au`). Kosongkan `catatan_petugas` (null) bila perubahan tidak memiliki catatan agar tidak terbawa dari status sebelumnya.

**Zona waktu:** `config/app.php` → `Asia/Jakarta`; `config/database.php` koneksi mysql → `'timezone' => '+07:00'` agar `NOW()` di trigger sama dengan waktu aplikasi.

## 5. Seeder yang Dibutuhkan

1. `DusunSeeder` — ±11 dusun dummy mengikuti struktur organisasi skripsi (Dusun I A, I B, II, III, IV, V A, V B, VI, VII, VIII, IX); dikonfirmasi pengguna.
2. `LayananSeeder` — **4 layanan** dan seluruh syarat (bagian 6).
3. `PengaturanSeeder` — keempat kunci pengaturan (nilai awal contoh; alamat/telepon/WhatsApp diisi pengguna).
4. `PenggunaSeeder` — akun `kasi` dan `kepala` (password awal dev, wajib diganti sebelum penyerahan) serta ±8 akun warga dummy dengan NIK dummy (bukan NIK asli) dan profil lengkap.
5. `PengajuanSeeder` — ±15 pengajuan tersebar di seluruh status (via Eloquent/Service agar trigger berjalan), berkas dummy berupa gambar placeholder; pengajuan `selesai` dibuat lewat `SuratService` sehingga nomor surat dan PDF konsisten.

## 6. Data Awal Layanan dan Syarat

```sql
INSERT INTO layanan (id_layanan, kode, nama_layanan, deskripsi, urutan) VALUES
(1,'SKCK','Pengantar SKCK','Surat pengantar untuk mengurus SKCK di kepolisian',1),
(2,'DOM','Keterangan Domisili','Keterangan tempat tinggal warga',2),
(3,'SKTM','Keterangan Tidak Mampu','Keterangan kondisi ekonomi untuk keperluan sekolah/kesehatan/lainnya',3),
(4,'SKU','Keterangan Usaha','Keterangan bahwa warga memiliki usaha',4);

INSERT INTO syarat_layanan (id_layanan, nama_syarat, petunjuk, wajib, urutan) VALUES
(1,'Foto KTP','Foto KTP asli, seluruh bagian terlihat dan terbaca',1,1),
(1,'Foto KK','Foto Kartu Keluarga, seluruh bagian terlihat',1,2),
(1,'Pengantar RT/RW','Foto surat pengantar dari RT/RW',1,3),
(2,'Foto KTP','Foto KTP asli, seluruh bagian terlihat dan terbaca',1,1),
(2,'Foto KK','Foto Kartu Keluarga, seluruh bagian terlihat',1,2),
(2,'Pengantar RT/RW','Foto surat pengantar dari RT/RW',1,3),
(3,'Foto KTP','Foto KTP asli, seluruh bagian terlihat dan terbaca',1,1),
(3,'Foto KK','Foto Kartu Keluarga, seluruh bagian terlihat',1,2),
(3,'Pengantar RT/RW','Foto surat pengantar dari RT/RW',1,3),
(4,'Foto KTP','Foto KTP asli, seluruh bagian terlihat dan terbaca',1,1),
(4,'Foto KK','Foto Kartu Keluarga, seluruh bagian terlihat',1,2),
(4,'Pengantar RT/RW','Foto surat pengantar dari RT/RW',1,3),
(4,'Foto Tempat Usaha','Foto tempat usaha yang jelas dari depan',1,4);
```

## 7. Objek Basis Data Terprogram

Dibuat lewat migration terpisah setelah seluruh tabel (gunakan `DB::unprepared()` per objek; satu pernyataan `CREATE` per panggilan; tanpa `DELIMITER`). Sediakan `down()` yang menjatuhkan objek (`DROP TRIGGER/PROCEDURE/VIEW IF EXISTS`).

### 7.1 Trigger

```sql
-- Mencatat pembuatan pengajuan
CREATE TRIGGER trg_pengajuan_ai AFTER INSERT ON pengajuan
FOR EACH ROW
BEGIN
  INSERT INTO status_log (id_pengajuan, status_lama, status_baru, id_pengguna, catatan)
  VALUES (NEW.id_pengajuan, NULL, NEW.status, NEW.id_pengubah, 'Pengajuan dibuat');
END;

-- Menjaga status final dan mengisi tanggal otomatis
CREATE TRIGGER trg_pengajuan_bu BEFORE UPDATE ON pengajuan
FOR EACH ROW
BEGIN
  IF OLD.status IN ('selesai','ditolak') AND NEW.status <> OLD.status THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Pengajuan berstatus final tidak dapat diubah statusnya';
  END IF;
  IF NEW.status = 'diproses' AND OLD.status <> 'diproses' AND NEW.tgl_diproses IS NULL THEN
    SET NEW.tgl_diproses = NOW();
  END IF;
  IF NEW.status = 'selesai' AND OLD.status <> 'selesai' THEN
    SET NEW.tgl_selesai = NOW();
  END IF;
END;

-- Mencatat setiap perubahan status
CREATE TRIGGER trg_pengajuan_au AFTER UPDATE ON pengajuan
FOR EACH ROW
BEGIN
  IF NEW.status <> OLD.status THEN
    INSERT INTO status_log (id_pengajuan, status_lama, status_baru, id_pengguna, catatan)
    VALUES (NEW.id_pengajuan, OLD.status, NEW.status, NEW.id_pengubah, NEW.catatan_petugas);
  END IF;
END;
```

> Trigger hanya menjaga status final. **Matriks perpindahan status yang valid** (PRD §7) ditegakkan di `PengajuanService`.

### 7.2 Stored Procedure

```sql
-- Nomor urut surat atomik per layanan per tahun (dipanggil dalam DB::transaction milik aplikasi)
CREATE PROCEDURE sp_ambil_nomor_surat(
  IN p_id_layanan SMALLINT UNSIGNED, IN p_tahun SMALLINT UNSIGNED, OUT p_urut INT UNSIGNED)
BEGIN
  INSERT IGNORE INTO counter_surat (id_layanan, tahun, nomor_terakhir)
  VALUES (p_id_layanan, p_tahun, 0);

  SELECT nomor_terakhir + 1 INTO p_urut
  FROM counter_surat
  WHERE id_layanan = p_id_layanan AND tahun = p_tahun
  FOR UPDATE;

  UPDATE counter_surat SET nomor_terakhir = p_urut
  WHERE id_layanan = p_id_layanan AND tahun = p_tahun;
END;

-- Rekap per layanan untuk periode tertentu (arsip dan laporan)
CREATE PROCEDURE sp_rekap_periode(IN p_dari DATE, IN p_sampai DATE)
BEGIN
  SELECT l.kode, l.nama_layanan,
         COUNT(p.id_pengajuan) AS total,
         COALESCE(SUM(p.status = 'selesai'), 0) AS selesai,
         COALESCE(SUM(p.status = 'ditolak'), 0) AS ditolak,
         COALESCE(SUM(p.status IN ('menunggu','kurang_berkas','diproses','menunggu_ttd')), 0) AS berjalan,
         ROUND(AVG(TIMESTAMPDIFF(HOUR, p.tgl_pengajuan, p.tgl_selesai)), 1) AS rata_jam_selesai
  FROM layanan l
  LEFT JOIN pengajuan p
         ON p.id_layanan = l.id_layanan
        AND p.deleted_at IS NULL
        AND p.tgl_pengajuan >= p_dari
        AND p.tgl_pengajuan <  p_sampai + INTERVAL 1 DAY
  GROUP BY l.id_layanan, l.kode, l.nama_layanan, l.urutan
  ORDER BY l.urutan;
END;
```

### 7.3 View

```sql
-- Antrean untuk Kasi/Kepala (tanpa pengajuan yang di-soft-delete)
CREATE OR REPLACE VIEW v_antrean_pengajuan AS
SELECT p.id_pengajuan, p.no_pengajuan, p.status, p.tgl_pengajuan,
       w.nik, pg.nama AS nama_warga, pg.no_hp,
       l.kode AS kode_layanan, l.nama_layanan,
       (SELECT COUNT(*) FROM berkas_pengajuan b
         WHERE b.id_pengajuan = p.id_pengajuan AND b.status_verifikasi = 'menunggu')        AS berkas_menunggu,
       (SELECT COUNT(*) FROM berkas_pengajuan b
         WHERE b.id_pengajuan = p.id_pengajuan AND b.status_verifikasi = 'perlu_perbaikan') AS berkas_perbaikan
FROM pengajuan p
JOIN warga w    ON w.id_warga = p.id_warga
JOIN pengguna pg ON pg.id_pengguna = w.id_pengguna
JOIN layanan l  ON l.id_layanan = p.id_layanan
WHERE p.deleted_at IS NULL;

-- Rekap bulanan per layanan dan status (untuk filter laporan)
CREATE OR REPLACE VIEW v_rekap_bulanan AS
SELECT YEAR(p.tgl_pengajuan) AS tahun, MONTH(p.tgl_pengajuan) AS bulan,
       p.id_layanan, p.status, COUNT(*) AS jumlah
FROM pengajuan p
WHERE p.deleted_at IS NULL
GROUP BY YEAR(p.tgl_pengajuan), MONTH(p.tgl_pengajuan), p.id_layanan, p.status;
```

### 7.4 Index dan Constraint

| Tabel | Index / Constraint | Tujuan |
|---|---|---|
| `warga` | UNIQUE `nik`; UNIQUE `id_pengguna`; INDEX `id_dusun`; CHECK `nik REGEXP '^[0-9]{16}$'`; CHECK `no_kk IS NULL OR no_kk REGEXP '^[0-9]{16}$'` | cari warga dan integritas NIK |
| `pengajuan` | UNIQUE `no_pengajuan`; INDEX (`status`, `tgl_pengajuan`); INDEX (`id_warga`, `tgl_pengajuan`); INDEX (`id_layanan`, `tgl_pengajuan`) | antrean, riwayat warga, laporan |
| `berkas_pengajuan` | UNIQUE (`id_pengajuan`, `id_syarat`); CHECK `ukuran_kb > 0` | satu berkas per syarat |
| `status_log` | INDEX (`id_pengajuan`, `created_at`) | timeline |
| `surat` | UNIQUE `no_surat`, `kode_verifikasi`, `id_pengajuan` | verifikasi QR dan keunikan |
| `counter_surat` | PK (`id_layanan`, `tahun`); CHECK `nomor_terakhir >= 0` | penomoran atomik |
| `syarat_layanan` | UNIQUE (`id_layanan`, `nama_syarat`) | tanpa duplikasi syarat |

CHECK dibuat dengan `DB::statement('ALTER TABLE ... ADD CONSTRAINT ... CHECK (...)')` (periksa versi MariaDB XAMPP; CHECK ditegakkan sejak 10.2).

## 8. Struktur JSON `pengajuan.data_isian` per Layanan

Disimpan sebagai JSON dengan alasan formulir berbeda per layanan dan jumlah layanan tetap; validasi dilakukan server-side lewat Form Request sesuai kode layanan (definisi di `config/layanan.php`).

| Layanan | Kunci | Aturan |
|---|---|---|
| SKCK | `keperluan` | wajib, teks maks 150 |
| DOM | `alamat_domisili` | wajib, teks maks 255 (isian awal dari profil) |
| | `lama_tinggal_tahun` | wajib, bilangan bulat 0–100 |
| | `keperluan` | wajib, teks maks 150 |
| SKTM | `tujuan` | wajib, teks maks 150 (sekolah, kesehatan, dll.) |
| | `penghasilan_per_bulan` | wajib, bilangan bulat ≥ 0 (rupiah) |
| | `jumlah_tanggungan` | wajib, bilangan bulat 0–20 |
| SKU | `nama_usaha` | wajib, teks maks 100 |
| | `jenis_usaha` | wajib, teks maks 100 |
| | `alamat_usaha` | wajib, teks maks 255 |
| | `lama_usaha_tahun` | wajib, bilangan bulat 0–100 |

Data pemohon pada surat (nama, NIK, TTL, jenis kelamin, agama, pekerjaan, alamat) diambil dari `pengguna` dan `warga`.

## 9. Catatan Migrasi

Urutan migration (mengikuti dependency FK):
`dusun` → `pengguna` → `warga` → `layanan` → `syarat_layanan` → `pengajuan` → `berkas_pengajuan` → `status_log` → `counter_surat` → `surat` → `pengaturan` → (CHECK) → trigger → stored procedure → view

- Hapus migration bawaan Laravel yang tidak dipakai (`users`, `cache`, `jobs`); set `SESSION_DRIVER=file`.
- Gunakan `restrictOnDelete()` untuk FK ke master dan data yang sudah dipakai transaksi; `cascadeOnDelete()` hanya untuk `berkas_pengajuan`, `status_log`, dan `syarat_layanan` terhadap induknya, serta `warga` terhadap `pengguna`.
- Pengujian trigger, stored procedure, dan view memerlukan **MySQL/MariaDB** (bukan SQLite in-memory). Siapkan database terpisah untuk test (`.env.testing`).
- Database sudah dibuat pengguna; migration hanya membuat tabel di dalamnya. Nama database dibaca dari `.env`.
- Penyimpanan berkas memakai disk privat (`storage/app/private`), bukan `public`; path disimpan relatif.
