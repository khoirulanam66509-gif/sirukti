@extends('surat.layout')

@section('content')
    @php
        $jabatan = $surat_cfg['jabatan_penandatangan'] ?? 'Kepala Kampung';
        $kecamatan = $kop['kecamatan'] ?? '';
        $kabupaten = $kop['kabupaten'] ?? '';
        $namaKampung = $kop['nama_kampung'] ?? '';

        $tempatTanggalLahir = ($warga->tempat_lahir ?: '-').', '.($warga->tgl_lahir ? tgl_indo($warga->tgl_lahir) : '-');

        $identitas = [
            ['label' => 'Nama Lengkap', 'value' => $warga->pengguna->nama ?? '-'],
            ['label' => 'NIK', 'value' => $warga->nik ?: '-'],
            ['label' => 'No KK', 'value' => $warga->no_kk ?: '-'],
            ['label' => 'Tempat / Tanggal Lahir', 'value' => $tempatTanggalLahir],
            ['label' => 'Jenis Kelamin', 'value' => $warga->jenis_kelamin === 'L' ? 'Laki-laki' : ($warga->jenis_kelamin === 'P' ? 'Perempuan' : '-')],
            ['label' => 'Kewarganegaraan / Agama', 'value' => 'Indonesia / '.($warga->agama ?: '-')],
            ['label' => 'Pekerjaan', 'value' => $warga->pekerjaan ?: '-'],
            ['label' => 'Alamat Sesuai KTP Asal', 'value' => $data_isian['alamat_asal'] ?? ($warga->alamat ?: '-')],
        ];
    @endphp

    <p>
        Yang bertanda tangan di bawah ini {{ $jabatan }}, Kecamatan {{ $kecamatan }}, Kabupaten {{ $kabupaten }},
        menerangkan dengan sebenarnya bahwa:
    </p>

    @include('surat.partials.tabel-data', ['rows' => $identitas])

    <p>
        Berdasarkan surat permohonan yang bersangkutan dan pencatatan aparat rukun tetangga setempat, yang bersangkutan
        adalah benar saat ini bertempat tinggal dan berdomisili di wilayah Kampung {{ $namaKampung }} pada alamat:
    </p>

    <p>
        <strong>{{ $data_isian['alamat_domisili'] ?? $warga->alamat }}</strong>
    </p>

    <p>
        Yang bersangkutan tercatat telah berdomisili di alamat tersebut
        @if(! empty($data_isian['lama_tinggal_tahun']))
            selama kurang lebih <strong>{{ $data_isian['lama_tinggal_tahun'] }} tahun</strong>,
        @elseif(! empty($data_isian['sejak_tanggal']))
            sejak tanggal <strong>{{ tgl_indo($data_isian['sejak_tanggal']) }}</strong>,
        @endif
        dengan keperluan:
    </p>

    <p>
        <strong>"{{ $data_isian['keperluan'] ?? ($data_isian['alasan'] ?? 'Kebutuhan administrasi kependudukan dan kegiatan bermasyarakat.') }}"</strong>
    </p>

    <p>
        Demikian surat keterangan ini dibuat dengan sebenarnya dan diberikan kepada yang bersangkutan untuk dapat
        dipergunakan sebagaimana mestinya.
    </p>
@endsection
