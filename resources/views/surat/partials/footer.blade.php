<div class="footer">
    <div class="footer-garis"></div>
    <div class="footer-teks">
        @if(! empty($is_draft))
            Pratinjau draft - belum diterbitkan dan belum bernomor
        @else
            Dokumen ini diterbitkan secara digital melalui {{ $namaSistem }} — Kode: {{ $no_surat ?? '-' }}
        @endif
    </div>
</div>
