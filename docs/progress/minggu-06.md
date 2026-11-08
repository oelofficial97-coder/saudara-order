# Minggu ke-6 — Login admin, dashboard, dan manajemen batch

Tanggal: 2 November 2026 (Asia/Jakarta).

## Acuan dan kondisi awal

Implementasi mengikuti permintaan Minggu ke-6 pada sesi ini serta aturan bisnis `docs/PRD.md` dan kamus fisik `docs/DATABASE_ERD.md`. Model User, Product, ProductionBatch, BusinessSetting, AuditLog, migration, konfigurasi, route dan pengujian yang sudah tersedia diperiksa.

Git awal bersih. Koneksi terverifikasi MySQL 8.4.3 menuju `preorder_4_saudara`, dengan 17 tabel dan kelima migration Minggu ke-5 berstatus Ran. Struktur yang tersedia cukup; **tidak ada migration, tabel, atau kolom baru**.

## Fitur yang selesai

### Autentikasi dan otorisasi

- GET `/admin/login` dan POST `/admin/login` menggunakan username/password melalui session guard Laravel.
- Username dinormalisasi trim/huruf kecil; field divalidasi di server, termasuk penolakan input array yang tidak valid.
- Login hanya untuk role pemilik/pengelola dengan is_active=true. Pesan gagal tidak mengungkap apakah username terdaftar. Password tidak dimasukkan kembali ke old input.
- Rate limiter: 5 request per menit per kombinasi username ternormalisasi/IP, serta 30 request per menit per IP. Batas menghitung seluruh percobaan, termasuk login berhasil. Penyimpanan mengikuti cache Laravel yang sudah dikonfigurasi.
- Session diregenerasi setelah login berhasil.
- POST `/admin/logout` menghapus autentikasi, menginvalidasi session dan mengganti token CSRF. Logout tidak tersedia melalui GET.
- Middleware auth dan admin.active memeriksa ulang akun dari database pada setiap request admin; penonaktifan akun mencabut akses session yang sudah ada.
- Gate manage-batches membatasi pembuatan, pengubahan, pembukaan/penutupan batch serta pengaturan usaha kepada pemilik aktif. Pengelola boleh membaca dashboard dan daftar batch.
- Seluruh form mutasi memakai CSRF middleware Laravel. Halaman admin memakai header Cache-Control no-store/private dan X-Robots-Tag noindex/nofollow.
- Tidak ada registrasi admin publik, password bawaan, atau akun yang disisipkan ke database pengembangan.

### Provisioning pemilik pertama

Jalankan di terminal interaktif pada direktori project:

```powershell
php artisan saudara:owner
```

Isi nama, username, kata sandi, dan konfirmasi. Password disembunyikan tanpa fallback teks terbuka; minimal 12 karakter dengan huruf besar/kecil, angka dan simbol. Password memakai hashed cast Laravel. Command menolak mode non-interaktif, username duplikat, password lemah/tidak cocok, dan pembuatan pemilik kedua, termasuk apabila pemilik lama nonaktif.

Pembuatan dijalankan dalam transaksi. Baris produk singleton dikunci sebagai mutex provisioning, lalu keberadaan pemilik diperiksa ulang. Produk hasil seeder Minggu ke-5 wajib tersedia. Command tidak menyediakan argumen password sehingga rahasia tidak perlu dimasukkan ke command history.

Command sudah diuji dengan input interaktif tiruan **hanya di SQLite in-memory**. Belum dijalankan untuk membuat akun pada database pengembangan; pemilik harus memasukkan kredensial pilihannya sendiri.

### Dashboard

GET `/admin/dashboard` menampilkan data server: jumlah produk aktif, pointer batch penerimaan aktif, kapasitas, alokasi, sisa kuota, estimasi siap WIB, status penerimaan, dan lima batch terbaru. Tidak ada angka penjualan/pelanggan/transaksi rekaan. Jika belum ada pointer batch, metrik terkait tampil sebagai tanda kosong dan empty state menjelaskan tindakan berikutnya.

### Batch produksi

- Daftar batch dengan pagination 12 baris, kode unik, kapasitas, alokasi/sisa, estimasi siap WIB, status dan tindakan sesuai role.
- Pemilik dapat membuat dan mengedit nama, kapasitas, estimasi siap serta catatan internal.
- Kode batch dibuat server dengan ULID. Produk memakai satu produk aktif yang sudah tersedia; ID produk/alokasi/status dari input tidak diterima.
- Batch baru selalu disimpan dengan penerimaan **tertutup**, walaupun default database adalah true. Pembukaan dilakukan melalui tindakan terpisah.
- Jumlah pouch divalidasi integer 1–4.294.967.295. Estimasi dari input datetime-local dipahami sebagai Asia/Jakarta lalu disimpan UTC dan ditampilkan WIB.
- Tidak menambahkan jadwal buka/tutup otomatis: PRD menyediakan estimasi siap dan penerimaan manual, tanpa kolom jadwal penerimaan otomatis.
- BatchService memakai transaksi dengan percobaan ulang deadlock maksimal 3 kali. Urutan lock perubahan batch: pengaturan usaha singleton → batch. Layanan pesanan mendatang harus melanjutkan urutan yang sama sebelum mengunci pesanan.
- Pembukaan memerlukan pengaturan usaha, produk aktif, estimasi siap di masa mendatang, sisa kuota positif, dan tidak adanya batch/pointer penerimaan lain. Pointer active_batch_id dan flag batch diperbarui dalam transaksi yang sama.
- Penutupan ditolak selama masih ada pesanan menunggu_konfirmasi, sesuai PRD. Penutupan berhasil mengosongkan pointer jika menunjuk batch tersebut.
- Kapasitas tidak boleh lebih kecil dari nilai terbesar antara counter alokasi batch dan jumlah pesanan dengan status dikonfirmasi/diproduksi/siap_diambil/selesai. Pesanan menunggu konfirmasi tidak dihitung.
- Kapasitas terkunci apabila started_at sudah terisi atau ditemukan pesanan yang sudah diproduksi/siap diambil/selesai. Informasi lain masih dapat diperbarui selama batch belum selesai.
- Batch terbuka tidak dapat kehilangan estimasi siap mendatang melalui edit. Batch selesai menolak perubahan dan pembukaan/penutupan.
- Pembuatan/edit/buka/tutup dicatat dalam audit pada transaksi yang sama. Jika audit gagal, perubahan di-rollback. Pengulangan buka/tutup yang tidak mengubah flag tidak menambah event duplikat.
- Tidak ada endpoint DELETE. Riwayat batch dipertahankan sesuai PRD dan larangan penghapusan data; penutupan adalah tindakan operasional yang tersedia.

### Pengaturan usaha

GET/PUT `/admin/settings` ditambahkan sebagai prasyarat nyata pointer batch aktif. Skema Minggu ke-5 mewajibkan kontak, lokasi dan rekening; mengisi singleton dengan placeholder akan melanggar batasan data rekaan.

Pemilik mengisi informasi resmi melalui form tervalidasi. Penyimpanan awal singleton diserialisasi melalui lock produk, lalu lock pengaturan; nilai active_batch_id dari request diabaikan. Audit hanya mencatat event perubahan pengaturan, tanpa kontak/rekening. Halaman ini menyiapkan informasi usaha dan **tidak melakukan pembayaran** atau mengubah konten landing page prototype.

### Tampilan

Layout admin terpisah menggunakan hijau gelap, krem dan sage. Tersedia sidebar desktop, menu disclosure native pada mobile, kartu ringkasan, tabel yang dapat digulir, pagination khusus, form berlabel, ringkasan error serta pesan berhasil setelah penyimpanan. CSS admin terpisah dan tidak memerlukan JavaScript baru. Font sistem/serif digunakan tanpa dependensi font eksternal.

## Cara demonstrasi

1. Jalankan `php artisan saudara:owner` di terminal interaktif dan masukkan data pemilik nyata.
2. Buka `/admin/login` pada URL project Laravel yang digunakan. Jika perlu server lokal: `php artisan serve` dan gunakan alamat yang ditampilkan.
3. Login; dashboard tersedia pada `/admin/dashboard`.
4. Pemilik membuka Pengaturan usaha dan mengisi kontak/lokasi/rekening yang benar.
5. Buka Batch produksi → Tambah batch; tentukan nama, kapasitas dan estimasi siap mendatang dalam WIB, lalu simpan.
6. Dari daftar batch, pilih Buka penerimaan. Dashboard sekarang menampilkan kapasitas/alokasi/sisa dari batch tersebut.
7. Edit kapasitas sebelum produksi dimulai; tutup penerimaan melalui tombol pada daftar. Gunakan Keluar untuk mengakhiri session.

Membuka batch pada admin belum mengaktifkan form pengajuan publik: landing page dan formulir publik tetap prototype sesuai batasan tugas. Demonstrasi tidak memerlukan pembuatan pesanan atau transaksi pembayaran.

## Pengujian dan verifikasi

Hasil akhir pada source implementasi:

| Pemeriksaan | Hasil |
| --- | --- |
| `php artisan route:list --except-vendor` | 17 route: tiga route publik tetap, ditambah 14 route admin. |
| `php artisan route:list --path=admin -v` | Middleware web/auth/akun aktif dan Gate pemilik pada mutasi terpasang. |
| `php artisan test --compact` | **44 passed, 367 assertions**; terdiri dari 25 test admin baru dan 19 test sebelumnya. |
| `npm.cmd run build` | Berhasil, Vite 7.3.7, 58 modul. |
| `php vendor/bin/pint app tests routes bootstrap/app.php --test --format=txt` | Passed. |
| `git diff --check` | Bersih. |

Pemanggilan awal `npm run build` melalui npm.ps1 ditolak execution policy PowerShell. Build berhasil memakai executable npm.cmd tanpa mengubah execution policy, dependensi, atau konfigurasi sistem.

Feature test mencakup login pemilik/pengelola, password salah/akun nonaktif, validasi input normal dan array, rate limit dan masa jedanya, regenerasi session, logout/token CSRF, akses tamu, pencabutan session akun nonaktif, larangan registrasi, role, form/list/create/update, pagination, konversi WIB/UTC, penolakan kapasitas invalid/di bawah alokasi/terkunci produksi, satu batch terbuka, idempotensi buka/tutup, prasyarat pembukaan, penutupan dengan pengajuan tertunda, batch selesai, pengaturan singleton, command pemilik dan password lemah. CSRF diuji dengan middleware aktif; rollback diuji dengan kegagalan audit yang disimulasikan.

Semua pengujian mutasi menggunakan SQLite :memory: yang dilindungi guard Tests\TestCase dari Minggu ke-5. Tidak ada reset database pengembangan.

Smoke test HTTP nyata melalui server Laravel sementara pada 127.0.0.1:8097:

- GET `/admin/login`: 200, field CSRF tersedia, Cache-Control no-store/private.
- GET `/admin/dashboard` sebagai tamu: 302 ke login.
- GET `/saudara/css/admin.css`: 200.
- GET `/`: 200.

Server pemeriksaan sudah dihentikan. Tidak tersedia tool browser visual pada sesi ini; pemeriksaan screenshot, tampilan mobile di browser, dan login interaktif menggunakan akun nyata belum dilakukan.

## Status data dan batas verifikasi MySQL

Pemeriksaan SELECT baca-saja sebelum dan sesudah pengujian memastikan database tetap `preorder_4_saudara`, dengan satu produk dan nol baris pada users, business_settings, production_batches, orders, cash_transactions, serta audit_logs. Session tamu dapat tercatat secara normal oleh smoke test HTTP karena aplikasi memakai session Laravel. Tidak ada data usaha yang dihapus.

Database `preorder_4_saudara_testing` belum terlihat melalui information_schema dengan akun aplikasi; laporan Minggu ke-5 juga mencatat pembuatan database testing ditolak oleh izin MySQL. Tidak mencoba kredensial lain atau mengubah grant. Tidak menjalankan suite mutasi pada MySQL pengembangan.

Penggunaan lockForUpdate dan transaksi sudah diimplementasikan, tetapi **uji persaingan dua koneksi MySQL belum dijalankan**. SQLite tidak membuktikan penguncian InnoDB atau perilaku deadlock. OrderService belum ada sehingga pengendalian alokasi pesanan secara konkurensi juga belum dapat diklaim selesai.

## File yang dibuat/diubah

Dibuat:

- `app/Console/Commands/CreateFirstOwner.php`.
- `app/Http/Controllers/Admin/AuthController.php`.
- `app/Http/Controllers/Admin/DashboardController.php`.
- `app/Http/Controllers/Admin/BatchController.php`.
- `app/Http/Controllers/Admin/BusinessSettingController.php`.
- `app/Http/Middleware/EnsureActiveAdmin.php`.
- `app/Http/Middleware/AdminResponseHeaders.php`.
- `app/Http/Requests/Admin/LoginRequest.php`.
- `app/Http/Requests/Admin/BatchRequest.php`.
- `app/Services/BatchService.php`.
- `routes/admin.php`.
- `public/saudara/css/admin.css`.
- `resources/views/admin/layout.blade.php`.
- `resources/views/admin/auth/login.blade.php`.
- `resources/views/admin/dashboard.blade.php`.
- `resources/views/admin/batches/index.blade.php`.
- `resources/views/admin/batches/form.blade.php`.
- `resources/views/admin/settings.blade.php`.
- `resources/views/admin/partials/navigation.blade.php`.
- `resources/views/admin/partials/pagination.blade.php`.
- `tests/Feature/AdminWorkspaceTest.php`.
- `docs/progress/minggu-06.md`.

Diubah:

- `app/Providers/AppServiceProvider.php`: Gate dan rate limiter login.
- `bootstrap/app.php`: redirect autentikasi dan alias middleware akun aktif.
- `routes/web.php`: memuat routes/admin.php, tanpa mengganti tiga route publik.

Skrip sementara pemeriksaan metadata database di storage sudah dihapus setelah verifikasi. Tidak mengubah `.env`, PRD, ERD, model/skema Minggu ke-5, frontend publik, atau lockfile. Tidak ada commit/push otomatis. Skill Sites digunakan hanya sebagai panduan implementasi lokal, tanpa registrasi atau deployment hosting.

## Pekerjaan tahap berikutnya

1. Sediakan MySQL testing terpisah dengan akses terbatas dan guard konfigurasi, lalu uji persaingan pembukaan batch dan perubahan kapasitas memakai dua koneksi nyata.
2. Implementasikan OrderService dengan transaksi pengajuan/konfirmasi, idempotensi, token privat, serta penguncian yang kompatibel dengan BatchService. Semua penulis data wajib mengikuti urutan lock yang sama; penulisan SQL langsung di luar service dapat melewati aturan aplikasi.
3. Tambahkan pengelolaan akun pengelola, ubah/reset password dan aturan pemilik terakhir sesuai PRD; command saat ini hanya provisioning pemilik pertama.
4. Implementasikan lifecycle produksi/penyelesaian batch setelah aturan pesanan dan pembayaran tersedia. Pada tahap ini batch yang sudah selesai dilindungi dari perubahan, tetapi belum ada tombol untuk menandai selesai.
5. Lanjutkan pesanan, pembayaran manual, pembatalan, laporan BI/ekspor dan integrasi frontend publik pada tahap yang memang mencakup fitur-fitur tersebut.
6. Lakukan QA visual desktop/mobile dan demonstrasi dengan akun serta informasi usaha nyata. Penyelarasan PHP deployment dengan target 8.4.x PRD masih menjadi catatan dari Minggu ke-5.
