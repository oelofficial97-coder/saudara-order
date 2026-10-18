@extends('layouts.saudara')

@section('title', 'Pre-Order (Prototipe) — SaudaraOrder')

@section('main')
<main id="konten" class="inner-main">
    <header class="inner-nav">
        <a class="wordmark" href="{{ route('home') }}">4Saudara<span>.</span></a>
        <a class="text-link" href="{{ route('home') }}">← Kembali ke beranda</a>
    </header>

    <section class="inner-intro" aria-labelledby="preorder-title">
        <span class="eyebrow light">PROTOTIPE FORM / MINGGU 4</span>
        <h1 id="preorder-title">Pesan yang khas.<br><em>Dengan mudah.</em></h1>
        <p>Simulasikan pengisian pre-order untuk satu rasa khas Keripik Singkong 4 Saudara. Pengajuan belum dibuka dan tidak ada data yang dikirim ke server.</p>
    </section>

    <div class="order-layout">
        <section class="order-panel" aria-labelledby="order-form-title">
            <span class="section-index">SIMULASI FORM PRE-ORDER</span>
            <h2 id="order-form-title">Detail pemesanan</h2>

            <div class="notice" role="note">
                <strong>Prototipe interaktif</strong>
                <p>Form dapat dicoba untuk melihat validasi dan perhitungan harga. Batch produksi dan penerimaan pesanan belum dibuka.</p>
            </div>

            <form id="prototype-form" data-unit-price="5000" novalidate>
                <div class="form-field">
                    <label for="customer-name">Nama pelanggan <span aria-hidden="true">*</span></label>
                    <input
                        type="text"
                        id="customer-name"
                        name="customer_name"
                        placeholder="Contoh: Budi Santoso"
                        autocomplete="name"
                        maxlength="100"
                        required
                        aria-describedby="customer-name-hint customer-name-error"
                    >
                    <span class="input-hint" id="customer-name-hint">Gunakan nama yang mudah dikenali oleh pengelola.</span>
                    <span class="field-error" id="customer-name-error" data-error-for="customer-name" aria-live="polite"></span>
                </div>

                <div class="form-field">
                    <label for="customer-phone">Nomor WhatsApp <span aria-hidden="true">*</span></label>
                    <input
                        type="tel"
                        id="customer-phone"
                        name="customer_phone"
                        placeholder="08xxxxxxxxxx"
                        autocomplete="tel"
                        inputmode="tel"
                        minlength="10"
                        maxlength="16"
                        required
                        aria-describedby="customer-phone-hint customer-phone-error"
                    >
                    <span class="input-hint" id="customer-phone-hint">Masukkan 10–16 digit nomor aktif, diawali 08 atau kode negara +62.</span>
                    <span class="field-error" id="customer-phone-error" data-error-for="customer-phone" aria-live="polite"></span>
                </div>

                <div class="form-field">
                    <label for="order-quantity">Jumlah pouch <span aria-hidden="true">*</span></label>
                    <div class="quantity-picker" data-quantity>
                        <button type="button" data-decrease aria-label="Kurangi jumlah pouch" aria-controls="order-quantity">−</button>
                        <input
                            type="number"
                            id="order-quantity"
                            name="quantity"
                            value="1"
                            min="1"
                            max="999"
                            step="1"
                            inputmode="numeric"
                            required
                            data-count
                            aria-describedby="quantity-hint quantity-error"
                        >
                        <button type="button" data-increase aria-label="Tambah jumlah pouch" aria-controls="order-quantity">+</button>
                    </div>
                    <span class="input-hint" id="quantity-hint">Satu rasa khas · Rp5.000 per pouch · Maksimal 999 pouch pada simulasi.</span>
                    <span class="field-error" id="quantity-error" data-error-for="order-quantity" aria-live="polite"></span>
                </div>

                <fieldset class="form-field payment-options" aria-describedby="payment-type-error">
                    <legend>Metode pembayaran <span aria-hidden="true">*</span></legend>
                    <label class="payment-option" for="payment-transfer">
                        <input type="radio" id="payment-transfer" name="payment_type" value="transfer_manual" required>
                        <span>
                            <strong>Transfer manual</strong>
                            <small>Instruksi pembayaran akan diberikan pengelola setelah pesanan dikonfirmasi.</small>
                        </span>
                    </label>
                    <label class="payment-option" for="payment-pickup">
                        <input type="radio" id="payment-pickup" name="payment_type" value="bayar_saat_ambil" required>
                        <span>
                            <strong>Bayar saat ambil</strong>
                            <small>Pembayaran dilakukan saat pesanan diambil sesuai arahan pengelola.</small>
                        </span>
                    </label>
                    <span class="field-error" id="payment-type-error" data-error-for="payment-type" aria-live="polite"></span>
                </fieldset>

                <div class="form-field">
                    <label for="order-notes">Catatan <span class="optional-label">(opsional)</span></label>
                    <textarea
                        id="order-notes"
                        name="order_notes"
                        rows="4"
                        maxlength="300"
                        placeholder="Contoh: Mohon kabari perkiraan waktu pengambilan"
                        aria-describedby="order-notes-hint order-notes-error"
                    ></textarea>
                    <span class="input-hint" id="order-notes-hint">Maksimal 300 karakter. Jangan cantumkan informasi pribadi yang tidak diperlukan.</span>
                    <span class="field-error" id="order-notes-error" data-error-for="order-notes" aria-live="polite"></span>
                </div>

                <button type="button" class="btn btn-accent form-submit" data-validate-order>Periksa Form (Simulasi)</button>
                <p class="form-status" data-form-message role="status" aria-live="polite" tabindex="-1" hidden></p>
                <p class="form-explainer"><strong>Penting:</strong> Tidak ada data yang dikirim atau disimpan. Pemeriksaan hanya berlangsung di browser, tidak membuat pesanan, dan bukan konfirmasi bahwa pre-order berhasil.</p>
            </form>
        </section>

        <aside class="summary-panel" aria-labelledby="order-summary-title">
            <div class="summary-photo" role="img" aria-label="Ilustrasi Keripik Singkong 4 Saudara dalam kemasan konsep"></div>
            <span class="section-index light-index">RINGKASAN PRODUK</span>
            <h2 id="order-summary-title">Keripik Singkong<br>4 Saudara</h2>
            <p class="summary-flavour">Satu produk · Satu rasa khas</p>
            <div class="summary-line"><span>Harga / pouch</span><strong>Rp5.000</strong></div>
            <div class="summary-line"><span>Jumlah</span><strong><span data-summary-count>1</span> pouch</strong></div>
            <div class="summary-line"><span>Pembayaran</span><strong data-summary-payment>Belum dipilih</strong></div>
            <div class="summary-total"><span>Estimasi total</span><strong data-total aria-live="polite">Rp5.000</strong></div>
            <p>Ringkasan ini hanya pratinjau perhitungan antarmuka, bukan tagihan, konfirmasi pembayaran, atau alokasi kuota produksi.</p>
        </aside>
    </div>
</main>
@endsection
