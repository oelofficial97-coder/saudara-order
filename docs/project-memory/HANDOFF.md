# Panduan Serah Terima (Handoff) — SaudaraOrder

Dokumen ini menjadi titik masuk bagi sesi berikutnya (AI maupun developer) untuk langsung melanjutkan pekerjaan dengan aman tanpa kehilangan konteks.

---

## 1. Pekerjaan Terakhir yang Diselesaikan

1. **Perbaikan Deployment & HTTP 500 Production (`25 Nov 2026`)**:
   - Mengatasi `Access denied` Aiven MySQL dengan memperbarui `DB_PASSWORD` di Vercel Production.
   - Memblokir akses mentah `public/index.php` di `/` dan `/index.php` via `vercel.json`, mengaktifkan `$middleware->trustProxies(at: '*')` untuk HTTPS, serta menyinkronkan hash aset Vite (`composer.json` script `vercel`).
2. **Optimasi Performa Production (`25 Nov 2026`)**:
   - Memindahkan Vercel Function ke `sin1` (Singapore), mengaktifkan `SESSION_DRIVER=cookie`, memindahkan `Cache-Control` ke dalam `"routes"`, memangkas query duplikat di `AppServiceProvider`, `PreorderController`, `BatchController`, dan `OrderController`, serta mengoptimasi gambar WebP, preload LCP, dan animasi CSS.
   - Hasil: TTFB warm turun dari **3,66–6,33 detik** menjadi **256–564 ms**; Production dipromosikan ke `https://saudara-order.vercel.app`.
3. **Pembuatan Showcase `README.md` & Screenshot Live (`26 Nov 2026`)**:
   - Merombak [README.md](../../README.md) dengan tabel fitur aktual, diagram Mermaid User Flow & Arsitektur, panduan instalasi, dan roadmap.
   - Mengambil 5 screenshot aktual (`homepage-live.png`, `product-live.png`, `preorder-live.png`, `tracking-live.png`, `admin-login.png`) di `docs/screenshots/` beserta panduan di [docs/screenshots/README.md](../screenshots/README.md).
4. **Integrasi Project Memory & Continuity System (`26 Nov 2026`)**:
   - Membangun 7 dokumen memory terstruktur di `docs/project-memory/` (`PROJECT_CONTEXT.md`, `WORK_HISTORY.md`, `DECISIONS.md`, `CURRENT_STATE.md`, `HANDOFF.md`, `SESSION_LOG.md`, `AI_WORK_RULES.md`) dan mengintegrasikan navigasi vertikalnya langsung ke section *Project Memory & Development History* di [README.md](../../README.md).

---

## 2. File yang Sedang Berada di Working Tree (Belum Di-Commit)

Daftar ringkas kategori file yang termodifikasi/baru di branch `feat/redesign-ui` (periksa `git status --short` untuk daftar lengkap):
- **Backend Pre-Order & Optimasi Query:** `PreorderController.php`, `OrderController.php`, `BatchController.php`, `StorePreorderRequest.php`, `OrderService.php`, `AppServiceProvider.php`, `bootstrap/app.php`, `routes/web.php`, `routes/admin.php`, `tests/Feature/PreorderBackendTest.php`.
- **Frontend UI/UX Pro Max & Aset Optimasi:** `public/saudara/css/site.css`, `admin.css`, `public/saudara/js/site.js`, `public/saudara/images/hero-keripik-konsep*.webp`, `public/build/*`, `resources/views/*`, `design-system/`.
- **Konfigurasi Vercel:** `vercel.json`, `composer.json`, `.vercelignore`.
- **Dokumentasi:** `README.md`, `docs/PROGRESS.md`, `docs/screenshots/README.md`, `docs/screenshots/*-live.png`, `docs/screenshots/admin-login.png`, `docs/project-memory/*`.

---

## 3. Pekerjaan Selesai vs Belum Selesai

### Sudah Selesai
- Fondasi 9 entitas database + migrasi bank nullable + `ProductSeeder`.
- Login/logout admin, `saudara:owner`, dashboard, manajemen batch & kapasitas, pengaturan usaha.
- Backend & frontend Pre-Order + halaman sukses + Tracking privat token SHA-256.
- Deployment Production Vercel (`sin1`) + koneksi TLS Aiven MySQL Singapore + optimasi performa.
- Showcase `README.md` + 5 screenshot publik & login admin + Project Memory `docs/project-memory/`.

### Belum Selesai
1. **Review & Commit Git** seluruh perubahan di working tree branch `feat/redesign-ui` (dan sinkronisasi PR/merge ke `main` bila disetujui mahasiswa).
2. **Pengambilan 5 Screenshot Admin Terautentikasi** ke `docs/screenshots/` dan pengaktifan blok HTML di [README.md](../../README.md#L101-L128).
3. **Verifikasi UAT & Login Admin Nyata di Production** oleh mahasiswa/pemilik (menjalankan alur admin end-to-end dengan akun resmi).
4. **Backlog Fitur F1, F2, F3, F5, F6 yang tersisa** sesuai [SPEC.json](../../SPEC.json) dan [TASKS.md](../TASKS.md):
   - `T-006`: Pengujian konkurensi MySQL 2 koneksi terpisah.
   - `T-007`: Input pesanan manual admin (`whatsapp`, `langsung`, `titipan`) + UI pencarian/filter di `/admin/orders`.
   - `T-009` & `T-010`: Pencatatan kas tunai (`cash_transactions`) & penyelesaian terpisah dari penyerahan.
   - `T-004` / `T-014` lanjutan: Opsi metode pengantaran & alamat bersyarat bila masih dibutuhkan sesuai keputusan final mahasiswa.
   - `T-011`: Dashboard rekap & ekspor laporan terfilter (`Should`).
   - `T-012` / `T-013` / `T-019`: Browser E2E & UAT bersama pemilik.

---

## 4. Prioritas Langkah Berikutnya

1. **Prioritas 1 (Git Hygiene & Sinkronisasi Progres):** Mahasiswa meninjau diff working tree (`git status`, `git diff`), menyinkronkan bagian `In Progress`/`Next Up` di [docs/PROGRESS.md](../PROGRESS.md) yang masih bernarasi `2026-11-06`, lalu merapikan commit di branch `feat/redesign-ui` (backend pre-order, redesign UI, konfigurasi & optimasi Vercel, dokumentasi/README/memory) dan mem-push branch atas izin mahasiswa.
2. **Prioritas 2 (Kelengkapan Showcase):** Pemilik/mahasiswa login ke `https://saudara-order.vercel.app/admin/login`, memverifikasi tampilan dashboard/batch/pesanan/settings secara langsung, mengambil 5 screenshot admin (`admin-dashboard.png`, `admin-batches.png`, `admin-batch-capacity.png`, `admin-settings.png`, `admin-orders.png`), lalu membuka blok komentar di `README.md`.
3. **Prioritas 3 (Fitur Operasional Inti Berikutnya):** Memilih **1 tugas utama** per Loop dari `TASKS.md` (disarankan `T-007` pencarian/input manual admin atau `T-009`/`T-010` pencatatan pembayaran tunai `cash_transactions`), lengkap dengan rencana, kode, tes, review, dan persetujuan.

---

## 5. Langkah Pertama Saat Sesi Baru Dimulai

1. Baca [CURRENT_STATE.md](CURRENT_STATE.md), [AI_WORK_RULES.md](AI_WORK_RULES.md), dan entri terakhir [SESSION_LOG.md](SESSION_LOG.md).
2. Jalankan pemeriksaan baca-saja:
   ```powershell
   git branch --show-current
   git status --short
   git log -n 5 --oneline
   ```
3. Pastikan kondisi working tree cocok dan tanyakan/konfirmasikan **satu tugas utama** yang ingin dikerjakan sebelum mengedit file apa pun.
4. Jika sesi menyentuh kode atau konfigurasi, pastikan `php artisan test`, `php vendor/bin/pint --test`, dan `npm run build` tetap lulus sebelum laporan akhir.

---

## 6. Acceptance Criteria untuk Pekerjaan Penting Berikutnya

- **Commit & Sinkronisasi Git:** Tidak ada file `.env`, `vercel.production.env`, atau kredensial yang masuk staging; `.vercelignore` dan `.gitignore` tetap utuh; `php artisan test` & `pint --test` hijau sebelum commit.
- **Input Manual & Pencarian Admin (`T-007`):** Role `pemilik`/`pengelola` dapat memasukkan pesanan dari `whatsapp`/`langsung`/`titipan`, nomor WA boleh opsional untuk input manual, status awal `menunggu_konfirmasi` (`allocated_pouches = 0`), pencarian kode/nama mengembalikan hasil akurat tanpa membocorkan akses tamu.
- **Pencatatan Kas Tunai & Penyerahan (`T-009`, `T-010`):** Nominal integer rupiah sesuai transaksi, `action_key` idempotent mencegah duplikasi `cash_transactions`, status `selesai` ditolak jika belum diterima dan lunas, setiap perubahan tercatat di `audit_logs`.
- **Pengujian Konkurensi MySQL (`T-006`):** Terbukti di database testing MySQL terpisah (bukan development/production DB dan bukan hanya SQLite) bahwa dua konfirmasi paralel tidak pernah membuat `allocated_pouches > capacity_pouches`.
