@php($tahun = date('Y'))

<footer class="footer-ppdb mt-auto">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="d-flex align-items-center gap-2 mb-3 text-white">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-white text-success" style="width: 2.4rem; height: 2.4rem;">
                        <i class="bi bi-mortarboard-fill"></i>
                    </span>
                    <span class="fw-bold fs-5">PPDB SMAKNIS</span>
                </div>
                <p class="small mb-3">
                    Penerimaan Peserta Didik Baru online yang mudah, transparan, dan terintegrasi.
                    Daftar dari mana saja, pantau status verifikasi dan hasil penerimaan dalam satu akun.
                </p>
                <div class="d-flex gap-2">
                    <a href="{{ url('/') }}" class="btn btn-sm btn-outline-light" aria-label="Website"><i class="bi bi-globe2"></i></a>
                    <a href="{{ url('/') }}" class="btn btn-sm btn-outline-light" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="{{ url('/') }}" class="btn btn-sm btn-outline-light" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                </div>
            </div>

            <div class="col-6 col-lg-2">
                <h6 class="text-white fw-bold mb-3">Informasi</h6>
                <ul class="list-unstyled small d-grid gap-2 mb-0">
                    <li><a href="{{ url('/informasi') }}">Informasi PPDB</a></li>
                    <li><a href="{{ route('jurusan') }}">Jurusan</a></li>
                    <li><a href="{{ url('/informasi#program') }}">Program Keahlian</a></li>
                    <li><a href="{{ url('/jadwal') }}">Jadwal PPDB</a></li>
                    <li><a href="{{ url('/persyaratan') }}">Persyaratan</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-2">
                <h6 class="text-white fw-bold mb-3">Pendaftaran</h6>
                <ul class="list-unstyled small d-grid gap-2 mb-0">
                    <li><a href="{{ url('/alur') }}">Alur Pendaftaran</a></li>
                    <li><a href="{{ url('/pengumuman') }}">Pengumuman</a></li>
                    <li><a href="{{ route('register') }}">Buat Akun</a></li>
                    <li><a href="{{ route('login') }}">Masuk</a></li>
                </ul>
            </div>

            <div class="col-lg-4">
                <h6 class="text-white fw-bold mb-3">Kontak Panitia</h6>
                <ul class="list-unstyled small d-grid gap-2 mb-0">
                    <li class="d-flex gap-2">
                        <i class="bi bi-geo-alt mt-1"></i>
                        <span>Jl. Pendidikan No. 1, Kota Sekolah, Indonesia</span>
                    </li>
                    <li class="d-flex gap-2">
                        <i class="bi bi-telephone mt-1"></i>
                        <span>(021) 1234 5678</span>
                    </li>
                    <li class="d-flex gap-2">
                        <i class="bi bi-envelope mt-1"></i>
                        <span>ppdb@smaknis.sch.id</span>
                    </li>
                    <li class="d-flex gap-2">
                        <i class="bi bi-clock mt-1"></i>
                        <span>Senin - Jumat, 08.00 - 15.00 WIB</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="border-top border-white border-opacity-10">
        <div class="container py-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 small">
            <span>&copy; {{ $tahun }} PPDB SMAKNIS. Seluruh hak cipta dilindungi.</span>
            <span>Dibangun dengan Laravel &amp; Bootstrap 5</span>
        </div>
    </div>
</footer>
