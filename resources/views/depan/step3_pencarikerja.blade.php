    <div class="step d-none" id="step3">
        <?php
        $kabkotas = getKabkota();
        $agamas = getAgama();
        $pendidikans = getPendidikan();
        $maritals = getMarital();
        ?>
        {{-- <div class="mb-3">
            <label for="email" class="form-label">email</label>
            <input type="text" class="form-control" id="emaill" value="{{ session('email_registered') }}" disabled>
        </div> --}}
        <div class="mb-3">
            <label for="nik" class="form-label">NIK</label>
            <input type="text" class="form-control" id="nik" required>
        </div>
        <div class="mb-3">
            <label for="fullName" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="fullName" required>
        </div>
        <div class="mb-3">
            <label for="birthPlace" class="form-label">Tempat Lahir</label>
            <input type="text" class="form-control" id="birthPlace" required>
        </div>
        <div class="mb-3">
            <label for="birthDate" class="form-label">Tanggal Lahir</label>
            <input type="date" class="form-control" id="birthDate" required>
        </div>
        <div class="mb-3">
            <label for="district" class="form-label">Jenis Kelamin</label>
            <select class="form-select" id="gender" required>
                <option selected disabled>Pilih Jenis Kelamin</option>
                <option value="L">Laki - laki</option>
                <option value="P">Perempuan</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="agama" class="form-label">Agama</label>
            <select class="form-select" id="agama_id" name="agama_id" required>
                <option selected disabled>Pilih Agama</option>
                @foreach ($agamas as $ag)
                    <option value="{{ $ag->id }}">{{ $ag->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="kabkota" class="form-label">Kabupaten / Kota</label>
            <select class="form-select" id="kabkota_id" name="kabkota_id" required>
                <option selected disabled>Pilih Kabupaten/Kota</option>
                @foreach ($kabkotas as $kabkot)
                    <option value="{{ $kabkot->id }}">{{ $kabkot->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="kecamatan" class="form-label">Kecamatan</label>
            <select class="form-select" id="kecamatan_id" name="kecamatan_id" required>
                <option selected disabled>Pilih Kecamatan</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="kelurahan" class="form-label">Desa / Kelurahan</label>
            <select class="form-select" id="desa_id" name="desa_id" required>
                <option selected disabled>Pilih Desa/Kelurahan</option>
            </select>
        </div>
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
                <option selected disabled>Pilih Pendidikan</option>
                @foreach ($pendidikans as $pend)
                    <option value="{{ $pend->id }}">{{ $pend->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="jurusan" class="form-label">Jurusan</label>
            <select class="form-select" id="jurusan_id" name="jurusan_id" required>
                <option selected disabled>Pilih Jurusan</option>

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
            <label for="mediaSosial" class="form-label">Media Sosial</label>
            <input type="text" class="form-control" id="medsos" name="medsos" required>
        </div>

        {{-- <button type="button" class="btn btn-secondary w-100 mt-3" onclick="previousStep()">Back</button> --}}
        <button type="submit" class="btn btn-success w-100 mt-3">Daftar</button>
    </div>
