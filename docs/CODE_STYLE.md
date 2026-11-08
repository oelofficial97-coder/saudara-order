# Code Style: SaudaraOrder

## Language and Tooling

composer.json: PHP ^8.3; Laravel ^13.0; PHPUnit ^12.5.12; Pint ^1.27.

package.json: Vite ^7.0.7; Tailwind CSS ^4.0.0.

MySQL 8.4.3 menurut kondisi proyek yang dikonfirmasi; verifikasi runtime sebelum deployment.

Gunakan Laravel Pint: `php vendor/bin/pint --test`. PHP, Blade, CSS dan JavaScript mengikuti gaya existing. Tidak menambahkan TypeScript, ESLint atau formatter lain hanya karena contoh template. Deklarasi Composer bukan bukti versi PHP tersedia pada Vercel.

## Naming

| Objek | Konvensi | Contoh |
|---|---|---|
| Kelas | PascalCase | ProductionBatch |
| Request | Akhiran Request | BusinessSettingRequest |
| Method | camelCase | confirmOrder (usulan) |
| Tabel/kolom | snake_case | production_batches, allocated_pouches |
| Variabel | istilah domain jelas | quantityPouches |
| Environment | UPPER_SNAKE_CASE | DB_CONNECTION |
| Test | menjelaskan perilaku | konfirmasi tidak melebihi kapasitas |

Jangan mengganti nama enum existing hanya untuk menyesuaikan label UI tanpa evaluasi kompatibilitas.

## Structure and Patterns

Controller mengoordinasikan request/response; Form Request memvalidasi input; middleware/policy memeriksa akses; service mengelola transaksi; model memetakan data; Blade menampilkan keluaran. Pertahankan service batch existing. Ukuran fungsi dibatasi tanggung jawab, tanpa menetapkan angka baris yang belum disetujui.

Contoh konsep F1 yang benar: total dihitung dari quantity tervalidasi dan harga produk server, lalu harga disalin ke snapshot. Contoh salah: menyimpan total_rupiah dari browser tanpa menghitung ulang. F2 memeriksa kapasitas di transaksi yang sama dengan perubahan alokasi; pemeriksaan sebelum transaksi saja rentan race condition. Contoh ini adalah pedoman, bukan implementasi fitur.

## Error Handling

Bedakan validasi yang dapat diperbaiki pelanggan dengan kegagalan infrastruktur. Kesalahan kuota memberi pesan kuota tidak cukup dan tidak menyimpan perubahan sebagian. Jaringan putus setelah POST tidak membuktikan gagal simpan; retry harus memakai identitas pengajuan yang sama. Log teknis disamarkan; pengguna tidak menerima SQL, stack trace, password atau token. Jangan menangkap semua exception lalu mengembalikan sukses.

## Comments and Documentation

Komentar menjelaskan alasan invariant, misalnya pending tidak mengurangi kuota. Dokumentasikan kontrak service dan efek transaksi yang tidak jelas dari tipe. Hindari kode mati yang dikomentari. Perubahan kebutuhan dicatat dalam DECISIONS dan hasil nyata di PROGRESS. PRD/README existing masih dilindungi sampai draf penggantinya disetujui. Jangan mengubah catatan historis agar terlihat seolah fitur pernah lulus tes.

## Commit Messages

Format Conventional Commits: `tipe(ruang-lingkup): ringkasan imperatif`.

| Tipe | Contoh |
|---|---|
| feat | feat(orders): simpan pengajuan privat |
| fix | fix(batch): cegah alokasi berulang |
| docs | docs(spec): catat milestone UTS |
| test | test(tracking): tolak token pesanan lain |
| refactor | refactor(payments): pisahkan pencatatan kas |
| chore | chore(deps): perbarui dependensi yang disetujui |
| ci | ci(test): isolasi database pengujian |

Ringkasan maksimal 72 karakter, satu perubahan logis. Sebut task terkait dalam badan PR, misalnya T-005 untuk pengajuan. Semua contoh bukan izin commit/push.

## Dependencies

Composer dan NPM tetap memakai lockfile existing. Sebelum menambah paket browser automation atau runtime hosting, periksa keberadaan paket, pemeliharaan, lisensi dan kompatibilitas PHP/Node. Jangan membuat ulang lockfile tanpa alasan. Dependensi besar dan perubahan arsitektur dicatat sebagai ADR baru. Evaluasi runtime komunitas PHP tidak sama dengan memasangnya.

## Forbidden Patterns

- Rahasia atau token privat dalam kode/log.
- SQL hasil penyambungan input pelanggan.
- Mengubah total atau status berdasarkan browser tanpa validasi.
- Menonaktifkan test agar lulus.
- Mengubah assertion untuk menutupi bug.
- Kode tidak terpakai atau dependency ganda.
- Membocorkan detail exception kepada pelanggan.
- Menganggap SQLite membuktikan locking MySQL.
- Menghapus transaksi/audit operasional.
- Mengubah frontend publik di luar scope.

## AI-Generated Code Rules

Mahasiswa meninjau setiap perubahan dan menjelaskan alasan teknisnya. Kerjakan satu tugas per Loop dan verifikasi API dari kode/dokumentasi resmi saat diperlukan. Jalankan pengujian relevan sebelum menerima hasil; kegagalan menghentikan klaim selesai. Setelah dua percobaan perbaikan otomatis gagal, catat bukti dan minta arahan. Jangan menambah test yang sekadar mengulang implementasi tanpa memeriksa aturan bisnis.
