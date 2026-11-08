@extends('admin.layout')
@section('title', 'Batch produksi')
@section('intro', 'Atur kapasitas dan penerimaan untuk setiap kelompok produksi.')
@section('actions')@can('manage-batches')<a class="button" href="{{ route('admin.batches.create') }}"><span aria-hidden="true">＋</span> Tambah batch</a>@endcan @endsection
@section('content')
@if(!$settingsReady)<div class="notice">Pengaturan usaha belum tersedia. @can('manage-batches')<a href="{{ route('admin.settings.edit') }}">Lengkapi informasi usaha</a> sebelum membuka batch.@else Hubungi pemilik untuk melengkapi informasi usaha.@endcan</div>@endif
<section class="panel table-panel"><div class="panel-heading"><div><h2>Semua batch</h2><p class="muted small">{{ $batches->total() }} batch tercatat · Hanya satu batch menerima pengajuan pada satu waktu.</p></div></div>
@if($batches->isEmpty())
    <div class="empty-state"><span aria-hidden="true">▤</span><h3>Mulai dari batch pertama</h3><p>Belum ada data produksi. Batch baru disimpan dengan penerimaan tertutup sampai pemilik membukanya.</p>@can('manage-batches')<a href="{{ route('admin.batches.create') }}" class="button">Tambah batch</a>@endcan</div>
@else
    <div class="table-scroll" tabindex="0" role="region" aria-label="Daftar batch produksi"><table><thead><tr><th scope="col">Batch</th><th scope="col">Kapasitas</th><th scope="col">Alokasi / sisa</th><th scope="col">Estimasi siap (WIB)</th><th scope="col">Status</th>@can('manage-batches')<th scope="col">Tindakan</th>@endcan</tr></thead><tbody>
    @foreach($batches as $batch)<tr><td><strong>{{ $batch->batch_name }}</strong><small class="mono">{{ $batch->batch_code }}</small>@if($activeBatchId === $batch->id)<small class="active-label">Batch aktif</small>@endif</td><td>{{ number_format($batch->capacity_pouches, 0, ',', '.') }} <small>pouch</small></td><td><strong>{{ number_format($batch->allocated_pouches, 0, ',', '.') }}</strong> / {{ number_format(max(0, $batch->capacity_pouches - $batch->allocated_pouches), 0, ',', '.') }}<small>teralokasi / tersedia</small></td><td>{{ $batch->estimated_ready_at?->setTimezone('Asia/Jakarta')->format('d M Y') ?? 'Belum ditentukan' }}<small>{{ $batch->estimated_ready_at?->setTimezone('Asia/Jakarta')->format('H:i') }}</small></td><td><span class="badge {{ $batch->is_open_for_orders ? 'open' : '' }}">{{ $batch->completed_at ? 'Selesai' : ($batch->is_open_for_orders ? 'Terbuka' : 'Tertutup') }}</span>@if($batch->started_at)<small>Produksi dimulai</small>@endif</td>
        @can('manage-batches')<td><div class="row-actions">@if(!$batch->completed_at)<a class="button compact secondary-button" href="{{ route('admin.batches.edit', $batch) }}">Edit</a><form method="post" action="{{ route($batch->is_open_for_orders ? 'admin.batches.close' : 'admin.batches.open', $batch) }}">@csrf<button class="button compact {{ $batch->is_open_for_orders ? 'secondary-button' : '' }}" type="submit">{{ $batch->is_open_for_orders ? 'Tutup penerimaan' : 'Buka penerimaan' }}</button></form>@else<span class="muted">Arsip produksi</span>@endif</div></td>@endcan
    </tr>@endforeach
    </tbody></table></div>
    <div class="pagination">{{ $batches->links('admin.partials.pagination') }}</div>
@endif
</section><p class="footnote">Kapasitas terkunci setelah produksi dimulai. Selesaikan keputusan pengajuan menunggu konfirmasi sebelum menutup batch. Riwayat batch tidak dihapus.</p>
@endsection
