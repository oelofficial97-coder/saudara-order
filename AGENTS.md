# AGENTS.md: SaudaraOrder

## Project

SaudaraOrder adalah web existing untuk Keripik Singkong 4 Saudara: satu produk, satu rasa, Rp5.000/pouch. Pelanggan tanpa akun; pemilik/pengelola memakai session Laravel. Level Loop; satu mahasiswa bertanggung jawab.

## Read First

SPEC.json adalah sumber keputusan terbaru. PRD/README existing belum diganti; draf ada di docs/review-drafts sampai persetujuan.

- [PRD](docs/PRD.md): sumber historis; bandingkan SPEC dan draf.
- [ARCHITECTURE](docs/ARCHITECTURE.md): panduan architecture SaudaraOrder.
- [TASKS](docs/TASKS.md): panduan tasks SaudaraOrder.
- [PROGRESS](docs/PROGRESS.md): panduan progress SaudaraOrder.
- [WORKFLOW](docs/WORKFLOW.md): panduan workflow SaudaraOrder.
- [GUARDRAILS](docs/GUARDRAILS.md): panduan guardrails SaudaraOrder.
- [EVALS](docs/EVALS.md): panduan evals SaudaraOrder.
- [CODE_STYLE](docs/CODE_STYLE.md): panduan code_style SaudaraOrder.
- [SECURITY](docs/SECURITY.md): panduan security SaudaraOrder.
- [TESTING](docs/TESTING.md): panduan testing SaudaraOrder.
- [DESIGN_SYSTEM](docs/DESIGN_SYSTEM.md): panduan design_system SaudaraOrder.
- [DECISIONS](docs/DECISIONS.md): panduan decisions SaudaraOrder.
- [REVIEW](docs/REVIEW.md): panduan review SaudaraOrder.
- [LESSONS](docs/LESSONS.md): panduan lessons SaudaraOrder.
- [LOOP](docs/LOOP.md): panduan loop SaudaraOrder.

Muat dokumen saat dibutuhkan; jangan membaca semuanya jika tidak perlu, kecuali PROGRESS.md, TASKS.md, dan GUARDRAILS.md di awal sesi.

## Tech Stack

PHP, Laravel, Blade, MySQL, Vite, PHPUnit. CSS/JavaScript existing, Tailwind tersedia, Pint untuk format. Deklarasi versi ada di SPEC.json; bukan jaminan runtime hosting. Composer dan NPM lockfile dipertahankan. Stack dipilih mahasiswa: jangan mengusulkan penggantian stack; ikuti konvensi resmi teknologi ini. Vercel dievaluasi pertama untuk P0 UTS, Aiven kandidat DB, Render hanya alternatif bersyarat.

## Commands

| Tujuan | Perintah |
|---|---|
| install | `composer install --no-interaction && npm ci` |
| dev | `php artisan serve` |
| lint | `php vendor/bin/pint --test` |
| test | `php artisan test` |
| build | `npm run build` |

Instal = dependency; lint = format; test = PHPUnit; build = aset; dev = server lokal. Tidak menjalankan composer run setup. Tes hanya pada database testing terisolasi. Typecheck/ci_setup kosong; CI direncanakan, belum dibuat oleh dokumentasi. Pada PowerShell lama pisahkan Composer dan NPM.

## Conventions

- Gunakan Form Request, middleware/policy, service transaksi, Eloquent dan Blade sesuai existing.
- Harga snapshot dan nominal integer rupiah; pending tidak memakai kuota.
- Kode DAN token diperlukan untuk tracking; token disimpan hash.
- Pengantaran belum disepakati tidak boleh dikonfirmasi.
- Selesai hanya setelah diterima dan lunas; pembayaran tidak digandakan.
- Pertahankan desain publik/admin dan Bahasa Indonesia.

## Workflow Rules

Satu tugas utama setiap Loop. Plan → Code → Test → Review → Approval. Maksimal dua perbaikan otomatis untuk kegagalan sama, lalu minta arahan. P0 UTS 10 November 2026: link GitHub dan Vercel diuji nyata, bukan sekadar build. Catat hasil di PROGRESS dan keputusan di DECISIONS. Evaluasi hosting tidak mengizinkan deployment. Jangan spawn agen lain tanpa permintaan pengguna.

## Boundaries

### Always

- Baca keadaan existing dan scope sebelum bertindak.
- Pertahankan pekerjaan pengguna dan catat hasil pengujian sebenarnya.
- Gunakan acceptance F1–F6 dan pisahkan status implementasi dari rencana.

### Ask First

- Mengganti dokumen existing: tampilkan draf/diff.
- Mengubah source/skema/CI/dependency di luar tugas yang diizinkan.
- Commit, push, deployment atau perubahan cakupan.

### Never

- Mengubah .env atau membocorkan rahasia.
- Reset/menghapus database pengembangan.
- Commit langsung main atau force push.
- Melemahkan tes atau mengarang hasil.
- Mengaktifkan transfer, ongkir otomatis atau varian baru.
- Menganggap simulasi batch sebagai kebijakan usaha.

## Definition of Done

- Acceptance tugas yang disetujui terpenuhi.
- Pemeriksaan relevan lulus; kegagalan dilaporkan.
- Tidak ada perubahan di luar scope atau rahasia.
- PROGRESS/TASKS dan keputusan relevan diperbarui.
- Mahasiswa meninjau hasil sebelum tindakan penting berikutnya.
