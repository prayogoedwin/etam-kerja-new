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
        <div class="esitmate-form2">
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
            <div class="row" style="padding-top:50px">

                @forelse ($lowonganDisetujui as $lowongan)
                    <div class="col-xl-4 col-md-6 single-item mb-4">
                        <div class="loker-card-modern">
                            <div class="loker-logo-wrapper">
                                {{-- <img src="{{ asset('assets') }}/etam_fe/images/default/logo-perusahaan.png" 
                                     alt="Logo Perusahaan"> --}}
                                 <img src="{{ asset('assets/' . $lowongan->postedBy->penyedia->name) }}" 
                                            alt="Logo"
                                            onerror="this.onerror=null; this.src='{{ asset('assets/etam_fe/images/default/logo-perusahaan.png') }}'">
                            </div>
                            
                            <h3 class="loker-title-modern">
                                <a href="{{ route('lowongan.show', ['id' => encode_url($lowongan->id)]) }}" 
                                   style="color: inherit; text-decoration: none;">
                                    {{ $lowongan->judul_lowongan }}
                                </a>
                            </h3>
                            
                            <span class="loker-company-modern">
                                {{-- {{ $lowongan->postedBy->name ?? 'Perusahaan' }} --}}
                                  {{ $lowongan->postedBy->penyedia->name ?? $lowongan->postedBy->name }}
                            </span>
                            
                            {{-- <p class="loker-desc-modern">
                                {{ \Illuminate\Support\Str::limit($lowongan->deskripsi ?? 'Deskripsi lowongan tidak tersedia.', 120, '...') }}
                            </p> --}}
                            
                            <div class="loker-meta-modern">
                                <span class="loker-expired-modern">
                                    <i class="fas fa-clock"></i>
                                    Exp: {{ \Carbon\Carbon::parse($lowongan->tanggal_end)->format('d M Y') }}
                                </span>
                                <a href="{{ route('lowongan.show', ['id' => encode_url($lowongan->id)]) }}" 
                                   class="loker-link-modern">
                                    {{-- Lihat Detail <i class="fas fa-arrow-right"></i> --}}
                                     <span class="loker-badge">Lihat Detail <i class="fas fa-arrow-right"></i></span>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div style="text-align: center; padding: 60px 20px;">
                            <i class="fas fa-search" style="font-size: 48px; color: #cbd5e0; margin-bottom: 16px;"></i>
                            <p style="color: #718096; font-size: 16px;">Tidak ada lowongan yang sesuai dengan pencarian.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        <div class="row">
            <div class="col-md-12 pagi-area text-center">
                <nav aria-label="navigation">
                    <ul class="pagination">
                        <!-- Previous Page Link -->
                        @if ($lowonganDisetujui->onFirstPage())
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1" aria-disabled="true"><i
                                        class="fas fa-angle-double-left"></i></a>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $lowonganDisetujui->previousPageUrl() }}"
                                    rel="prev"><i class="fas fa-angle-double-left"></i></a>
                            </li>
                        @endif

                        <!-- Pagination Links -->
                        @foreach ($lowonganDisetujui->getUrlRange(1, $lowonganDisetujui->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $lowonganDisetujui->currentPage() ? 'active' : '' }}">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach

                        <!-- Next Page Link -->
                        @if ($lowonganDisetujui->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $lowonganDisetujui->nextPageUrl() }}" rel="next"><i
                                        class="fas fa-angle-double-right"></i></a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1" aria-disabled="true"><i
                                        class="fas fa-angle-double-right"></i></a>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
        <!-- End Pagination -->

    </div>
</div>
<!-- End Blog -->
@include('components.footer')