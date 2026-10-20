<div class="card mb-3">
    <div class="card-body">
        <p><strong>Perusahaan:</strong>
            {{ $ajuan->profilPenyedia->name ?? ($ajuan->penyedia->name ?? '-') }}</p>
        <p><strong>Tanggal ajuan:</strong> {{ optional($ajuan->tanggal_ajuan)->format('d-m-Y') }}</p>
        <p><strong>Status:</strong> {{ $ajuan->statusLabel() }}</p>
        <p><strong>Masa berlaku:</strong> {{ optional($ajuan->masa_berlaku)->format('d-m-Y') ?? '-' }}</p>
        <p><strong>Alasan/Keterangan:</strong> {{ $ajuan->alasan ?: '-' }}</p>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0">Dokumen</h5>
    </div>
    <div class="card-body">
        <ul class="list-unstyled mb-0">
            @foreach ($dokumenKeys as $key => $label)
                @php $item = $ajuan->dokumen[$key] ?? null; @endphp
                <li class="mb-2">
                    <strong>{{ $label }}</strong><br>
                    @if (($item['tipe'] ?? null) === 'link' && !empty($item['value']))
                        <a href="{{ $item['value'] }}" target="_blank" rel="noopener">{{ $item['value'] }}</a>
                    @elseif (($item['tipe'] ?? null) === 'file' && !empty($item['value']))
                        <a href="{{ route($fileRouteName ?? 'pengawasan.pjk3.admin.file', [$ajuan->id, $key]) }}"
                            target="_blank">Lihat file</a>
                    @else
                        <span class="text-muted">Tidak ada</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0">Log Verifikasi</h5>
    </div>
    <div class="card-body">
        @php $logs = $ajuan->log_verifikasi ?? []; @endphp
        @if (count($logs))
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Waktu</th>
                            <th>Oleh</th>
                            <th>Alasan/Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                <td>{{ \App\Models\Pengawasan\Pjk3\EtamPjk3Ajuan::logStatusLabel($log['status'] ?? null) }}</td>
                                <td>{{ $log['waktu'] ?? '-' }}</td>
                                <td>{{ $log['nama'] ?? '-' }}</td>
                                <td>{{ $log['alasan'] ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted mb-0">Belum ada log verifikasi.</p>
        @endif
    </div>
</div>
