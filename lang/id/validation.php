<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute harus diterima.',
    'accepted_if' => ':attribute harus diterima ketika :other adalah :value.',
    'active_url' => ':attribute bukan URL yang valid.',
    'after' => ':attribute harus berupa tanggal setelah :date.',
    'after_or_equal' => ':attribute harus berupa tanggal setelah atau sama dengan :date.',
    'alpha' => ':attribute hanya boleh berisi huruf.',
    'alpha_dash' => ':attribute hanya boleh berisi huruf, angka, setrip, dan garis bawah.',
    'alpha_num' => ':attribute hanya boleh berisi huruf dan angka.',
    'array' => ':attribute harus berupa array.',
    'ascii' => ':attribute hanya boleh berisi karakter dan simbol alfanumerik satu bita.',
    'before' => ':attribute harus berupa tanggal sebelum :date.',
    'before_or_equal' => ':attribute harus berupa tanggal sebelum atau sama dengan :date.',
    'between' => [
        'array' => ':attribute harus memiliki antara :min dan :max anggota.',
        'file' => ':attribute harus berukuran antara :min dan :max kilobita.',
        'numeric' => ':attribute harus bernilai antara :min dan :max.',
        'string' => ':attribute harus berisi antara :min dan :max karakter.',
    ],
    'boolean' => ':attribute harus bernilai benar atau salah.',
    'can' => ':attribute berisi nilai yang tidak sah.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'contains' => ':attribute kehilangan nilai yang diperlukan.',
    'current_password' => 'Kata sandi salah.',
    'date' => ':attribute bukan tanggal yang valid.',
    'date_equals' => ':attribute harus berupa tanggal yang sama dengan :date.',
    'date_format' => ':attribute tidak cocok dengan format :format.',
    'decimal' => ':attribute harus memiliki :decimal tempat desimal.',
    'declined' => ':attribute harus ditolak.',
    'declined_if' => ':attribute harus ditolak ketika :other adalah :value.',
    'different' => ':attribute dan :other harus berbeda.',
    'digits' => ':attribute harus terdiri dari :digits digit.',
    'digits_between' => ':attribute harus terdiri dari :min sampai :max digit.',
    'dimensions' => ':attribute memiliki dimensi gambar yang tidak valid.',
    'distinct' => ':attribute memiliki nilai duplikat.',
    'doesnt_end_with' => ':attribute tidak boleh diakhiri dengan salah satu dari berikut: :values.',
    'doesnt_start_with' => ':attribute tidak boleh diawali dengan salah satu dari berikut: :values.',
    'email' => ':attribute harus berupa alamat surel yang valid.',
    'ends_with' => ':attribute harus diakhiri dengan salah satu dari berikut: :values.',
    'enum' => ':attribute yang dipilih tidak valid.',
    'exists' => ':attribute yang dipilih tidak valid.',
    'extensions' => ':attribute harus memiliki salah satu ekstensi berikut: :values.',
    'file' => ':attribute harus berupa berkas.',
    'filled' => ':attribute harus memiliki nilai.',
    'gt' => [
        'array' => ':attribute harus memiliki lebih dari :value anggota.',
        'file' => ':attribute harus berukuran lebih besar dari :value kilobita.',
        'numeric' => ':attribute harus bernilai lebih besar dari :value.',
        'string' => ':attribute harus berisi lebih dari :value karakter.',
    ],
    'gte' => [
        'array' => ':attribute harus memiliki minimal :value anggota.',
        'file' => ':attribute harus berukuran lebih besar atau sama dengan :value kilobita.',
        'numeric' => ':attribute harus bernilai lebih besar atau sama dengan :value.',
        'string' => ':attribute harus berisi minimal :value karakter.',
    ],
    'hex_color' => ':attribute harus berupa warna heksadesimal yang valid.',
    'image' => ':attribute harus berupa gambar.',
    'in' => ':attribute yang dipilih tidak valid.',
    'in_array' => ':attribute tidak ada di dalam :other.',
    'integer' => ':attribute harus berupa bilangan bulat.',
    'ip' => ':attribute harus berupa alamat IP yang valid.',
    'ipv4' => ':attribute harus berupa alamat IPv4 yang valid.',
    'ipv6' => ':attribute harus berupa alamat IPv6 yang valid.',
    'json' => ':attribute harus berupa string JSON yang valid.',
    'list' => ':attribute harus berupa daftar.',
    'lowercase' => ':attribute harus berupa huruf kecil.',
    'lt' => [
        'array' => ':attribute harus memiliki kurang dari :value anggota.',
        'file' => ':attribute harus berukuran kurang dari :value kilobita.',
        'numeric' => ':attribute harus bernilai kurang dari :value.',
        'string' => ':attribute harus berisi kurang dari :value karakter.',
    ],
    'lte' => [
        'array' => ':attribute tidak boleh memiliki lebih dari :value anggota.',
        'file' => ':attribute harus berukuran kurang dari atau sama dengan :value kilobita.',
        'numeric' => ':attribute harus bernilai kurang dari atau sama dengan :value.',
        'string' => ':attribute tidak boleh berisi lebih dari :value karakter.',
    ],
    'mac_address' => ':attribute harus berupa alamat MAC yang valid.',
    'max' => [
        'array' => ':attribute tidak boleh memiliki lebih dari :max anggota.',
        'file' => ':attribute tidak boleh lebih besar dari :max kilobita.',
        'numeric' => ':attribute tidak boleh lebih besar dari :max.',
        'string' => ':attribute tidak boleh lebih dari :max karakter.',
    ],
    'max_digits' => ':attribute tidak boleh memiliki lebih dari :max digit.',
    'mimes' => ':attribute harus berupa berkas berformat: :values.',
    'mimetypes' => ':attribute harus berupa berkas berformat: :values.',
    'min' => [
        'array' => ':attribute minimal harus memiliki :min anggota.',
        'file' => ':attribute minimal berukuran :min kilobita.',
        'numeric' => ':attribute minimal harus bernilai :min.',
        'string' => ':attribute minimal harus berisi :min karakter.',
    ],
    'min_digits' => ':attribute harus memiliki minimal :min digit.',
    'missing' => ':attribute tidak boleh disertakan.',
    'missing_if' => ':attribute tidak boleh disertakan ketika :other bernilai :value.',
    'missing_unless' => ':attribute tidak boleh disertakan kecuali jika :other bernilai :value.',
    'missing_with' => ':attribute tidak boleh disertakan ketika :values ada.',
    'missing_with_all' => ':attribute tidak boleh disertakan ketika :values ada semua.',
    'multiple_of' => ':attribute harus merupakan kelipatan dari :value.',
    'not_in' => ':attribute yang dipilih tidak valid.',
    'not_regex' => 'Format :attribute tidak valid.',
    'numeric' => ':attribute harus berupa angka.',
    'password' => [
        'letters' => ':attribute harus berisi minimal satu huruf.',
        'mixed' => ':attribute harus berisi minimal satu huruf besar dan satu huruf kecil.',
        'numbers' => ':attribute harus berisi minimal satu angka.',
        'symbols' => ':attribute harus berisi minimal satu simbol.',
        'uncompromised' => ':attribute yang diberikan telah bocor dalam kebocoran data. Silakan pilih :attribute yang berbeda.',
    ],
    'present' => ':attribute wajib ada.',
    'present_if' => ':attribute wajib ada ketika :other adalah :value.',
    'present_unless' => ':attribute wajib ada kecuali :other adalah :value.',
    'present_with' => ':attribute wajib ada ketika :values ada.',
    'present_with_all' => ':attribute wajib ada ketika :values ada semua.',
    'prohibited' => ':attribute dilarang disertakan.',
    'prohibited_if' => ':attribute dilarang disertakan ketika :other bernilai :value.',
    'prohibited_unless' => ':attribute dilarang disertakan kecuali jika :other ada di dalam :values.',
    'prohibits' => ':attribute melarang :other untuk disertakan.',
    'regex' => 'Format :attribute tidak valid.',
    'required' => ':attribute wajib diisi.',
    'required_array_keys' => ':attribute harus berisi entri untuk: :values.',
    'required_if' => ':attribute wajib diisi bila :other adalah :value.',
    'required_if_accepted' => ':attribute wajib diisi ketika :other diterima.',
    'required_if_declined' => ':attribute wajib diisi ketika :other ditolak.',
    'required_unless' => ':attribute wajib diisi kecuali jika :other ada di dalam :values.',
    'required_with' => ':attribute wajib diisi ketika :values ada.',
    'required_with_all' => ':attribute wajib diisi bila :values ada semua.',
    'required_without' => ':attribute wajib diisi bila :values tidak ada.',
    'required_without_all' => ':attribute wajib diisi bila tidak ada satupun :values yang ada.',
    'same' => ':attribute dan :other harus sama.',
    'size' => [
        'array' => ':attribute harus mengandung :size anggota.',
        'file' => ':attribute harus berukuran :size kilobita.',
        'numeric' => ':attribute harus bernilai :size.',
        'string' => ':attribute harus berisi :size karakter.',
    ],
    'starts_with' => ':attribute harus diawali dengan salah satu dari berikut: :values.',
    'string' => ':attribute harus berupa teks.',
    'timezone' => ':attribute harus berupa zona waktu yang valid.',
    'unique' => ':attribute sudah digunakan.',
    'uploaded' => ':attribute gagal diunggah.',
    'uppercase' => ':attribute harus berupa huruf besar.',
    'url' => ':attribute harus berupa URL yang valid.',
    'ulid' => ':attribute harus berupa ULID yang valid.',
    'uuid' => ':attribute harus berupa UUID yang valid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute place-holders
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'aksi' => 'tindakan verifikasi',
        'catatan_petugas' => 'catatan petugas',
        'berkas' => 'berkas persyaratan',
        'berkas.*.status_verifikasi' => 'status verifikasi berkas',
        'berkas.*.catatan' => 'catatan alasan perbaikan',
        'username' => 'nama pengguna',
        'nik' => 'NIK',
        'no_kk' => 'nomor KK',
        'nama' => 'nama lengkap',
        'no_hp' => 'nomor WhatsApp/telepon',
        'password' => 'kata sandi',
        'password_confirmation' => 'konfirmasi kata sandi',
        'email' => 'alamat surel',
        'pekerjaan' => 'pekerjaan',
        'alamat' => 'alamat lengkap',
        'rt' => 'RT',
        'rw' => 'RW',
        'id_dusun' => 'dusun',
        'id_layanan' => 'jenis layanan surat',
    ],

];
