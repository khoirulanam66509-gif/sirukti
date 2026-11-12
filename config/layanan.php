<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Layanan Surat Desa SIRUKTI
    |--------------------------------------------------------------------------
    |
    | Berisi spesifikasi formulir data isian dinamis per kode layanan
    | sesuai dengan schema-database.md §8.
    |
    */

    'SKCK' => [
        'nama' => 'Pengantar SKCK',
        'deskripsi' => 'Surat pengantar untuk mengurus Surat Keterangan Catatan Kepolisian di Polsek/Polres',
        'fields' => [
            'keperluan' => [
                'label' => 'Keperluan Pembuatan SKCK',
                'type' => 'textarea',
                'placeholder' => 'Contoh: Persyaratan melamar pekerjaan di instansi BUMN / Swasta',
                'rules' => ['required', 'string', 'max:150'],
                'help' => 'Jelaskan tujuan penggunaan SKCK secara ringkas dan jelas (maks. 150 karakter).',
            ],
        ],
    ],

    'DOM' => [
        'nama' => 'Keterangan Domisili',
        'deskripsi' => 'Surat keterangan resmi yang menerangkan domisili tempat tinggal warga di Kampung Rukti Harjo',
        'fields' => [
            'alamat_domisili' => [
                'label' => 'Alamat Domisili Lengkap',
                'type' => 'text',
                'placeholder' => 'Contoh: Jl. Poros Utama No. 12, RT 001 / RW 001',
                'rules' => ['required', 'string', 'max:255'],
                'prefill' => 'alamat_lengkap',
                'help' => 'Isikan alamat domisili tempat tinggal Anda saat ini di Kampung Rukti Harjo.',
            ],
            'lama_tinggal_tahun' => [
                'label' => 'Lama Tinggal (Tahun)',
                'type' => 'number',
                'placeholder' => 'Contoh: 5',
                'rules' => ['required', 'integer', 'min:0', 'max:100'],
                'help' => 'Durasi lama menetap dalam hitungan tahun (0 jika kurang dari setahun).',
            ],
            'keperluan' => [
                'label' => 'Keperluan Surat Domisili',
                'type' => 'text',
                'placeholder' => 'Contoh: Pendaftaran sekolah anak / Persyaratan administrasi perbankan',
                'rules' => ['required', 'string', 'max:150'],
                'help' => 'Tujuan pembuatan surat domisili (maks. 150 karakter).',
            ],
        ],
    ],

    'SKTM' => [
        'nama' => 'Keterangan Tidak Mampu',
        'deskripsi' => 'Surat keterangan kondisi ekonomi keluarga untuk keperluan keringanan biaya pendidikan/kesehatan',
        'fields' => [
            'tujuan' => [
                'label' => 'Tujuan / Keperluan SKTM',
                'type' => 'text',
                'placeholder' => 'Contoh: Pengajuan KIP Kuliah / Keringanan Biaya Rumah Sakit',
                'rules' => ['required', 'string', 'max:150'],
                'help' => 'Sebutkan instansi atau program penerima SKTM.',
            ],
            'penghasilan_per_bulan' => [
                'label' => 'Penghasilan Rata-rata per Bulan (Rp)',
                'type' => 'number',
                'placeholder' => 'Contoh: 1200000',
                'rules' => ['required', 'integer', 'min:0'],
                'help' => 'Penghasilan rata-rata gabungan kepala keluarga per bulan (tuliskan angka saja).',
            ],
            'jumlah_tanggungan' => [
                'label' => 'Jumlah Tanggungan Keluarga (Jiwa)',
                'type' => 'number',
                'placeholder' => 'Contoh: 3',
                'rules' => ['required', 'integer', 'min:0', 'max:20'],
                'help' => 'Jumlah anggota keluarga yang menjadi tanggungan hidup.',
            ],
        ],
    ],

    'SKU' => [
        'nama' => 'Keterangan Usaha',
        'deskripsi' => 'Surat keterangan kepemilikan dan legalitas operasional usaha mikro/kecil warga di desa',
        'fields' => [
            'nama_usaha' => [
                'label' => 'Nama Usaha',
                'type' => 'text',
                'placeholder' => 'Contoh: Toko Kelontong Berkah Mandiri',
                'rules' => ['required', 'string', 'max:100'],
                'help' => 'Nama merek dagang atau nama unit usaha yang dijalankan.',
            ],
            'jenis_usaha' => [
                'label' => 'Jenis / Bidang Usaha',
                'type' => 'text',
                'placeholder' => 'Contoh: Perdagangan Sembako, Peternakan Kambing, Bengkel Motor',
                'rules' => ['required', 'string', 'max:100'],
                'help' => 'Komoditas atau bidang kegiatan usaha yang dijalankan.',
            ],
            'alamat_usaha' => [
                'label' => 'Alamat / Lokasi Usaha',
                'type' => 'text',
                'placeholder' => 'Contoh: Jl. Diponegoro No. 8, Dusun I A',
                'rules' => ['required', 'string', 'max:255'],
                'help' => 'Lokasi fisik tempat kegiatan usaha berlangsung di Kampung Rukti Harjo.',
            ],
            'lama_usaha_tahun' => [
                'label' => 'Lama Usaha Berjalan (Tahun)',
                'type' => 'number',
                'placeholder' => 'Contoh: 3',
                'rules' => ['required', 'integer', 'min:0', 'max:100'],
                'help' => 'Lama usaha telah beroperasi dalam hitungan tahun.',
            ],
        ],
    ],
];
