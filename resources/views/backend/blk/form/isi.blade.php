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
                                    <h5 class="m-b-10">
                                        {{ $jenis === 'pretest' ? 'Pretest' : 'Wawancara' }} -
                                        {{ $pelatihan->nama_pelatihan }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @php
                    $answerService = app(\App\Services\Blk\BlkFormAnswerService::class);
                    $backUrl = auth()->user()->roles[0]['name'] ?? '';
                    $isPeserta = in_array($backUrl, ['pencari-kerja', 'penyedia-kerja'], true);
                @endphp

                <div class="row justify-content-center">
                    <div class="col-xl-8">
                        <div class="card mb-3" style="border-top: 8px solid #673ab7;">
                            <div class="card-body">
                                <h4 class="mb-1">{{ $form->nama ?? ($jenis === 'pretest' ? 'Pretest' : 'Wawancara') }}</h4>
                                <p class="text-muted mb-1">Peserta: {{ $peserta->name ?? '-' }}</p>
                                @if ($form && $form->deskripsi)
                                    <p class="mb-0">{{ $form->deskripsi }}</p>
                                @endif
                                @if ($submitted)
                                    <div class="alert alert-info mt-3 mb-0">Form sudah dikirim.</div>
                                @elseif ($readonly && $jenis === 'pretest')
                                    <div class="alert alert-warning mt-3 mb-0">Peserta belum mengisi pretest.</div>
                                @endif
                            </div>
                        </div>

                        @if ($rows->isEmpty() && $readonly)
                            <a href="{{ $isPeserta ? route('blk.pelatihan.daftar', $pelatihan->id) : route('blk.pelatihan.peserta', $pelatihan->id) }}"
                                class="btn btn-secondary">Kembali</a>
                        @else
                            <form action="{{ $action }}" method="POST">
                                @csrf
                                @foreach ($rows as $row)
                                    @php
                                        $pilihan = $answerService->pilihanFromSnapshot($row);
                                        $checkboxValues = $answerService->decodedCheckbox($row);
                                        $oldAnswer = old('jawaban.' . $row->id, $row->jawaban);
                                    @endphp
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <label class="form-label fw-bold">
                                                {{ $row->pertanyaan }}
                                                @if ((int) $row->wajib === 1)
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>

                                            @if ((int) $row->jenis_pertanyaan === 1)
                                                <input type="text" class="form-control"
                                                    name="jawaban[{{ $row->id }}]"
                                                    value="{{ is_array($oldAnswer) ? implode(', ', $oldAnswer) : $oldAnswer }}"
                                                    @disabled($readonly)>
                                            @elseif ((int) $row->jenis_pertanyaan === 2)
                                                <textarea class="form-control" rows="4" name="jawaban[{{ $row->id }}]"
                                                    @disabled($readonly)>{{ is_array($oldAnswer) ? implode("\n", $oldAnswer) : $oldAnswer }}</textarea>
                                            @elseif ((int) $row->jenis_pertanyaan === 3)
                                                @foreach ($pilihan as $opsi)
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio"
                                                            name="jawaban[{{ $row->id }}]"
                                                            value="{{ $opsi }}"
                                                            @checked((string) $oldAnswer === (string) $opsi)
                                                            @disabled($readonly)>
                                                        <label class="form-check-label">{{ $opsi }}</label>
                                                    </div>
                                                @endforeach
                                            @else
                                                @php
                                                    $selected = is_array(old('jawaban.' . $row->id))
                                                        ? old('jawaban.' . $row->id)
                                                        : $checkboxValues;
                                                @endphp
                                                @foreach ($pilihan as $opsi)
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="jawaban[{{ $row->id }}][]"
                                                            value="{{ $opsi }}"
                                                            @checked(in_array($opsi, (array) $selected, true))
                                                            @disabled($readonly)>
                                                        <label class="form-check-label">{{ $opsi }}</label>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                <div class="mb-4">
                                    @if (! $readonly && $action)
                                        <button type="submit" name="submit" value="0" class="btn btn-outline-primary">Simpan
                                            Draft</button>
                                        <button type="submit" name="submit" value="1" class="btn btn-primary">Kirim</button>
                                    @endif
                                    <a href="{{ $isPeserta ? route('blk.pelatihan.daftar', $pelatihan->id) : route('blk.pelatihan.peserta', $pelatihan->id) }}"
                                        class="btn btn-secondary">Kembali</a>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </body>
@endsection
