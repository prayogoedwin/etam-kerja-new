@include('components.header')
<!-- Start Banner Area
    ============================================= -->
{{-- <div class="banner-area banner-style-two content-right navigation-icon-solid navigation-right-botom navigation-custom-large overflow-hidden bg-cover"
    style="background: url({{ asset('assets') }}/etam_fe/img/shape/banner.jpg);"> --}}
<div
    class="banner-area banner-style-two content-right navigation-icon-solid navigation-right-botom navigation-custom-large overflow-hidden bg-cover">
    <!-- Slider main container -->
    <div class="banner-style-two-carousel">
        <!-- Additional required wrapper -->
        <div class="swiper-wrapper">
            <div class="swiper-slide banner-style-two" style="padding:0;">
                <img src="{{ asset('assets') }}/banner/etam-kerja-kaltim.jpg" alt="Banner Kaltim"
                    style="width:100%; height:auto; display:block;">
            </div>

            <div class="swiper-slide banner-style-two" style="padding:0;">
                <img src="{{ asset('assets') }}/banner/gen-emas-kaltim.jpg" alt="Banner Kaltim"
                    style="width:100%; height:auto; display:block;">
            </div>
        </div>

        <!-- Navigation -->
        {{-- <div class="swiper-nav-left">
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div> --}}

    </div>
</div>
<!-- End Main -->

<!-- Start Services
    ============================================= -->


<br/>
<br/>
<br/>

<!-- Ganti bagian services-style-three-area dengan ini -->
<div class="services-style-three-area half-bg-dark"
    style="background-image: url({{ asset('assets') }}/etam_fe/img/shape/52.png);">

    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="site-heading secondary text-center">
                    <h2 class="title">Lowongan Kerja Terbaru</h2>
                    <br />
                    <div class="devider"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="container container-stage">
        <div class="row">
            <div class="col-lg-12">
                <div class="services-carousel swiper">
                    <div class="swiper-wrapper">
                        @if ($lowonganDisetujui15->count())
                            @foreach ($lowonganDisetujui15 as $lindex => $lokerTerbaru)
                                <div class="swiper-slide">
                                    <div class="loker-card-modern">
                                        <div class="loker-logo-wrapper">
                                            {{-- <img src="{{ asset('assets') }}/etam_fe/images/default/logo-perusahaan.png" 
                                                 alt="Logo"> --}}

                                            <img src="{{ asset('assets/' . $lokerTerbaru->postedBy->penyedia->name) }}" 
                                            alt="Logo"
                                            onerror="this.onerror=null; this.src='{{ asset('assets/etam_fe/images/default/logo-perusahaan.png') }}'">

                                                  
                                        </div>
                                        
                                        <h3 class="loker-title-modern">
                                            <a href="#" style="color: inherit; text-decoration: none;">
                                                {{ $lokerTerbaru->judul_lowongan }}
                                            </a>
                                        </h3>
                                        
                                        <span class="loker-company-modern">
                                            {{-- {{ $lokerTerbaru->postedBy->name }} --}}
                                            {{ $lokerTerbaru->postedBy->penyedia->name ?? $lokerTerbaru->postedBy->name }}
                                        </span>
                                        
                                        {{-- <p class="loker-desc-modern">
                                            {{ \Illuminate\Support\Str::limit($lokerTerbaru->deskripsi, 120, '...') }}
                                        </p> --}}
                                        
                                        <span class="loker-badge">Lihat Detail</span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p>Belum ada lowongan yang tersedia.</p>
                        @endif
                    </div>

                    <div class="services-swiper-nav">
                        <div class="services-button-prev"></div>
                        <div class="services-button-next"></div>
                    </div>
                </div>
                <br />
                <div style="text-align: center;">
                    <a href="{{ url('/depan/lowongan-kerja') }}" style="text-align: center; color:#fff">Lihat Lowongan Lebih Banyak</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Services -->

<!-- Start Estimate
    ============================================= -->
<div class="estimate-area bg-dark text-light default-padding">
    <div class="container">
        <div class="row">

            <div class="col-lg-6">
                <div class="estimate-style-one">
                    <h4 class="sub-heading secondary">Job Matching</h4>
                    <h2 class="title">Cari Lowongan Kerja <br> Paling Sesuai</h2>
                    <div class="esitmate-form mt-40">
                        <form action="{{ url('/depan/lowongan-kerja') }}" method="GET">
                            <div class="row">


                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="name">Judul Lowongan</label>
                                        <input class="form-control" id="judul_lowongan" name="judul_lowongan"
                                            placeholder="Cari Judul Lowongan Kerja" type="text">
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="subject">Pendidikan</label>
                                        <select id="pendidikan_id" name="pendidikan_id" class="form-control">
                                            <option value="">-- Pilih Pendidikan --</option>
                                            @foreach (getPendidikan() as $id => $pendidikan)
                                                <option value="{{ $pendidikan->id }}">{{ $pendidikan->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="subject">Lokasi Perusahaan</label>
                                        <select id="kabkota_id" name="kabkota_id" class="form-control">
                                            <option value="">-- Pilih Lokasi --</option>
                                            @foreach (getKabkota() as $id => $lokasi)
                                                <option value="{{ $lokasi->id }}">{{ $lokasi->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <button type="submit" name="submit" id="submit">
                                        Cari Lowongan Kerja
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 offset-lg-1">
                <div class="estimate-thumb">
                    <img src="{{ asset('assets') }}/etam_fe/images/default/jon-matching.png" alt="Image Not Found">
                    <div class="shape">
                        <img src="{{ asset('assets') }}/etam_fe/img/shape/53-5.png" alt="Shape">
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- End Estimate -->

<!-- Start Partner Area ============================================= -->
<!-- sumber image ambil dari https://p2k.stekom.ac.id/ensiklopedia/Daftar_kabupaten_dan_kota_di_Kalimantan_Timur -->
<div class="partner-style-one-area default-padding">
    <div class="container">
        <h4 class="sub-heading secondary">Tersedia Lowongan Di 10 Kabupaten Kota di Kalimatan Timur</h4>
        <div class="row align-center">
            <div class="col-lg-3">
                <!-- <div class="partner-map" style="background-image: url({{ asset('assets') }}/etam_fe/img/shape/map.png);"> -->
                <div class="partner-map"
                    style="background-image: url({{ asset('assets') }}/etam_fe/images/logo/logo_kaltim.png);">
                    <h2 class="mask-text"
                        style="background-image: url({{ asset('assets') }}/etam_fe/img/2440x1578.png);">&nbsp;</h2>
                    <!-- <br/>
                        <h5>Lowongan-lowongan tersebar <br/>di 10 Kabupaten/Kota</h5> -->
                </div>
            </div>
            <div class="col-lg-9">
                <div class="partner-items">
                    <ul>
                        <li><img src="{{ asset('assets') }}/etam_fe/images/logo/kabkota/kabupaten-berau.png"
                                alt="Image Not FOund"></li>
                        <li><img src="{{ asset('assets') }}/etam_fe/images/logo/kabkota/kabupaten-kutai-barat.png"
                                alt="Image Not FOund"></li>
                        <li><img src="{{ asset('assets') }}/etam_fe/images/logo/kabkota/kabupaten-kutai-kartanegara.png"
                                alt="Image Not FOund"></li>
                        <li><img src="{{ asset('assets') }}/etam_fe/images/logo/kabkota/kabupaten-kutai-timur.png"
                                alt="Image Not FOund"></li>
                        <li><img src="{{ asset('assets') }}/etam_fe/images/logo/kabkota/kabupaten-mahakam-ulu.png"
                                alt="Image Not FOund"></li>
                        <li><img src="{{ asset('assets') }}/etam_fe/images/logo/kabkota/kabupaten-paser.png"
                                alt="Image Not FOund"></li>
                        <li><img src="{{ asset('assets') }}/etam_fe/images/logo/kabkota/kabupaten-penajam-paser-utara.jpeg"
                                alt="Image Not FOund">
                        </li>
                        <li><img src="{{ asset('assets') }}/etam_fe/images/logo/kabkota/kota-balikpapan.png"
                                alt="Image Not FOund"></li>
                        <li><img src="{{ asset('assets') }}/etam_fe/images/logo/kabkota/kota-bontang.png"
                                alt="Image Not FOund">
                        </li>
                        <li><img src="{{ asset('assets') }}/etam_fe/images/logo/kabkota/kota-samarinda.png"
                                alt="Image Not FOund"></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Partner Area -->


<!-- Start Faq
    ============================================= -->
<div class="faq-style-one-area relative"
    style="background-image: url({{ asset('assets') }}/etam_fe/img/shape/32-5.png);">


    <div class="container">
        <div class="row align-center">

            <div class="col-lg-6">
                <div class="faq-style-one default-padding">
                    <h4 class="sub-heading">FAQ</h4>
                    <h2 class="title mb-30">Jenis Pertanyaan Umum <br></h2>
                    <div class="accordion" id="faqAccordion">
                        @if ($faq->count())
                            @foreach ($faq as $index => $faq)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading-{{ $index }}">
                                        <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }}"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse-{{ $index }}"
                                            aria-expanded="{{ $index == 0 ? 'true' : 'false' }}"
                                            aria-controls="collapse-{{ $index }}">
                                            {{ $faq->name }}
                                        </button>
                                    </h2>
                                    <div id="collapse-{{ $index }}"
                                        class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}"
                                        aria-labelledby="heading-{{ $index }}" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            <p>
                                                {!! $faq->description !!}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p>Belum ada pertanyaan yang tersedia.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-5 offset-lg-1 mt-120 mt-md-50 mt-xs-30">
                <div class="faq-thumb">
                    <img src="{{ asset('assets') }}/etam_fe/img/illustration/6.png" alt="Image Not Found">
                </div>
            </div>

        </div>
    </div>
</div>
<!-- End Faq -->


<!-- Start Fun Factor Area
    ============================================= -->
<div class="fun-factor-style-one-area bg-gray default-padding"
    style="background-image: url({{ asset('assets') }}/etam_fe/img/shape/41-5.png);">
    <div class="container">
        <div class="fun-factor-style-one-box">

            <div class="shape-animated-up-down">
                <img src="{{ asset('assets') }}/etam_fe/img/shape/39.png" alt="Image Not Found">
            </div>

            <div class="row align-center">

                <div class="col-lg-10 offset-lg-1 text-center fun-fact-style-one">
                    <div class="row">
                        <!-- Single item -->
                        <div class="col-lg-4 col-md-4 item">
                            <div class="fun-fact">
                                <div class="counter">
                                    <div class="timer" data-to="{{ $lowongan }}" data-speed="2000">
                                        {{ $lowongan }}</div>
                                    <div class="operator"></div>
                                </div>
                                <span class="medium">Lowongan Tersedia</span>
                            </div>
                        </div>
                        <!-- End Single item -->

                        <!-- Single item -->
                        <div class="col-lg-4 col-md-4 item">
                            <div class="fun-fact">
                                <div class="counter">
                                    <div class="timer" data-to="{{ $pencari }}" data-speed="2000">
                                        {{ $pencari }}</div>
                                    <div class="operator"></div>
                                </div>
                                <span class="medium">Pencari Kerja</span>
                            </div>
                        </div>
                        <!-- End Single item -->

                        <!-- Single item -->
                        <div class="col-lg-4 col-md-4 item">
                            <div class="fun-fact">
                                <div class="counter">
                                    <div class="timer" data-to="{{ $penyedia }}" data-speed="2000">
                                        {{ $penyedia }}</div>
                                    <div class="operator"></div>
                                </div>
                                <span class="medium">Pemberi Kerja</span>
                            </div>
                        </div>
                        <!-- End Single item -->
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- End Fun Factor Area -->

<!-- Start Blog
    ============================================= -->
<div class="home-blog-area default-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="site-heading text-center">
                    <h4 class="sub-heading">Terbaru</h4>
                    <h2 class="title">Berita & Informasi</h2>
                    <div class="devider"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">

            @if ($beritaTerbaru->count())
                @foreach ($beritaTerbaru as $bindex => $beritaTerbaru)
                    <div class="col-lg-6 mt-md-30 mt-xs-30">
                        <div class="blog-style-one solid">
                            <div class="thumb">
                                <img src="{{ asset('storage/' . $beritaTerbaru->cover) }}" alt="Image Not Found">
                                <a href="{{ route('berita.show', ['id' => $beritaTerbaru->id]) }}">Berita</a>
                                <div class="info">
                                    <div class="blog-meta">
                                        <ul>
                                            <li>
                                                <a
                                                    href="{{ route('berita.show', ['id' => encode_url($beritaTerbaru->id)]) }}"><i
                                                        class="fas fa-user"></i> ADMIN ETAMKERJA</a>
                                            </li>
                                            <li>
                                                {{ \Carbon\Carbon::parse($beritaTerbaru->created_at)->format('d F, Y') }}
                                            </li>
                                        </ul>
                                    </div>
                                    <h4>
                                        <a
                                            href="{{ route('berita.show', ['id' => encode_url($beritaTerbaru->id)]) }}">{{ $beritaTerbaru->name }}</a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Single Item -->
                @endforeach
            @else
                <p>Belum ada pertanyaan yang tersedia.</p>
            @endif

        </div>
    </div>
</div>
<!-- End Blog  -->

@include('components.footer')
