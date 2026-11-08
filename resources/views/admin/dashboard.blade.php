@extends('admin.layout')
@section('title', 'Dashboard')
@section('eyebrow', 'RINGKASAN USAHA')
@section('intro', 'Pantau kesiapan produksi, satu batch pada satu waktu.')
@section('actions')<a class="button secondary-button" href="{{ route('admin.batches.index') }}">Lihat batch <span aria-hidden="true">↗</span></a>@endsection
@section('content')
<div class="metrics">
    <article class="metric"><span>Produk aktif</span><strong>{{ number_format($productCount, 0, ',', '.') }}</strong><small>Satu produk, satu rasa khas</small></article>
    <article class="metric"><span>Kapasitas batch aktif</span><strong>{{ $activeBatch ? number_format($activeBatch->capacity_pouches, 0, ',', '.') : '—' }}</strong><small>{{ $activeBatch ? 'pouch per batch' : 'Belum ada batch aktif' }}</small></article>
    <article class="metric"><span>Kuota dialokasikan</span><strong>{{ $activeBatch ? number_format($activeBatch->allocated_pouches, 0, ',', '.') : '—' }}</strong><small>{{ $activeBatch ? 'pouch terikat pesanan' : 'Belum ada alokasi batch aktif' }}</small></article>
    <article class="metric accent"><span>Sisa kuota</span><strong>{{ $remaining !== null ? number_format($remaining, 0, ',', '.') : '—' }}</strong><small>{{ $remaining !== null ? 'pouch tersedia pada batch aktif' : 'Buka batch untuk memulai' }}</small></article>
</div>
<div class="dashboard-grid">
    <section class="panel"><div class="panel-heading"><h2>Penerimaan pre-order</h2><span class="badge {{ $activeBatch?->is_open_for_orders && !$activeBatch?->completed_at && $remaining > 0 && $activeBatch?->product?->is_active ? 'open' : '' }}">{{ $activeBatch?->is_open_for_orders && !$activeBatch?->completed_at && $remaining > 0 && $activeBatch?->product?->is_active ? 'Terbuka' : 'Tidak menerima' }}</span></div>
        @if($activeBatch)
            <h3>{{ $activeBatch->batch_name }}</h3><p class="muted mono">{{ $activeBatch->batch_code }}</p>
            <dl class="facts"><div><dt>Estimasi siap</dt><dd>{{ $activeBatch->estimated_ready_at?->setTimezone('Asia/Jakarta')->format('d M Y, H:i').' WIB' }}</dd></div><div><dt>Terpakai</dt><dd>{{ number_format($activeBatch->allocated_pouches, 0, ',', '.') }} dari {{ number_format($activeBatch->capacity_pouches, 0, ',', '.') }} pouch</dd></div></dl>
            <progress aria-label="Penggunaan kuota batch aktif" value="{{ $activeBatch->allocated_pouches }}" max="{{ $activeBatch->capacity_pouches }}"></progress>
        @else
            <div class="empty-state"><span aria-hidden="true">◌</span><h3>Belum ada batch aktif</h3><p>Penerimaan pre-order belum dibuka. Siapkan batch dan tentukan kapasitas sebelum membuka penerimaan.</p>@can('manage-batches')<a class="button" href="{{ route('admin.batches.create') }}">Buat batch pertama</a>@endcan</div>
        @endif
    </section>
    <aside class="panel soft"><p class="eyebrow">CATATAN OPERASIONAL</p><h2>Kuota dipastikan<br>saat konfirmasi.</h2><p>Pesanan menunggu konfirmasi belum mengurangi kuota. Kuota teralokasi tetap terikat hingga pesanan selesai.</p><hr><p class="small">Halaman publik masih berupa prototype. Membuka batch di sini belum mengaktifkan pengajuan pesanan melalui website.</p>
        @if(!$settingsReady)<p class="small">Informasi usaha belum dilengkapi.</p>@can('manage-batches')<a class="text-link" href="{{ route('admin.settings.edit') }}">Lengkapi pengaturan usaha →</a>@endcan @endif
    </aside>
</div>
<section class="panel"><div class="panel-heading"><h2>Batch terbaru</h2><a class="text-link" href="{{ route('admin.batches.index') }}">Lihat semua →</a></div>
    @forelse($recentBatches as $batch)<div class="recent-row"><div><strong>{{ $batch->batch_name }}</strong><small>{{ $batch->batch_code }}</small></div><span class="badge {{ $batch->is_open_for_orders ? 'open' : '' }}">{{ $batch->completed_at ? 'Selesai' : ($batch->is_open_for_orders ? 'Terbuka' : 'Tertutup') }}</span><span>{{ number_format($batch->capacity_pouches, 0, ',', '.') }} pouch</span></div>@empty<p class="muted">Belum ada batch produksi. Data akan muncul setelah batch dibuat.</p>@endforelse
</section>
@endsection
