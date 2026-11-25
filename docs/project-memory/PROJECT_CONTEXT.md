# Konteks Proyek — SaudaraOrder

Dokumen ini merangkum identitas, konteks bisnis, aturan domain, cakupan produk, arsitektur, dan daftar fitur aktual **SaudaraOrder** berdasarkan [SPEC.json](../../SPEC.json), [PRD.md](../PRD.md), [ARCHITECTURE.md](../ARCHITECTURE.md), [SECURITY.md](../SECURITY.md), dan audit langsung terhadap source code Laravel.

---

## 1. Identitas Proyek

| Atribut | Nilai |
|---|---|
| **Nama Aplikasi** | SaudaraOrder |
| **Slug / Repository** | `oelofficial97-coder/saudara-order` |
| **Domain Produksi** | `https://saudara-order.vercel.app` |
| **Jenis Proyek** | Capstone Project — Teknik Informatika (Web App, level proses *Loop*, 1 mahasiswa) |
| **Objek Usaha** | UMKM **Keripik Singkong 4 Saudara** |
| **Status Saat Ini** | Aplikasi live di Vercel (`sin1`) + Aiven MySQL Singapore; pengembangan bertahap menuju kelengkapan seluruh fitur Must/Should |

---

## 2. Latar Belakang Usaha & Permasalahan Awal

Usaha **Keripik Singkong 4 Saudara** memproduksi camilan tunggal khas berbasis pesanan (*made-to-order*) dengan perkiraan pengerjaan 1–3 hari. Sebelum sistem dibangun:

- Pencatatan pesanan masih mengandalkan ingatan dan catatan manual untuk pesanan dari WhatsApp, datang langsung, maupun perantara (*titipan*), sehingga pesanan berisiko terlewat atau tidak tersampaikan ke dapur produksi.
- Belum ada sistem terpusat untuk memantau antrean produksi, mengendalikan batas kapasitas batch, melacak status pesanan pelanggan secara privat, dan mencatat penyelesaian transaksi secara rapi.

---

## 3. Tujuan Sistem & Target Pengguna

### Tujuan Utama
1. Memusatkan pencatatan pesanan dari berbagai saluran (`website`, `whatsapp`, `langsung`, `titipan`) ke dalam daftar pesanan admin yang terintegrasi.
2. Mengendalikan kapasitas kuota produksi per batch secara atomik sehingga tidak terjadi kelebihan alokasi (*overselling*).
3. Menyediakan pelacakan status pesanan privat bagi pelanggan tanpa mewajibkan pembuatan akun.

### Target Pengguna (Persona)

| Persona | Peran di Sistem | Kebutuhan Inti |
|---|---|---|
| **Pelanggan masyarakat umum** | Publik (tanpa akun) | Mengajukan pre-order dengan cepat lewat HP/laptop dan memantau status pesanan secara privat via kode & token pribadi. |
| **Ibu / Pengelola usaha** | Role `pengelola` (aktif) | Memantau dashboard, daftar batch, dan memproses alur pesanan pelanggan sesuai hak akses. |
| **Pemilik usaha** | Role `pemilik` (aktif) | Mengatur profil usaha resmi, membuat/mengedit/membuka/menutup batch & kapasitas, memproses pesanan, dan memantau operasional. |

---

## 4. Aturan Bisnis & Batasan Kritis

Aturan berikut ditegakkan di layer validasi, service, dan constraint database:

1. **Produk Tunggal & Harga Snapshot**: Hanya 1 produk aktif (`Keripik Singkong 4 Saudara`, `Satu Rasa Khas`, satuan `pouch`, default `Rp5.000`). Server selalu menghitung `total_rupiah = quantity_pouches * unit_price_rupiah` dalam integer rupiah dan menyimpan snapshot harga di baris `orders`.
2. **Pending Tidak Mengurangi Kuota**: Pengajuan baru dari pelanggan berstatus `menunggu_konfirmasi` dengan `allocated_pouches = 0`. Kuota batch (`allocated_pouches`) **hanya berkurang saat admin mengonfirmasi pesanan**.
3. **Konfirmasi & Pembatalan Atomik**: `OrderService` dan `BatchService` menggunakan `DB::transaction(..., 3)` dengan urutan penguncian konsisten (`BusinessSetting` → `ProductionBatch` → `Order`) memakai `lockForUpdate()`. Pembatalan pesanan yang sudah teralokasi mengembalikan kuota ke batch.
4. **Satu Batch Terbuka pada Satu Waktu**: Hanya 1 batch boleh aktif menerima pesanan (`business_settings.active_batch_id` + `is_open_for_orders = true`). Batch baru selalu dibuat tertutup, dan kapasitas terkunci setelah produksi dimulai (`started_at` atau ada pesanan berstatus `diproduksi`/`siap_diambil`/`selesai`). Penutupan ditolak bila masih ada pesanan `menunggu_konfirmasi`.
5. **Tracking Privat Hash SHA-256**: Token tracking acak 32 karakter hex hanya ditampilkan sekali kepada pelanggan dan disimpan di database sebagai hash `SHA-256` 64 karakter (`tracking_token_hash`). Kode pesanan biasa (`SO-XXXXXX`) tidak boleh membuka detail pelanggan.
6. **Idempotency Pengajuan**: Pengajuan ulang dengan `idempotency_key` sama tidak menggandakan pesanan.
7. **Non-Goals (Dilarang Tanpa Izin Khusus)**: Tidak menambah varian rasa/produk baru, tidak mewajibkan akun pelanggan, tidak mengaktifkan transfer/unggah bukti bayar/payment gateway, tidak menambah integrasi WhatsApp API/kurir/ongkir otomatis, dan tidak mengaktifkan AI dalam aplikasi.

---

## 5. Technology Stack & Arsitektur Ringkas

Detail lengkap arsitektur dan perintah dapat dilihat di [README.md](../../README.md) dan [ARCHITECTURE.md](../ARCHITECTURE.md).

- **Backend**: PHP `^8.3` (Runtime Vercel: PHP `8.4.27`), Laravel `^13.0` (`FormRequest`, `Gate`, Middleware `EnsureActiveAdmin` & `AdminResponseHeaders`, `OrderService`, `BatchService`, 9 model Eloquent).
- **Frontend**: Laravel Blade, CSS/JS kustom ([site.css](../../public/saudara/css/site.css), [admin.css](../../public/saudara/css/admin.css), [site.js](../../public/saudara/js/site.js)), Tailwind CSS `^4.0.0`, Vite `^7.0.7`.
- **Database**: Aiven MySQL `8.4.11` (Singapore, koneksi TLS/SSL `database/certs/ca.pem`); pengujian otomatis memakai SQLite `:memory:` ([phpunit.xml](../../phpunit.xml)).
- **Hosting & Tooling**: Vercel Serverless PHP (`vercel-php@0.8.0`, region `sin1`), Git/GitHub, Composer, NPM, PHPUnit `^12.5.12`, Laravel Pint `^1.27`.

### Alur Request Production

`Browser` → `Vercel Edge CDN (Cache-Control + blokir /index.php)` → `Vercel Function sin1 (vercel-php@0.8.0 -> api/index.php -> public/index.php)` → `Laravel 13 (TrustProxies, Cookie Session terenkripsi, CSRF, Controllers & Services)` → `Aiven MySQL 8.4 Singapore (TLS ca.pem)`.

---

## 6. Peta Fitur Aktual (`Implemented` vs `Partial` vs `Planned`)

| Domain | Fitur | Status Aktual | Rujukan Kode |
|---|---|---|---|
| **Customer** | Beranda, pengenalan produk tunggal, status batch aktif, FAQ | `Implemented` | `PreorderController@home`, `pages/home.blade.php` |
| **Customer** | Form pre-order tanpa akun, validasi WA/jumlah, idempotency, snapshot harga | `Implemented` | `PreorderController@create/@store`, `StorePreorderRequest`, `OrderService` |
| **Customer** | Halaman sukses & pelacakan privat token SHA-256 (5 tahap) | `Implemented` | `PreorderController@success/@tracking`, `pages/tracking.blade.php` |
| **Customer** | Pilihan pengantaran vs pengambilan & input alamat kondisional (F1/F3) | `Planned` | Belum di `StorePreorderRequest` / `preorder.blade.php` |
| **Admin** | Login/logout session, rate limiting (`5/mnt`), `EnsureActiveAdmin`, `saudara:owner` | `Implemented` | `AuthController`, `CreateFirstOwner`, `EnsureActiveAdmin` |
| **Admin** | Dashboard ringkasan batch & metrik kuota | `Implemented` | `DashboardController`, `admin/dashboard.blade.php` |
| **Admin** | Manajemen batch & kapasitas (`pemilik` mutasi, `pengelola` baca) | `Implemented` | `BatchController`, `BatchService`, `admin/batches/*` |
| **Admin** | Pengaturan usaha resmi & detail rekening opsional nullable | `Implemented` | `BusinessSettingController`, `admin/settings.blade.php` |
| **Admin** | Daftar pesanan, detail, konfirmasi kuota, update status, batal, `audit_logs` | `Implemented` | `OrderController`, `OrderService`, `admin/orders/*` |
| **Admin** | Pencarian/filter UI & input manual saluran WA/langsung/titipan (F3) | `Partial` | Query filter `status`/`batch_id` ada di `OrderController@index`; UI & input manual belum ada |
| **Admin** | Pencatatan kas tunai terpisah (`cash_transactions`), `payment_proofs`, `cancellation_requests` | `Partial` | Tabel & model ada (`2026_11_02_000001`), belum ada controller/UI operasional |
| **Admin** | Rekap laporan terfilter & ekspor CSV/cetak (F6) | `Planned` | Belum diimplementasikan |
