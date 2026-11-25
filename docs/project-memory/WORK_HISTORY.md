# Riwayat Pekerjaan (Work History) — SaudaraOrder

Kronologi perjalanan pengembangan **SaudaraOrder** yang disusun berdasarkan riwayat commit Git, laporan mingguan ([minggu-03.md](../progress/minggu-03.md), [minggu-05.md](../progress/minggu-05.md), [minggu-06.md](../progress/minggu-06.md)), [PROGRESS.md](../PROGRESS.md), dan pekerjaan sesi terverifikasi.

> **Keterangan Verifikasi:** Tanggal dan hash commit hanya dicantumkan jika teramati langsung pada `git log` atau laporan progres. Perubahan yang masih berada di working tree ditandai sebagai *Uncommitted*.

---

## Ringkasan Fase Pengembangan

### Fase 1 — Analisis Kebutuhan & Inisialisasi Proyek (12 Oktober 2026)
- Repository GitHub `oelofficial97-coder/saudara-order` dibuat dan menerima instalasi awal Laravel 13.
- **Commit:** `5ee4995` (`2026-10-12`) — *Initial commit: Laravel 13 SaudaraOrder*.

### Fase 2 — Penyusunan PRD & Penyelarasan Spesifikasi (12 Okt & 6 Nov 2026)
- Penyusunan PRD awal untuk masalah pencatatan manual Keripik Singkong 4 Saudara.
- **Commit:** `e28aa81` (`2026-10-12`) — *docs: add SaudaraOrder PRD*.
- Paket Agentic Engineering lengkap (`SPEC.json`, `AGENTS.md`, `ARCHITECTURE.md`, `SECURITY.md`, `TESTING.md`, `TASKS.md`, `GUARDRAILS.md`, `DECISIONS.md`, draf `docs/review-drafts/`) disusun pada `2026-11-06` dan masuk pada commit `f37c6f3` (`2026-11-09`).

### Fase 3 — Perancangan Sistem & ERD (26 Oktober 2026)
- Rancangan basis data relasional 9 entitas (`users`, `products`, `production_batches`, `business_settings`, `orders`, `payment_proofs`, `cash_transactions`, `cancellation_requests`, `audit_logs`) beserta diagram ERD disimpan di [DATABASE_ERD.md](../DATABASE_ERD.md).
- **Commit:** `ce4fb07` (`2026-10-26`) — *update schema database*.

### Fase 4 — Pengembangan Fondasi Database (2 November 2026)
- Penerapan 9 tabel inti, foreign key `RESTRICT`, 14 constraint `CHECK` khusus MySQL, `ProductSeeder` idempotent (produk tunggal Rp5.000/pouch), guard isolasi test SQLite `:memory:`, serta 19 test lulus (121 assertions).
- **Commit:** `553df85` (`2026-11-02`) — *feat: implement week 5 database foundation*.
- Tambahan migration bank nullable (`2026_11_02_000003_make_business_setting_bank_fields_nullable.php`) masuk dalam commit `f37c6f3` (`2026-11-09`).

### Fase 5 — Pengembangan Frontend Awal & Penyempurnaan Minggu Ke-4 (12–26 Oktober 2026)
- Pemasangan desain publik *Cinematic Premium* (`/`, `/pre-order`, `/cek-pesanan`), dokumentasi screenshot awal (`docs/screenshots/beranda-*.png`, `form-preorder.png`, `cek-pesanan.png`), serta penyempurnaan responsivitas & interaktivitas UI.
- **Commits:**
  - `34cb8c8` (`2026-10-12`) — *feat: implement cinematic SaudaraOrder frontend*
  - `3b59e89` (`2026-10-12`) — *docs: add week 3 website screenshots*
  - `4bae1fd` (`2026-10-12`) — *docs: add capstone week 3 progress report*
  - `79c9994`, `d7e87f3` (`2026-10-12`, `2026-10-18`) — update README awal
  - `b57fccd` (`2026-10-18`) — *feat: complete week 4 frontend improvements*
  - `fbe9001` (`2026-10-26`) — *feat: add frontend pages, layout, and styling for saudara order application*

### Fase 6 — Pengembangan Backend Admin, Batch & Pre-Order/Tracking (2–15 November 2026)
- **Minggu ke-6 (`2026-11-02`, commit `f37c6f3` tanggal `2026-11-09`)**:
  - Autentikasi admin (`/admin/login`, `/admin/logout`), rate limiter, `EnsureActiveAdmin`, `AdminResponseHeaders`, command `php artisan saudara:owner`.
  - `DashboardController`, `BatchController`, `BatchService` (konversi WIB ↔ UTC, penguncian kapasitas, 1 batch terbuka), `BusinessSettingController`, dan 44 PHPUnit test lulus.
- **Aktivasi Backend Pre-Order & Tracking (`2026-11-15`, Uncommitted di branch `feat/redesign-ui`)**:
  - Penambahan `StorePreorderRequest`, `OrderService` (`createPreorder`, `findForTracking`, `confirmOrder`, `updateStatus`, `cancelOrder`), `PreorderController`, `Admin\OrderController`, view `pages/order-success.blade.php`, `admin/orders/*`, badge pending di `AppServiceProvider`, serta `PreorderBackendTest` (total 64 PHPUnit test lulus).

### Fase 7 — Redesign UI/UX & Design System (13–15 November 2026, Uncommitted)
- Pembuatan panduan di `design-system/saudaraorder/MASTER.md` (`2026-11-13`) dan penyempurnaan stylesheet/Blade publik & admin (`public/saudara/css/site.css`, `admin.css`, `site.js`, `resources/views/*`) menggunakan pendekatan **UI/UX Pro Max** dengan tetap mempertahankan palet editorial utama (`#162016`, `#D2E59E`, `#FBF9F4`, `DM Serif Display` + `DM Sans`).
- Perubahan ini masih berada di working tree branch `feat/redesign-ui` (*Uncommitted*).

### Fase 8 & 9 — Integrasi Aiven MySQL & Konfigurasi Vercel Serverless (9–13 November 2026)
- Penambahan sertifikat CA Aiven (`database/certs/ca.pem`) dan resolver SSL di `config/database.php`.
- Konfigurasi runtime `vercel-php@0.8.0`, entrypoint `api/index.php` dengan direktori `/tmp/storage` writable, dan `.vercelignore`.
- **Commits:**
  - `f37c6f3` (`2026-11-09`) — *Prepare SaudaraOrder for deployment* (awal Dockerfile/Caddyfile)
  - `c38b2fb` (`2026-11-09`) — *Add Aiven CA certificate for deployment*
  - `cb26a4d` (`2026-11-09`) — *build: configure vercel-php runtime deployment for Laravel 13*
  - `bf9da28` (`2026-11-13`) — *fix(vercel): resolve runtime HTTP 500 and static asset routing* (branch `deploy-vercel-php` & `feat/redesign-ui`)

### Fase 10 — Troubleshooting Production HTTP 500, HTTPS & Static Assets (25 November 2026, Uncommitted)
- Investigasi staged & live logs membuktikan:
  1. Deployment Production lama (`dpl_BiVX1HhBxxBgm3mLFAtrqjpiKbDm`) masih mendahului perbaikan `bf9da28`.
  2. Setelah staged pertama, rute `/` sempat menyajikan isi mentah `public/index.php` karena `outputDirectory: "public"` + `handle: filesystem`, serta `/index.php` sempat dapat diakses mentah (`200`).
  3. Asset CSS Vite `404` karena script `"vercel"` di `composer.json` memicu `npm run build` kedua kali sehingga hash manifest PHP tidak cocok dengan static CDN.
  4. Seluruh endpoint ber-DB menghasilkan HTTP 500 dengan log `SAUDARA_EXCEPTION`: `SQLSTATE[HY000] [1045] Access denied for user 'avnadmin'@...` (SSL berhasil, tetapi `DB_PASSWORD` di Vercel env dan `vercel.production.env` sudah usang dibanding `.env` lokal yang terhubung ke Aiven).
  5. URL `route()`/`asset()` sempat memakai `http://` karena proxy Vercel belum dipercaya (`TrustProxies`).
- **Solusi diterapkan**: Perbarui `DB_PASSWORD` di Vercel Production env, perbaiki routing `^/$` & blokir `^/index\.php$` di `vercel.json`, tambahkan `$middleware->trustProxies(at: '*')` di `bootstrap/app.php`, jadikan script `"vercel"` di `composer.json` no-op echo, dan kecualikan `.agents/` & `design-system/` di `.vercelignore`. Staged berhasil & dipromosikan ke `https://saudara-order.vercel.app`.

### Fase 11 — Audit & Optimasi Performa Menyeluruh (25 November 2026, Uncommitted)
- **Temuan audit baseline**: Function Vercel berjalan di `iad1` (US East) sementara Aiven MySQL di Singapore (`146.190.93.221`, `max_connections = 46`), menghasilkan TTFB `/` ~6,0 detik dan `/pre-order` ~6,3 detik; ditambah query duplikat di `AppServiceProvider` (`Schema::hasTable` + `COUNT` 2x per halaman admin), query berulang di `OrderController`/`BatchController`/`PreorderController`, header `Cache-Control` di `vercel.json` diabaikan karena blok `"routes"`, gambar hero 146,9 KB tanpa varian mobile/preload, Google Fonts render-blocking, dan animasi `heroZoom` 14s + `backdrop-filter: blur(14px)`.
- **Optimasi diterapkan**: Pindah region Vercel ke `sin1`, aktifkan `SESSION_DRIVER=cookie` + `SESSION_SECURE_COOKIE=true` di Production, pindahkan `Cache-Control` ke dalam `"routes"` dengan `continue: true`, pangkas query duplikat Laravel, optimasi gambar WebP (`73,7 KB` desktop & `34,4 KB` mobile) + preload LCP + non-blocking fonts + penghapusan blur/animasi berat.
- **Hasil terukur**: TTFB warm turun ~11–13x (dari `3,66–6,33 dtk` ke `256–564 ms`), Lighthouse Speed Index `/` turun dari `11,7 dtk` ke `4,8 dtk`, `/cek-pesanan` naik skor `90 → 97`, `/admin/login` naik `92 → 99`. Dipromosikan ke `https://saudara-order.vercel.app` (`dpl_CYw5ZCRVmhe89X5DQAFiARphMtfJ`).

### Fase 12 — Dokumentasi Showcase README, Screenshot Live & Project Memory (26 November 2026, Uncommitted)
- Pembuatan ulang [README.md](../../README.md) menjadi GitHub Capstone Showcase lengkap dengan tabel fitur aktual (`Implemented`/`Partial`/`Planned`), diagram Mermaid User Flow & Arsitektur, panduan instalasi, dan roadmap.
- Pengambilan 5 screenshot aktual aplikasi live di `docs/screenshots/` (`homepage-live.png`, `product-live.png`, `preorder-live.png`, `tracking-live.png`, `admin-login.png`) serta panduan admin screenshots di [docs/screenshots/README.md](../screenshots/README.md).
- Pembentukan dan audit sinkronisasi sistem memori persisten di `docs/project-memory/` (7 dokumen inti; indeks navigasi diintegrasikan langsung ke section *Project Memory & Development History* pada [README.md](../../README.md)) terhadap seluruh `git diff --stat` (`29` berkas tracked berubah `+5.639/-522`, `17` entri untracked sejak commit `bf9da28`).
