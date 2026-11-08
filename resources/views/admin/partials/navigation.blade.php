<nav class="admin-nav" aria-label="Navigasi admin">
    <a href="{{ route('admin.dashboard') }}" @class(['nav-item', 'selected' => request()->routeIs('admin.dashboard')]) @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif><span aria-hidden="true">◫</span> Dashboard</a>
    <a href="{{ route('admin.batches.index') }}" @class(['nav-item', 'selected' => request()->routeIs('admin.batches.*')]) @if(request()->routeIs('admin.batches.*')) aria-current="page" @endif><span aria-hidden="true">▤</span> Batch produksi</a>
    @can('manage-batches')<a href="{{ route('admin.settings.edit') }}" @class(['nav-item', 'selected' => request()->routeIs('admin.settings.*')]) @if(request()->routeIs('admin.settings.*')) aria-current="page" @endif><span aria-hidden="true">⚙</span> Pengaturan usaha</a>@endcan
    <a class="nav-item secondary" href="{{ route('home') }}"><span aria-hidden="true">↗</span> Lihat website</a>
    <form method="post" action="{{ route('admin.logout') }}">@csrf<button class="nav-item logout" type="submit"><span aria-hidden="true">↪</span> Keluar</button></form>
</nav>
