# Evals: SaudaraOrder

## Purpose

Evals SaudaraOrder menghubungkan F1–F6 dengan hasil yang dapat diperiksa. Pada level Loop, kegagalan gerbang menghentikan klaim selesai dan tidak boleh dilewati dengan mematikan tes.

## Quality Gates

| Tujuan | Perintah |
|---|---|
| install | `composer install --no-interaction && npm ci` |
| dev | `php artisan serve` |
| lint | `php vendor/bin/pint --test` |
| test | `php artisan test` |
| build | `npm run build` |

Pint tanpa galat, seluruh tes yang relevan lulus, dan build berhasil adalah gerbang implementasi. Instalasi dicantumkan untuk keseragaman, bukan harus diulang tiap sesi. Pemeriksaan dependency Composer/NPM dan secret scanning perlu dicatat setelah benar-benar dijalankan. E2E serta MySQL concurrency merupakan gerbang tambahan untuk fitur terkait, belum punya perintah kanonis sampai T-006/T-012. Tidak ada typecheck khusus saat ini. Paket dokumen diperiksa dengan validator kit dan diff, tanpa mengubah source atau DB.

## Feature Acceptance Evals

| ID fitur | Eval | Cara dijalankan | Kriteria lulus |
|---|---|---|---|
| F1 | Pre-Order Pelanggan | Otomatis: php artisan test setelah test fitur diimplementasikan | Pengajuan valid menghasilkan kode dan token tracking privat, status Menunggu Konfirmasi, tanpa alokasi kuota. Server menghitung 3 pouch sebagai Rp15.000 dan menyimpan snapshot harga; jumlah bukan integer positif ditolak. Pengantaran tanpa alamat ditolak; pengambilan tidak mewajibkan alamat; WhatsApp website wajib. Idempotency key sama tidak menggandakan pesanan; payload berbeda dengan key sama ditolak. Tidak menampilkan keberhasilan sebelum penyimpanan dikonfirmasi server. |
| F2 | Manajemen Batch dan Kuota Produksi | Otomatis: php artisan test setelah test fitur diimplementasikan | Kuota hanya dialokasikan ketika konfirmasi berhasil; konfirmasi ulang tidak menambah alokasi lagi. Dua konfirmasi bersamaan tidak boleh mengalokasikan melebihi kapasitas; buktikan dengan MySQL testing terpisah. Kapasitas di bawah alokasi ditolak; kapasitas terkunci setelah produksi dimulai. Hanya satu batch menerima pengajuan pada satu waktu; batch tertutup menolak pengajuan baru. Selesai tidak melepas kuota; penutupan penerimaan mengikuti penyelesaian pengajuan tertunda sesuai PRD existing. |
| F3 | Manajemen Pesanan Admin | Otomatis: php artisan test setelah test fitur diimplementasikan | Pemilik/pengelola berwenang memasukkan pesanan WhatsApp, Langsung, atau Perantara; WhatsApp manual boleh kosong; pesanan menunggu konfirmasi tanpa alokasi. Pencarian kode atau nama mengembalikan pesanan yang sesuai; akses tanpa izin ditolak. Pengantaran belum disepakati dengan pelanggan tidak dapat dikonfirmasi atau mengalokasikan kuota. Pesanan hanya dapat menjadi Selesai setelah produk diterima dan pembayaran lunas; penyerahan tanpa pelunasan dicatat terpisah. Penolakan mempunyai alasan dan setiap perubahan penting menyimpan audit pelaku, waktu, dan perubahan. |
| F4 | Tracking Pesanan Privat | Otomatis: php artisan test setelah test fitur diimplementasikan | Kode dan token valid membuka pesanan terkait; kode saja, token salah, atau token pesanan lain ditolak. Detail menampilkan status, quantity, total, batch, estimasi kesiapan, pembayaran, dan timeline sesuai database. Token disimpan sebagai hash; catatan internal admin dan rahasia tidak ditampilkan pada pelanggan. |
| F5 | Pencatatan dan Verifikasi Pembayaran | Otomatis: php artisan test setelah test fitur diimplementasikan | Admin berwenang mencatat pembayaran tunai dengan nominal sesuai transaksi yang disepakati; nominal rupiah berupa integer. Pengulangan tindakan pembayaran tidak menggandakan transaksi penerimaan. Status pembayaran terpisah; produk diserahkan tetapi belum lunas tidak menjadi Selesai. Transfer, gateway, dan unggahan bukti tidak diaktifkan; struktur existing tetap dipertahankan. |
| F6 | Dashboard, Pencarian, dan Laporan | Manual/otomatis setelah fitur tersedia | Ringkasan pesanan, pembayaran, dan kuota cocok dengan data rinci; tanpa data menampilkan empty state, bukan statistik palsu. Filter menghasilkan data sesuai kriteria; laporan unduhan sesuai data rinci dan filter yang sama. Pencarian dasar kode/nama tetap Must pada F3; perluasan laporan tidak menunda fitur inti. |
| R1 | Prioritas | Review task | Must didahulukan, kriteria setiap task jelas; P0 akademik terpisah |
| R2 | Loop | Review log siklus | Satu tugas, hasil tes, maksimal dua perbaikan otomatis |
| R3 | Mingguan | Review laporan | Minimal sekali seminggu; perubahan scope disetujui |

Skenario tabel adalah kewajiban pengujian, bukan daftar tes yang sudah tersedia. UTS mempunyai pemeriksaan URL GitHub dan Vercel terpisah dari penyelesaian seluruh acceptance fitur.

## CI Configuration

Rencana GitHub Actions: pull request dan push main setelah workflow disetujui; checkout, runtime sesuai lockfile, cache dependency, instalasi kanonis, lint, tes pada environment testing terisolasi, lalu build. APP_KEY khusus testing disediakan melalui environment CI; jangan menyalin atau mengubah .env lokal. Versi action perlu diverifikasi dan dipin sesuai review keamanan. Proteksi main/CI belum diklaim aktif. Pembuatan workflow dan konfigurasi GitHub tidak termasuk pekerjaan dokumentasi saat ini.

## Regression Policy

Perbaikan bug logika perlu tes yang menangkap kondisi gagal sebelum perbaikan dan lulus sesudahnya. Jangan menghapus kondisi kuota atau akses hanya karena fixture sulit dibuat. Perubahan test untuk kebutuhan baru harus menjelaskan keputusan bisnis yang mendasarinya dan mempertahankan bukti perilaku sebelumnya bila masih berlaku.

## Failure Handling

Baca log yang telah disamarkan, identifikasi penyebab, lakukan paling banyak dua percobaan perbaikan otomatis untuk kegagalan yang sama. Jalankan ulang hanya pemeriksaan relevan. Jika tetap gagal, berhenti pada bagian terkait, catat hasil dan minta arahan mahasiswa. Jangan menandai done, menyatakan concurrency berhasil dari SQLite, atau menganggap build Vercel hijau sebagai bukti session/database berjalan. Kendala dicatat pada PROGRESS dan pembelajaran aktual pada LESSONS.
