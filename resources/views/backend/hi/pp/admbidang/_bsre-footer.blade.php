<div class="hi-pp-bsre-footer">
    <table>
        <tr>
            <td class="hi-pp-bsre-logo">
                @if (!empty($bsreLogoDataUri))
                    <img src="{{ $bsreLogoDataUri }}" alt="Logo BSrE">
                @endif
            </td>
            <td class="hi-pp-bsre-text">{!! nl2br(e($bsreFooterText ?? 'Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik yang diterbitkan oleh Balai Sertifikasi Elektronik (BSrE), Badan Siber dan Sandi Negara (BSSN).')) !!}</td>
        </tr>
    </table>
</div>
