@extends('backend.template.backend')

@php
    $cbtTemplates = $cbtTemplates ?? collect();
@endphp

@section('content')

    <body class="box-layout container background-green">
        <div class="pcoded-main-container">
            <div class="pcoded-content">
                <div class="page-header">
                    <div class="page-block">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <div class="page-header-title">
                                    <h5 class="m-b-10">{{ $pelatihan->id ? 'Edit Pelatihan BLK' : 'Tambah Pelatihan BLK' }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <form
                                    action="{{ $pelatihan->id ? route('blk.pelatihan.update', $pelatihan->id) : route('blk.pelatihan.store') }}"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @if ($pelatihan->id)
                                        @method('PUT')
                                    @endif

                                @if ($blkOptions->isEmpty())
                                    <div class="alert alert-warning">
                                        BLK untuk balai Anda belum terdaftar. Minta super admin mengisi
                                        <strong>Daftar BLK</strong> lalu pilih Struktur/Balai yang sama dengan akun
                                        ini.
                                    </div>
                                @endif
                                    <h6 class="mb-3">Informasi Pelatihan</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3" hidden>
                                            <label class="form-label">Pelatihan Untuk</label>
                                            <select name="pelatihan_untuk" class="form-control" required>
                                                @foreach (\App\Models\BLK\EtamBlkPelatihan::untukLabels() as $key => $label)
                                                    <option value="{{ $key }}" @selected(old('pelatihan_untuk', $pelatihan->pelatihan_untuk) == $key)>
                                                        {{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">BLK</label>
                                            <select name="blk_id" class="form-control" required @disabled(($isBlkStaff ?? false) && $blkOptions->count() === 1)>
                                                @if ($blkOptions->count() !== 1)
                                                    <option value="">-- Pilih BLK --</option>
                                                @endif
                                                @foreach ($blkOptions as $blk)
                                                    <option value="{{ $blk->id }}" @selected(old('blk_id', $pelatihan->blk_id) == $blk->id)>
                                                        {{ $blk->nama_lembaga }}</option>
                                                @endforeach
                                            </select>
                                            @if (($isBlkStaff ?? false) && $blkOptions->count() === 1)
                                                <input type="hidden" name="blk_id" value="{{ $blkOptions->first()->id }}">
                                            @endif
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Nama Pelatihan</label>
                                            <input type="text" name="nama_pelatihan" class="form-control"
                                                value="{{ old('nama_pelatihan', $pelatihan->nama_pelatihan) }}" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Kejuruan</label>
                                            <input type="text" name="kejuruan" class="form-control"
                                                value="{{ old('kejuruan', $pelatihan->kejuruan) }}"
                                                placeholder="Contoh: Teknik Manufaktur">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Angkatan</label>
                                            <input type="text" name="angkatan" class="form-control"
                                                value="{{ old('angkatan', $pelatihan->angkatan) }}"
                                                placeholder="Contoh: II">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Jam Pelatihan</label>
                                            <input type="number" name="jam_pelatihan" class="form-control" min="1"
                                                value="{{ old('jam_pelatihan', $pelatihan->jam_pelatihan) }}"
                                                placeholder="Contoh: 260">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Sumber Pembiayaan</label>
                                            <select name="sumber_pembiayaan" class="form-control" required>
                                                @foreach (\App\Models\BLK\EtamBlkPelatihan::pembiayaanLabels() as $key => $label)
                                                    <option value="{{ $key }}" @selected(old('sumber_pembiayaan', $pelatihan->sumber_pembiayaan) == $key)>
                                                        {{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Tipe Pelatihan</label>
                                            <select name="tipe_pelatihan" class="form-control" required>
                                                @foreach (\App\Models\BLK\EtamBlkPelatihan::tipeLabels() as $key => $label)
                                                    <option value="{{ $key }}" @selected(old('tipe_pelatihan', $pelatihan->tipe_pelatihan) == $key)>
                                                        {{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-control" required>
                                                @foreach (\App\Models\BLK\EtamBlkPelatihan::statusLabels() as $key => $label)
                                                    <option value="{{ $key }}" @selected(old('status', $pelatihan->status) == $key)>
                                                        {{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Pendaftaran Mulai</label>
                                            <input type="date" name="tanggal_pendaftaran" class="form-control"
                                                value="{{ old('tanggal_pendaftaran', optional($pelatihan->tanggal_pendaftaran)->format('Y-m-d')) }}"
                                                required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Pendaftaran Selesai</label>
                                            <input type="date" name="tanggal_pendaftaran_selesai" class="form-control"
                                                value="{{ old('tanggal_pendaftaran_selesai', optional($pelatihan->tanggal_pendaftaran_selesai)->format('Y-m-d')) }}"
                                                required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Pelaksanaan Mulai</label>
                                            <input type="date" name="tanggal_pelaksanaan" class="form-control"
                                                value="{{ old('tanggal_pelaksanaan', optional($pelatihan->tanggal_pelaksanaan)->format('Y-m-d')) }}"
                                                required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Pelaksanaan Selesai</label>
                                            <input type="date" name="tanggal_pelaksanaan_selesai" class="form-control"
                                                value="{{ old('tanggal_pelaksanaan_selesai', optional($pelatihan->tanggal_pelaksanaan_selesai)->format('Y-m-d')) }}"
                                                required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Tanggal TTD Kepala Dinas</label>
                                            <input type="date" name="tanggal_ttd_dinas" class="form-control"
                                                value="{{ old('tanggal_ttd_dinas', optional($pelatihan->tanggal_ttd_dinas)->format('Y-m-d')) }}">
                                            <small class="text-muted">Muncul sebagai Samarinda, tanggal ini. Nanti terisi otomatis saat TTE.</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Tanggal TTD Kepala UPTD</label>
                                            <input type="date" name="tanggal_ttd_uptd" class="form-control"
                                                value="{{ old('tanggal_ttd_uptd', optional($pelatihan->tanggal_ttd_uptd)->format('Y-m-d')) }}">
                                            <small class="text-muted">Muncul sebagai Balikpapan/Bontang, tanggal ini. Nanti terisi otomatis saat TTE.</small>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Info Lokasi / Link</label>
                                            <input type="text" name="info_lokasi" class="form-control"
                                                value="{{ old('info_lokasi', $pelatihan->info_lokasi) }}">
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Deskripsi</label>
                                            <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi', $pelatihan->deskripsi) }}</textarea>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Template Wawancara</label>
                                            <select name="wawancara_form_id" id="wawancara_form_id" class="form-control">
                                                <option value="">-- Tidak ada --</option>
                                                @foreach ($wawancaraTemplates as $template)
                                                    <option value="{{ $template->id }}" data-blk="{{ $template->blk_id }}"
                                                        @selected(old('wawancara_form_id', $pelatihan->wawancara_form_id) == $template->id)>
                                                        {{ $template->nama }}</option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">Diisi admin saat wawancara peserta.</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Template Pretest</label>
                                            <select name="pretest_form_id" id="pretest_form_id" class="form-control">
                                                <option value="">-- Tidak ada --</option>
                                                @foreach ($pretestTemplates as $template)
                                                    <option value="{{ $template->id }}" data-blk="{{ $template->blk_id }}"
                                                        @selected(old('pretest_form_id', $pelatihan->pretest_form_id) == $template->id)>
                                                        {{ $template->nama }}</option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">Jika dipilih, peserta mengisi sendiri setelah daftar.</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Template CBT</label>
                                            <select name="cbt_form_id" id="cbt_form_id" class="form-control">
                                                <option value="">-- Tidak ada --</option>
                                                @foreach ($cbtTemplates as $template)
                                                    <option value="{{ $template->id }}" data-blk="{{ $template->blk_id }}"
                                                        @selected(old('cbt_form_id', $pelatihan->cbt_form_id) == $template->id)>
                                                        {{ $template->nama }}</option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">Soal pilihan ganda. Peserta mengerjakan jika tes dibuka.</small>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Status tes CBT</label>
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="cbt_buka"
                                                    id="cbt_buka" value="1"
                                                    @checked((int) old('cbt_buka', $pelatihan->cbt_buka) === 1)>
                                                <label class="form-check-label" for="cbt_buka">Buka tes</label>
                                            </div>
                                            <small class="text-muted">Jika ditutup, peserta tidak bisa mengerjakan.</small>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Kode tes (4 digit)</label>
                                            <input type="text" class="form-control" value="{{ $pelatihan->cbt_kode ?: 'Otomatis saat disimpan' }}"
                                                disabled>
                                            @if ($pelatihan->cbt_kode)
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" name="regenerate_cbt_kode"
                                                        id="regenerate_cbt_kode" value="1">
                                                    <label class="form-check-label" for="regenerate_cbt_kode">Generate ulang
                                                        kode</label>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Poster</label>
                                            <input type="file" name="poster" class="form-control" accept="image/*">
                                            @if ($pelatihan->poster)
                                                <small class="text-muted">Poster saat ini:
                                                    {{ $pelatihan->poster }}</small>
                                            @endif
                                        </div>
                                    </div>

                                    <h6 class="mt-3 mb-3">Persyaratan</h6>
                                    <div id="syarat-wrap">
                                        @php
                                            $syaratItems = old('syarat', $syarat->pluck('persyaratan')->all() ?: ['']);
                                        @endphp
                                        @foreach ($syaratItems as $item)
                                            <div class="input-group mb-2 syarat-row">
                                                <input type="text" name="syarat[]" class="form-control"
                                                    value="{{ $item }}">
                                                <button type="button" class="btn btn-danger btn-remove-row">Hapus</button>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="add-syarat">Tambah
                                        Syarat</button>

                                    <h6 class="mt-3 mb-3">Fasilitas</h6>
                                    <div id="fasilitas-wrap">
                                        @php
                                            $fasilitasItems = old('fasilitas', $fasilitas->pluck('fasilitas')->all() ?: ['']);
                                        @endphp
                                        @foreach ($fasilitasItems as $item)
                                            <div class="input-group mb-2 fasilitas-row">
                                                <input type="text" name="fasilitas[]" class="form-control"
                                                    value="{{ $item }}">
                                                <button type="button" class="btn btn-danger btn-remove-row">Hapus</button>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm mb-3"
                                        id="add-fasilitas">Tambah Fasilitas</button>

                                    <h6 class="mt-3 mb-1">Unit Kompetensi Inti</h6>
                                    <p class="text-muted small mb-2">Nama unit kompetensi dan kode unit. Kode kosong otomatis
                                        menjadi <strong>-</strong>.</p>
                                    <div id="kompetensi-inti-wrap">
                                        @php
                                            $intiOld = old('kompetensi_inti.nama');
                                            if (is_array($intiOld)) {
                                                $intiRows = collect($intiOld)->map(function ($nama, $i) {
                                                    return ['nama' => $nama, 'kode' => old('kompetensi_inti.kode.'.$i, '')];
                                                })->all();
                                            } else {
                                                $intiRows = $kompetensiInti->isEmpty()
                                                    ? [['nama' => '', 'kode' => '']]
                                                    : $kompetensiInti->map(fn ($item) => ['nama' => $item->nama, 'kode' => $item->kode])->all();
                                            }
                                        @endphp
                                        @foreach ($intiRows as $item)
                                            <div class="input-group mb-2 kompetensi-inti-row">
                                                <input type="text" name="kompetensi_inti[nama][]" class="form-control"
                                                    placeholder="Nama unit kompetensi" value="{{ $item['nama'] }}">
                                                <input type="text" name="kompetensi_inti[kode][]" class="form-control"
                                                    placeholder="Kode unit" style="max-width: 220px"
                                                    value="{{ $item['kode'] }}">
                                                <button type="button" class="btn btn-danger btn-remove-row">Hapus</button>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm mb-3"
                                        id="add-kompetensi-inti">Tambah Unit Kompetensi Inti</button>

                                    <h6 class="mt-3 mb-1">Unit Kompetensi Penunjang</h6>
                                    <p class="text-muted small mb-2">Nama unit kompetensi dan kode unit. Kode kosong otomatis
                                        menjadi <strong>-</strong>.</p>
                                    <div id="kompetensi-penunjang-wrap">
                                        @php
                                            $penunjangOld = old('kompetensi_penunjang.nama');
                                            if (is_array($penunjangOld)) {
                                                $penunjangRows = collect($penunjangOld)->map(function ($nama, $i) {
                                                    return ['nama' => $nama, 'kode' => old('kompetensi_penunjang.kode.'.$i, '')];
                                                })->all();
                                            } else {
                                                $penunjangRows = $kompetensiPenunjang->isEmpty()
                                                    ? [['nama' => '', 'kode' => '']]
                                                    : $kompetensiPenunjang->map(fn ($item) => ['nama' => $item->nama, 'kode' => $item->kode])->all();
                                            }
                                        @endphp
                                        @foreach ($penunjangRows as $item)
                                            <div class="input-group mb-2 kompetensi-penunjang-row">
                                                <input type="text" name="kompetensi_penunjang[nama][]" class="form-control"
                                                    placeholder="Nama unit kompetensi" value="{{ $item['nama'] }}">
                                                <input type="text" name="kompetensi_penunjang[kode][]" class="form-control"
                                                    placeholder="Kode unit" style="max-width: 220px"
                                                    value="{{ $item['kode'] }}">
                                                <button type="button" class="btn btn-danger btn-remove-row">Hapus</button>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm mb-3"
                                        id="add-kompetensi-penunjang">Tambah Unit Kompetensi Penunjang</button>

                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <a href="{{ route('blk.pelatihan.index') }}" class="btn btn-secondary">Kembali</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $('#add-syarat').on('click', function() {
                $('#syarat-wrap').append(
                    '<div class="input-group mb-2 syarat-row"><input type="text" name="syarat[]" class="form-control"><button type="button" class="btn btn-danger btn-remove-row">Hapus</button></div>'
                );
            });
            $('#add-fasilitas').on('click', function() {
                $('#fasilitas-wrap').append(
                    '<div class="input-group mb-2 fasilitas-row"><input type="text" name="fasilitas[]" class="form-control"><button type="button" class="btn btn-danger btn-remove-row">Hapus</button></div>'
                );
            });
            $('#add-kompetensi-inti').on('click', function() {
                $('#kompetensi-inti-wrap').append(
                    '<div class="input-group mb-2 kompetensi-inti-row"><input type="text" name="kompetensi_inti[nama][]" class="form-control" placeholder="Nama unit kompetensi"><input type="text" name="kompetensi_inti[kode][]" class="form-control" placeholder="Kode unit" style="max-width:220px"><button type="button" class="btn btn-danger btn-remove-row">Hapus</button></div>'
                );
            });
            $('#add-kompetensi-penunjang').on('click', function() {
                $('#kompetensi-penunjang-wrap').append(
                    '<div class="input-group mb-2 kompetensi-penunjang-row"><input type="text" name="kompetensi_penunjang[nama][]" class="form-control" placeholder="Nama unit kompetensi"><input type="text" name="kompetensi_penunjang[kode][]" class="form-control" placeholder="Kode unit" style="max-width:220px"><button type="button" class="btn btn-danger btn-remove-row">Hapus</button></div>'
                );
            });
            $(document).on('click', '.btn-remove-row', function() {
                $(this).closest('.input-group').remove();
            });

            function filterTemplates() {
                var blkId = String($('select[name="blk_id"]').val() || $('input[name="blk_id"]').val() || '');
                $('#wawancara_form_id option, #pretest_form_id option, #cbt_form_id option').each(function() {
                    var optBlk = $(this).attr('data-blk');
                    if (!optBlk) {
                        $(this).prop('disabled', false);
                        return;
                    }
                    var match = !blkId || String(optBlk) === blkId;
                    $(this).prop('disabled', !match);
                    if (!match && this.selected) {
                        this.selected = false;
                    }
                });
            }
            $('[name="blk_id"]').on('change', filterTemplates);
            filterTemplates();
        });
    </script>
@endpush
