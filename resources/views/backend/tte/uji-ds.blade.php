@extends('backend.template.backend')

@section('content')

    <body class="box-layout container background-green">
        <div class="pcoded-main-container">
            <div class="pcoded-content">
                <div class="page-header">
                    <div class="page-block">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <div class="page-header-title">
                                    <h5 class="m-b-10">Uji DS (3 langkah)</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <p class="text-muted">Halaman uji Super Admin. Langsung ke server DS, bukan mode demo. Passphrase boleh tempel.</p>
                                <ol class="small mb-3">
                                    <li><code>POST /ttdds</code> — unggah PDF, simpan kode</li>
                                    <li><code>POST /sign</code> — kode + passphrase</li>
                                    <li><code>POST /dokumen</code> — unduh PDF TTE (hanya jika sign berhasil)</li>
                                </ol>
                                <p class="small mb-4">Template dari env: <code>{{ $template !== '' ? $template : '(belum diisi)' }}</code></p>

                                @if ($uji)
                                    <div class="alert {{ $uji->unduh_ok ? 'alert-success' : ($uji->kode_ds ? 'alert-warning' : 'alert-danger') }}">
                                        <strong>Hasil terakhir</strong>
                                        <ul class="mb-0 mt-2">
                                            <li>1. Kirim: {{ $uji->kirim_ok ? 'OK' : 'GAGAL' }} (HTTP {{ $uji->kirim_http ?? '-' }}) — {{ $uji->kirim_pesan }}</li>
                                            <li>Kode DS: <code>{{ $uji->kode_ds ?: '—' }}</code> @if($uji->kode_ds) (tersimpan) @endif</li>
                                            <li>2. Sign: {{ is_null($uji->sign_ok) ? 'tidak dijalankan' : ($uji->sign_ok ? 'OK' : 'GAGAL') }} (HTTP {{ $uji->sign_http ?? '-' }}) — {{ $uji->sign_pesan }}</li>
                                            <li>3. Unduh: {{ is_null($uji->unduh_ok) ? 'tidak dijalankan' : ($uji->unduh_ok ? 'OK' : 'GAGAL') }} (HTTP {{ $uji->unduh_http ?? '-' }}) — {{ $uji->unduh_pesan }}</li>
                                        </ul>
                                    </div>
                                    <div class="mb-4">
                                        <h6>Respons asli DS</h6>
                                        <p class="small text-muted mb-2">1. POST /ttdds (HTTP {{ $uji->kirim_http ?? '-' }})</p>
                                        <pre class="bg-dark text-white p-3 small" style="white-space:pre-wrap;max-height:280px;overflow:auto;">{{ $uji->kirim_body ?: '—' }}</pre>
                                        <p class="small text-muted mb-2">2. POST /sign (HTTP {{ $uji->sign_http ?? '-' }})</p>
                                        <pre class="bg-dark text-white p-3 small" style="white-space:pre-wrap;max-height:280px;overflow:auto;">{{ $uji->sign_body ?: '—' }}</pre>
                                        <p class="small text-muted mb-2">3. POST /dokumen (HTTP {{ $uji->unduh_http ?? '-' }})</p>
                                        <pre class="bg-dark text-white p-3 small" style="white-space:pre-wrap;max-height:280px;overflow:auto;">{{ $uji->unduh_body ?: '—' }}</pre>
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                                @endif

                                <form method="POST" action="{{ route('tte.uji-ds.kirim') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="pdf">File PDF</label>
                                        <input type="file" class="form-control" id="pdf" name="pdf" accept="application/pdf" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="nik">NIK penandatangan</label>
                                        <input type="text" class="form-control" id="nik" name="nik" maxlength="16" inputmode="numeric"
                                            value="{{ old('nik', $nik) }}" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="passphrase">Passphrase</label>
                                        <input type="text" class="form-control" id="passphrase" name="passphrase" autocomplete="off" required>
                                        <small class="text-muted">Boleh salin-tempel. Tidak disimpan.</small>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="keterangan">Keterangan (opsional)</label>
                                        <input type="text" class="form-control" id="keterangan" name="keterangan" value="{{ old('keterangan', 'Uji DS ETAM') }}">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Jalankan 3 langkah</button>
                                </form>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <h6>Riwayat uji</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th>Waktu</th>
                                                <th>File</th>
                                                <th>NIK</th>
                                                <th>Kode DS</th>
                                                <th>Kirim</th>
                                                <th>Sign</th>
                                                <th>Unduh</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($daftar as $row)
                                                <tr>
                                                    <td>{{ optional($row->created_at)->format('d/m/Y H:i:s') }}</td>
                                                    <td>{{ $row->nama_file }}</td>
                                                    <td>{{ $row->nik }}</td>
                                                    <td><code>{{ $row->kode_ds ?: '—' }}</code></td>
                                                    <td>{{ $row->kirim_ok ? 'OK' : 'Gagal' }}</td>
                                                    <td>{{ is_null($row->sign_ok) ? '—' : ($row->sign_ok ? 'OK' : 'Gagal') }}</td>
                                                    <td>{{ is_null($row->unduh_ok) ? '—' : ($row->unduh_ok ? 'OK' : 'Gagal') }}</td>
                                                    <td>
                                                        @if ($row->path_file_after || $row->path_file)
                                                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('tte.uji-ds.berkas', $row) }}">PDF</a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8">Belum ada uji.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
@endsection
