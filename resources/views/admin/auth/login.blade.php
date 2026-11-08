<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow"><meta name="theme-color" content="#173c32">
    <title>Masuk Admin · SaudaraOrder</title>
    <link rel="stylesheet" href="{{ asset('saudara/css/admin.css') }}">
</head>
<body class="login-body">
<main class="login-shell">
    <section class="login-story"><a class="brand" href="{{ route('home') }}">4Saudara<span>.</span><small>KERIPIK SINGKONG</small></a><div><span class="eyebrow">DARI DAPUR, DENGAN HATI</span><h1>Satu ruang.<br>Setiap persiapan.</h1><p>Kelola batch produksi dan siapkan setiap pouch untuk momen yang sederhana.</p></div><span class="story-footer">SAUDARAORDER · RUANG PENGELOLA</span></section>
    <section class="login-panel"><div class="login-form"><p class="eyebrow">SELAMAT DATANG KEMBALI</p><h2>Masuk ke ruang kerja.</h2><p class="muted">Gunakan akun pemilik atau pengelola yang aktif.</p>
        @if(session('status'))<div class="notice success" role="status">{{ session('status') }}</div>@endif
        @if($errors->any())<div class="notice error" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
        <form method="post" action="{{ route('admin.login.store') }}">
            @csrf
            <div class="field"><label for="username">Username</label><input id="username" name="username" type="text" autocomplete="username" value="{{ old('username') }}" maxlength="50" required autofocus @error('username') aria-invalid="true" @enderror></div>
            <div class="field"><label for="password">Kata sandi</label><input id="password" name="password" type="password" autocomplete="current-password" maxlength="255" required @error('password') aria-invalid="true" @enderror></div>
            <button type="submit" class="button wide">Masuk <span aria-hidden="true">→</span></button>
        </form>
        <p class="login-help">Akses khusus tim 4 Saudara. Hubungi pemilik jika Anda memerlukan bantuan akun.</p>
        <a class="text-link" href="{{ route('home') }}">← Kembali ke website</a>
    </div></section>
</main>
</body>
</html>
