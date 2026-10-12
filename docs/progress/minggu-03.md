# Laporan Progres Capstone — Minggu Ke-03

**Nama proyek:** SaudaraOrder — Sistem Pre-Order Keripik Singkong 4 Saudara  
**Mata kuliah:** Capstone Project — Teknik Informatika  
**Periode:** Minggu ke-3 (laporan disusun 12 Oktober 2026)  
**Repository:** https://github.com/oelofficial97-coder/saudara-order  
**Status:** Frontend/prototipe tahap awal; **belum siap digunakan untuk transaksi nyata**.

## 1. Ringkasan perkembangan

Pada minggu ketiga, pengembangan berfokus pada penyiapan aplikasi Laravel dan rancangan antarmuka publik SaudaraOrder. Repository GitHub telah dibuat dan berhasil menerima *initial commit* Laravel. Aplikasi Laravel juga telah berhasil dijalankan melalui server lokal dan ditampilkan di browser.

Setelah itu, rancangan **Cinematic Premium** untuk halaman publik telah dipasang dan ditampilkan secara lokal. Gaya visual menggunakan tipografi editorial, warna hijau gelap dan krem, serta visual produk keripik singkong. Desain mengacu pada kebutuhan usaha **4 Saudara** yang menawarkan **satu produk, satu rasa khas, dengan harga Rp5.000 per pouch**.

> **Catatan status:** Pengamatan antarmuka didasarkan pada tangkapan layar lokal. Keberhasilan semua navigasi, pengujian otomatis, dan *push* commit frontend ke GitHub **belum terkonfirmasi** pada saat laporan ini dibuat. Jangan menandainya selesai sebelum diperiksa.

## 2. Teknologi dan lingkungan kerja

| Komponen | Teknologi/keterangan |
|---|---|
| Bahasa dan framework | PHP 8.3.30 dan Laravel 13.35.0 (terlihat pada pemeriksaan lokal) |
| Dependensi backend | Composer 2.9.4 |
| Perangkat frontend | Node.js 22.22.0, npm 10.9.4, Vite |
| Editor/AI | Google Antigravity IDE; penggunaan Codex direncanakan untuk implementasi lanjutan |
| Pengelolaan kode | Git 2.51.1 dan GitHub |
| Server pengembangan | `php artisan serve`, alamat `http://127.0.0.1:8000` |
| Database | Instalasi Laravel awal menggunakan SQLite bawaan; MySQL direncanakan sesuai PRD dan belum dikonfigurasi untuk aplikasi utama |
| Desain frontend saat ini | Blade, CSS kustom, dan JavaScript ringan; Tailwind CSS 4/Alpine.js tercantum sebagai target PRD, belum dinyatakan terimplementasi penuh |

## 3. Pekerjaan yang sudah dilakukan

- [x] Membuat repository publik `oelofficial97-coder/saudara-order`.
- [x] Menginisialisasi Git dan berhasil *push* **initial commit** Laravel ke branch `main`.
- [x] Menginstal Laravel 13 dan dependensi awal.
- [x] Menjalankan `npm run build` untuk aset bawaan Laravel tanpa kesalahan yang terlihat.
- [x] Menjalankan Laravel secara lokal dan membuka halaman awal di browser.
- [x] Menyimpan dokumen kebutuhan pada `docs/PRD.md` di folder proyek lokal.
- [x] Menampilkan desain awal **landing page cinematic** pada browser lokal (berdasarkan screenshot).
- [ ] Memastikan frontend terbaru telah di-*commit* dan di-*push* ke GitHub.
- [ ] Menjalankan dan mencatat hasil pengujian frontend secara lengkap.
- [ ] Mengundang dosen sebagai collaborator (jika diwajibkan).

## 4. Antarmuka yang dikembangkan

### A. Beranda `/`

Berdasarkan screenshot, beranda telah menampilkan:

- Navigasi dan identitas usaha **4 Saudara**.
- Hero sinematik dengan visual produk.
- Penjelasan tentang produk satu rasa khas.
- Harga produk **Rp5.000/pouch**.
- Tombol menuju alur pre-order.
- Bagian produk, cara pesan, informasi batch, FAQ, dan footer.

### B. Form pre-order `/pre-order`

Halaman ini direncanakan/tersedia sebagai **prototipe frontend**. Pengisian belum boleh dianggap sebagai pesanan sungguhan sampai validasi backend, penyimpanan database, dan pengelolaan kuota berhasil diimplementasikan dan diuji.

### C. Cek pesanan `/cek-pesanan`

Halaman pengecekan pesanan juga masih berada pada tahap **prototipe**. Data pesanan pribadi nantinya hanya boleh dibuka dengan token pelacakan yang aman sesuai PRD, bukan berdasarkan kode pesanan biasa.

**Batasan versi minggu ketiga:** Belum ada klaim implementasi backend pesanan, pembayaran, batch produksi, dashboard admin, ataupun laporan BI.

## 5. Pemeriksaan yang perlu diselesaikan

| Pemeriksaan | Status / bukti yang diperlukan |
|---|---|
| Beranda tampil di desktop | Terlihat pada screenshot lokal |
| Halaman `/pre-order` dan `/cek-pesanan` dapat dibuka | Perlu verifikasi langsung |
| Menu navigasi, tombol, dan FAQ berfungsi | Perlu pengujian manual |
| Tampilan responsif di layar ponsel | Perlu screenshot browser lokal |
| `npm run build` setelah pemasangan frontend | Perlu dijalankan ulang |
| `php artisan route:list` | Perlu dicatat hasilnya |
| `php artisan test` | Perlu dicatat hasilnya |
| Commit frontend tampil di GitHub | Perlu verifikasi setelah `git push` |
| `.env`, kredensial, database privat tidak masuk Git | Perlu pemeriksaan sebelum commit |

Perintah pemeriksaan dari root proyek:

```powershell
npm run build
php artisan route:list
php artisan test
git status
git check-ignore .env database/database.sqlite
```

Jika tidak ada kendala dan daftar file aman, commit serta push dengan pesan yang menjelaskan perubahan:

```powershell
git add docs/PRD.md docs/progress/minggu-03.md public resources routes
git diff --cached --name-only
git commit -m "feat: add SaudaraOrder cinematic frontend and week-3 report"
git push origin main
```

> Periksa dulu isi `git diff --cached --name-only` dan pastikan tidak ada berkas rahasia. Daftar path pada `git add` perlu disesuaikan dengan file yang benar-benar berubah.

## 6. Kendala dan penyelesaian

| Kendala | Penanganan |
|---|---|
| GitHub menolak `git push` dengan HTTP 403 karena akun yang digunakan berbeda dari pemilik repository | Autentikasi GitHub diperbaiki; initial commit akhirnya berhasil diunggah |
| Folder hasil ekstraksi frontend awalnya berada di dalam `docs/design` | Berkas frontend dipindahkan/digabungkan ke struktur root Laravel; setelah itu beranda tampil secara lokal |
| Kebutuhan visual awal masih berupa prototipe Stitch | Desain diubah ke arah **Cinematic Premium** berdasarkan referensi tampilan yang dipilih |

## 7. Rencana minggu ke-04

1. Menyempurnakan UI form pre-order dan halaman cek pesanan.
2. Memeriksa navigasi, interaksi, animasi, dan responsivitas desktop/mobile.
3. Menyelaraskan frontend dengan arsitektur Blade, Tailwind CSS, dan Alpine.js sesuai kebutuhan PRD secara bertahap.
4. Mengganti gambar ilustrasi produk dengan foto asli jika sudah tersedia; tidak menampilkan klaim usaha atau angka yang belum terverifikasi.
5. Melengkapi dokumentasi screenshot dan commit GitHub.
6. Menyiapkan rancangan ERD sebagai bahan awal pengerjaan database pada minggu berikutnya.

## 8. Bukti dokumentasi

Simpan screenshot yang benar-benar diambil dari aplikasi lokal di:

```text
docs/
├── PRD.md
├── progress/
│   └── minggu-03.md
└── screenshots/
    ├── beranda-desktop.png
    ├── beranda-mobile.png
    ├── form-preorder.png
    └── cek-pesanan.png
```

Dokumentasi dapat mencakup screenshot repository GitHub yang menampilkan commit, hasil pengujian di terminal, dan antarmuka website. File bukti yang belum ada **tidak perlu dibuat kosong atau diklaim sudah tersedia**.

## 9. Kesimpulan

Pada minggu ketiga, SaudaraOrder telah memiliki fondasi project Laravel, repository GitHub, dan tampilan awal beranda dengan konsep visual yang sesuai arah produk. **Capaian antarmuka masih berada pada tahap frontend/prototipe**, sedangkan transaksi pre-order, database MySQL, dan pengelolaan pesanan akan dikembangkan pada tahapan berikutnya. Sebelum laporan dinyatakan final, pengujian dan *push* versi frontend terbaru perlu diselesaikan.

---

*Laporan perkembangan ini disusun berdasarkan PRD proyek serta bukti pekerjaan yang tersedia sampai 12 Oktober 2026. Perbarui checklist dan hasil uji setelah verifikasi langsung.*
