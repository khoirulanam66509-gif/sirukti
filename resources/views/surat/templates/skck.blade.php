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
            ['label' => 'Jenis Kelamin', 'value' => $warga->jenis_kelamin === 'L' ? 'Laki-laki' : ($warga->jenis_kelamin === 'P' ? 'Perempuan' : '-')],
            ['label' => 'Kewarganegaraan / Agama', 'value' => 'Indonesia / '.($warga->agama ?: '-')],
            ['label' => 'Pekerjaan', 'value' => $warga->pekerjaan ?: '-'],
            ['label' => 'Alamat', 'value' => $alamatHtml, 'raw' => true],
        ];
    @endphp

    <p>
        Yang bertanda tangan di bawah ini {{ $jabatan }}, Kecamatan {{ $kecamatan }}, Kabupaten {{ $kabupaten }},
        menerangkan dengan sebenarnya bahwa:
    </p>

    @include('surat.partials.tabel-data', ['rows' => $identitas])

    <p>
        Berdasarkan data kependudukan dan catatan yang ada pada kami, orang yang namanya tersebut di atas adalah benar
        warga kami yang bertempat tinggal di alamat tersebut, berkelakuan baik dalam kehidupan bermasyarakat, serta:
    </p>

    <p>
        <strong>"{{ $data_isian['riwayat_pidana'] ?? 'Tidak pernah tersangkut perkara kriminal/pidana dan tidak sedang dalam proses hukum kepolisian.' }}"</strong>
    </p>

    <p>
        Surat Pengantar ini kami berikan kepada yang bersangkutan untuk melengkapi permohonan penerbitan
        <strong>Surat Keterangan Catatan Kepolisian (SKCK)</strong> pada Kepolisian Sektor (Polsek) / Kepolisian Resor
        (Polres) setempat, dengan keperluan:
    </p>

    <p>
        <strong>{{ $data_isian['keperluan'] ?? 'MELAMAR PEKERJAAN' }}</strong>
    </p>

    <p>
        Demikian surat keterangan ini dibuat dengan sebenarnya dan diberikan kepada yang bersangkutan untuk dapat
        dipergunakan sebagaimana mestinya.
    </p>
@endsection

