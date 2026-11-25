# Catatan Keputusan Teknis & Arsitektur — SaudaraOrder

Dokumen ini melengkapi ADR resmi pada [docs/DECISIONS.md](../DECISIONS.md) (`ADR-001` s.d. `ADR-006`) dengan konteks teknis terbaru hasil implementasi, troubleshooting Vercel/Aiven, optimasi performa, dan redesain UI/UX. Untuk aturan bisnis dan batas cakupan yang disepakati, lihat [SPEC.json](../../SPEC.json).

---

### DEC-001: Penggunaan PHP 8.3+ & Laravel 13 sebagai Fondasi Backend
- **Status:** Accepted (lihat `ADR-003` di [docs/DECISIONS.md](../DECISIONS.md)).
- **Keputusan:** Mempertahankan Laravel `^13.0` (`composer.json`) dengan arsitektur Form Request, Service transaksi (`OrderService`, `BatchService`), Gate otorisasi, dan Blade.
- **Alasan:** Proyek telah memiliki struktur Laravel 13, autentikasi admin, 9 entitas Eloquent, dan 64 suite PHPUnit yang stabil. Membangun ulang berisiko menunda tenggat Capstone dan merusak fitur yang sudah teruji.
- **Alternatif yang Dipertimbangkan:** Node.js/Express atau menulis ulang tanpa framework; ditolak karena membuang fondasi yang sudah berjalan.
- **Konsekuensi:** Deployment serverless harus menyesuaikan mekanisme path writable (`/tmp/storage`) dan kompatibilitas runtime PHP `8.4.x` (`vercel-php`).

### DEC-002: Pemilihan Aiven MySQL (Singapore) dengan Koneksi TLS/SSL CA
- **Status:** Accepted (`ADR-003`, `ADR-005`).
- **Keputusan:** Menggunakan layanan terkelola Aiven MySQL `8.4.11` di region Singapore (`146.190.93.221`) dengan verifikasi sertifikat CA `database/certs/ca.pem` yang dibundel ke fungsi serverless (`includeFiles: "database/certs/**"` di `vercel.json`) dan disalin ke `/tmp/storage/certs/ca.pem` pada `api/index.php`, serta resolved lewat helper `$resolveSslCa` di [config/database.php](../../config/database.php).
- **Alasan:** Mempertahankan skema MySQL `8.4` beserta constraint `CHECK`, `InnoDB`, dan row-locking `SELECT ... FOR UPDATE` yang sudah dirancang di ERD tanpa memindahkan database ke Postgres/Supabase.
- **Alternatif yang Dipertimbangkan:** PostgreSQL/Supabase (ditolak di `ADR-003` karena mengubah constraint dan SQLite/MySQL parity) atau MySQL tanpa TLS (ditolak oleh kebijakan keamanan Aiven dan [SECURITY.md](../SECURITY.md)).
- **Konsekuensi:** Batas `max_connections` pada paket Aiven adalah **46**, sehingga koneksi `PDO::ATTR_PERSISTENT` **tidak diaktifkan** dan query publik tidak perlu membuka koneksi berlebihan.

### DEC-003: Pemilihan Vercel (`vercel-php@0.8.0`) di Region `sin1` vs Render Docker
- **Status:** Accepted untuk P0 & Production saat ini (`ADR-005`).
- **Keputusan:** Menggunakan Vercel dengan community runtime `vercel-php@0.8.0` dan memindahkan region fungsi serverless dari default `iad1` ke **`sin1` (Singapore)** di `vercel.json`.
- **Alasan:** Kebutuhan link publik Vercel sebagai target awal hosting dan kolokasi geografis langsung dengan Aiven MySQL di Singapore. Pemindahan ke `sin1` memangkas round-trip DB dari ~250 ms/query menjadi ~1–3 ms/query.
- **Alternatif yang Dipertimbangkan:** Render Docker (`deploy/docker/Dockerfile.vercel` tersedia di branch pengembangan). Tetap dijadikan **alternatif bersyarat** bila suatu saat dibutuhkan zero cold-start, worker antrean daemon, atau koneksi DB pooled.
- **Konsekuensi:** Terbatas 1 region di paket Hobby Vercel (`"regions": ["sin1"]`), filesystem root read-only (wajib `/tmp/storage`), serta cold start PHP-CLI `php -S` (~2,4 dtk pada `/up` saat container tidur).

### DEC-004: Blade + CSS/JS Kustom + Vite & Tailwind CSS (Tanpa SPA Rewrite)
- **Status:** Accepted (`ADR-002`).
- **Keputusan:** Mempertahankan Blade server-side rendering dengan stylesheet terpisah (`public/saudara/css/site.css`, `admin.css`) dan JavaScript ringan (`site.js`), sembari tetap mempertahankan Vite `^7.0.7` + Tailwind `^4.0.0` (`resources/css/app.css`). Pada [composer.json](../../composer.json), script `"vercel"` dijadikan no-op echo agar Vite hanya di-build satu kali oleh Vercel build step (mencegah ketidakcocokan hash CSS antara `manifest.json` lambda dan CDN statis).
- **Alasan:** Menjaga kesederhanaan, kecepatan rendering HTML langsung dari server, serta kompatibilitas tes existing (`FrontendPagesTest`, `AdminWorkspaceTest`).
- **Alternatif yang Dipertimbangkan:** SPA (React/Vue) atau menulis ulang seluruh template ke utility class Tailwind; ditolak untuk menjaga stabilitas dan batas waktu pengerjaan individu.
- **Konsekuensi:** Aset di `public/saudara/` memerlukan cache-busting `?v=filemtime(...)` di Blade agar aman diberi header `Cache-Control: max-age=604800` di Vercel.

### DEC-005: Adopsi UI/UX Pro Max dengan Palet Editorial Eksisting
- **Status:** Implemented (Uncommitted di branch `feat/redesign-ui`).
- **Keputusan:** Menerapkan peningkatan tata letak, ritme spasi 8dp, hierarki tipografi (`DM Serif Display` + `DM Sans`), state form/modal/timeline yang jelas, serta desain responsif memakai panduan UI/UX Pro Max sambil **tetap mempertahankan palet asli** (`#162016` Forest, `#D2E59E` Sage, `#FBF9F4` Paper, `#EFECE3` Cream, `#E6A855` Ochre) — bukan palet oranye generik `#EA580C` pada berkas referensi `design-system/saudaraorder/MASTER.md`.
- **Alasan:** Menjaga konsistensi identitas visual Keripik Singkong 4 Saudara dan aturan [DESIGN_SYSTEM.md](../DESIGN_SYSTEM.md) agar redesign terasa menyatu dengan workspace admin (`#173C32`).
- **Konsekuensi:** Developer/AI berikutnya tidak boleh menimpa CSS publik dengan warna `#EA580C`/`Outfit` dari `design-system/saudaraorder/MASTER.md`.

### DEC-006: Cookie Session Terenkripsi di Production + Database Cache untuk Rate Limiter
- **Status:** Implemented di Production (`vercel.json` + Vercel Production Env).
- **Keputusan:** Menggunakan `SESSION_DRIVER=cookie` dan `SESSION_SECURE_COOKIE=true` di environment Production Vercel, sementara `CACHE_STORE=database` dipertahankan untuk rate limiting login (`admin-login`).
- **Alasan:** Dengan `SESSION_DRIVER=database`, setiap halaman publik (`/`, `/pre-order`, `/cek-pesanan`, `/admin/login`) melakukan 2 query DB tambahan untuk membaca/menulis sesi, yang menghabiskan kuota `max_connections = 46` Aiven dan menambah TTFB. Cookie session Laravel 13 terenkripsi penuh dengan `APP_KEY` (AES-256-CBC + HMAC-SHA256, `HttpOnly`, `Secure`, `SameSite=Lax`). Penonaktifan akun admin tetap langsung memutus sesi karena middleware `EnsureActiveAdmin` memeriksa `$request->user()?->fresh()` ke MySQL di setiap request admin.
- **Alternatif yang Dipertimbangkan:** Mempertahankan `SESSION_DRIVER=database` di Production; ditolak demi efisiensi koneksi Aiven dan responsivitas halaman publik (`0` query DB pada `/cek-pesanan` & `/admin/login`).
- **Konsekuensi:** Payload flash session harus tetap ringkas (< 4 KB batas cookie browser), dan `APP_KEY` Production wajib dijaga kerahasiaannya.

### DEC-007: Konfigurasi Keamanan Routing Vercel & Proxy HTTPS
- **Status:** Implemented di Production (`vercel.json` di branch pengembangan + [bootstrap/app.php](../../bootstrap/app.php)).
- **Keputusan:** Menempatkan `^/$` sebelum `handle: filesystem`, memblokir `^/index\.php$` dengan status `404` (terblokir `403` di edge), memindahkan `Cache-Control` ke dalam `"routes"` (`continue: true`), memblokir folder sensitif (`.env*`, `vendor`, `storage`, `config`, `database`, dll.), mengecualikan `.agents` dan `design-system` di `.vercelignore`, serta mengaktifkan `$middleware->trustProxies(at: '*')`.
- **Alasan:** Pada percobaan staged sebelumnya, `handle: filesystem` melayani `public/index.php` sebagai teks PHP mentah pada `/` dan `/index.php`, blok top-level `"headers"` diabaikan karena adanya `"routes"`, dan helper `route()`/`asset()` menghasilkan URL `http://` (Mixed Content) karena proxy Vercel belum dipercaya.
- **Konsekuensi:** Seluruh URL HTML & redirect Production menggunakan `https://`, aset statis tercache optimal di CDN/browser, dan kode sumber PHP tidak pernah terbuka publik.

### DEC-008: Desain Database 9 Entitas & Perlindungan Integritas
- **Status:** Accepted ([Minggu-05](../progress/minggu-05.md), [DATABASE_ERD.md](../DATABASE_ERD.md)).
- **Keputusan:** Menggunakan foreign key `RESTRICT` pada relasi historis (bukti, kas, pembatalan, audit), kolom `singleton_key` dengan `UNIQUE` + `CHECK = 1` pada `products` dan `business_settings`, snapshot harga di `orders`, pemisahan status pesanan vs pembayaran, model append-only `AuditLog`/`CashTransaction` di event Eloquent, serta pemisahan pengujian terisolasi pada SQLite `:memory:` (`tests/TestCase.php`).
- **Alasan:** Mencegah terhapusnya riwayat transaksi, menjamin histori harga tidak berubah jika harga produk diperbarui, dan melindungi database pengembangan/produksi dari reset oleh PHPUnit.
- **Konsekuensi:** Pengujian locking konkurensi InnoDB MySQL tidak bisa dibuktikan hanya lewat SQLite `:memory:` dan tetap memerlukan database testing MySQL terpisah (`T-006`).
