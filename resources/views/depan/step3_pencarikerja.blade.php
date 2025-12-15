    <div class="step d-none" id="step3">
        <?php
        $kabkotas = getKabkota();
        $agamas = getAgama();
        $pendidikans = getPendidikan();
        $maritals = getMarital();
        $disabilitases = getJenisDisabilitas();
        ?>
        {{-- <div class="mb-3">
            <label for="email" class="form-label">email</label>
            <input type="text" class="form-control" id="emaill" value="{{ session('email_registered') }}" disabled>
        </div> --}}
        <form action="{{ route('akhir-daftar-akun') }}" method="post">
            @csrf
            <input type="hidden" name="bkk" id="bkk" value="{{ request('bkk') }}">

             {{-- KTP Upload Section --}}
            <div class="mb-4 p-3 border rounded bg-light" id="ktp-upload-area">
                <label class="form-label fw-bold">
                    <i class="bi bi-camera"></i> Upload / Foto KTP (Opsional)
                </label>
                <p class="text-muted small mb-2">
                    Upload foto KTP untuk mengisi data secara otomatis. Foto tidak akan disimpan ke server.
                </p>
                
                {{-- Pilihan: Upload atau Kamera --}}
                <div class="d-flex gap-2 mb-3">
                    <label class="btn btn-outline-primary flex-fill" for="ktp-upload">
                        <i class="bi bi-image"></i> Pilih File
                    </label>
                    <button type="button" class="btn btn-outline-success flex-fill" id="btn-open-camera">
                        <i class="bi bi-camera-fill"></i> Buka Kamera
                    </button>
                </div>
                
                <input type="file" class="d-none" id="ktp-upload" accept="image/*">
                
                {{-- Preview --}}
                <div id="ktp-preview-wrapper" class="mt-3 text-center" style="display: none;">
                    <img id="ktp-preview" src="" alt="Preview KTP" class="img-fluid rounded" style="max-height: 200px;">
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-reset-ktp">
                            <i class="bi bi-arrow-clockwise"></i> Foto Ulang
                        </button>
                    </div>
                </div>
                
                {{-- Progress Bar --}}
                <div id="ocr-progress-wrapper" class="mt-3" style="display: none;">
                    <p id="ocr-progress-text" class="small mb-1">Memproses: 0%</p>
                    <div class="progress" style="height: 20px;">
                        <div id="ocr-progress-bar" 
                            class="progress-bar progress-bar-striped progress-bar-animated" 
                            role="progressbar" 
                            style="width: 0%;" 
                            aria-valuenow="0" 
                            aria-valuemin="0" 
                            aria-valuemax="100">
                        </div>
                    </div>
                </div>
            </div>
        <hr class="my-4">

            
            <div id="wrap_ket_disabilitas" class="mb-3 d-none">
                <label for="" class="form-label">Keterangan Disabilitas</label>
                <input type="text" class="form-control" id="keterangan_disabilitas" name="keterangan_disabilitas">
            </div>
            <div class="mb-3">
                <label for="nik" class="form-label">NIK</label>
                <input type="text" class="form-control" id="nik" name="nik" required>
            </div>
            <div class="mb-3">
                <label for="fullName" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
            </div>
            <div class="mb-3">
                <label for="birthPlace" class="form-label">Tempat Lahir</label>
                <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" required>
            </div>
            <div class="mb-3">
                <label for="birthDate" class="form-label">Tanggal Lahir</label>
                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
            </div>
            <div class="mb-3">
                <label for="district" class="form-label">Jenis Kelamin</label>
                <select class="form-select" id="gender_id" name="gender_id" required>
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="L">Laki - laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="agama" class="form-label">Agama</label>
                <select class="form-select" id="agama_id" name="agama_id" required>
                    <option value="">Pilih Agama</option>
                    @foreach ($agamas as $ag)
                        <option value="{{ $ag->id }}">{{ $ag->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="kabkota" class="form-label">Kabupaten / Kota</label>
                <select class="form-select" id="kabkota_id" name="kabkota_id" required>
                    <option value="">Pilih Kabupaten/Kota</option>
                    @foreach ($kabkotas as $kabkot)
                        <option value="{{ $kabkot->id }}">{{ $kabkot->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="kecamatan" class="form-label">Kecamatan</label>
                <select class="form-select" id="kecamatan_id" name="kecamatan_id" required>
                    <option value="">Pilih Kecamatan</option>
                </select>
            </div>
            {{-- <div class="mb-3">
                <label for="kelurahan" class="form-label">Desa / Kelurahan</label>
                <select class="form-select" id="desa_id" name="desa_id" required>
                    <option selected disabled>Pilih Desa/Kelurahan</option>
                </select>
            </div> --}}
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat Lengkap</label>
                <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="kodepos" class="form-label">Kode Pos</label>
                <input type="text" class="form-control" id="kodepos" name="kodepos" required>
            </div>
            <div class="mb-3">
                <label for="pendidikan" class="form-label">Pendidikan</label>
                <select class="form-select" id="pendidikan_id" name="pendidikan_id" required>
                    <option value="">Pilih Pendidikan</option>
                    @foreach ($pendidikans as $pend)
                        <option value="{{ $pend->id }}">{{ $pend->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="jurusan" class="form-label">Jurusan</label>
                <select class="form-select" id="jurusan_id" name="jurusan_id" style="width: 100%" required>
                    <option value="">Pilih Jurusan</option>

                </select>
            </div>
            <div class="mb-3">
                <label for="tahunLulus" class="form-label">Tahun Lulus</label>
                <input type="number" class="form-control" id="tahun_lulus" name="tahun_lulus" required>
            </div>
            <div class="mb-3">
                <label for="stsperkawinan" class="form-label">Status Perkawinan</label>
                <select class="form-select" id="status_perkawinan_id" name="status_perkawinan_id" required>
                    <option selected disabled>Pilih Status</option>
                    @foreach ($maritals as $marit)
                        <option value="{{ $marit->id }}">{{ $marit->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="district" class="form-label">Disabilitas</label>
                <select class="form-select" id="disabilitas" name="disabilitas" required>
                    <option value="">Pilih Disabilitas</option>
                    <option value="0">Tidak</option>
                    <option value="1">Ya</option>
                </select>
            </div>
            <div id="drop_jenisdisabilitas" class="mb-3 d-none">
                <label for="" class="form-label">Jenis Disabilitas</label>
                <select class="form-select" id="jenis_disabilitas" name="jenis_disabilitas">
                    <option value="">Jenis Disabilitas</option>
                    @foreach ($disabilitases as $dis)
                        <option value="{{ $dis->id }}">{{ $dis->nama_disabilitas }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="jabhar" class="form-label">Jabatan Harapan</label>
                <select class="form-select" id="jabatan_harapan_id" name="jabatan_harapan_id" style="width: 100%"
                    required>
                    <option value="">Pilih Jabatan Harapan</option>
                    @foreach ($jabatans as $jab)
                        <option value="{{ $jab->id }}">{{ $jab->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="mediaSosial" class="form-label">Media Sosial</label>
                <input type="text" class="form-control" id="medsos" name="medsos" required>
            </div>

            {{-- <button type="button" class="btn btn-secondary w-100 mt-3" onclick="previousStep()">Back</button> --}}
            <button type="submit" class="btn btn-success w-100 mt-3">Daftar</button>
        </form>
    </div>
