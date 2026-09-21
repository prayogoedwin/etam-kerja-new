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
                                    <h5 class="m-b-10">{{ $form->id ? 'Edit' : 'Buat' }} Template {{ $judul }}</h5>
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

                @php
                    $oldPertanyaan = old('pertanyaan');
                    if (is_array($oldPertanyaan) && count($oldPertanyaan)) {
                        $builderItems = array_values($oldPertanyaan);
                    } else {
                        $builderItems = $pertanyaan
                            ->map(function ($item) {
                                return [
                                    'teks' => $item->pertanyaan,
                                    'jenis_pertanyaan' => $item->jenis_pertanyaan,
                                    'wajib' => (int) $item->wajib,
                                    'pilihan' => $item->daftarPilihan(),
                                ];
                            })
                            ->values()
                            ->all();
                    }
                @endphp

                <form
                    action="{{ $form->id ? route('blk.form.update', [$jenis, $form->id]) : route('blk.form.store', $jenis) }}"
                    method="POST" id="form-builder">
                    @csrf
                    @if ($form->id)
                        @method('PUT')
                    @endif

                    <div class="row justify-content-center">
                        <div class="col-xl-8">
                            <div class="card mb-3" style="border-top: 8px solid #673ab7;">
                                <div class="card-body">
                                    @if ($blkOptions->isEmpty())
                                        <div class="alert alert-warning">BLK untuk balai Anda belum terdaftar.</div>
                                    @endif
                                    <div class="mb-3">
                                        <label class="form-label">BLK</label>
                                        <select name="blk_id" class="form-control" required
                                            @disabled(($isBlkStaff ?? false) && $blkOptions->count() === 1)>
                                            @if ($blkOptions->count() !== 1)
                                                <option value="">-- Pilih BLK --</option>
                                            @endif
                                            @foreach ($blkOptions as $blk)
                                                <option value="{{ $blk->id }}" @selected(old('blk_id', $form->blk_id) == $blk->id)>
                                                    {{ $blk->nama_lembaga }}</option>
                                            @endforeach
                                        </select>
                                        @if (($isBlkStaff ?? false) && $blkOptions->count() === 1)
                                            <input type="hidden" name="blk_id" value="{{ $blkOptions->first()->id }}">
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Judul formulir</label>
                                        <input type="text" name="nama" class="form-control form-control-lg"
                                            placeholder="Form tanpa judul"
                                            value="{{ old('nama', $form->nama) }}" required>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label">Deskripsi</label>
                                        <textarea name="deskripsi" class="form-control" rows="2"
                                            placeholder="Deskripsi formulir">{{ old('deskripsi', $form->deskripsi) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div id="question-list"></div>

                            <button type="button" class="btn btn-light border mb-3" id="add-question">
                                <i class="feather icon-plus"></i> Tambah pertanyaan
                            </button>

                            <div class="mb-4">
                                <button type="submit" class="btn btn-primary">Simpan Template</button>
                                <a href="{{ route('blk.form.index', $jenis) }}" class="btn btn-secondary">Kembali</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </body>
@endsection

@push('js')
    <script>
        (function() {
            const jenisLabels = @json(\App\Models\BLK\EtamBlkFormPertanyaan::jenisPertanyaanLabels());
            const existing = @json($builderItems);
            let index = 0;

            function pilihanHtml(i, pilihan) {
                const rows = (pilihan && pilihan.length ? pilihan : ['']).map(function(text) {
                    return pilihanRow(i, text);
                }).join('');
                return '<div class="pilihan-wrap mt-2">' + rows +
                    '<button type="button" class="btn btn-link btn-sm px-0 add-pilihan">Tambah opsi</button></div>';
            }

            function pilihanRow(i, text) {
                return '<div class="input-group mb-2 pilihan-row">' +
                    '<span class="input-group-text">○</span>' +
                    '<input type="text" name="pertanyaan[' + i + '][pilihan][]" class="form-control" value="' +
                    escapeHtml(text || '') + '" placeholder="Opsi">' +
                    '<button type="button" class="btn btn-outline-danger remove-pilihan">Hapus</button>' +
                    '</div>';
            }

            function questionCard(data) {
                const i = index++;
                const jenis = String(data.jenis_pertanyaan || 1);
                const wajib = data.wajib ? 'checked' : '';
                const showPilihan = jenis === '3' || jenis === '4';
                let options = '';
                Object.keys(jenisLabels).forEach(function(key) {
                    options += '<option value="' + key + '"' + (jenis === String(key) ? ' selected' : '') + '>' +
                        jenisLabels[key] + '</option>';
                });

                return '<div class="card mb-3 question-card" data-index="' + i + '">' +
                    '<div class="card-body">' +
                    '<div class="row">' +
                    '<div class="col-md-8 mb-2">' +
                    '<input type="text" name="pertanyaan[' + i + '][teks]" class="form-control" required placeholder="Pertanyaan" value="' +
                    escapeHtml(data.teks || '') + '">' +
                    '</div>' +
                    '<div class="col-md-4 mb-2">' +
                    '<select name="pertanyaan[' + i + '][jenis_pertanyaan]" class="form-control jenis-pertanyaan">' +
                    options + '</select>' +
                    '</div>' +
                    '</div>' +
                    '<div class="pilihan-holder"' + (showPilihan ? '' : ' style="display:none"') + '>' +
                    pilihanHtml(i, data.pilihan || []) +
                    '</div>' +
                    '<div class="d-flex justify-content-between align-items-center mt-2">' +
                    '<label class="mb-0"><input type="checkbox" name="pertanyaan[' + i + '][wajib]" value="1" ' +
                    wajib + '> Wajib diisi</label>' +
                    '<button type="button" class="btn btn-outline-danger btn-sm remove-question">Hapus</button>' +
                    '</div>' +
                    '</div></div>';
            }

            function escapeHtml(text) {
                return String(text || '').replace(/[&<>"']/g, function(m) {
                    return ({
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#39;'
                    })[m];
                });
            }

            function togglePilihan($card) {
                const jenis = String($card.find('.jenis-pertanyaan').val());
                $card.find('.pilihan-holder').toggle(jenis === '3' || jenis === '4');
            }

            $('#add-question').on('click', function() {
                $('#question-list').append(questionCard({
                    teks: '',
                    jenis_pertanyaan: 1,
                    wajib: 0,
                    pilihan: ['']
                }));
            });

            $(document).on('change', '.jenis-pertanyaan', function() {
                togglePilihan($(this).closest('.question-card'));
            });
            $(document).on('click', '.remove-question', function() {
                $(this).closest('.question-card').remove();
            });
            $(document).on('click', '.add-pilihan', function() {
                const $card = $(this).closest('.question-card');
                const i = $card.data('index');
                $(this).before(pilihanRow(i, ''));
            });
            $(document).on('click', '.remove-pilihan', function() {
                const $wrap = $(this).closest('.pilihan-wrap');
                $(this).closest('.pilihan-row').remove();
                if ($wrap.find('.pilihan-row').length === 0) {
                    $wrap.find('.add-pilihan').before(pilihanRow($wrap.closest('.question-card').data('index'), ''));
                }
            });

            if (existing.length) {
                existing.forEach(function(item) {
                    $('#question-list').append(questionCard(item));
                });
            } else {
                $('#add-question').trigger('click');
            }
        })();
    </script>
@endpush
