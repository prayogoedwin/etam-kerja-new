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
                <h1>Lowongan Kerja</h1>
                <ul class="breadcrumb">
                    <li><a href="#"><i class="fas fa-home"></i> Beranda</a></li>
                    <li>Lowongan Kerja</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumb -->

<!-- Start Blog
    ============================================= -->
<div class="blog-area blog-grid default-padding">
    <div class="container">
        <div class="esitmate-form2 mt-40">
            <form action="#" method="GET">
                <div class="row">
                    <!-- Input Judul Lowongan -->
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="judul_lowongan">Judul Lowongan</label>
                            <input class="form-control" id="judul_lowongan" name="judul_lowongan" 
                                placeholder="Cari Judul Lowongan Kerja" type="text">
                        </div>
                    </div>
            
                    <!-- Dropdown Pendidikan -->
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="pendidikan_id">Pendidikan</label>
                            <select id="pendidikan_id" name="pendidikan_id" class="form-control">
                                <option value="">-- Pilih Pendidikan --</option>
                                @foreach (getPendidikan() as $id => $pendidikan)
                                    <option value="{{ $pendidikan->kode }}">{{ $pendidikan->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
            
                    <!-- Dropdown Lokasi Perusahaan -->
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="kabkota_id">Lokasi Perusahaan</label>
                            <select id="kabkota_id" name="kabkota_id" class="form-control">
                                <option value="">-- Pilih Lokasi --</option>
                                @foreach (getKabkota() as $id => $lokasi)
                                    <option value="{{ $id }}">{{ $lokasi->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
            
                    <!-- Button Cari -->
                    <div class="col-lg-3">
                        <button type="submit" name="submit" id="submit" class="btn btn-success mt-4">
                            Cari Lowongan Kerja
                        </button>
                    </div>
                </div>
            </form>
            
        </div>

        <div class="blog-item-box">
            <div class="row">
                @forelse ($lowonganDisetujui as $lowongan)
                    <div class="col-xl-4 col-md-6 single-item">
                        <div class="blog-style-one">
                            <div class="thumb">
                                <a href="#">
                                    <img src="{{ asset('assets/nakerbisa_fe/img/800x600.png') }}" alt="Thumb">

                                </a>
                            </div>
                            <div class="info">
                                <div class="blog-meta">
                                    <ul>
                                        <li class="sub-title">Perusahaan</li>
                                    </ul>
                                    <ul>
                                        <li>Expire in:
                                            {{ \Carbon\Carbon::parse($lowongan->tanggal_end)->format('d F, Y') }}</li>
                                    </ul>
                                </div>
                                <h3>
                                    <a href="#">{{ $lowongan->judul_lowongan }}</a>
                                </h3>
                                <a href="#" class="btn-simple"><i class="fas fa-angle-right"></i> Read more</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-center">Tidak ada lowongan yang sesuai dengan pencarian.</p>
                    </div>
                @endforelse
            </div>
        </div>
        <!-- Pagination -->
        <div class="row">
            <div class="col-md-12 pagi-area text-center">
                <nav aria-label="navigation">
                    {{ $lowonganDisetujui->links() }}
                </nav>
            </div>
        </div>
        <!-- End Pagination -->
    </div>
</div>
<!-- End Blog -->
@include('components.footer')
