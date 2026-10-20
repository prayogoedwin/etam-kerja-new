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
                                    <h5 class="m-b-10">CBT - {{ $pelatihan->nama_pelatihan }}</h5>
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
                @endphp

                <div class="row justify-content-center">
                    <div class="col-xl-8">
                        <div class="card mb-3" style="border-top: 8px solid #1e88e5;">
                            <div class="card-body">
                                <h4 class="mb-1">{{ $form->nama ?? 'CBT' }}</h4>
                                <p class="text-muted mb-1">Peserta: {{ $peserta->name ?? '-' }}</p>
                                @if ($form && $form->deskripsi)
                                    <p class="mb-0">{{ $form->deskripsi }}</p>
                                @endif
                            </div>
                        </div>

                        @if (!empty($score))
                            <div class="card mb-3 border-success">
                                <div class="card-body">
                                    <h5 class="mb-3">Hasil tes</h5>
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="h3 mb-0 text-success">{{ $score['benar'] }}</div>
                                            <small>Benar</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="h3 mb-0 text-danger">{{ $score['salah'] }}</div>
                                            <small>Salah</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="h3 mb-0 text-secondary">{{ $score['kosong'] }}</div>
                                            <small>Tidak diisi</small>
                                        </div>
                                    </div>
                                    <p class="text-center mt-3 mb-0">Nilai: <strong>{{ $score['benar'] }}</strong> dari
                                        {{ $score['total'] }} soal</p>
                                </div>
                            </div>
                        @endif

                        @if (!empty($closed))
                            <div class="alert alert-warning">CBT belum dibuka atau sudah ditutup. Hubungi petugas BLK.</div>
                            <a href="{{ route('blk.pelatihan.daftar', $pelatihan->id) }}"
                                class="btn btn-secondary">Kembali</a>
                        @elseif (!empty($needKode))
                            <form action="{{ route('blk.pelatihan.cbt.kode', $pelatihan->id) }}" method="POST">
                                @csrf
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <label class="form-label">Masukkan kode tes 4 digit</label>
                                        <input type="text" name="cbt_kode" class="form-control" maxlength="4"
                                            inputmode="numeric" pattern="[0-9]{4}" required
                                            value="{{ old('cbt_kode') }}" placeholder="0000">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Mulai tes</button>
                                <a href="{{ route('blk.pelatihan.daftar', $pelatihan->id) }}"
                                    class="btn btn-secondary">Kembali</a>
                            </form>
                        @else
                            <form action="{{ $action }}" method="POST">
                                @csrf
                                @foreach ($rows as $row)
                                    @php
                                        $pilihan = $answerService->pilihanFromSnapshot($row);
                                        $oldAnswer = old('jawaban.' . $row->id, $row->jawaban);
                                        $letters = range('A', 'F');
                                    @endphp
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <label class="form-label fw-bold">{{ $loop->iteration }}.
                                                {{ $row->pertanyaan }}</label>
                                            @foreach ($pilihan as $opsiIndex => $opsi)
                                                @php
                                                    $isCorrect = $readonly && (string) $row->kunci === (string) $opsi;
                                                    $isPicked = (string) $oldAnswer === (string) $opsi;
                                                @endphp
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio"
                                                        name="jawaban[{{ $row->id }}]" value="{{ $opsi }}"
                                                        @checked($isPicked) @disabled($readonly)>
                                                    <label
                                                        class="form-check-label @if ($readonly && $isCorrect) text-success fw-bold @elseif ($readonly && $isPicked && !$isCorrect) text-danger @endif">
                                                        {{ $letters[$opsiIndex] ?? '' }}. {{ $opsi }}
                                                        @if ($readonly && $isCorrect)
                                                            (kunci)
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                            @if ($readonly && empty($oldAnswer))
                                                <small class="text-muted">Tidak diisi</small>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                <div class="mb-4">
                                    @if (! $readonly)
                                        <button type="submit" class="btn btn-primary">Kirim jawaban</button>
                                    @endif
                                    <a href="{{ route('blk.pelatihan.daftar', $pelatihan->id) }}"
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
