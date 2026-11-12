@php
    $rows = $rows ?? [];
    $variant = $variant ?? '';
@endphp

<div class="tabel-indent">
    <table class="tabel-data {{ $variant }}" width="100%" cellpadding="0" cellspacing="0" border="0">
        @foreach($rows as $row)
            <tr>
                <td class="label">{{ $row['label'] }}</td>
                <td class="titik-dua">:</td>
                <td class="nilai">
                    @if(! empty($row['raw']))
                        {!! $row['value'] !!}
                    @else
                        {{ $row['value'] }}
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
</div>
