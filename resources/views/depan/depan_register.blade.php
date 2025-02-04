@include('components.header')
<div class="faq-area bg-gray default-padding">
    <!-- End Shape -->
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-md-30 mb-xs-30">
                <div class="faq-sidebar">
                    <div class="faq-sidebar-item bg-theme text-light"
                        style="background-image: asset(etam_fe/img/shape/map-light.png);">
                        <h4>Ingin Mendaftar Sebagai?</h4>
                        <ul>
                            <li><a href="#">Pemberi Kerja</a></li>
                            <li><a href="#">Perusahaan</a></li>
                            <li><a href="#">BKK</a></li>
                            <li><a href="{{ url('/depan/login') }}">Sudah punya akun? Login <span
                                        style="color:yellow">disini</span></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 faq-style-one dark pl-50 pl-md-15 pl-xs-15">

                <h2 class="title mb-40">Form Pendaftaran .</h2>

                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Pemberi Kerja
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <!-- form -->
                                <div class="contact-form-style-one">
                                    <!-- <h5 class="sub-title">Have Questions?</h5>
                                    <h2 class="heading">Send us a Massage</h2> -->
                                    <form action="assets/mail/contact.php" method="POST"
                                        class="contact-form contact-form">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <input class="form-control" id="ktp" name="ktp"
                                                        placeholder="NIK KTP" type="number">
                                                    <span class="alert-error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <input class="form-control" id="name" name="name"
                                                        placeholder="Nama Lengkap" type="text">
                                                    <span class="alert-error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <input class="form-control" id="email" name="email"
                                                        placeholder="Email" type="email">
                                                    <span class="alert-error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <input class="form-control" id="nama" name="nama"
                                                        placeholder="Nama" type="text">
                                                    <span class="alert-error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <input class="form-control" id="whatsapp" name="whatsapp"
                                                        placeholder="HP Whatsapp" type="text">
                                                    <span class="alert-error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <input class="form-control" id="emai" name="email"
                                                        placeholder="Email" type="text">
                                                    <span class="alert-error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <input class="form-control" id="tempat_lahir" name="tempat_lahir"
                                                        placeholder="Tempat Lahir" type="text">
                                                    <span class="alert-error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <input class="form-control" id="tanggal_lahir"
                                                        name="tanggal_lahir" placeholder="Tanggal Lahir"
                                                        type="text">
                                                    <span class="alert-error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="">Jenis Kelamin</label>
                                                    <select class="form-control" name="gender" id="gender"
                                                        required>
                                                        <option value="">Pilih</option>
                                                        <option value="L">Laki - laki</option>
                                                        <option value="P">Perempuan</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="">Agama</label>
                                                    <select class="form-control" name="agama" id="agama"
                                                        required>
                                                        <option value="">Pilih</option>
                                                        @foreach ($agama as $ag)
                                                            <option value="{{ $ag->id }}">{{ $ag->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="">Provinsi</label>
                                                    <input class="form-control" id="provinsi_id" name="provinsi_id"
                                                        placeholder="Kalimantan Timur" type="text" readonly>
                                                    <span class="alert-error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="">Kabupaten/Kota</label>
                                                    <select class="form-control" name="kabkota_id" id="kabkota_id"
                                                        required>
                                                        <option value="">Pilih</option>
                                                        @foreach ($kabkota as $kabkot)
                                                            <option value="{{ $kabkot->id }}">{{ $kabkot->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="">Kecamatan</label>
                                                    <select class="form-control" name="kecamatan_id"
                                                        id="kecamatan_id" required>
                                                        <option value="">Pilih</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="">Desa/Kelurahan</label>
                                                    <select class="form-control" name="kelurahan_id"
                                                        id="kelurahan_id" required>
                                                        <option value="">Pilih</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group comments">
                                                    <textarea class="form-control" id="alamat" name="alamat" placeholder="Alamat"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <input class="form-control" id="kodepos" name="kodepos"
                                                        placeholder="KODEPOS" type="number">
                                                    <span class="alert-error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                {{-- <div class="form-group">
                                                    <input class="form-control" id="tanggal_lahir"
                                                        name="tanggal_lahir" placeholder="Tanggal Lahir"
                                                        type="text">
                                                    <span class="alert-error"></span>
                                                </div> --}}
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="">Pendidikan</label>
                                                    <select class="form-control" name="pendidikan_id"
                                                        id="pendidikan_id" required>
                                                        <option value="">Pilih</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="">Jurusan</label>
                                                    <select class="form-control" name="jurusan_id" id="jurusan_id"
                                                        required>
                                                        <option value="">Pilih</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="">Tahun Lulus</label>
                                                    <select class="form-control" name="tahun_lulus" id="tahun_lulus"
                                                        required>
                                                        <option value="">Pilih</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="">Status Kawin</label>
                                                    <select class="form-control" name="status_perkawinan_id"
                                                        id="status_perkawinan_id" required>
                                                        <option value="">Pilih</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="">Disabilitas</label>
                                                    <select class="form-control" name="disabilitas" id="disabilitas"
                                                        required>
                                                        <option value="">Pilih</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="">Jenis Disabilitas</label>
                                                    <select class="form-control" name="status_perkawinan_id"
                                                        id="status_perkawinan_id" required>
                                                        <option value="">Pilih</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <button type="submit" name="submit" id="submit">
                                                    <i class="fa fa-paper-plane"></i>Daftar Sekarang
                                                </button>
                                            </div>
                                        </div>
                                        <!-- Alert Message -->
                                        <div class="col-lg-12 alert-notification">
                                            <div id="message" class="alert-msg"></div>
                                        </div>
                                    </form>
                                </div>
                                <!-- form -->
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Perusahaan
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <!-- form -->
                                <div class="contact-form-style-one">
                                    <!-- <h5 class="sub-title">Have Questions?</h5>
                                    <h2 class="heading">Send us a Massage</h2> -->
                                    <form action="assets/mail/contact.php" method="POST"
                                        class="contact-form contact-form">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <input class="form-control" id="name" name="name"
                                                        placeholder="Name" type="text">
                                                    <span class="alert-error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <input class="form-control" id="email" name="email"
                                                        placeholder="Email*" type="email">
                                                    <span class="alert-error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <input class="form-control" id="phone" name="phone"
                                                        placeholder="Phone" type="text">
                                                    <span class="alert-error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group comments">
                                                    <textarea class="form-control" id="comments" name="comments" placeholder="Tell Us About Project *"></textarea>
                                                </div>
                                            </div>
                                        </div> -->
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <button type="submit" name="submit" id="submit">
                                                    <i class="fa fa-paper-plane"></i>Daftar Sekarang
                                                </button>
                                            </div>
                                        </div>
                                        <!-- Alert Message -->
                                        <div class="col-lg-12 alert-notification">
                                            <div id="message" class="alert-msg"></div>
                                        </div>
                                    </form>
                                </div>
                                <!-- form -->
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                BKK
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <!-- form -->
                                <div class="contact-form-style-one">
                                    <!-- <h5 class="sub-title">Have Questions?</h5>
                                    <h2 class="heading">Send us a Massage</h2> -->
                                    <form action="assets/mail/contact.php" method="POST"
                                        class="contact-form contact-form">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <input class="form-control" id="name" name="name"
                                                        placeholder="Name" type="text">
                                                    <span class="alert-error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <input class="form-control" id="email" name="email"
                                                        placeholder="Email*" type="email">
                                                    <span class="alert-error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <input class="form-control" id="phone" name="phone"
                                                        placeholder="Phone" type="text">
                                                    <span class="alert-error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group comments">
                                                    <textarea class="form-control" id="comments" name="comments" placeholder="Tell Us About Project *"></textarea>
                                                </div>
                                            </div>
                                        </div> -->
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <button type="submit" name="submit" id="submit">
                                                    <i class="fa fa-paper-plane"></i>Daftar Sekarang
                                                </button>
                                            </div>
                                        </div>
                                        <!-- Alert Message -->
                                        <div class="col-lg-12 alert-notification">
                                            <div id="message" class="alert-msg"></div>
                                        </div>
                                    </form>
                                </div>
                                <!-- form -->
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

@include('components.footer')
