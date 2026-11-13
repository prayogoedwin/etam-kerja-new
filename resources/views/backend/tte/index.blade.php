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
                                    <h5 class="m-b-10">{{ $judul }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="tte-alert"></div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center m-l-0 mb-3">
                                    <div class="col-sm-6">
                                        <p class="mb-0 text-muted">
                                            Antrean tanda tangan elektronik dari seluruh modul.
                                            @if ($waiting)
                                                Satuan atau massal. Passphrase diminta setiap proses, tidak tersimpan.
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-sm-6 text-end">
                                        @if ($waiting)
                                            <button type="button" id="btn-bulk-ttd" class="btn btn-primary btn-sm">TTD terpilih</button>
                                            <button type="button" id="btn-ttd-semua" class="btn btn-outline-primary btn-sm">TTD semua antrean</button>
                                        @endif
                                        <a href="{{ route('tte.log') }}" class="btn btn-outline-secondary btn-sm">Log TTE</a>
                                        <a href="{{ ($waiting ? route('tte.sudah') : route('tte.menunggu')) . ($filterModul !== '' ? '?filter_modul='.urlencode($filterModul) : '') }}"
                                            class="btn btn-secondary btn-sm">
                                            {{ $waiting ? 'Sudah ditandatangani' : 'Menunggu TTD' }}
                                        </a>
                                    </div>
                                </div>
                                <div class="row align-items-end m-l-0 mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label mb-1" for="filter_modul">Filter modul</label>
                                        <select id="filter_modul" class="form-control">
                                            <option value="">Semua modul</option>
                                            @foreach ($modulList as $modul)
                                                <option value="{{ $modul }}" @selected($filterModul === $modul)>{{ $modul }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="ttetable" class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                @if ($waiting)
                                                    <th width="40">
                                                        <input type="checkbox" id="check-all" title="Pilih semua di halaman ini">
                                                    </th>
                                                @endif
                                                <th>No</th>
                                                <th>Judul</th>
                                                <th>Modul</th>
                                                <th>Status</th>
                                                <th>Options</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-passphrase" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Passphrase TTE</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small" id="passphrase-info">Masukkan passphrase sertifikat elektronik. Tidak boleh disalin tempel.</p>
                        <label class="form-label" for="tte_passphrase">Passphrase</label>
                        <input type="password" class="form-control" id="tte_passphrase" name="tte_kunci_{{ now()->timestamp }}"
                            autocomplete="new-password" autocapitalize="off" autocorrect="off" spellcheck="false"
                            data-lpignore="true" data-1p-ignore="true" data-form-type="other" inputmode="text" readonly>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="btn-kirim-tte">Tandatangani</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-proses" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Proses TTE</h5>
                    </div>
                    <div class="modal-body">
                        <p id="proses-status" class="mb-2">Menyiapkan...</p>
                        <div class="progress mb-3">
                            <div id="proses-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%">0%</div>
                        </div>
                        <ul id="proses-hasil" class="list-unstyled mb-0 small"></ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="btn-tutup-proses" data-bs-dismiss="modal" disabled>Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            var waiting = {{ $waiting ? 'true' : 'false' }};
            var bisaTarik = {{ !empty($bisaTarik) ? 'true' : 'false' }};
            var tteUrl = '{{ route('tte.tandatangani') }}';
            var tarikUrlTpl = '{{ url('tte') }}/__ID__/tarik';
            var csrf = '{{ csrf_token() }}';
            var pendingIds = [];
            var pendingSemua = false;
            var columns = [];
            if (waiting) {
                columns.push({
                    data: 'checkbox',
                    orderable: false,
                    searchable: false,
                    width: '40px'
                });
            }
            columns.push({
                data: 'DT_RowIndex',
                orderable: false,
                searchable: false
            }, {
                data: 'judul'
            }, {
                data: 'modul',
                orderable: false,
                searchable: false
            }, {
                data: 'status_label',
                orderable: false,
                searchable: false
            }, {
                data: 'options',
                orderable: false,
                searchable: false,
                width: '180px'
            });

            var table = $('#ttetable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route($waiting ? 'tte.menunggu' : 'tte.sudah') }}',
                    data: function(d) {
                        d.filter_modul = $('#filter_modul').val();
                    }
                },
                autoWidth: false,
                columns: columns,
                drawCallback: function() {
                    $('#check-all').prop('checked', false);
                }
            });

            $('#filter_modul').on('change', function() {
                table.ajax.reload();
            });

            $('#check-all').on('change', function() {
                $('.tte-check').prop('checked', this.checked);
            });

            $('#btn-bulk-ttd').on('click', function() {
                var ids = $('.tte-check:checked').map(function() {
                    return this.value;
                }).get();
                if (!ids.length) {
                    tampilkanAlert('Pilih minimal satu dokumen, atau gunakan TTD semua antrean', 'danger');
                    return;
                }
                bukaPassphrase(ids, false);
            });

            $('#btn-ttd-semua').on('click', function() {
                bukaPassphrase([], true);
            });

            $(document).on('click', '.btn-ttd-satu', function() {
                bukaPassphrase([$(this).data('id')], false);
            });

            $(document).on('click', '.btn-tarik', function() {
                if (!bisaTarik) {
                    return;
                }
                var id = $(this).data('id');
                var kode = $(this).data('kode') || '-';
                if (!confirm('Tarik berkas bertanda tangan dari DS?\nKode: ' + kode)) {
                    return;
                }
                $.ajax({
                    type: 'POST',
                    url: tarikUrlTpl.replace('__ID__', id),
                    data: { _token: csrf },
                    success: function(res) {
                        tampilkanAlert(res.message || 'Berkas berhasil ditarik', 'success');
                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        var res = xhr.responseJSON || {};
                        tampilkanAlert(res.message || 'Gagal tarik berkas dari DS', 'danger');
                    }
                });
            });

            var $pass = $('#tte_passphrase');
            $pass.on('focus', function() {
                $pass.removeAttr('readonly');
            });
            $pass.on('paste copy cut drop contextmenu', function(e) {
                e.preventDefault();
                return false;
            });
            $pass.on('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && ['v', 'c', 'x'].indexOf(String(e.key).toLowerCase()) !== -1) {
                    e.preventDefault();
                    return false;
                }
            });

            $('#btn-kirim-tte').on('click', function() {
                var passphrase = $pass.val();
                if (!passphrase) {
                    tampilkanAlert('Passphrase wajib diisi', 'danger');
                    return;
                }
                $('#modal-passphrase').modal('hide');
                jalankanTte(passphrase);
            });

            function bukaPassphrase(ids, pilihSemua) {
                pendingIds = ids;
                pendingSemua = pilihSemua;
                $pass.val('').attr('name', 'tte_kunci_' + Date.now());
                $('#passphrase-info').text(pilihSemua
                    ? 'Tandatangani semua dokumen dalam antrean. Passphrase tidak disimpan.'
                    : 'Tandatangani ' + ids.length + ' dokumen. Passphrase tidak disimpan.');
                $('#modal-passphrase').modal('show');
                setTimeout(function() { $pass.trigger('focus'); }, 300);
            }

            function jalankanTte(passphrase) {
                $('#proses-hasil').empty();
                $('#proses-bar').addClass('progress-bar-animated').css('width', '8%').text('...');
                $('#proses-status').text('Menghubungi DS...');
                $('#btn-tutup-proses').prop('disabled', true);
                $('#modal-proses').modal('show');

                if (pendingSemua) {
                    kirimBatch(passphrase, [], true, tampilkanHasil);
                    return;
                }

                var ids = pendingIds.slice();
                var ringkas = { berhasil: 0, gagal: 0, items: [], kode: null, message: '' };
                var i = 0;

                function lanjut() {
                    if (i >= ids.length) {
                        ringkas.message = ringkas.berhasil + ' dokumen berhasil ditandatangani' +
                            (ringkas.gagal > 0 ? ', ' + ringkas.gagal + ' gagal' : '');
                        tampilkanHasil(ringkas, true);
                        return;
                    }
                    var pct = Math.round((i / ids.length) * 90);
                    $('#proses-bar').css('width', Math.max(8, pct) + '%').text((i + 1) + '/' + ids.length);
                    $('#proses-status').text('Memproses dokumen ' + (i + 1) + ' dari ' + ids.length + '...');
                    kirimBatch(passphrase, [ids[i]], false, function(res) {
                        (res.items || []).forEach(function(item) {
                            ringkas.items.push(item);
                            if (item.ok) {
                                ringkas.berhasil++;
                            } else {
                                ringkas.gagal++;
                            }
                            tampilkanItem(item);
                        });
                        if (res.kode) {
                            ringkas.kode = ringkas.kode || res.kode;
                        }
                        if (res.kode === 'passphrase_salah' || res.kode === 'koneksi_putus') {
                            tampilkanHasil($.extend({}, res, {
                                items: ringkas.items,
                                berhasil: ringkas.berhasil,
                                gagal: ringkas.gagal
                            }), true);
                            return;
                        }
                        i++;
                        lanjut();
                    });
                }
                lanjut();
            }

            function kirimBatch(passphrase, ids, pilihSemua, done) {
                $.ajax({
                    type: 'POST',
                    url: tteUrl,
                    data: {
                        _token: csrf,
                        passphrase: passphrase,
                        pilih_semua: pilihSemua ? 1 : 0,
                        filter_modul: $('#filter_modul').val(),
                        tte_ids: ids
                    },
                    success: function(res) {
                        done(res);
                    },
                    error: function(xhr) {
                        var res = xhr.responseJSON || {};
                        if (!res.items) {
                            res = {
                                berhasil: 0,
                                gagal: ids.length || 1,
                                kode: res.kode || null,
                                message: res.message || 'Proses TTE gagal',
                                items: []
                            };
                        }
                        done(res);
                    }
                });
            }

            function tampilkanItem(item) {
                var li = $('<li class="mb-1">');
                li.append($('<span class="badge me-1">').addClass(item.ok ? 'bg-success' : 'bg-danger').text(item.ok ? 'Berhasil' : 'Gagal'));
                li.append(document.createTextNode(' ' + item.judul + ' — ' + item.pesan));
                $('#proses-hasil').append(li);
            }

            function tampilkanHasil(res, itemSudahDitulis) {
                var items = res.items || [];
                $('#proses-bar').removeClass('progress-bar-animated').css('width', '100%').text('100%');
                if (res.kode === 'passphrase_salah') {
                    $('#proses-status').text('Passphrase salah');
                    tampilkanAlert('Passphrase salah', 'danger');
                } else if (res.kode === 'koneksi_putus') {
                    $('#proses-status').text('Koneksi ke server DS terputus');
                    tampilkanAlert('Koneksi ke server DS terputus', 'danger');
                } else if (res.kode === 'nik_kosong') {
                    $('#proses-status').text('NIK penandatangan belum diisi');
                    tampilkanAlert(res.message || 'NIK penandatangan belum diisi', 'danger');
                } else {
                    $('#proses-status').text(res.message || 'Selesai');
                    tampilkanAlert(res.message || 'Selesai', res.gagal > 0 ? 'warning' : 'success');
                }
                if (!itemSudahDitulis) {
                    if (!items.length) {
                        $('#proses-hasil').append($('<li>').text(res.message || 'Tidak ada dokumen diproses'));
                    }
                    items.forEach(tampilkanItem);
                }
                $('#btn-tutup-proses').prop('disabled', false);
                table.ajax.reload(null, false);
                $pass.val('');
            }

            function tampilkanAlert(pesan, tipe) {
                var html = '<div class="alert alert-' + tipe + ' alert-dismissible fade show" role="alert">' +
                    pesan +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                $('#tte-alert').html(html);
            }
        });
    </script>
@endpush
