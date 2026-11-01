# Minggu ke-5 — Fondasi database SaudaraOrder

Tanggal: 2 November 2026 (Asia/Jakarta).

## Ruang lingkup dan kondisi awal

Pekerjaan mengikuti permintaan Minggu ke-5 pada sesi ini: fondasi database, model, seeder, dan pengujian. Ini berbeda dari pembagian jadwal awal PRD yang menempatkan dashboard BI pada Minggu ke-5. Dashboard belum diimplementasikan dalam pekerjaan ini.

Acuan: `docs/PRD.md` versi 2.0 dan `docs/DATABASE_ERD.md` versi 1.0. Migration, model, factory, seeder, konfigurasi, dan pengujian bawaan diperiksa sebelum perubahan.

- Status Git awal bersih.
- Laravel terpasang: 13.35.0; PHP CLI: 8.3.30; MySQL: 8.4.3.
- `php artisan db:show` memastikan koneksi MySQL menuju `preorder_4_saudara`, dengan **0 tabel** sebelum migration.
- `php artisan migrate:status` awal melaporkan tabel migration belum ditemukan.
- Karena migration users bawaan belum diterapkan dan database kosong, migration tersebut disesuaikan langsung. Tidak ada tabel users kedua atau data lama yang dikonversi/dihapus.
- `.env`, PRD, ERD, route publik, serta file frontend tidak diubah. Tidak ada commit/push.

## Struktur yang diterapkan

Semua tabel memakai primary key; sembilan tabel inti menggunakan BIGINT UNSIGNED auto increment. Waktu aplikasi tetap UTC sesuai konfigurasi yang ada; penyajian Asia/Jakarta menjadi tanggung jawab tahap layanan/tampilan.

| Tabel | Struktur dan integritas utama |
| --- | --- |
| `users` | Nama 100 karakter, username 50 karakter unik, password hash, role `pemilik`/`pengelola` (default pengelola), `is_active` default true, remember token nullable, created/updated timestamps. Tidak ada akun pelanggan. |
| `products` | Nama/slug 100 karakter, slug unik, satu `flavor_name`, deskripsi wajib, foto nullable, harga BIGINT UNSIGNED default 5000, unit default pouch, aktif default true, timestamps. Singleton dan satuan pouch ditegakkan melalui UNIQUE/CHECK. Harga boleh diperbarui pemilik sesuai PRD. |
| `production_batches` | FK produk, kode batch unik, nama batch, kapasitas INT UNSIGNED positif, alokasi default nol dan tidak melebihi kapasitas, penerimaan default terbuka; estimasi, mulai/selesai dan catatan nullable; timestamps dan indeks penerimaan/selesai. |
| `business_settings` | Singleton, nama usaha default, kontak/lokasi/rekening wajib tanpa data rekaan; maps URL nullable; pointer batch aktif nullable dan unik; timestamps. Belum ada baris sampai pemilik memberikan informasi nyata. |
| `orders` | FK batch, kode/idempotency key/hash token unik, pelanggan, sumber website/whatsapp/langsung/titipan, jumlah positif, snapshot harga dan total integer rupiah, metode transfer/bayar saat ambil, status pesanan dan pembayaran terpisah, alokasi default nol, waktu token wajib, estimasi/catatan/alasan/waktu transisi nullable, timestamps. CHECK total = jumlah × harga; alokasi sama dengan jumlah hanya pada status yang mengikat kuota. |
| `payment_proofs` | FK pesanan, path privat/nama/ukuran/MIME, status default menunggu verifikasi, catatan/verifikator/waktu verifikasi nullable; pengunggah admin nullable, timestamps. Ukuran 1–5.242.880 byte dan MIME JPEG/PNG dibatasi CHECK. Ini belum menggantikan pemeriksaan isi berkas pada layanan upload. |
| `cash_transactions` | FK pesanan, bukti nullable dan unik, jenis penerimaan/pengembalian, nominal BIGINT UNSIGNED positif, kanal transfer_bank/tunai, action key unik, catatan nullable, FK pencatat wajib, waktu transaksi wajib, timestamps. |
| `cancellation_requests` | FK pesanan, alasan, status default menunggu persetujuan, catatan/pengambil keputusan/waktu keputusan nullable, timestamps dan indeks status. |
| `audit_logs` | Pelaku/order/batch nullable, event, deskripsi, JSON sebelum/sesudah nullable, IP/user agent nullable, created_at wajib; tanpa updated_at. Indeks event/waktu serta waktu. |

Tabel pendukung dipertahankan: `password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, dan `migrations`. Keberadaan password_reset_tokens tidak berarti alur reset password email sudah tersedia; akun internal menggunakan username.

Urutan pembuatan: produk → batch → pengaturan → pesanan → bukti → kas → pembatalan → audit. Pointer pengaturan ke batch tidak membuat circular foreign key. Migration kedua menambahkan CHECK khusus MySQL setelah semua tabel tersedia.

Indeks pesanan meliputi batch/status, tanggal, telepon, sumber/tanggal, status/tanggal, dan pembayaran/tanggal. Kas diindeks berdasarkan waktu serta jenis/waktu. Unique constraint tersedia pada username, slug, kode batch, kode pesanan, idempotency key, hash token, bukti kas, dan action key.

### Penyesuaian terhadap ERD

PRD melarang penghapusan transaksi lama melalui operasi aplikasi. Karena itu FK bukti/pembatalan/audit dan FK admin menggunakan `RESTRICT`, lebih ketat dibanding CASCADE/SET NULL pada sebagian rancangan ERD. FK bukti kas juga RESTRICT agar keterkaitan bukti tidak hilang. Pointer `business_settings.active_batch_id` tetap SET NULL karena hanya pointer operasional.

Kolom `singleton_key` dengan default 1, UNIQUE, dan CHECK = 1 ditambahkan pada produk/pengaturan untuk memastikan satu baris. ID tetap auto increment; aplikasi tidak boleh mengasumsikan singleton selalu memiliki ID 1 setelah suatu rollback insert. Seeder mencari singleton_key, bukan ID.

Kolom `orders.estimated_ready_at` ditambahkan mengikuti kebutuhan estimasi pesanan pada PRD. Kolom `payment_proofs.uploaded_by` nullable mendukung identitas pengunggah admin; null menandai pelanggan/sistem tanpa akun. Relasi pengguna ke pengelolaan batch ditelusuri melalui audit; kamus fisik ERD tidak menetapkan kolom pengelola pada batch.

## Model dan perlindungan data

Sembilan model Eloquent tersedia dengan tipe relasi eksplisit:

- User memiliki audit, pencatatan kas, unggahan/verifikasi bukti, dan keputusan pembatalan.
- Product memiliki banyak ProductionBatch.
- ProductionBatch milik Product, memiliki Order/AuditLog, dan dapat ditunjuk satu BusinessSetting aktif.
- BusinessSetting menunjuk batch aktif.
- Order milik batch dan memiliki bukti, kas, pembatalan, serta audit.
- PaymentProof milik pesanan, berelasi ke pengunggah/verifikator, dan memiliki maksimal satu transaksi kas.
- CashTransaction milik pesanan/bukti serta pencatat.
- CancellationRequest milik pesanan dan pengambil keputusan.
- AuditLog terkait pengguna/pesanan/batch secara opsional.

Nominal/jumlah dicast integer, flag boolean, tanggal domain immutable_datetime, JSON audit array, password hashed. Password/remember token, hash pelacakan/idempotency key, path bukti/catatan internal, dan rekening disembunyikan pada serialisasi model yang relevan. Ini belum menggantikan otorisasi/resource respons: controller kelak harus memilih data pelanggan secara eksplisit.

Field status, alokasi, harga pesanan, token, role, dan verifikasi tidak dibuka untuk mass assignment dari request. Layanan tepercaya harus mengisi field terlindungi secara eksplisit; jangan meneruskan seluruh input request ke forceFill/unguard.

AuditLog dan CashTransaction menolak perubahan/penghapusan melalui event Eloquent. Snapshot JSON audit hanya mengizinkan field domain scalar/null, sehingga password, token, kunci idempotensi, kontak dan payload bersarang tidak tersimpan. Deskripsi bebas harus tetap dibangun dari teks aman oleh layanan kelak. Guard Eloquent dapat dilewati query builder/direct SQL; belum ada jaminan append-only pada tingkat hak akses database atau trigger.

Database hanya menyediakan `tracking_token_hash` CHAR(64) unik dengan CHECK format SHA-256 heksadesimal. Penerbitan token 32-byte acak, pengembalian token mentah satu kali, pencarian hash, dan rotasi tautan belum diimplementasikan.

## Seeder

`ProductSeeder` dipanggil `DatabaseSeeder`. Seeder memakai firstOrCreate pada singleton_key sehingga pengulangan tidak menggandakan produk atau menimpa perubahan harga/deskripsi pemilik.

Seeder dijalankan **dua kali** pada database pengembangan setelah migration berhasil. Pemeriksaan baca-saja membuktikan satu produk:

- Keripik Singkong 4 Saudara.
- Rp5.000 per pouch.
- Satu Rasa Khas.

Delapan tabel inti lainnya tetap **0 baris**. Tidak ada akun admin/password tetap, pelanggan fiktif, batch demonstrasi, rekening, atau kontak rekaan yang diisikan ke database pengembangan. Factory pengguna hanya untuk pengujian dan menghasilkan password acak.

## Hasil migration dan pemeriksaan MySQL

`php artisan migrate --force` berhasil tanpa menghapus data. `php artisan migrate:status` akhir:

| Migration | Hasil |
| --- | --- |
| `0001_01_01_000000_create_users_table` | Ran, batch 1 |
| `0001_01_01_000001_create_cache_table` | Ran, batch 1 |
| `0001_01_01_000002_create_jobs_table` | Ran, batch 1 |
| `2026_11_02_000001_create_order_foundation_tables` | Ran, batch 1 |
| `2026_11_02_000002_add_foundation_integrity_checks` | Ran, batch 1 |

`php artisan db:show` akhir: MySQL 8.4.3, database `preorder_4_saudara`, **17 tabel**. Inspeksi SELECT information_schema memastikan semua tabel InnoDB dan utf8mb4_unicode_ci; terdapat **17 primary key, 14 foreign key, 12 unique constraint, dan 14 CHECK dengan ENFORCED=YES**. Pemeriksaan ini membuktikan pemasangan constraint, bukan uji penolakan seluruh input salah pada MySQL.

Tidak ada migrate:fresh, migrate:reset, db:wipe, DROP DATABASE, atau reset database pengembangan. Skrip sementara pemeriksaan koneksi/metadata sudah dihapus setelah dipakai karena bukan bagian aplikasi.

## Pengujian dan batas verifikasi

Percobaan membuat `preorder_4_saudara_testing` dengan kredensial aplikasi ditolak MySQL (SQLSTATE 42000). Tidak menggunakan kredensial lain atau mengubah izin. Pengujian otomatis memakai **database SQLite :memory: terpisah**.

phpunit.xml memaksa APP_ENV=testing, DB_CONNECTION=sqlite, DB_DATABASE=:memory:, dan DB_URL kosong. Tests\TestCase menolak konfigurasi lain sebelum trait pengujian dapat mereset tabel, termasuk bila konfigurasi cache salah. Setiap pengujian fondasi melakukan migrate ke memori baru; pengujian pembalikan migration memanggil down/up hanya pada memori terisolasi tersebut.

Hasil akhir:

- `php artisan test`: **19 passed, 121 assertions**, termasuk **14 pengujian fondasi baru** dan lima pengujian bawaan/frontend.
- Cakupan: tabel/kolom, migration berulang dan down/up, seeder idempotent dan pelestarian edit pemilik, semua relasi model, default status/alokasi, snapshot harga, cast/serialisasi/password hash, proteksi mass assignment, keunikan idempotency/hash token/action key/bukti kas, RESTRICT relasi, proteksi riwayat, dan penyaringan snapshot audit.
- Pengujian halaman home/preorder/tracking tetap lulus. Tidak dilakukan pengujian visual browser karena frontend tidak diubah.
- `php vendor/bin/pint app/Models database tests --format=txt`: passed setelah formatting.
- `git diff --check`: bersih.

CHECK tambahan pada migration kedua hanya berlaku untuk MySQL; pengujian SQLite tidak membuktikan penegakan CHECK MySQL, unsigned BIGINT/overflow, atau locking InnoDB. Uji perilaku tersebut dan konkurensi dua koneksi tetap memerlukan database testing MySQL terpisah dengan izin yang sesuai.

## File yang dibuat atau diubah

Dibuat:

- `app/Models/AuditLog.php`, `BusinessSetting.php`, `CancellationRequest.php`, `CashTransaction.php`, `Order.php`, `PaymentProof.php`, `Product.php`, `ProductionBatch.php`.
- `database/migrations/2026_11_02_000001_create_order_foundation_tables.php`.
- `database/migrations/2026_11_02_000002_add_foundation_integrity_checks.php`.
- `database/seeders/ProductSeeder.php`.
- `tests/Feature/DatabaseFoundationTest.php`.
- `docs/progress/minggu-05.md`.

Diubah:

- `app/Models/User.php`.
- `config/database.php`: engine MySQL InnoDB eksplisit.
- `database/migrations/0001_01_01_000000_create_users_table.php`.
- `database/factories/UserFactory.php`.
- `database/seeders/DatabaseSeeder.php`.
- `phpunit.xml` dan `tests/TestCase.php`.

## Kendala dan pekerjaan berikutnya

Fondasi ini belum menjalankan transaksi pengajuan/konfirmasi/pembayaran. CHECK per baris tidak menjumlahkan alokasi lintas pesanan, tidak memastikan bukti kas berasal dari order yang sama, dan tidak mengatur transisi status. **Pengendalian kuota aman terhadap konkurensi belum berfungsi sebelum service transaksi dibuat.** Pointer singleton membantu menyediakan baris penguncian; belum otomatis menjamin hanya satu batch bertanda terbuka.

PHP aktual 8.3.30 berbeda dari target operasional 8.4.x pada PRD; Laravel terpasang berjalan dan seluruh tes di atas lulus pada runtime aktual. Penyelarasan runtime deployment tetap perlu dilakukan.

Rekomendasi Minggu ke-6:

1. Sediakan database MySQL testing terpisah dan akun terbatas; tambahkan konfigurasi/guard khusus sebelum menjalankan integration test MySQL. Jangan mengendurkan guard PHPUnit saat ini untuk mengarah ke database pengembangan.
2. Implementasikan OrderService/BatchService: transaksi, urutan lock pengaturan → batch → pesanan, perhitungan server, penyesuaian alokasi tepat sekali, snapshot harga, dan rollback audit bersama perubahan.
3. Lengkapi idempotensi payload/session, penerbitan serta rotasi token privat, dan uji konkurensi dua koneksi MySQL.
4. Buat provisioning pemilik tanpa password statis, autentikasi username, otorisasi role/akun aktif, dan pengaturan usaha nyata.
5. Implementasikan pembatalan, produksi, upload bukti privat tervalidasi, kas/refund manual dan audit dalam service terpisah; verifikasi keterkaitan bukti dengan pesanan dan cegah pencatatan ulang.
6. Hubungkan formulir frontend ke respons server setelah layanan teruji; lanjutkan dashboard/laporan dan backup/pemulihan sesuai PRD.
