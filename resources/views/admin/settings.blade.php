@extends('admin.layout')
@section('title', 'Pengaturan usaha')
@section('intro', 'Lengkapi informasi resmi sebelum membuka penerimaan batch.')
@section('content')
<section class="panel settings-panel"><h2>Informasi resmi 4 Saudara</h2><p class="muted">Gunakan data yang sudah dikonfirmasi pemilik. Data ini tidak otomatis mengubah halaman publik prototype.</p>
<form method="post" action="{{ route('admin.settings.update') }}">@csrf @method('put')
    <div class="field"><label for="business_name">Nama usaha</label><input id="business_name" name="business_name" maxlength="100" required value="{{ old('business_name', $settings->business_name ?? 'Keripik Singkong 4 Saudara') }}"></div>
    <div class="field"><label for="whatsapp_number">Nomor WhatsApp resmi</label><input id="whatsapp_number" name="whatsapp_number" type="tel" maxlength="15" required value="{{ old('whatsapp_number', $settings->whatsapp_number) }}"><small>Awali dengan 62, tanpa spasi atau tanda +.</small></div>
    <div class="field"><label for="pickup_address">Alamat pengambilan</label><textarea id="pickup_address" name="pickup_address" maxlength="2000" rows="3" required>{{ old('pickup_address', $settings->pickup_address) }}</textarea></div>
    <div class="field"><label for="pickup_maps_url">Tautan lokasi (opsional)</label><input id="pickup_maps_url" name="pickup_maps_url" type="url" maxlength="500" value="{{ old('pickup_maps_url', $settings->pickup_maps_url) }}"><small>Gunakan tautan HTTPS.</small></div>
    <div class="form-section"><h3>Transfer manual</h3><p class="muted small">Informasi rekening untuk persiapan operasional. Belum ada pemrosesan pembayaran pada tahap ini.</p></div>
    <div class="field"><label for="bank_name">Nama bank</label><input id="bank_name" name="bank_name" maxlength="50" value="{{ old('bank_name', $settings->bank_name) }}"></div>
    <div class="field"><label for="bank_account_number">Nomor rekening resmi</label><input id="bank_account_number" name="bank_account_number" inputmode="numeric" maxlength="50" value="{{ old('bank_account_number', $settings->bank_account_number) }}"></div>
    <div class="field"><label for="bank_account_name">Nama pemilik rekening</label><input id="bank_account_name" name="bank_account_name" maxlength="100" value="{{ old('bank_account_name', $settings->bank_account_name) }}"></div>
    <button class="button" type="submit">Simpan pengaturan</button>
</form></section>
@endsection
