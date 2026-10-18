@extends('layouts.saudara')

@section('title', 'Cek Pesanan (Demo) — SaudaraOrder')

@section('main')
<main id="konten" class="inner-main tracking-main">
    <header class="inner-nav">
        <a class="wordmark" href="{{ route('home') }}">4Saudara<span>.</span></a>
        <a class="text-link" href="{{ route('home') }}">← Kembali ke beranda</a>
    </header>

    <section class="tracking-card" aria-labelledby="tracking-title">
        <div class="tracking-intro">
            <span class="eyebrow light">AKSES PELACAKAN PRIBADI</span>
            <h1 id="tracking-title">Pesananmu.<br><em>Dalam satu tempat.</em></h1>
            <p>Pelacakan nantinya hanya dapat dibuka melalui tautan pribadi atau kunci akses pribadi yang diberikan setelah pengajuan dikonfirmasi. Kode pesanan biasa tidak dapat digunakan untuk membuka data pelanggan.</p>
        </div>

        <div class="tracking-grid">
            <section class="tracking-access" aria-labelledby="tracking-form-title">
                <span class="section-index light-index">CEK AKSES</span>
                <h2 id="tracking-form-title">Buka pelacakan pribadi</h2>

                <div class="notice dark-notice" role="note">
                    <strong>Simulasi frontend</strong>
                    <p>Backend pelacakan belum tersedia. Form ini hanya mendemonstrasikan validasi akses dan tidak mencari atau menampilkan data pelanggan.</p>
                </div>

                <form id="tracking-form" novalidate>
                    <label for="tracking-access">Tautan atau kunci akses pribadi <span aria-hidden="true">*</span></label>
                    <input
                        type="text"
                        id="tracking-access"
                        placeholder="Tempel akses pribadi yang diberikan pengelola"
                        autocomplete="off"
                        autocapitalize="none"
                        spellcheck="false"
                        minlength="32"
                        maxlength="300"
                        required
                        aria-describedby="tracking-access-hint tracking-access-error"
                    >
                    <span class="input-hint" id="tracking-access-hint">Jangan masukkan nomor WhatsApp. Kode pesanan biasa seperti SO-1234 akan ditolak karena bukan kunci akses pribadi.</span>
                    <span class="field-error" id="tracking-access-error" data-error-for="tracking-access" aria-live="polite"></span>
                    <button type="button" class="btn btn-accent" data-track-check>Periksa Akses (Demo)</button>
                </form>

                <div class="tracking-how" aria-labelledby="tracking-how-title">
                    <h3 id="tracking-how-title">Cara melacak pesanan</h3>
                    <ol>
                        <li><span aria-hidden="true">01</span><p>Terima tautan atau kunci akses pribadi langsung dari pengelola.</p></li>
                        <li><span aria-hidden="true">02</span><p>Tempel akses tersebut pada kolom di atas, bukan kode pesanan biasa.</p></li>
                        <li><span aria-hidden="true">03</span><p>Lihat status hanya dari perangkat dan tautan yang kamu percayai.</p></li>
                    </ol>
                </div>
            </section>

            <section class="tracking-result" aria-labelledby="tracking-result-title" aria-live="polite">
                <div class="tracking-state is-active" data-state="empty">
                    <span class="state-icon" aria-hidden="true">↗</span>
                    <h2 id="tracking-result-title">Belum ada akses yang diperiksa</h2>
                    <p>Masukkan tautan atau kunci akses pribadi untuk melihat contoh alur status pesanan.</p>
                </div>

                <div class="tracking-state" data-state="loading" role="status" aria-busy="true" hidden>
                    <span class="loading-indicator" aria-hidden="true"></span>
                    <h2>Memeriksa format akses</h2>
                    <p>Tunggu sebentar. Tidak ada permintaan yang dikirim ke server.</p>
                </div>

                <div class="tracking-state error-state" data-state="error" role="alert" hidden>
                    <span class="state-label">AKSES DITOLAK</span>
                    <h2>Akses tidak dapat digunakan</h2>
                    <p data-state-message>Gunakan tautan atau kunci akses pribadi, bukan kode pesanan biasa.</p>
                </div>

                <div class="tracking-state prototype-state" data-state="prototype" hidden>
                    <div class="demo-heading">
                        <span class="demo-badge">DEMO</span>
                        <span>Bukan data pesanan nyata</span>
                    </div>
                    <h2>Contoh perjalanan pesanan</h2>
                    <p>Timeline berikut hanya memperlihatkan bentuk status yang direncanakan. Tidak ada pelanggan atau pesanan sungguhan yang ditampilkan.</p>

                    <ol class="tracking-timeline" aria-label="Contoh lima tahap status pesanan">
                        <li class="timeline-item is-complete">
                            <span class="timeline-marker" aria-hidden="true">01</span>
                            <div>
                                <strong>Menunggu Konfirmasi</strong>
                                <p>Pengajuan menunggu pemeriksaan jumlah, kuota batch, dan jadwal dari pengelola.</p>
                            </div>
                        </li>
                        <li class="timeline-item is-complete">
                            <span class="timeline-marker" aria-hidden="true">02</span>
                            <div>
                                <strong>Dikonfirmasi</strong>
                                <p>Detail pesanan dan metode pembayaran telah dikonfirmasi pengelola.</p>
                            </div>
                        </li>
                        <li class="timeline-item is-current" aria-current="step">
                            <span class="timeline-marker" aria-hidden="true">03</span>
                            <div>
                                <strong>Diproduksi</strong>
                                <p>Produk sedang disiapkan sesuai jadwal batch produksi.</p>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <span class="timeline-marker" aria-hidden="true">04</span>
                            <div>
                                <strong>Siap Diambil</strong>
                                <p>Pesanan siap diambil sesuai informasi waktu dari pengelola.</p>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <span class="timeline-marker" aria-hidden="true">05</span>
                            <div>
                                <strong>Selesai</strong>
                                <p>Pesanan telah diterima dan alur pre-order selesai.</p>
                            </div>
                        </li>
                    </ol>
                </div>
            </section>
        </div>
    </section>
</main>
@endsection
