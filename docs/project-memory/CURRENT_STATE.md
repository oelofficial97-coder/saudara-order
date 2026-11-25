# Kondisi Proyek Saat Ini (Current State) — SaudaraOrder

**Terakhir Diperbarui:** 26 November 2026, 00:48 WIB (`Asia/Jakarta`)

---

## 1. Status Git & Working Tree

| Komponen | Nilai Aktual |
|---|---|
| **Branch Aktif** | `feat/redesign-ui` |
| **Branch Lain** | `deploy-vercel-php` (di `bf9da28`), `main` & `origin/main` (di `c38b2fb`) |
| **Commit `HEAD`** | `bf9da28` — *fix(vercel): resolve runtime HTTP 500 and static asset routing* (13 Nov 2026) |
| **Status Commit** | **Working tree kotor (belum di-commit)** — `29` berkas tracked berubah (`+5.639 / -522` baris, termasuk penghapusan `app-DA3HUP3q.css`) dan `17` entri untracked. |

### Ringkasan Perubahan yang Belum Di-commit (`git diff --stat` & `git status --short`)
- **Backend & Routing:**
  - `routes/web.php`: beralih dari 3 `Route::view()` statis menjadi 5 route dinamis `PreorderController` (`home`, `create`, `store`, `success`, `tracking`).
  - `routes/admin.php`: menambah 5 route `OrderController` (`index`, `show`, `confirm`, `advance`, `cancel`) di bawah middleware `['auth', 'admin.active']` (dapat diakses oleh `pemilik` dan `pengelola` aktif).
  - Komponen baru/ubah: `PreorderController.php`, `Admin/OrderController.php`, `Admin/BatchController.php`, `StorePreorderRequest.php`, `OrderService.php`, `AppServiceProvider.php`, `bootstrap/app.php`, `tests/Feature/PreorderBackendTest.php`.
- **Frontend & Aset:** `public/saudara/css/site.css`, `admin.css`, `public/saudara/js/site.js`, `public/saudara/images/hero-keripik-konsep.webp` (`146.926 B -> 73.736 B`), `hero-keripik-konsep-sm.webp` (`34.404 B`), `public/build/assets/app-DdOQJ5ke.css` (menggantikan `app-DA3HUP3q.css`), `public/build/manifest.json`, `resources/css/app.css`, `resources/views/layouts/saudara.blade.php`, `resources/views/pages/*` (termasuk `order-success.blade.php`), `resources/views/admin/*` (termasuk `admin/orders/`), dan `design-system/`.
- **Konfigurasi Deployment:** `vercel.json` (`regions: ["sin1"]`, `SESSION_DRIVER=cookie`, inline `Cache-Control`, blokir `/index.php`), `composer.json` (no-op script `vercel`), `.vercelignore` (mengecualikan `.agents`, `design-system`).
- **Dokumentasi & Tooling Lokal:** `README.md` (termasuk navigasi *Project Memory & Development History*), `docs/PROGRESS.md` (menambah baris `2026-11-15`, sementara bagian `In Progress`/`Next Up` masih peninggalan `2026-11-06`), `docs/screenshots/README.md`, 5 screenshot live (`homepage-live.png`, `product-live.png`, `preorder-live.png`, `tracking-live.png`, `admin-login.png`), 7 dokumen di `docs/project-memory/` (`README.md` internal sudah digabung dan dihapus), serta `.agents/`.

> **Peringatan Penting:** Deployment Production Vercel saat ini memuat kode terbaru dari working tree lokal (via `vercel deploy --prod`), **namun repository GitHub (`origin/main`) masih berada pada `c38b2fb`**. Perubahan lokal belum di-commit atau di-push ke GitHub.

---

## 2. Status Deployment Production Vercel

| Atribut | Nilai Aktual |
|---|---|
| **URL Publik** | `https://saudara-order.vercel.app` |
| **Deployment Aktif** | `https://saudara-order-1g69oenn3-leos-projects-d7c8a14f.vercel.app` (`dpl_CYw5ZCRVmhe89X5DQAFiARphMtfJ`) |
| **Team / Project** | `leos-projects-d7c8a14f` / `saudara-order` (`prj_O1jsFBF9N4fQxim8lLVX4Lml4Trw`) |
| **Status** | `● Ready` (dipromosikan ke Production pada 25 Nov 2026) |
| **Runtime & Region** | `vercel-php@0.8.0` (PHP `8.4.27`), Serverless Function di **`sin1` (Singapore)** |
| **HTTP 500** | **Teratasi** (0 error pada pengecekan endpoint & Vercel logs terbaru) |

---

## 3. Kondisi Database Aiven MySQL

- **Host:** `saudara-order-mysql-leoprayogasaputra97-1e95.k.aivencloud.com` (IP `146.190.93.221`, DigitalOcean **Singapore**)
- **Versi & Batas:** MySQL `8.4.11`, `max_connections = 46`, koneksi wajib TLS/SSL menggunakan `database/certs/ca.pem`.
- **Struktur Tabel (17 tabel via `php artisan db:show`):** `audit_logs`, `business_settings`, `cache`, `cache_locks`, `cancellation_requests`, `cash_transactions`, `failed_jobs`, `job_batches`, `jobs`, `migrations`, `orders`, `password_reset_tokens`, `payment_proofs`, `production_batches`, `products`, `sessions`, `users`.
- **Catatan Operasional:** Saat screenshot live diambil pada 26 Nov 2026, tercatat batch aktif `Batch 01 - November 2026` dengan sisa kuota `137 pouch` di Production. Tidak ada modifikasi data produksi oleh AI.

---

## 4. Environment Variables (Nama Variabel Saja — Tanpa Nilai)

Variabel yang tercatat aktif pada Production Vercel (`vercel env ls` + `vercel.json`):
- **Aplikasi & URL:** `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL`, `LOG_CHANNEL`
- **Database & SSL:** `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (diperbarui pada 25 Nov 2026 agar sinkron dengan Aiven aktif), `MYSQL_ATTR_SSL_CA`
- **Session, Cache, Queue, Storage Serverless:** `SESSION_DRIVER` (`cookie`), `SESSION_SECURE_COOKIE` (`true`), `SESSION_LIFETIME`, `CACHE_STORE` (`database`), `QUEUE_CONNECTION` (`sync`), `LARAVEL_STORAGE_PATH`, `VIEW_COMPILED_PATH`, `APP_CONFIG_CACHE`, `APP_EVENTS_CACHE`, `APP_PACKAGES_CACHE`, `APP_ROUTES_CACHE`, `APP_SERVICES_CACHE`

*(Catatan: File lokal `vercel.production.env` masih menyimpan password lama yang ditolak Aiven; jangan gunakan berkas tersebut untuk sinkronisasi ke Vercel. `.env` lokal adalah konfigurasi aktif pengembang.)*

---

## 5. Status Fitur Aplikasi

- **Sudah Berjalan & Terverifikasi (`Implemented`):**
  - Landing page, katalog produk tunggal Rp5.000/pouch, tampilan kuota batch aktif (`/`)
  - Formulir pre-order publik dengan validasi server, idempotency, snapshot harga, status `menunggu_konfirmasi` (`/pre-order`)
  - Halaman sukses & tracking privat 5 tahap berbasis token SHA-256 (`/pesanan-berhasil/{order_code}`, `/cek-pesanan`)
  - Login admin ber-rate-limit, logout, middleware akun aktif (`/admin/login`), dashboard (`/admin/dashboard`), manajemen batch & kapasitas (`/admin/batches*`), pengaturan usaha (`/admin/settings`), manajemen pesanan (`/admin/orders*`: konfirmasi atomik, ubah status, batal + pulihkan kuota, `audit_logs`).
- **Sebagian (`Partial`) / Terencana (`Planned`):**
  - Filter/pencarian UI dan input pesanan manual (`whatsapp`, `langsung`, `titipan`) di `/admin/orders` (`Partial`)
  - Pencatatan kas tunai terpisah (`cash_transactions`), bukti bayar (`payment_proofs`), pengajuan batal pelanggan (`cancellation_requests`) (`Partial` — baru tabel & model)
  - Pilihan pengantaran vs pengambilan + alamat kondisional (`Planned`)
  - Rekap & ekspor laporan terfilter F6 (`Planned`).

---

## 6. Hasil Pengujian & Performa Terbaru

### Pengujian Lokal (Sesudah Perubahan Kode Terakhir, 25 Nov 2026)
- `php artisan test`: **64 passed (507 assertions)** dalam 14,66 detik (SQLite `:memory:`).
- `php vendor/bin/pint --test`: **passed**.
- `npm run build`: **sukses** (`app-DdOQJ5ke.css`, `app-DMsN-rLE.js`, `manifest.json`).

### Pengujian Live Production (`https://saudara-order.vercel.app`, 25 Nov 2026)

| Endpoint | HTTP | TTFB Sebelum (3 Run) | TTFB Sesudah (3 Run) | Lighthouse Score (Sesudah) |
|---|---:|---|---|---:|
| `/up` | `200` | 467–686 ms | 2.416 ms (cold) / 328–341 ms (warm) | — |
| `/` | `200` | 5.496–6.055 ms | **385–475 ms** | **78** (SI 4,8 s, CLS 0) |
| `/pre-order` | `200` | 5.520–6.328 ms | **367–564 ms** | **91** (FCP/LCP 1,2 s, SI 2,8 s) |
| `/cek-pesanan` | `200` | 3.976–5.198 ms | **335–393 ms** | **97** (FCP/LCP 1,1 s, SI 2,2 s) |
| `/admin/login` | `200` | 3.658–4.135 ms | **256–406 ms** | **99** (FCP/LCP 1,3 s, SI 2,2 s) |
| `/admin/dashboard` (guest) | `302` → `/admin/login` | 3.850–4.590 ms | **289–373 ms** | — |
| `/index.php` | `403` | `403` (diblokir) | `403` (`X-Vercel-Mitigated: deny`) | — |
| `/saudara/css/site.css` | `200` | 225–535 ms | `Cache-Control: max-age=604800` | — |

---

## 7. Kendala / Hal yang Belum Terverifikasi

1. **Perubahan Git belum di-commit & belum di-push ke GitHub**: Seluruh pekerjaan sejak 15 Nov s.d. 26 Nov 2026 masih berada di working tree branch `feat/redesign-ui`.
2. **Akses authenticated `/admin/dashboard`, `/admin/batches`, `/admin/orders`, `/admin/settings` di Production** belum diuji secara langsung melalui browser (karena tidak membuat akun dummy di DB Production), baru diverifikasi redirect keamanannya (`302` → `/admin/login`) serta query/HTTP-nya secara lokal & di PHPUnit.
3. **5 Screenshot Admin belum tersedia**: `admin-dashboard.png`, `admin-batches.png`, `admin-batch-capacity.png`, `admin-settings.png`, dan `admin-orders.png` perlu diambil manual setelah login admin.
4. **Uji konkurensi MySQL 2 koneksi (`T-006`) & Browser E2E (`T-012`/`T-019`)** belum dijalankan pada database testing MySQL terpisah (baru teruji di SQLite `:memory:`).
5. **Dokumentasi `docs/PROGRESS.md` & draf `docs/review-drafts/`** belum disinkronkan dengan progres pasca-15 Nov 2026 dan `README.md` baru.
