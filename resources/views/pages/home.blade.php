@extends('layouts.saudara')

@section('main')
<main id="konten">
    <section class="hero" id="beranda" aria-label="Perkenalan Keripik Singkong 4 Saudara">
        <div class="hero-bg" role="img" aria-label="Foto ilustrasi keripik singkong dan kemasan polos dengan pencahayaan sinematik"></div>
        <div class="hero-shade"></div>
        <header class="nav-wrap">
            <a href="{{ route('home') }}" class="wordmark">4Saudara<span>.</span></a>
            <nav class="desktop-nav" aria-label="Navigasi utama"><a class="active" href="#beranda" aria-current="page">Beranda</a><a href="#produk">Produk</a><a href="#cara-pesan">Cara pesan</a><a href="{{ route('tracking') }}">Cek pesanan</a></nav>
            <a class="nav-pill" href="{{ route('preorder') }}">Pre-Order <span aria-hidden="true">↗</span></a>
            <button class="menu-toggle" type="button" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="mobile-nav"><span aria-hidden="true"></span><span aria-hidden="true"></span></button>
            <nav class="mobile-nav" id="mobile-nav" aria-label="Navigasi seluler" hidden><a href="#beranda" aria-current="page">Beranda</a><a href="#produk">Produk</a><a href="#cara-pesan">Cara pesan</a><a href="{{ route('tracking') }}">Cek pesanan</a><a href="{{ route('preorder') }}">Ajukan Pre-Order ↗</a></nav>
        </header>
        <div class="hero-copy">
            <div class="eyebrow light"><span class="eyebrow-line"></span> KERIPIK SINGKONG 4 SAUDARA</div>
            <h1>Satu rasa.<br><em>Banyak cerita.</em></h1>
            <p>Sederhana, tapi punya tempat tersendiri. Kenali satu rasa khas Keripik Singkong 4 Saudara.</p>
            <div class="hero-actions"><a class="btn btn-accent" href="{{ route('preorder') }}">Ajukan Pre-Order <span aria-hidden="true">↗</span></a><a class="text-link" href="#cerita">Kenali produknya <span aria-hidden="true">↘</span></a></div>
        </div>
        <div class="hero-bottom"><div class="hero-price"><span>MULAI DARI</span><strong>Rp5.000</strong><small>/ pouch</small></div><div class="hero-caption">SATU PRODUK <span>✳</span> SATU RASA KHAS</div><a class="scroll-cue" href="#cerita" aria-label="Gulir ke bagian cerita">SCROLL TO EXPLORE <span aria-hidden="true">↓</span></a></div>
        <span class="photo-note">Visual konsep · bukan foto produk asli</span>
    </section>

    <section class="intro section-light" id="cerita">
        <div class="intro-top reveal"><span class="section-index">01 / KENALI KAMI</span><span class="mini-stamp">4S ↗</span></div>
        <div class="intro-grid"><h2 class="display-heading reveal">Yang khas<br>tak perlu <em>ramai.</em></h2><div class="intro-copy reveal"><p>Kami percaya satu rasa yang dipilih dengan sepenuh hati layak mendapat perhatian lebih.</p><p>Di 4 Saudara, kamu tidak perlu memilih banyak varian. Cukup kenali satu keripik singkong khas yang kami tawarkan.</p><a href="#produk" class="underlined-link">Lihat produk kami <span aria-hidden="true">↗</span></a></div></div>
    </section>

    <section class="product-section" id="produk">
        <div class="section-heading-row reveal"><div><span class="section-index light-index">02 / PRODUK KAMI</span><h2 class="display-heading pale">Satu pilihan.<br><em>Sesuai selera.</em></h2></div><p>Tidak ada daftar rasa yang panjang. Hanya satu produk khas Keripik Singkong 4 Saudara.</p></div>
        <div class="product-grid"><div class="product-image reveal" role="img" aria-label="Ilustrasi keripik singkong dalam mangkuk dengan kemasan sederhana"><span class="image-label">VISUAL ILUSTRASI</span></div><div class="product-detail reveal"><div class="product-topline"><span>PRODUK 001</span><span>SATU RASA KHAS</span></div><h3>Keripik<br>Singkong<br><em>4 Saudara.</em></h3><p>Teman camilan untuk momen sehari-hari. Satu rasa khas, satu harga yang mudah diingat.</p><div class="product-price"><strong>Rp5.000</strong><span>/ pouch</span></div><a class="btn btn-accent dark-button" href="{{ route('preorder') }}">Lihat Form Pre-Order <span aria-hidden="true">↗</span></a><small>Foto dan kemasan pada rancangan ini adalah ilustrasi konsep.</small></div></div>
    </section>

    <section class="steps-section section-light" id="cara-pesan"><div class="steps-head reveal"><span class="section-index">03 / CARA PESAN</span><h2 class="display-heading">Dari klik,<br>jadi <em>camilan.</em></h2><p>Proses pemesanan akan mengikuti empat langkah sederhana setelah layanan dibuka.</p></div><div class="steps-grid"><article class="step reveal"><span>01</span><h3>Isi pesanan</h3><p>Tentukan jumlah pouch dan lengkapi informasi kontak.</p></article><article class="step reveal"><span>02</span><h3>Tunggu konfirmasi</h3><p>Pengelola memeriksa pesanan dan ketersediaan kuota batch.</p></article><article class="step reveal"><span>03</span><h3>Atur pembayaran</h3><p>Pilih transfer manual atau bayar saat pengambilan.</p></article><article class="step reveal"><span>04</span><h3>Ambil pesanan</h3><p>Ambil pesanan sesuai informasi jadwal dari pengelola.</p></article></div></section>

    <section class="batch-section section-light" id="batch"><div class="batch-card reveal"><div><span class="section-index light-index">INFORMASI PRODUKSI</span><h2>Batch berikutnya<br><em>sedang disiapkan.</em></h2></div><div class="batch-aside"><span class="batch-status"><span class="live-dot"></span> Belum dibuka</span><p>Kuota dan estimasi waktu pengambilan akan tampil setelah pengelola membuka batch. Belum ada data produksi yang ditampilkan.</p><a href="{{ route('preorder') }}" class="underlined-link light-link">Lihat rancangan pemesanan <span aria-hidden="true">↗</span></a></div></div></section>

    <section class="faq-section section-light" id="faq"><div class="faq-title reveal"><span class="section-index">04 / PERTANYAAN UMUM</span><h2 class="display-heading">Ada yang<br>ingin <em>ditanyakan?</em></h2></div><div class="faq-list reveal"><details><summary>Apakah tersedia banyak pilihan rasa?<span aria-hidden="true">+</span></summary><p>Tidak. Keripik Singkong 4 Saudara memiliki satu rasa khas.</p></details><details><summary>Berapa harga satu pouch?<span aria-hidden="true">+</span></summary><p>Harga awal yang direncanakan adalah Rp5.000 per pouch. Total menyesuaikan jumlah yang dipesan.</p></details><details><summary>Kapan pre-order bisa dilakukan?<span aria-hidden="true">+</span></summary><p>Pemesanan akan tersedia setelah pengelola membuka batch produksi. Ketersediaan dan kuota diperiksa saat konfirmasi pesanan.</p></details><details><summary>Bagaimana cara pembayarannya?<span aria-hidden="true">+</span></summary><p>Metode yang direncanakan adalah transfer manual atau bayar saat pengambilan, menunggu konfirmasi final pengelola.</p></details><details><summary>Bagaimana cara memeriksa status pesanan?<span aria-hidden="true">+</span></summary><p>Nantinya kamu akan menerima tautan pelacakan pribadi setelah mengajukan pesanan. Kode biasa tidak cukup untuk membuka detail pesanan.</p></details></div></section>

    <section class="closing-banner"><div class="closing-content reveal"><span class="eyebrow light">SATU RASA KHAS · 4 SAUDARA</span><h2>Yang sederhana,<br><em>selalu dirindukan.</em></h2><a href="{{ route('preorder') }}" class="btn btn-accent">Jelajahi Pre-Order <span aria-hidden="true">↗</span></a></div><div class="closing-orb" aria-hidden="true">4S</div></section>
</main>
@endsection
