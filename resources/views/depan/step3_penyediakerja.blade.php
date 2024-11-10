<div class="step d-none" id="step3">
    <?php
    $sektors = getSektor();
    $provinsis = getProvinsi();
    ?>
    <form action="{{ route('akhir-daftar-akun-perush') }}" method="post">
        @csrf
        <div class="mb-3">
            <label for="perusahaanName" class="form-label">Nama Perusahaan</label>
            <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan" required>
        </div>
        <div class="mb-3">
            <label for="" class="form-label">Penyedia lowongan kerja luar negeri</label>
            <select class="form-select" id="luar_negri" name="luar_negri" required>
                <option selected disabled>Pilih</option>
                <option value="0">Tidak</option>
                <option value="1">Ya</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label for="" class="form-label">Jenis Perusahaan</label>
            <select class="form-select" id="jenis_perusahaan" name="jenis_perusahaan" required>
                <option selected disabled>Pilih Jenis</option>
                <option value="bumd">Badan Usaha Milik Daerah</option>
                <option value="bumn">Badan Usaha Milik Negara</option>
                <option value="cv">Comanditer Venotschaap</option>
                <option value="firma">Firma</option>
                <option value="instansi">Instansi</option>
                <option value="kp">Koperasi</option>
                <option value="pt">Perseroan Terbatas</option>
                <option value="pp">Perusahaan Perorangan</option>
                <option value="po">PO*</option>
                <option value="yayasan">Yayasan</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="nik" class="form-label">Nomor Induk Berusaha (NIB)</label>
            <input type="text" class="form-control" id="nib" name="nib" required>
        </div>
        <div class="mb-3">
            <label for="stsperkawinan" class="form-label">Sektor</label>
            <select class="form-select" id="sektor_id" name="sektor_id" required>
                <option selected disabled>Pilih Sektor</option>
                @foreach ($sektors as $sekt)
                    <option value="{{ $sekt->id }}">{{ $sekt->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="kabkota" class="form-label">Provinsi</label>
            <select class="form-select" id="provinsi_id" name="provinsi_id" required>
                <option selected disabled>Pilih Provinsi</option>
                @foreach ($provinsis as $prov)
                    <option value="{{ $prov->id }}">{{ $prov->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="kabkota" class="form-label">Kabupaten / Kota</label>
            <select class="form-select" id="kabkota_id" name="kabkota_id" required>
                <option selected disabled>Pilih Kabupaten/Kota</option>
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
            <label for="telpon" class="form-label">Telpon</label>
            <input type="number" class="form-control" id="telpon" name="telpon" required>
        </div>
        <div class="mb-3">
            <label for="jabatan" class="form-label">Jabatan</label>
            <input type="text" class="form-control" id="jabatan" name="jabatan" required>
        </div>
        <div class="mb-3">
            <label for="jabatan" class="form-label">Website</label>
            <input type="text" class="form-control" id="website" name="website" required>
        </div>


        {{-- <button type="button" class="btn btn-secondary w-100 mt-3" onclick="previousStep()">Back</button> --}}
        <button type="submit" class="btn btn-success w-100 mt-3">Submit</button>
    </form>
</div>
