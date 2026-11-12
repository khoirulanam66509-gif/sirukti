@php
    $logoSrc = null;
    $kandidatLogo = [
        base_path('resources/surat/logo-lampung-tengah.png'),
        base_path('resources/surat/logo-lampung-tengah.jpg'),
        base_path('resources/surat/logo-lampung-tengah.jpeg'),
        public_path('images/logo-lampung-tengah.png'),
        public_path('images/logo-lampung-tengah.jpg'),
        public_path('images/logo-lampung-tengah.jpeg'),
        public_path('logo-lampung-tengah.png'),
    ];
    foreach ($kandidatLogo as $path) {
        if (file_exists($path)) {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $mime = ($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : 'image/png';
            $logoSrc = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($path));
            break;
        }
    }

    $namaKampung = $kop['nama_kampung'] ?? '';
    $alamatKop = $kop['alamat'] ?? '';
    if ($namaKampung !== '' && stripos($alamatKop, $namaKampung) === false) {
        $alamatKop .= ($alamatKop !== '' ? ', ' : '').'Kampung '.$namaKampung;
    }

    $kontak = [];
    if (! empty($kop['telepon'])) {
        $kontak[] = 'Telp: '.$kop['telepon'];
    }
    if (! empty($kop['whatsapp'])) {
        $kontak[] = 'WhatsApp Resmi: '.$kop['whatsapp'];
    }
@endphp

<table class="kop" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td class="kop-logo-cell">
            @if($logoSrc)
                <img src="{{ $logoSrc }}" class="kop-logo" alt="Logo Kabupaten">
            @endif
        </td>
        <td class="kop-teks">
            <div class="kop-baris kop-pemkab">PEMERINTAH KABUPATEN {{ strtoupper($kop['kabupaten'] ?? '') }}</div>
            <div class="kop-baris kop-kecamatan">KECAMATAN {{ strtoupper($kop['kecamatan'] ?? '') }}</div>
            <div class="kop-baris kop-kampung">KAMPUNG {{ strtoupper($namaKampung) }}</div>
            @if($alamatKop !== '' || ! empty($kop['kode_pos']))
                <div class="kop-alamat">
                    {{ $alamatKop }}@if(! empty($kop['kode_pos'])) Kode Pos {{ $kop['kode_pos'] }}@endif
                </div>
            @endif
            @if(! empty($kontak))
                <div class="kop-kontak">{{ implode(' | ', $kontak) }}</div>
            @endif
        </td>
    </tr>
</table>

<div class="garis-ganda"></div>
