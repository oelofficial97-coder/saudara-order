# Testing: SaudaraOrder

## Strategy

SaudaraOrder memakai PHPUnit untuk logika dan Feature Test, browser automation untuk alur utama setelah alat dipilih, serta UAT bersama pemilik/pengelola dan pelanggan uji. Sepuluh minggu proyek individu menuntut tes proporsional: banyak pemeriksaan invariant dan beberapa perjalanan browser penting. Loop hanya selesai ketika bukti relevan tersedia; tes dokumentasi bukan bukti runtime aplikasi.

## Test Types

| Jenis | Alat/lokasi | Kapan dan perintah |
|---|---|---|
| Unit/Feature | PHPUnit, tests/ | Perubahan logika: php artisan test |
| Browser | Alat belum dipilih; evaluasi Playwright | T-012 menetapkan dependency dan perintah setelah kompatibilitas diperiksa |
| MySQL | Database testing terpisah | T-006; perintah spesifik ditetapkan setelah koneksi testing tersedia |
| UAT | Checklist dan catatan hasil | T-013 bersama pemilik/pengelola |
| Dokumentasi | Validator kit | Setelah paket dokumen siap; tidak menjalankan Laravel migration |

## What Must Be Tested

F1 Pre-Order Pelanggan: Pengajuan valid menghasilkan kode dan token tracking privat, status Menunggu Konfirmasi, tanpa alokasi kuota. Server menghitung 3 pouch sebagai Rp15.000 dan menyimpan snapshot harga; jumlah bukan integer positif ditolak. Pengantaran tanpa alamat ditolak; pengambilan tidak mewajibkan alamat; WhatsApp website wajib. Idempotency key sama tidak menggandakan pesanan; payload berbeda dengan key sama ditolak. Tidak menampilkan keberhasilan sebelum penyimpanan dikonfirmasi server.

F2 Manajemen Batch dan Kuota Produksi: Kuota hanya dialokasikan ketika konfirmasi berhasil; konfirmasi ulang tidak menambah alokasi lagi. Dua konfirmasi bersamaan tidak boleh mengalokasikan melebihi kapasitas; buktikan dengan MySQL testing terpisah. Kapasitas di bawah alokasi ditolak; kapasitas terkunci setelah produksi dimulai. Hanya satu batch menerima pengajuan pada satu waktu; batch tertutup menolak pengajuan baru. Selesai tidak melepas kuota; penutupan penerimaan mengikuti penyelesaian pengajuan tertunda sesuai PRD existing.

F3 Manajemen Pesanan Admin: Pemilik/pengelola berwenang memasukkan pesanan WhatsApp, Langsung, atau Perantara; WhatsApp manual boleh kosong; pesanan menunggu konfirmasi tanpa alokasi. Pencarian kode atau nama mengembalikan pesanan yang sesuai; akses tanpa izin ditolak. Pengantaran belum disepakati dengan pelanggan tidak dapat dikonfirmasi atau mengalokasikan kuota. Pesanan hanya dapat menjadi Selesai setelah produk diterima dan pembayaran lunas; penyerahan tanpa pelunasan dicatat terpisah. Penolakan mempunyai alasan dan setiap perubahan penting menyimpan audit pelaku, waktu, dan perubahan.

F4 Tracking Pesanan Privat: Kode dan token valid membuka pesanan terkait; kode saja, token salah, atau token pesanan lain ditolak. Detail menampilkan status, quantity, total, batch, estimasi kesiapan, pembayaran, dan timeline sesuai database. Token disimpan sebagai hash; catatan internal admin dan rahasia tidak ditampilkan pada pelanggan.

F5 Pencatatan dan Verifikasi Pembayaran: Admin berwenang mencatat pembayaran tunai dengan nominal sesuai transaksi yang disepakati; nominal rupiah berupa integer. Pengulangan tindakan pembayaran tidak menggandakan transaksi penerimaan. Status pembayaran terpisah; produk diserahkan tetapi belum lunas tidak menjadi Selesai. Transfer, gateway, dan unggahan bukti tidak diaktifkan; struktur existing tetap dipertahankan.

Tambahkan login berhasil/gagal, logout, role salah, akun nonaktif, CSRF, pengajuan jaringan gagal dan kebocoran detail pesanan. Penyerahan tanpa lunas harus terlihat sebagai kondisi terpisah tanpa Selesai.

## Commands

| Tujuan | Perintah |
|---|---|
| install | `composer install --no-interaction && npm ci` |
| dev | `php artisan serve` |
| lint | `php vendor/bin/pint --test` |
| test | `php artisan test` |
| build | `npm run build` |

Perintah utama tetap identik pada AGENTS dan EVALS; CI belum dipasang oleh pekerjaan dokumentasi ini. `php artisan test --filter=BusinessSettingValidationTest` adalah contoh filter kelas existing, bukan bukti tes dijalankan sesi ini. Tidak ada script E2E/watch/typecheck yang dikarang. Pada Windows PowerShell lama jalankan Composer dan NPM terpisah; gabungan && digunakan pada cmd atau shell yang mendukung.

## Coverage and Quality Bar

Target persentase coverage belum ditentukan. Utamakan harga snapshot, idempotensi, kapasitas, otorisasi, tracking dan pembayaran. Bug perubahan perilaku harus memiliki regresi bermakna. Tes tidak boleh dihapus/dilemahkan untuk mengejar hasil hijau. Pengujian konkurensi harus melibatkan transaksi MySQL nyata; SQLite tidak membuktikan perilaku penguncian. Status pending, rejected, cancelled dan selesai perlu diuji terhadap perhitungan kuota sesuai aturan yang disetujui.

## Test Data

Gunakan fixture sintetis di SQLite in-memory atau MySQL testing khusus. Sebelum tes yang dapat mereset tabel, verifikasi koneksi efektif dan guard database. Jangan jalankan RefreshDatabase atau migration pada preorder_4_saudara. Jangan membuat pelanggan atau admin palsu pada database operasional. Seeder produk tetap satu produk Rp5.000. Snapshot/bukti hasil uji tidak boleh membawa nama, kontak, kredensial atau token pelanggan nyata.

## Manual Test Checklist

- [ ] Halaman publik dan aset tampil pada desktop.
- [ ] Form nyaman di smartphone.
- [ ] Login/logout admin sesuai role.
- [ ] Navigasi keyboard dan fokus terlihat.
- [ ] Jumlah 3 menghasilkan Rp15.000.
- [ ] Alamat wajib hanya pengantaran.
- [ ] Pengantaran belum disepakati tidak dapat dikonfirmasi.
- [ ] Pending tidak mengurangi kuota.
- [ ] Token salah tidak membuka data.
- [ ] Produk diserahkan tetapi belum lunas tidak Selesai.
- [ ] Retry pembayaran tidak menggandakan kas.
- [ ] Empty/loading/error state jelas.
- [ ] Link GitHub dapat diakses dosen.
- [ ] URL Vercel HTTPS, routing dan aset benar.

## Definition of Done

Kriteria fitur terpenuhi dengan bukti; tes relevan, Pint dan build lulus bila ada perubahan aplikasi; database testing terisolasi; tidak ada rahasia dalam diff; PROGRESS dan keputusan diperbarui. Review mahasiswa diperlukan sebelum langkah penting. Untuk tugas dokumen, validasi struktur dan konsistensi serta diff cukup; jangan menjalankan tes aplikasi yang tidak relevan hanya untuk mengisi checklist. Kegagalan atau pemeriksaan belum dijalankan harus dilaporkan.
