@if($paginator->hasPages())
<nav aria-label="Halaman daftar batch">
    @if($paginator->onFirstPage())<span aria-disabled="true" class="muted">← Sebelumnya</span>@else<a class="text-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">← Sebelumnya</a>@endif
    <span>Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</span>
    @if($paginator->hasMorePages())<a class="text-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya →</a>@else<span aria-disabled="true" class="muted">Berikutnya →</span>@endif
</nav>
@endif
