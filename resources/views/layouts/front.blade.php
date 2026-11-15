<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'Penerimaan Peserta Didik Baru SMAKNIS online: daftar, unggah dokumen, pantau verifikasi, dan lihat hasil penerimaan.')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PPDB SMAKNIS')</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    <a href="#konten-utama" class="visually-hidden-focusable position-absolute top-0 start-0 m-2 btn btn-light btn-sm">
        Lewati ke konten utama
    </a>

    <x-navbar />

    <main id="konten-utama" class="flex-grow-1">
        <x-alert />
        @yield('content')
    </main>

    <x-footer />

    @stack('scripts')
</body>
</html>
