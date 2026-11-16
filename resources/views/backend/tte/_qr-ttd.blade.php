@php
    $urlVerifikasi = $urlVerifikasi ?? null;
    $qrVerifikasi = $qrVerifikasi ?? null;
@endphp
@if ($qrVerifikasi || $urlVerifikasi)
    <table class="ttd-qr-wrap">
        <tr>
            <td>
                @if ($urlVerifikasi)
                    <a href="{{ $urlVerifikasi }}">
                @endif
                @if ($qrVerifikasi)
                    <img src="{{ $qrVerifikasi }}" alt="QR verifikasi TTE" class="ttd-qr">
                @endif
                @if ($urlVerifikasi)
                    </a>
                @endif
            </td>
        </tr>
    </table>
@endif
