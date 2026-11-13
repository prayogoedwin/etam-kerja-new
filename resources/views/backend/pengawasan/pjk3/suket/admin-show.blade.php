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
                                    <h5 class="m-b-10">Detail Pengajuan Suket Layak K3</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @include('backend.pengawasan.pjk3.suket._timeline-style')
                @include('backend.pengawasan.pjk3.suket._info', ['timelineAdmin' => true])

                @if ((int) $suket->status >= \App\Models\Pengawasan\Pjk3\EtamPjk3Suket::STATUS_DRAFT)
                    <div class="mb-3">
                        <a href="{{ route('pengawasan.suket.admin.cetak-lhpp', $suket->id) }}" target="_blank"
                            class="btn btn-primary">Cetak LHPP</a>
                        <a href="{{ route('pengawasan.suket.admin.cetak-suket', $suket->id) }}" target="_blank"
                            class="btn btn-success">Cetak Suket</a>
                    </div>
                    @if (in_array((int) $suket->status, [
                            \App\Models\Pengawasan\Pjk3\EtamPjk3Suket::STATUS_DRAFT,
                            \App\Models\Pengawasan\Pjk3\EtamPjk3Suket::STATUS_TTD_PENGAWAS,
                        ], true) && auth()->user()?->bisaAksesTte())
                        <div class="alert alert-info">
                            Tanda tangan elektronik LHPP/Suket dilakukan dari menu
                            <a href="{{ route('tte.menunggu') }}">TTE</a>.
                            Daftar hanya menampilkan dokumen yang ditujukan kepada Anda.
                        </div>
                    @endif
                @endif

                @if ($canDispo)
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">Disposisi ke Pengawas</h5>
                        </div>
                        <div class="card-body">
                            <form id="formDispo">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Pengawas (Bidang Pembinaan dan Pengawasan / seksi
                                        turunan) <span class="text-danger">*</span></label>
                                    <select name="pengawas_id" class="form-control" required>
                                        <option value="">Pilih pengawas</option>
                                        @foreach ($daftarPengawas as $pw)
                                            <option value="{{ $pw->id }}">{{ $pw->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Catatan disposisi</label>
                                    <textarea name="catatan_disposisi" class="form-control" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Teruskan ke Pengawas</button>
                            </form>
                        </div>
                    </div>
                @endif

                @if ($canAnalisis)
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">LHPP dinas — diisi pengawas</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Isi sesuai LHPP PJK3 yang diunggah di atas. Data ini masuk ke LHPP dan
                                Suket yang ditandatangani.</p>
                            <form id="formAnalisis">
                                @csrf
                                <h6 class="mt-2">1. Pembuka &amp; dasar</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Bidang Ahli K3 <span class="text-danger">*</span></label>
                                        <select name="ahli_k3_kategori_id" id="ahli_k3_kategori_id" class="form-control"
                                            required>
                                            <option value="">Pilih bidang</option>
                                            @foreach ($kategoriList as $kategori)
                                                <option value="{{ $kategori->id }}"
                                                    @selected((int) $lhppForm['ahli_k3_kategori_id'] === (int) $kategori->id)>
                                                    {{ $kategori->nama }}
                                                    @if ($kategori->kode)
                                                        ({{ $kategori->kode }})
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tanggal pemeriksaan <span
                                                class="text-danger">*</span></label>
                                        <input type="date" name="tanggal_pemeriksaan" class="form-control"
                                            value="{{ optional($suket->tanggal_pemeriksaan)->format('Y-m-d') ?? now()->toDateString() }}"
                                            required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Dasar pelaksanaan</label>
                                        <textarea name="dasar_hukum" class="form-control" rows="3"
                                            placeholder="UU, Permenaker, instruksi terkait">{{ $lhppForm['dasar_hukum'] }}</textarea>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Tempat pengujian</label>
                                        <input type="text" name="tempat_pengujian" class="form-control"
                                            value="{{ $lhppForm['tempat_pengujian'] }}">
                                    </div>
                                </div>

                                <h6>2. Data perusahaan yang diuji</h6>
                                <p class="small text-muted mb-2">Nama, alamat, NIB diambil dari akun perusahaan.</p>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nama pengurus <span class="text-danger">*</span></label>
                                        <input type="text" name="nama_pengurus" class="form-control"
                                            value="{{ $lhppForm['nama_pengurus'] }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jenis usaha</label>
                                        <input type="text" name="jenis_usaha" class="form-control"
                                            value="{{ $lhppForm['jenis_usaha'] }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jumlah pekerja</label>
                                        <input type="number" name="jumlah_pekerja" class="form-control" min="0"
                                            value="{{ $lhppForm['jumlah_pekerja'] }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nomor WLKP</label>
                                        <input type="text" name="nomor_wlkp" class="form-control"
                                            value="{{ $lhppForm['nomor_wlkp'] }}">
                                    </div>
                                </div>

                                <h6>3. Identitas objek K3</h6>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Nomor unit</label>
                                        <input type="text" name="nomor_unit" class="form-control"
                                            value="{{ $lhppForm['nomor_unit'] }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Nomor seri</label>
                                        <input type="text" name="nomor_seri" class="form-control"
                                            value="{{ $lhppForm['nomor_seri'] }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Pabrik / pembuat</label>
                                        <input type="text" name="pabrik" class="form-control"
                                            value="{{ $lhppForm['pabrik'] }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Model / tipe</label>
                                        <input type="text" name="model" class="form-control"
                                            value="{{ $lhppForm['model'] }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Negara pembuat</label>
                                        <input type="text" name="negara_pembuat" class="form-control"
                                            value="{{ $lhppForm['negara_pembuat'] }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Tahun pasang</label>
                                        <input type="number" name="tahun_pasang" class="form-control" min="1900"
                                            max="2100" value="{{ $lhppForm['tahun_pasang'] }}">
                                    </div>
                                </div>

                                <h6>4. Data teknis objek pengujian</h6>
                                <p class="small text-muted">Salin dari LHPP PJK3. Satu baris per objek (Panel Control, Gas
                                    Detector, dst.). Jenis dipakai untuk pemetaan laporan; nama boleh diubah.</p>
                                <div class="table-responsive mb-2">
                                    <table class="table table-bordered table-sm" id="tabelTeknis">
                                        <thead>
                                            <tr>
                                                <th style="width:3%">No</th>
                                                <th>Jenis</th>
                                                <th>Nama objek</th>
                                                <th>Merk</th>
                                                <th>Jumlah</th>
                                                <th>Lokasi</th>
                                                <th style="width:4%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="daftarPerangkat">
                                            @foreach ($lhppForm['perangkat'] as $i => $row)
                                                <tr class="js-perangkat" data-index="{{ $i }}">
                                                    <td class="js-nomor align-middle">{{ $i + 1 }}</td>
                                                    <td>
                                                        <select name="perangkat[{{ $i }}][jenis_id]"
                                                            class="form-control form-control-sm js-jenis-perangkat">
                                                            <option value="">Pilih jenis</option>
                                                            @foreach ($jenisList as $jenis)
                                                                <option value="{{ $jenis->id }}"
                                                                    data-kategori="{{ $jenis->kategori_id }}"
                                                                    data-nama="{{ $jenis->nama }}"
                                                                    @selected((int) ($row['jenis_id'] ?? 0) === (int) $jenis->id)>
                                                                    {{ $jenis->nama }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="text" name="perangkat[{{ $i }}][nama]"
                                                            class="form-control form-control-sm js-nama-perangkat"
                                                            value="{{ $row['nama'] ?? '' }}"
                                                            placeholder="Contoh: Panel Control"></td>
                                                    <td><input type="text" name="perangkat[{{ $i }}][merk]"
                                                            class="form-control form-control-sm"
                                                            value="{{ $row['merk'] ?? '' }}" placeholder="Merk"></td>
                                                    <td><input type="text" name="perangkat[{{ $i }}][jumlah]"
                                                            class="form-control form-control-sm"
                                                            value="{{ $row['jumlah'] ?? '' }}"
                                                            placeholder="1 Unit"></td>
                                                    <td><input type="text" name="perangkat[{{ $i }}][lokasi]"
                                                            class="form-control form-control-sm"
                                                            value="{{ $row['lokasi'] ?? '' }}"
                                                            placeholder="Control Room"></td>
                                                    <td class="text-center"><button type="button"
                                                            class="btn btn-outline-danger btn-sm js-hapus-perangkat">&times;</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm mb-3"
                                    id="btnTambahPerangkat">Tambah baris objek</button>

                                <h6>5. Hasil pengujian</h6>
                                <div class="mb-3">
                                    <label class="form-label">Nomor LHPP PJK3 <span class="text-danger">*</span></label>
                                    <input type="text" name="nomor_lhpp_pjk3" class="form-control"
                                        value="{{ $lhppForm['nomor_lhpp_pjk3'] }}" required>
                                </div>
                                <p class="small text-muted">Setiap item uji dikaitkan ke objek di tabel atas.</p>
                                <div class="table-responsive mb-2">
                                    <table class="table table-bordered table-sm" id="tabelUji">
                                        <thead>
                                            <tr>
                                                <th>Objek diuji</th>
                                                <th>Item uji</th>
                                                <th>Status</th>
                                                <th>Hasil</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($lhppForm['uji'] as $j => $uji)
                                                <tr>
                                                    <td>
                                                        <select name="uji[{{ $j }}][perangkat_index]"
                                                            class="form-control form-control-sm js-uji-perangkat">
                                                            @foreach ($lhppForm['perangkat'] as $i => $row)
                                                                <option value="{{ $i }}"
                                                                    @selected((int) ($uji['perangkat_index'] ?? 0) === (int) $i)>
                                                                    {{ ($row['nama'] ?? '') !== '' ? $row['nama'] : 'Baris '.($i + 1) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="text" name="uji[{{ $j }}][item]"
                                                            class="form-control form-control-sm"
                                                            value="{{ $uji['item'] ?? '' }}"></td>
                                                    <td>
                                                        <select name="uji[{{ $j }}][status]"
                                                            class="form-control form-control-sm">
                                                            <option value="">-</option>
                                                            <option value="memenuhi"
                                                                @selected(($uji['status'] ?? '') === 'memenuhi')>
                                                                Memenuhi</option>
                                                            <option value="tidak_memenuhi"
                                                                @selected(($uji['status'] ?? '') === 'tidak_memenuhi')>
                                                                Tidak memenuhi</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" name="uji[{{ $j }}][hasil]"
                                                            class="form-control form-control-sm"
                                                            value="{{ $uji['hasil'] ?? '' }}"></td>
                                                    <td><button type="button"
                                                            class="btn btn-outline-danger btn-sm js-hapus-baris">&times;</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-outline-secondary btn-sm mb-3"
                                    id="btnTambahUji">Tambah item uji</button>
                                <div class="mb-3">
                                    <label class="form-label">Narasi pengujian</label>
                                    <textarea name="narasi_uji" class="form-control" rows="4">{{ $lhppForm['narasi_uji'] }}</textarea>
                                </div>

                                <h6>6. Kesimpulan</h6>
                                <div class="mb-3">
                                    <label class="form-label">Kesimpulan <span class="text-danger">*</span></label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="is_layak"
                                                id="layak" value="1" @checked((int) $suket->is_layak === 1)>
                                            <label class="form-check-label text-success" for="layak">Layak / MEMENUHI</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="is_layak"
                                                id="tidak-layak" value="0"
                                                @checked($suket->is_layak !== null && (int) $suket->is_layak === 0)>
                                            <label class="form-check-label text-danger" for="tidak-layak">Tidak layak</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Uraian kesimpulan <span class="text-danger">*</span></label>
                                    <textarea name="hasil_analisis" class="form-control" rows="4" required>{{ $suket->hasil_analisis }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Saran / rekomendasi</label>
                                    <textarea name="rekomendasi" class="form-control" rows="3">{{ $suket->rekomendasi }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Simpan hasil &amp; terbitkan draft</button>
                            </form>
                        </div>
                    </div>
                @endif

                <a href="{{ route('pengawasan.suket.admin.pengajuan') }}" class="btn btn-secondary mb-4">Kembali</a>
            </div>
        </div>
    </body>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $('#formDispo').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '{{ route('pengawasan.suket.admin.disposisi', $suket->id) }}',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil', response.message, 'success').then(function() {
                                location.reload();
                            });
                        } else if (response.errors) {
                            var msg = '';
                            $.each(response.errors, function(_, value) {
                                $.each(value, function(_, errorMessage) {
                                    msg += errorMessage + '\n';
                                });
                            });
                            Swal.fire('Periksa kembali', msg, 'warning');
                        } else {
                            Swal.fire('Gagal', response.message || 'Gagal memproses', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal', (xhr.responseJSON && xhr.responseJSON.message) ||
                            'Gagal memproses', 'error');
                    }
                });
            });

            if (!$('#formAnalisis').length) {
                return;
            }

            var jenisOptionsHtml = $('#daftarPerangkat .js-jenis-perangkat').first().html() ||
                '<option value="">Pilih jenis</option>';

            function filterJenisByKategori() {
                var kategoriId = String($('#ahli_k3_kategori_id').val() || '');
                $('.js-jenis-perangkat').each(function() {
                    var $select = $(this);
                    $select.find('option').each(function() {
                        var $opt = $(this);
                        if (!$opt.val()) {
                            return;
                        }
                        var match = !kategoriId || String($opt.data('kategori')) === kategoriId;
                        $opt.toggle(match);
                    });
                    var $selected = $select.find('option:selected');
                    if ($selected.val() && $selected.css('display') === 'none') {
                        $select.val('');
                        $select.closest('.js-perangkat').find('.js-nama-perangkat').val('');
                    }
                });
                refreshUjiPerangkatOptions();
            }

            function nextIndex($items) {
                var max = -1;
                $items.each(function() {
                    max = Math.max(max, parseInt($(this).attr('data-index'), 10) || 0);
                });
                return max + 1;
            }

            function nomorUlang() {
                $('#daftarPerangkat .js-perangkat').each(function(i) {
                    $(this).find('.js-nomor').text(i + 1);
                });
            }

            function labelPerangkat($row) {
                var nama = $.trim($row.find('.js-nama-perangkat').val() || '');
                if (nama) {
                    return nama;
                }
                var jenis = $.trim($row.find('.js-jenis-perangkat option:selected').text() || '');
                if (jenis && $row.find('.js-jenis-perangkat').val()) {
                    return jenis;
                }
                return 'Baris ' + $row.find('.js-nomor').text();
            }

            function refreshUjiPerangkatOptions() {
                var options = '';
                $('#daftarPerangkat .js-perangkat').each(function() {
                    var $row = $(this);
                    options += '<option value="' + $row.attr('data-index') + '">' + $('<div>').text(labelPerangkat(
                        $row)).html() + '</option>';
                });
                $('.js-uji-perangkat').each(function() {
                    var current = $(this).val();
                    $(this).html(options);
                    if (current !== null && $(this).find('option[value="' + current + '"]').length) {
                        $(this).val(current);
                    }
                });
            }

            function perangkatHtml(pIndex) {
                return '<tr class="js-perangkat" data-index="' + pIndex +
                    '"><td class="js-nomor align-middle"></td><td><select name="perangkat[' + pIndex +
                    '][jenis_id]" class="form-control form-control-sm js-jenis-perangkat">' + jenisOptionsHtml +
                    '</select></td><td><input type="text" name="perangkat[' + pIndex +
                    '][nama]" class="form-control form-control-sm js-nama-perangkat" placeholder="Contoh: Panel Control"></td><td><input type="text" name="perangkat[' +
                    pIndex +
                    '][merk]" class="form-control form-control-sm" placeholder="Merk"></td><td><input type="text" name="perangkat[' +
                    pIndex +
                    '][jumlah]" class="form-control form-control-sm" placeholder="1 Unit"></td><td><input type="text" name="perangkat[' +
                    pIndex +
                    '][lokasi]" class="form-control form-control-sm" placeholder="Control Room"></td><td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm js-hapus-perangkat">&times;</button></td></tr>';
            }

            function ujiRowHtml(uIndex) {
                var options = '';
                $('#daftarPerangkat .js-perangkat').each(function() {
                    var $row = $(this);
                    options += '<option value="' + $row.attr('data-index') + '">' + $('<div>').text(labelPerangkat(
                        $row)).html() + '</option>';
                });
                return '<tr><td><select name="uji[' + uIndex +
                    '][perangkat_index]" class="form-control form-control-sm js-uji-perangkat">' + options +
                    '</select></td><td><input type="text" name="uji[' + uIndex +
                    '][item]" class="form-control form-control-sm"></td><td><select name="uji[' + uIndex +
                    '][status]" class="form-control form-control-sm"><option value="">-</option><option value="memenuhi">Memenuhi</option><option value="tidak_memenuhi">Tidak memenuhi</option></select></td><td><input type="text" name="uji[' +
                    uIndex +
                    '][hasil]" class="form-control form-control-sm"></td><td><button type="button" class="btn btn-outline-danger btn-sm js-hapus-baris">&times;</button></td></tr>';
            }

            filterJenisByKategori();
            nomorUlang();
            $('#ahli_k3_kategori_id').on('change', filterJenisByKategori);

            $(document).on('change', '.js-jenis-perangkat', function() {
                var nama = $(this).find('option:selected').data('nama') || '';
                var $nama = $(this).closest('.js-perangkat').find('.js-nama-perangkat');
                if (!$nama.val() || $nama.data('auto') === $nama.val()) {
                    $nama.val(nama);
                    $nama.data('auto', nama);
                }
                refreshUjiPerangkatOptions();
            });

            $(document).on('input', '.js-nama-perangkat', function() {
                refreshUjiPerangkatOptions();
            });

            $(document).on('click', '.js-hapus-baris', function() {
                var $tbody = $(this).closest('tbody');
                if ($tbody.find('tr').length > 1) {
                    $(this).closest('tr').remove();
                } else {
                    $(this).closest('tr').find('input, select').val('');
                }
            });

            $(document).on('click', '.js-hapus-perangkat', function() {
                if ($('#daftarPerangkat .js-perangkat').length > 1) {
                    $(this).closest('.js-perangkat').remove();
                    nomorUlang();
                    refreshUjiPerangkatOptions();
                } else {
                    $(this).closest('.js-perangkat').find('input, select').val('');
                    refreshUjiPerangkatOptions();
                }
            });

            $('#btnTambahPerangkat').on('click', function() {
                var pIndex = nextIndex($('#daftarPerangkat .js-perangkat'));
                $('#daftarPerangkat').append(perangkatHtml(pIndex));
                var $baru = $('#daftarPerangkat .js-perangkat').last();
                $baru.find('.js-jenis-perangkat').val('');
                $baru.find('.js-nama-perangkat').val('');
                filterJenisByKategori();
                nomorUlang();
            });

            $('#btnTambahUji').on('click', function() {
                var uIndex = $('#tabelUji tbody tr').length;
                $('#tabelUji tbody').append(ujiRowHtml(uIndex));
            });

            $('#formAnalisis').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '{{ route('pengawasan.suket.admin.analisis', $suket->id) }}',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil', response.message, 'success').then(function() {
                                location.reload();
                            });
                        } else if (response.errors) {
                            var msg = '';
                            $.each(response.errors, function(_, value) {
                                $.each(value, function(_, errorMessage) {
                                    msg += errorMessage + '\n';
                                });
                            });
                            Swal.fire('Periksa kembali', msg, 'warning');
                        } else {
                            Swal.fire('Gagal', response.message || 'Gagal memproses', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal', (xhr.responseJSON && xhr.responseJSON.message) ||
                            'Gagal memproses', 'error');
                    }
                });
            });
        });
    </script>
@endpush
