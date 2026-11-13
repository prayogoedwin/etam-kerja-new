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
                                    <h5 class="m-b-10">Pengajuan Suket Layak K3 oleh PJK3</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="alert alert-info">
                                    Minta <b>kode Suket K3</b> dari perusahaan yang diperiksa (menu Kode Suket K3, berlaku 30 hari).
                                    Cari kode itu di sini; nama perusahaan baru muncul setelah kode cocok.
                                </div>
                                <form id="formSuket" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label fw-bold">Kode perusahaan yang diperiksa <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" name="kode" id="kode_suket"
                                                    class="form-control text-uppercase" maxlength="20"
                                                    placeholder="Contoh: SK3-AB12CD" autocomplete="off" required>
                                                <button type="button" class="btn btn-outline-primary"
                                                    id="btnCariPerusahaan">Cari</button>
                                            </div>
                                            <small class="text-muted">Kode tidak menampilkan daftar perusahaan. Hanya yang
                                                punya kode aktif yang bisa dipilih.</small>
                                            <div id="hasilPerusahaan" class="mt-2" style="display:none"></div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Kategori K3 <span
                                                    class="text-danger">*</span></label>
                                            <select name="kategori_id" id="kategori_id" class="form-control" required>
                                                <option value="">Pilih kategori</option>
                                                @foreach ($kategoriList as $kat)
                                                    <option value="{{ $kat->id }}">{{ $kat->nama }}
                                                        @if ($kat->kode)
                                                            ({{ $kat->kode }})
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <h6 class="mt-2">Alat / objek yang diuji</h6>
                                    <p class="small text-muted">Satu baris per alat. Bisa ditambah sesuai LHPP. Pengawas
                                        akan melihat daftar ini dan tetap bisa ubah, tambah, atau kurangi.</p>
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
                                                <tr class="js-perangkat" data-index="0">
                                                    <td class="js-nomor align-middle">1</td>
                                                    <td>
                                                        <select name="perangkat[0][jenis_id]"
                                                            class="form-control form-control-sm js-jenis-perangkat">
                                                            <option value="">Pilih jenis</option>
                                                            @foreach ($jenisList as $jenis)
                                                                <option value="{{ $jenis->id }}"
                                                                    data-kategori="{{ $jenis->kategori_id }}"
                                                                    data-nama="{{ $jenis->nama }}">
                                                                    {{ $jenis->nama }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="text" name="perangkat[0][nama]"
                                                            class="form-control form-control-sm js-nama-perangkat"
                                                            placeholder="Contoh: Panel Control"></td>
                                                    <td><input type="text" name="perangkat[0][merk]"
                                                            class="form-control form-control-sm" placeholder="Merk"></td>
                                                    <td><input type="text" name="perangkat[0][jumlah]"
                                                            class="form-control form-control-sm"
                                                            placeholder="1 Unit"></td>
                                                    <td><input type="text" name="perangkat[0][lokasi]"
                                                            class="form-control form-control-sm"
                                                            placeholder="Control Room"></td>
                                                    <td class="text-center"><button type="button"
                                                            class="btn btn-outline-danger btn-sm js-hapus-perangkat">&times;</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm mb-3"
                                        id="btnTambahPerangkat">Tambah baris alat</button>

                                    @foreach ($dokumenKeys as $key => $label)
                                        <div class="border rounded p-3 mb-3">
                                            <label class="form-label fw-bold">{{ $label }} <span
                                                    class="text-danger">*</span></label>
                                            <div class="mb-2">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input js-tipe" type="radio"
                                                        name="tipe_{{ $key }}" id="tipe_file_{{ $key }}"
                                                        value="file" data-key="{{ $key }}" checked>
                                                    <label class="form-check-label"
                                                        for="tipe_file_{{ $key }}">Upload file</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input js-tipe" type="radio"
                                                        name="tipe_{{ $key }}" id="tipe_link_{{ $key }}"
                                                        value="link" data-key="{{ $key }}">
                                                    <label class="form-check-label"
                                                        for="tipe_link_{{ $key }}">Kirim tautan</label>
                                                </div>
                                            </div>
                                            <div class="js-file-wrap" data-key="{{ $key }}">
                                                <input type="file" class="form-control" name="file_{{ $key }}"
                                                    accept=".pdf,.jpg,.jpeg,.png">
                                                <small class="text-muted">PDF / JPG / PNG, maks 2 MB.</small>
                                            </div>
                                            <div class="js-link-wrap" data-key="{{ $key }}" style="display:none">
                                                <input type="url" class="form-control" name="link_{{ $key }}"
                                                    placeholder="https://drive.google.com/...">
                                            </div>
                                        </div>
                                    @endforeach

                                    <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                                    <a href="{{ route('pengawasan.suket.penyedia.pengajuan.index') }}"
                                        class="btn btn-secondary">Batal</a>
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
            function tampilPerusahaan(data) {
                var nib = data.nib ? ' (NIB ' + data.nib + ')' : '';
                $('#hasilPerusahaan').html(
                    '<div class="alert alert-success mb-0">Perusahaan ditemukan: <strong>' +
                    $('<div>').text(data.name).html() + nib + '</strong></div>'
                ).show();
            }

            $('#btnCariPerusahaan').on('click', function() {
                var kode = $.trim($('#kode_suket').val());
                if (!kode) {
                    Swal.fire('Kode kosong', 'Masukkan kode dari perusahaan yang diperiksa.', 'warning');
                    return;
                }
                $.ajax({
                    url: '{{ route('pengawasan.suket.penyedia.pengajuan.cari') }}',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}', kode: kode },
                    success: function(response) {
                        if (response.success) {
                            tampilPerusahaan(response.perusahaan);
                        } else {
                            $('#hasilPerusahaan').hide().empty();
                            Swal.fire('Tidak ditemukan', response.message || 'Kode tidak valid.', 'warning');
                        }
                    },
                    error: function(xhr) {
                        $('#hasilPerusahaan').hide().empty();
                        Swal.fire('Gagal', (xhr.responseJSON && xhr.responseJSON.message) ||
                            'Gagal mencari perusahaan', 'error');
                    }
                });
            });

            $('#kode_suket').on('input', function() {
                $('#hasilPerusahaan').hide().empty();
            });

            $('.js-tipe').on('change', function() {
                var key = $(this).data('key');
                var tipe = $(this).val();
                $('.js-file-wrap[data-key="' + key + '"]').toggle(tipe === 'file');
                $('.js-link-wrap[data-key="' + key + '"]').toggle(tipe === 'link');
            });

            var jenisOptionsHtml = $('#daftarPerangkat .js-jenis-perangkat').first().html() ||
                '<option value="">Pilih jenis</option>';

            function filterJenisByKategori() {
                var kategoriId = String($('#kategori_id').val() || '');
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

            filterJenisByKategori();
            $('#kategori_id').on('change', filterJenisByKategori);

            $(document).on('change', '.js-jenis-perangkat', function() {
                var nama = $(this).find('option:selected').data('nama') || '';
                var $nama = $(this).closest('.js-perangkat').find('.js-nama-perangkat');
                if (!$nama.val() || $nama.data('auto') === $nama.val()) {
                    $nama.val(nama);
                    $nama.data('auto', nama);
                }
            });

            $(document).on('click', '.js-hapus-perangkat', function() {
                if ($('#daftarPerangkat .js-perangkat').length > 1) {
                    $(this).closest('.js-perangkat').remove();
                    nomorUlang();
                } else {
                    $(this).closest('.js-perangkat').find('input, select').val('');
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

            $('#formSuket').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                Swal.fire({
                    title: 'Konfirmasi pengajuan',
                    text: 'Kirim pengajuan Suket Layak K3 beserta dokumen yang diunggah?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, kirim',
                    cancelButtonText: 'Periksa kembali'
                }).then(function(result) {
                    if (!result.isConfirmed) {
                        return;
                    }
                    $.ajax({
                        url: '{{ route('pengawasan.suket.penyedia.pengajuan.store') }}',
                        type: 'POST',
                        data: new FormData(form),
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Berhasil', response.message, 'success').then(
                                    function() {
                                        window.location.href = response.redirect;
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
                                Swal.fire('Gagal', response.message || 'Gagal menyimpan',
                                    'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal', (xhr.responseJSON && xhr.responseJSON
                                .message) || 'Gagal menyimpan', 'error');
                        }
                    });
                });
            });
        });
    </script>
@endpush
