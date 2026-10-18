<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1c221a">
    <meta name="description" content="Keripik Singkong 4 Saudara. Satu rasa khas, nikmatnya harus kamu coba. Harga Rp5.000 per pouch.">
    <title>@yield('title', 'SaudaraOrder — Keripik Singkong 4 Saudara')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('saudara/css/site.css') }}">
    <script src="{{ asset('saudara/js/site.js') }}" defer></script>
    @stack('head')
</head>
<body>
    <a href="#konten" class="skip-link">Lewati ke konten</a>
    <div class="page-frame">
        <div class="demo-ribbon" role="note"><span class="live-dot" aria-hidden="true"></span> Prototype Capstone · Tampilan awal, pemesanan belum aktif</div>
        @yield('main')
        <footer class="footer" id="kontak">
            <div class="footer-brand"><a class="wordmark" href="{{ route('home') }}">4Saudara<span>.</span></a><p>Satu rasa khas.<br>Untuk momen yang sederhana.</p></div>
            <div class="footer-links"><strong>Jelajahi</strong><a href="{{ route('home') }}#produk">Produk</a><a href="{{ route('home') }}#cara-pesan">Cara pesan</a><a href="{{ route('tracking') }}">Cek pesanan</a></div>
            <div class="footer-contact"><strong>Kontak & pengambilan</strong><p>Nomor WhatsApp dan lokasi pengambilan akan tersedia setelah dikonfirmasi pengelola.</p><span class="footer-meta">Harga awal Rp5.000 / pouch</span></div>
            <div class="footer-bottom"><span>© {{ date('Y') }} Keripik Singkong 4 Saudara</span><span>Dirancang untuk Capstone · SaudaraOrder</span></div>
        </footer>
    </div>
</body>
</html>
