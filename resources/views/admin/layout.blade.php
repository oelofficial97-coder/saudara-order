<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <meta name="theme-color" content="#173c32">
    <title>@yield('title', 'Dashboard') · Admin SaudaraOrder</title>
    <link rel="stylesheet" href="{{ asset('saudara/css/admin.css') }}">
</head>
<body>
<a class="skip" href="#main">Lewati ke konten</a>
<div class="admin-shell">
    <aside class="sidebar">
        <a class="brand" href="{{ route('admin.dashboard') }}">4Saudara<span>.</span><small>RUANG PENGELOLA</small></a>
        <p class="nav-label">OPERASIONAL USAHA</p>
        @include('admin.partials.navigation')
        <div class="sidebar-note"><span class="seed-mark">✳</span><p>Satu rasa khas.<br>Kelola dengan sepenuh hati.</p></div>
    </aside>
    <div class="workspace">
        <header class="topbar">
            <div><span class="eyebrow">SAUDARAORDER</span><span class="topbar-sub">Keripik Singkong 4 Saudara</span></div>
            <div class="account"><span class="avatar" aria-hidden="true">{{ mb_substr(auth()->user()->name, 0, 1) }}</span><div><strong>{{ auth()->user()->name }}</strong><small>{{ ucfirst(auth()->user()->role) }}</small></div></div>
        </header>
        <details class="mobile-nav"><summary>Menu admin <span aria-hidden="true">☰</span></summary>@include('admin.partials.navigation')</details>
        <main id="main" class="main">
            <div class="page-heading"><div><p class="eyebrow">@yield('eyebrow', 'OPERASIONAL')</p><h1>@yield('title', 'Dashboard')</h1><p class="intro">@yield('intro')</p></div>@yield('actions')</div>
            @if(session('status'))<div class="notice success" role="status">{{ session('status') }}</div>@endif
            @if($errors->any())
                <div class="notice error" role="alert"><strong>Perubahan belum disimpan.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            @yield('content')
        </main>
        <footer class="admin-footer">4 Saudara <span>·</span> Ruang kerja pengelola <span>·</span> Waktu ditampilkan dalam WIB</footer>
    </div>
</div>
</body>
</html>
