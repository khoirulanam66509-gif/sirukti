@extends('surat.layout')

@section('content')
    @php
        $jabatan = $surat_cfg['jabatan_penandatangan'] ?? 'Kepala Kampung';
        $kecamatan = $kop['kecamatan'] ?? '';
        $kabupaten = $kop['kabupaten'] ?? '';
        $namaKampung = $kop['nama_kampung'] ?? '';

        $alamatParts = [];
        if (! empty($warga->alamat)) {
            $alamatParts[] = $warga->alamat;
        }

        $alamatSudahMemuatRt = ! empty($warga->alamat) && preg_match('/\bRT\b/i', $warga->alamat);
        if (! $alamatSudahMemuatRt && (! empty($warga->rt) || ! empty($warga->rw))) {
            $alamatParts[] = 'RT '.$warga->rt.'/RW '.$warga->rw;
        }

        if (! empty($warga->dusun?->nama_dusun)) {
            $alamatParts[] = $warga->dusun->nama_dusun;
        }
        if ($namaKampung !== '') {
            $alamatParts[] = 'Kampung '.$namaKampung;
        }
        $alamatHtml = implode(', ', array_map(fn ($bagian) => e($bagian), $alamatParts)) ?: '-';
        $alamatHtml = preg_replace('/, (Kampung )/u', ',<br>$1', $alamatHtml, 1);

        $tempatTanggalLahir = ($warga->tempat_lahir ?: '-').', '.($warga->tgl_lahir ? tgl_indo($warga->tgl_lahir) : '-');

        $identitas = [
            ['label' => 'Nama Lengkap', 'value' => $warga->pengguna->nama ?? '-'],
            ['label' => 'NIK', 'value' => $warga->nik ?: '-'],
            ['label' => 'No KK', 'value' => $warga->no_kk ?: '-'],
            ['label' => 'Tempat / Tanggal Lahir', 'value' => $tempatTanggalLahir],
            ['label' => 'Pekerjaan', 'value' => $warga->pekerjaan ?: '-'],
            ['label' => 'Alamat', 'value' => $alamatHtml, 'raw' => true],
        ];

        $lamaUsaha = '-';
        if (isset($data_isian['lama_usaha_tahun'])) {
            $lamaUsaha = $data_isian['lama_usaha_tahun'].' Tahun';
        } elseif (! empty($data_isian['tahun_mulai'])) {
            $lamaUsaha = 'Tahun '.$data_isian['tahun_mulai'];
        }

        $rincianUsaha = [
            ['label' => 'Nama Usaha', 'value' => $data_isian['nama_usaha'] ?? '-'],
            ['label' => 'Bidang / Jenis Usaha', 'value' => $data_isian['jenis_usaha'] ?? ($data_isian['bidang_usaha'] ?? '-')],
            ['label' => 'Alamat Lokasi Usaha', 'value' => $data_isian['alamat_usaha'] ?? '-'],
            ['label' => 'Lama Usaha Berjalan', 'value' => $lamaUsaha],
        ];

        if (! empty($data_isian['modal_usaha'])) {
            $rincianUsaha[] = ['label' => 'Modal Usaha Awal', 'value' => rupiah((int) $data_isian['modal_usaha'])];
        }
    @endphp

    <p>
        Yang bertanda tangan di bawah ini {{ $jabatan }}, Kecamatan {{ $kecamatan }}, Kabupaten {{ $kabupaten }},
        menerangkan dengan sebenarnya bahwa:
    </p>

    @include('surat.partials.tabel-data', ['rows' => $identitas])

    <p>
        Berdasarkan keterangan yang bersangkutan dan kenyataan di lapangan, orang yang namanya tersebut di atas adalah
        benar memiliki dan mengelola kegiatan usaha perseorangan / UMKM di wilayah Kampung {{ $namaKampung }}, dengan
        rincian data usaha sebagai berikut:
    </p>

    @include('surat.partials.tabel-data', ['rows' => $rincianUsaha])

    <p>
        Hingga saat surat keterangan ini diterbitkan, kegiatan usaha tersebut masih aktif beroperasi dan tidak
        menimbulkan gangguan ketertiban umum di lingkungan masyarakat Kampung {{ $namaKampung }}.
    </p>

    <p>
        Surat Keterangan Usaha ini diberikan untuk keperluan kelengkapan administrasi pengajuan bantuan permodalan /
        perbankan / perizinan usaha.
    </p>

    <p>
        Demikian surat keterangan ini dibuat dengan sebenarnya dan diberikan kepada yang bersangkutan untuk dapat
        dipergunakan sebagaimana mestinya.
    </p>
@endsection

