# Aturan Kerja AI & Developer — SaudaraOrder

Aturan ini wajib dipatuhi oleh seluruh asisten AI (Antigravity, ChatGPT, Gemini, Codex) maupun developer yang bekerja pada proyek **SaudaraOrder**. Seluruh ketentuan di bawah selaras dengan [AGENTS.md](../../AGENTS.md) dan [GUARDRAILS.md](../GUARDRAILS.md).

---

## 1. Protokol Awal Sesi (Wajib Sebelum Bertindak)

1. **Baca Project Memory terlebih dahulu**: periksa [CURRENT_STATE.md](CURRENT_STATE.md), [HANDOFF.md](HANDOFF.md), dan entri terakhir [SESSION_LOG.md](SESSION_LOG.md), bersama [PROGRESS.md](../PROGRESS.md), [TASKS.md](../TASKS.md), dan [GUARDRAILS.md](../GUARDRAILS.md).
2. **Periksa kondisi Git**: jalankan `git branch --show-current`, `git status --short`, dan `git log -n 5 --oneline` sebelum menyentuh file apa pun.
3. **Kerjakan satu tugas utama per siklus Loop**: *Plan → Code → Test → Review → Approval*. Jika perbaikan gagal 2 kali berturut-turut untuk error yang sama, berhenti dan minta arahan mahasiswa.

---

## 2. Aturan Git & Perlindungan Pekerjaan

- **Lindungi perubahan yang belum di-commit**: Jangan pernah menghapus, membuang, atau menimpa perubahan working tree pengguna.
- **Dilarang keras perintah destruktif**: `git reset --hard`, `git clean -fd` / `-fdx`, `git push --force`, dan menulis ulang riwayat branch bersama (`rebase`/`amend` pada commit yang sudah di-push) **dilarang**.
- **Jangan commit, push, atau merge tanpa izin**: Jangan pernah commit langsung ke `main`; lakukan commit/push/merge hanya atas persetujuan eksplisit mahasiswa.

---

## 3. Keamanan Data, Kredensial & Database

- **Rahasia & `.env` terlarang untuk dicetak atau di-commit**: Jangan pernah menampilkan, menyalin, atau menyimpan nilai `APP_KEY`, `DB_PASSWORD`, access token Vercel/GitHub, token tracking mentah, atau isi `.env` / `vercel.production.env` ke kode, Git, log, screenshot, atau chat.
- **Jangan ubah `.env` lokal atau menyalinnya langsung ke hosting**.
- **Lindungi Database Pengembangan & Production**:
  - Dilarang menjalankan `migrate:fresh`, `migrate:reset`, `db:wipe`, `DROP DATABASE`, `TRUNCATE`, atau `composer run setup` pada database pengembangan/Aiven Production.
  - Dilarang membuat data pelanggan palsu/dummy atau akun admin contoh pada database Production.
  - Pengujian otomatis (`php artisan test`) hanya berjalan pada database terisolasi (`SQLite :memory:` sesuai `phpunit.xml` atau database testing MySQL khusus terpisah).

---

## 4. Aturan Kode, Deployment & Kualitas

- **Jaga stack & ruang lingkup**: Tetap gunakan PHP 8.3+, Laravel 13, Blade, Vite, Tailwind CSS, dan MySQL Aiven sesuai [SPEC.json](../../SPEC.json). Jangan mengaktifkan fitur di luar scope (varian rasa baru, registrasi pelanggan, transfer/payment gateway, ongkir otomatis, AI in-app).
- **Jangan melemahkan tes atau lint** demi membuat hasil terlihat hijau.
- **Jangan deploy tanpa pengujian**: Sebelum deployment ke Vercel:
  1. Pastikan `php artisan test`, `php vendor/bin/pint --test`, dan `npm run build` lulus.
  2. Validasi `.vercelignore` agar `.env*`, file rahasia, dan direktori development tidak ikut terunggah.
  3. Gunakan staged deployment (`vercel deploy --prod --skip-domain`) dan uji seluruh endpoint kritis serta log runtime **sebelum** menjalankan `vercel promote`.
  4. Jangan menganggap build hijau sebagai bukti aplikasi sehat sebelum endpoint ber-DB teruji nyata.

---

## 5. Kejujuran Teknis & Pemeliharaan Memory

- **Bedakan fakta, asumsi, dan rencana**:
  - Tandai status fitur secara jujur (`Implemented`, `Partial`, `Planned`).
  - Jika informasi historis tidak ditemukan di repo/log, nyatakan *belum terverifikasi*, jangan mengarang tanggal, commit, atau angka.
  - Jangan pernah mengklaim tes, login, atau fitur berhasil bila belum benar-benar diuji setelah edit terakhir.
- **Perbarui Project Memory di akhir sesi**:
  - Perbarui [CURRENT_STATE.md](CURRENT_STATE.md) dan [HANDOFF.md](HANDOFF.md).
  - Tambahkan entri baru ke [SESSION_LOG.md](SESSION_LOG.md) (tanpa menghapus entri lama).
  - Catat keputusan penting ke [DECISIONS.md](DECISIONS.md) (serta sinkronkan dengan [docs/DECISIONS.md](../DECISIONS.md) bila berupa ADR resmi) dan milestone ke [WORK_HISTORY.md](WORK_HISTORY.md) bila relevan.

---

## 6. Peta Sumber Kebenaran (*Source of Truth*) & Resolusi Konflik

Folder `docs/project-memory/` **tidak menduplikasi** seluruh isi dokumen rekayasa yang sudah ada. Indeks navigasi utama berada pada section *Project Memory & Development History* di [README.md](../../README.md). Gunakan rujukan primer berikut bila membutuhkan detail lengkap:

| Kategori Informasi | Sumber Primer (Canonical) |
|---|---|
| Keputusan kebutuhan fitur (`F1–F6`) & kontrak acceptance | [SPEC.json](../../SPEC.json) |
| Aturan kerja agen & perintah standar | [AGENTS.md](../../AGENTS.md) |
| Kebutuhan produk historis & konteks awal | [PRD.md](../PRD.md) |
| Arsitektur komponen & batasan lingkungan | [ARCHITECTURE.md](../ARCHITECTURE.md) |
| Kebijakan privasi, akses, & mitigasi keamanan | [SECURITY.md](../SECURITY.md) |
| Strategi pengujian & daftar skenario | [TESTING.md](../TESTING.md) |
| Daftar tugas backlog (`T-001` s.d. `T-021`) | [TASKS.md](../TASKS.md) |
| Progres resmi terdokumentasi & laporan mingguan | [PROGRESS.md](../PROGRESS.md) dan [docs/progress/](../progress/) |
| ADR formal awal proyek | [docs/DECISIONS.md](../DECISIONS.md) |
| Status terkini real-time, Vercel/Aiven, & handoff | `docs/project-memory/` (`CURRENT_STATE.md`, `HANDOFF.md`, `SESSION_LOG.md`) |

> **Aturan Konflik:** Jika ada perbedaan antara dokumen lama (`PRD.md` awal atau `minggu-03.md`) dengan `SPEC.json` atau kondisi kode aktual di repository, **kode aktual yang teruji dan `SPEC.json` menjadi acuan**, lalu catat ketidaksinkronan dokumen di [CURRENT_STATE.md](CURRENT_STATE.md).
