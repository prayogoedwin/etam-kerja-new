@include('components.header')
<!-- Start Breadcrumb
    ============================================= -->
<div class="breadcrumb-area bg-theme shadow dark text-center text-light">
    <div class="breadcrum-shape">
        <!-- <img src="assets/img/shape/50.png" alt="Image Not Found"> -->
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <h1>Berita & Informasi</h1>
                <ul class="breadcrumb">
                    <li><a href="#"><i class="fas fa-home"></i> Beranda</a></li>
                    <li>Berita & Informasi</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumb -->

<!-- Start Blog
    ============================================= -->
<div class="home-blog-area default-padding">

    <div class="container">
        <div class="row">
            @foreach ($beritas as $ki => $berita)
                <div class="col-lg-6 mt-md-30 mt-xs-30">
                    <div class="blog-style-one solid mb-30">
                        <div class="thumb">
                            <img src="{{ asset('storage') . '/' . $berita->cover }}" alt="ImageFound">
                            <div class="tags"><a href="#">Berita</a></div>
                            <div class="info">
                                <div class="blog-meta">
                                    <ul>
                                        <li>
                                            <a href="{{ route('berita.show', ['id' => encode_url($berita->id)]) }}"><i
                                                    class="fas fa-user"></i> ADMIN ETAM KERKA</a>
                                        </li>
                                        <li>
                                            {{ $berita->created_at->format('d F Y') }}
                                        </li>
                                    </ul>
                                </div>
                                <h4>
                                    <a
                                        href="{{ route('berita.show', ['id' => encode_url($berita->id)]) }}">{{ $berita->name }}</a>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</div>
<!-- End Blog  -->

@include('components.footer')
