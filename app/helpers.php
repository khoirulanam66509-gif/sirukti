<?php

use Carbon\Carbon;

if (! function_exists('tgl_indo')) {
    /**
     * Format tanggal Indonesia (contoh: 19 Oktober 2026 atau 19 Oktober 2026, 14:30 WIB).
     */
    function tgl_indo($tanggal, bool $tampilkanWaktu = false): string
    {
        if (empty($tanggal)) {
            return '-';
        }

        $bulanIndo = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        if ($tanggal instanceof Carbon) {
            $dt = $tanggal->copy()->setTimezone('Asia/Jakarta');
        } else {
            $dt = Carbon::parse($tanggal, 'Asia/Jakarta')->setTimezone('Asia/Jakarta');
        }
        $tglFormatted = sprintf('%d %s %d', $dt->day, $bulanIndo[$dt->month], $dt->year);

        if ($tampilkanWaktu) {
            $tglFormatted .= sprintf(', %s WIB', $dt->format('H:i'));
        }

        return $tglFormatted;
    }
}

if (! function_exists('rupiah')) {
    /**
     * Format mata uang Rupiah (contoh: Rp 1.500.000).
     */
    function rupiah($angka): string
    {
        if (! is_numeric($angka)) {
            return 'Rp 0';
        }

        return 'Rp '.number_format((float) $angka, 0, ',', '.');
    }
}

if (! function_exists('samarkan_nik')) {
    /**
     * Menyamarkan NIK untuk privasi (contoh: 180201******0001).
     */
    function samarkan_nik(?string $nik): string
    {
        if (empty($nik) || strlen($nik) < 10) {
            return (string) $nik;
        }

        $prefix = substr($nik, 0, 6);
        $suffix = substr($nik, -4);

        return $prefix.'******'.$suffix;
    }
}
