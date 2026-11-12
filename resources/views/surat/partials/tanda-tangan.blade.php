@php
    $stempelSrc = $stempel_base64 ?? null;
    if (empty($stempelSrc)) {
        $stempelPath = base_path('resources/surat/stempel-kepala-kampung.png');
        if (file_exists($stempelPath)) {
            $stempelSrc = 'data:image/png;base64,'.base64_encode(file_get_contents($stempelPath));
        }
    }

    $namaKampungTtd = $kop['nama_kampung'] ?? '';
    $tglTtd = $tgl_surat ?? tgl_indo(now());
    $kotaTanggal = $namaKampungTtd !== '' ? $namaKampungTtd.', '.$tglTtd : $tglTtd;
    $jabatanTtd = $surat_cfg['jabatan_penandatangan'] ?? 'Kepala Kampung';
    $namaTtd = $penandatangan->nama ?? '-';
    $nipRaw = trim((string) ($penandatangan->nip ?? ''));
    $nipFormatted = null;
    if ($nipRaw !== '') {
        $nipFormatted = preg_match('/^NIP\.?\s*/i', $nipRaw)
            ? preg_replace('/^NIP\.?\s*/i', 'NIP. ', $nipRaw)
            : 'NIP. '.$nipRaw;
    }
@endphp

<table class="ttd-tabel" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td class="ttd-kiri">
            @if(! empty($is_draft))
                <div class="qr-ruang"></div>
            @elseif(! empty($qr_base64))
                <img src="{{ $qr_base64 }}" class="qr-gambar" alt="QR Verifikasi">
            @else
                <div class="qr-ruang"></div>
            @endif
        </td>
        <td class="ttd-kanan">
            <div class="ttd-tanggal">{{ $kotaTanggal }}</div>
            <div class="ttd-jabatan">{{ $jabatanTtd }}</div>

            <div class="ttd-area">
                @if(! empty($is_draft))
                    <div class="ttd-placeholder">(Tanda tangan digital saat disetujui)</div>
                @else
                    @if($stempelSrc)
                        <img src="{{ $stempelSrc }}" class="ttd-stempel" alt="Stempel Kepala Kampung">
                    @endif
                    @if(! empty($ttd_base64))
                        <img src="{{ $ttd_base64 }}" class="ttd-gambar" alt="Tanda Tangan Kepala Kampung">
                    @endif
                @endif
            </div>

            <div class="ttd-nama"><span>{{ $namaTtd }}</span></div>
            @if(! empty($nipFormatted))
                <div class="ttd-nip">{{ $nipFormatted }}</div>
            @endif
        </td>
    </tr>
</table>
