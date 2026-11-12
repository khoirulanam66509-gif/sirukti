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

        $penghasilan = '-';
        if (isset($data_isian['penghasilan_per_bulan'])) {
            $penghasilan = rupiah((int) $data_isian['penghasilan_per_bulan']);
        } elseif (isset($data_isian['penghasilan'])) {
            $penghasilan = rupiah((int) $data_isian['penghasilan']);
        }

        $jumlahTanggungan = isset($data_isian['jumlah_tanggungan'])
            ? $data_isian['jumlah_tanggungan'].' orang'
            : '-';

        $tujuan = $data_isian['tujuan'] ?? ($data_isian['keperluan'] ?? '-');

        $identitas = [
            ['label' => 'Nama Lengkap', 'value' => $warga->pengguna->nama ?? '-'],
            ['label' => 'NIK', 'value' => $warga->nik ?: '-'],
            ['label' => 'No KK', 'value' => $warga->no_kk ?: '-'],
            ['label' => 'Tempat / Tanggal Lahir', 'value' => $tempatTanggalLahir],
            ['label' => 'Pekerjaan Kepala Keluarga', 'value' => $warga->pekerjaan ?: '-'],
            ['label' => 'Alamat', 'value' => $alamatHtml, 'raw' => true],
        ];

        $rincianEkonomi = [
            ['label' => 'Penghasilan Rata-rata Bulanan', 'value' => $penghasilan],
            ['label' => 'Jumlah Tanggungan Keluarga', 'value' => $jumlahTanggungan],
        ];
    @endphp
    {{-- TODO: konfirmasi sumber data --}}

    <p>
        Yang bertanda tangan di bawah ini {{ $jabatan }}, Kecamatan {{ $kecamatan }}, Kabupaten {{ $kabupaten }},
        menerangkan dengan sebenarnya bahwa:
    </p>

    @include('surat.partials.tabel-data', ['rows' => $identitas])

    <p>
        Berdasarkan hasil peninjauan lapangan dan data sosial ekonomi warga Kampung {{ $namaKampung }}, orang yang
        namanya tersebut di atas adalah benar warga kami yang tergolong dalam keluarga TIDAK MAMPU (PRA-SEJAHTERA),
        dengan rincian data keadaan ekonomi keluarga sebagai berikut:
    </p>

    @include('surat.partials.tabel-data', ['rows' => $rincianEkonomi, 'variant' => 'tabel-data--ekonomi'])

    <p>
        Surat Keterangan Tidak Mampu ini diterbitkan atas permintaan yang bersangkutan untuk dipergunakan sebagai
        persyaratan <strong>{{ $tujuan }}</strong>.
    </p>

    <p>
        Demikian surat keterangan ini dibuat dengan sebenarnya dan diberikan kepada yang bersangkutan untuk dapat
        dipergunakan sebagaimana mestinya.
    </p>
@endsection

