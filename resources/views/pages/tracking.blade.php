@extends('layouts.saudara')
@section('title', 'Cek Pesanan (Dalam Pengembangan) — SaudaraOrder')
@section('main')
<main id="konten" class="inner-main tracking-main"><header class="inner-nav"><a class="wordmark" href="{{ route('home') }}">4Saudara<span>.</span></a><a class="text-link" href="{{ route('home') }}">← Kembali ke beranda</a></header><section class="tracking-card"><span class="eyebrow light">STATUS PESANAN</span><h1>Pesananmu.<br><em>Dalam satu tempat.</em></h1><p>Fitur pelacakan belum aktif. Nantinya status pesanan hanya dapat dibuka melalui tautan pribadi yang diberikan saat pengajuan berhasil, bukan menggunakan kode pesanan biasa.</p><div class="notice dark-notice"><strong>Masih dalam pengembangan</strong><p>Halaman ini tidak meminta nomor telepon atau data pribadi selama backend belum siap.</p></div><a class="btn btn-accent" href="{{ route('home') }}">Kembali ke Beranda <span aria-hidden="true">↗</span></a></section></main>
@endsection
