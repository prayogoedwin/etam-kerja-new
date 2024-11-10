<div class="step d-none" id="step3">
    <div class="mb-3">
        <label for="fullName" class="form-label">Nama Perusahaan</label>
        <input type="text" class="form-control" id="fullName" required>
    </div>
    <div class="mb-3">
        <label for="nik" class="form-label">NIK</label>
        <input type="text" class="form-control" id="nik" required>
    </div>
    <div class="mb-3">
        <label for="birthDate" class="form-label">Tanggal Lahir</label>
        <input type="date" class="form-control" id="birthDate" required>
    </div>
    <div class="mb-3">
        <label for="birthPlace" class="form-label">Tempat Lahir</label>
        <input type="text" class="form-control" id="birthPlace" required>
    </div>
    <div class="mb-3">
        <label for="city" class="form-label">Kota Domisili</label>
        <input type="text" class="form-control" id="city" required>
    </div>
    <div class="mb-3">
        <label for="district" class="form-label">Kecamatan</label>
        <select class="form-select" id="district" required>
            <option selected disabled>Pilih Kecamatan</option>
            <option value="Kecamatan 1">Kecamatan 1</option>
            <option value="Kecamatan 2">Kecamatan 2</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="village" class="form-label">Kelurahan</label>
        <select class="form-select" id="village" required>
            <option selected disabled>Pilih Kelurahan</option>
            <option value="Kelurahan 1">Kelurahan 1</option>
            <option value="Kelurahan 2">Kelurahan 2</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="postalCode" class="form-label">Kode Pos</label>
        <input type="text" class="form-control" id="postalCode" required>
    </div>
    <div class="mb-3">
        <label for="fullAddress" class="form-label">Alamat Lengkap</label>
        <textarea class="form-control" id="fullAddress" rows="3" required></textarea>
    </div>
    <button type="button" class="btn btn-secondary w-100 mt-3" onclick="previousStep()">Back</button>
    <button type="submit" class="btn btn-success w-100 mt-3">Submit</button>
</div>
