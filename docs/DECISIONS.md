# Decisions: SaudaraOrder

## How to Use

- Buat ADR ketika pilihan teknologi, skema atau kompromi keamanan berubah; jangan membatalkan keputusan diam-diam.
- Catat Status, Tanggal, Konteks, Keputusan, Konsekuensi dan Alternatif; ADR pengganti menyebut keputusan yang digantikan.
- Baca SPEC dan ADR sebelum mengusulkan perubahan. Tugas dokumentasi tidak memberikan izin kode, migration atau deployment.

## Decision Records

### ADR-001: Platform web

Status: Accepted sebagai keputusan dokumentasi; tindakan implementasi tetap memerlukan izin.
Tanggal: 2026-11-06.
Konteks: Pengguna mengakses melalui desktop, tablet dan smartphone.
Keputusan: Pertahankan web responsif existing.
Konsekuensi: Tidak perlu aplikasi native; smartphone didahulukan.
Alternatif yang dipertimbangkan: Android/iOS/desktop khusus tidak masuk scope.

### ADR-002: Frontend dan UI

Status: Accepted sebagai keputusan dokumentasi; tindakan implementasi tetap memerlukan izin.
Tanggal: 2026-11-06.
Konteks: Stack dipilih mahasiswa: "SaudaraOrder merupakan proyek existing yang telah memiliki implementasi frontend, backend, autentikasi, database, dan pengujian. Mempertahankan stack menghindari pembangunan ulang, migrasi yang tidak diperlukan, dan risiko kerusakan fitur yang sudah berjalan."
Keputusan: Pertahankan Blade, CSS/JavaScript, Vite serta karakter frontend existing.
Konsekuensi: Tidak mengganti desain publik; Tailwind dependency tidak berarti seluruh UI ditulis ulang.
Alternatif yang dipertimbangkan: Rekomendasi kit adalah pembanding umum; bukan alasan migrasi framework.

### ADR-003: Backend dan data

Status: Accepted sebagai keputusan dokumentasi; tindakan implementasi tetap memerlukan izin.
Tanggal: 2026-11-06.
Konteks: Laravel dan MySQL telah memiliki sembilan entitas.
Keputusan: Pertahankan PHP/Laravel/MySQL, integer rupiah, snapshot dan constraint.
Konsekuensi: Kebutuhan alamat/penyerahan serta WhatsApp manual nullable perlu audit minimal; tanpa migration saat dokumentasi.
Alternatif yang dipertimbangkan: PostgreSQL/Supabase dan pembangunan ulang tidak dipilih.

### ADR-004: Autentikasi dan keamanan

Status: Accepted sebagai keputusan dokumentasi; tindakan implementasi tetap memerlukan izin.
Tanggal: 2026-11-06.
Konteks: Pelanggan tidak perlu akun; staf mempunyai role.
Keputusan: Session Laravel untuk staf, kode DAN token privat untuk pelanggan.
Konsekuensi: Hash token dan password, CSRF serta otorisasi server; informasi pelanggan tidak publik.
Alternatif yang dipertimbangkan: Registrasi pelanggan wajib dan kode pesanan sebagai satu-satunya kunci ditolak.

### ADR-005: Hosting dan deployment

Status: Accepted sebagai keputusan dokumentasi; tindakan implementasi tetap memerlukan izin.
Tanggal: 2026-11-06.
Konteks: Dosen meminta link GitHub dan Vercel pada Selasa 10 November 2026.
Keputusan: P0 evaluasi Vercel dan Aiven MySQL; Render Docker hanya alternatif melalui keputusan terpisah.
Konsekuensi: Runtime PHP komunitas, paket gratis, session/routing/aset/filesystem/MySQL harus diuji; belum ada deployment berizin.
Alternatif yang dipertimbangkan: Render tidak otomatis memenuhi kewajiban URL Vercel; tidak mengganti database demi hosting.

### ADR-006: Strategi pengujian dan level proses

Status: Accepted sebagai keputusan dokumentasi; tindakan implementasi tetap memerlukan izin.
Tanggal: 2026-11-06.
Konteks: Proyek individu, sisa sepuluh minggu, mahasiswa meminta hasil dapat diuji.
Keputusan: Loop satu tugas, PHPUnit, evaluasi browser automation, UAT dan MySQL concurrency terpisah.
Konsekuensi: Maksimal dua perbaikan otomatis per kegagalan; tes gagal tidak disebut selesai.
Alternatif yang dipertimbangkan: SQLite saja tidak cukup untuk membuktikan locking; level proses tanpa review tidak dipilih.

## Delegated Choices

| ID pertanyaan | Pilihan asisten | Alasan | Disetujui mahasiswa |
|---|---|---|---|
| C-14 | Tidak memilih risiko; hanya memperjelas pertanyaan | Mahasiswa sempat menjawab belum tahu, lalu menulis refleksi sendiri | Ya, refleksi mahasiswa sendiri dipertahankan |

Pencatatan C-14 dalam delegated_questions mengikuti jawaban awal, bukan klaim bahwa asisten menulis refleksi. C-14b dan C-17 adalah usulan yang dikonfirmasi, bukan keputusan bisnis sepihak.

## Assistant Proposals

| Bidang SPEC | Nilai/usulan | Alasan | Disetujui mahasiswa |
|---|---|---|---|
| meta.slug | Lihat nilai persis pada SPEC.json | Menstrukturkan keputusan untuk dokumentasi | Ya, pada rancangan final |
| meta.one_liner | Lihat nilai persis pada SPEC.json | Menstrukturkan keputusan untuk dokumentasi | Ya, pada rancangan final |
| features | Pengelompokan F1–F6 dan redaksi acceptance | Menstrukturkan keputusan untuk dokumentasi | Ya, pada rancangan final |
| stack.commands | Perintah kanonis C-17 | Menstrukturkan keputusan untuk dokumentasi | Ya, pada rancangan final |
| student_reflection.risk_controls | Tiga mitigasi C-14b | Menstrukturkan keputusan untuk dokumentasi | Ya, pada rancangan final |
| risks[1] | Lihat nilai persis pada SPEC.json | Menstrukturkan keputusan untuk dokumentasi | Ya, pada rancangan final |
| milestones[0].acceptance | Lihat nilai persis pada SPEC.json | Menstrukturkan keputusan untuk dokumentasi | Ya, pada rancangan final |
| data.personal_data | sensitive sebagai pemetaan konservatif transaksi | Menstrukturkan keputusan untuk dokumentasi | Pemetaan teknis masih ditandai terbuka, bukan keputusan bisnis baru |
| platform_specific.web.seo | false berarti tidak menambah fitur SEO; tidak menghapus existing | Menstrukturkan keputusan untuk dokumentasi | Pemetaan teknis masih ditandai terbuka, bukan keputusan bisnis baru |

Perubahan kebutuhan terhadap PRD lama:

- Penyerahan diperluas dari ambil di lokasi menjadi pengambilan/pengantaran; kesepakatan manual wajib sebelum konfirmasi pengantaran.
- Pembayaran awal dibatasi tunai saat penyerahan; transfer dan unggahan bukti ditunda tanpa menghapus tabel.
- Penyelesaian mensyaratkan barang diterima dan lunas; penyerahan dan pembayaran dicatat terpisah.
- Tracking memakai kode DAN token.
- Laporan lanjutan Should, pencarian dasar Must.
- Sisa roadmap 10 minggu dan milestone UTS P0 terpisah.
- Stack mengikuti implementasi existing, bukan seluruh pilihan awal PRD.

Konflik template diselesaikan mengikuti instruksi mahasiswa: existing bukan new; tidak menimpa PRD/README; tidak membuat CI/scaffold otomatis; batas dua bukan tiga percobaan; tidak membuat commit/push otomatis. Kutipan persona tidak dibuat. Contoh UI dan pembagian task adalah usulan engineering untuk review, bukan kebijakan usaha tambahan.

## Open Questions

1. Jam batas pengumpulan UTS dan cakupan minimum demo dosen. Pemilik keputusan: mahasiswa bersama pemilik usaha/dosen jika terkait. Tenggat: sebelum evaluasi P0 untuk kebutuhan UTS/hosting, atau sebelum implementasi fitur terkait untuk aturan operasional.

2. Biaya, wilayah dan jadwal pengantaran; tidak ada ongkir otomatis. Pemilik keputusan: mahasiswa bersama pemilik usaha/dosen jika terkait. Tenggat: sebelum evaluasi P0 untuk kebutuhan UTS/hosting, atau sebelum implementasi fitur terkait untuk aturan operasional.

3. Hosting final dan kompatibilitas Vercel; Render perlu keputusan terpisah jika Vercel terkendala. Pemilik keputusan: mahasiswa bersama pemilik usaha/dosen jika terkait. Tenggat: sebelum evaluasi P0 untuk kebutuhan UTS/hosting, atau sebelum implementasi fitur terkait untuk aturan operasional.

4. Alat browser automation dan database MySQL testing yang tersedia. Pemilik keputusan: mahasiswa bersama pemilik usaha/dosen jika terkait. Tenggat: sebelum evaluasi P0 untuk kebutuhan UTS/hosting, atau sebelum implementasi fitur terkait untuk aturan operasional.

5. Target angka pencatatan dan tracking. Pemilik keputusan: mahasiswa bersama pemilik usaha/dosen jika terkait. Tenggat: sebelum evaluasi P0 untuk kebutuhan UTS/hosting, atau sebelum implementasi fitur terkait untuk aturan operasional.

6. Pemulihan token pelanggan tanpa WhatsApp. Pemilik keputusan: mahasiswa bersama pemilik usaha/dosen jika terkait. Tenggat: sebelum evaluasi P0 untuk kebutuhan UTS/hosting, atau sebelum implementasi fitur terkait untuk aturan operasional.

7. Penerapan perubahan jumlah, pembatalan dan pengembalian dari PRD lama terhadap tunai saat penyerahan. Pemilik keputusan: mahasiswa bersama pemilik usaha/dosen jika terkait. Tenggat: sebelum evaluasi P0 untuk kebutuhan UTS/hosting, atau sebelum implementasi fitur terkait untuk aturan operasional.

8. Detail pemetaan sumber Perantara ke nilai existing Titipan dan status siap diambil untuk pengantaran. Pemilik keputusan: mahasiswa bersama pemilik usaha/dosen jika terkait. Tenggat: sebelum evaluasi P0 untuk kebutuhan UTS/hosting, atau sebelum implementasi fitur terkait untuk aturan operasional.

9. Format dan cakupan laporan lanjutan; PRD lama menyebut CSV/cetak. Pemilik keputusan: mahasiswa bersama pemilik usaha/dosen jika terkait. Tenggat: sebelum evaluasi P0 untuk kebutuhan UTS/hosting, atau sebelum implementasi fitur terkait untuk aturan operasional.

10. Konfirmasi detail teknis klasifikasi data dan SEO: sensitive dan seo=false adalah pemetaan dokumentasi, bukan penambahan data atau penghapusan SEO existing. Pemilik keputusan: mahasiswa bersama pemilik usaha/dosen jika terkait. Tenggat: sebelum evaluasi P0 untuk kebutuhan UTS/hosting, atau sebelum implementasi fitur terkait untuk aturan operasional.

Verifikasi versi runtime saat audit: php --version, composer --version, node --version, npm --version; deklarasi dependency bukan bukti versi deployment. Batas paket gratis harus diperiksa dari sumber resmi saat T-002. Dalam sesi ini tidak ada klaim layanan atau tes aplikasi baru. Penyimpanan SPEC tidak dianggap persetujuan rollout database atau penerapan migration rekening nullable.
