@if (! empty($qrVerifikasi) || ! empty($urlVerifikasi))
    <div class="qr-tte">
        @if (! empty($qrVerifikasi))
            <img src="{{ $qrVerifikasi }}" alt="QR berkas asli">
        @endif
        <small>Berkas asli</small>
    </div>
@endif
