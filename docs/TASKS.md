# Tasks: SaudaraOrder

## Conventions

ID T-001 dan seterusnya unik. Status: todo, doing, review, done, blocked. Ukuran S kurang dari satu jam, M satu sampai tiga jam, L setengah hari merupakan perkiraan template, bukan jaminan. Pekerjaan yang ternyata lebih besar harus dipecah sebelum coding. Satu tugas utama setiap Loop; pilih todo yang dependensinya selesai. Status review menunggu mahasiswa; done setelah hasil diterima dan langkah Git yang relevan disetujui.

Daftar ini adalah rencana, bukan izin menjalankan semua task. T-001 mandiri sebagai audit existing, bukan setup ulang. P0 UTS berjalan sebelum backlog fitur; nomor task bukan urutan kalender mutlak. Seluruh perubahan CI/source/migration/deployment tetap memerlukan izin terpisah. Tidak memaksakan ketentuan generik kit 'CI hijau pada setup' ketika CI belum diizinkan.

## Milestones

| Milestone | Tujuan | Fitur | Perkiraan minggu/tanggal |
|---|---|---|---|
| Fondasi dan setup | Audit existing dan evaluasi demo UTS P0 | setup, R1–R3 | Sebelum Selasa 10 November 2026 |
| Backend pesanan | Pengajuan, integritas dan konfirmasi | F1, F2, F3 | 7–8 |
| Pelanggan dan tracking | Form publik dan detail privat | F1, F4 | 9–10 |
| Pembayaran dan laporan | Tunai, penyerahan, rekap | F3, F5, F6 | 11–12 |
| Integrasi dan UAT | Regresi, browser dan evaluasi pengguna | F1–F6 | 13–15 |
| Penyempurnaan dan rilis | Dokumen, video, presentasi dan rilis berizin | seluruh Must | 16 |

Deadline UTS tidak berarti seluruh fitur harus selesai. R1 memprioritaskan Must, R2 membatasi siklus, R3 mengevaluasi progres mingguan. Jam pengumpulan serta cakupan demo minimum dosen masih terbuka.

## Task List

| ID | Judul | Fitur | Ukuran | Status | Branch | Bergantung pada | Kriteria selesai |
|---|---|---|---|---|---|---|---|
| T-001 | Audit repositori existing dan kesenjangan engineering | setup, R1, R2, R3 | M | todo | docs/T-001-audit | - | Inventaris struktur, perintah, tests, status Git dan rahasia tanpa menampilkannya; laporan kesenjangan mandiri. Pemeriksaan runtime/CI hanya sesuai izin; tidak scaffold ulang. |
| T-002 | Evaluasi kompatibilitas Vercel untuk UTS P0 | setup | M | todo | docs/T-002-vercel | T-001 | Bukti kompatibilitas PHP/session/routing/aset/MySQL/filesystem dan batas paket; tandai belum terbukti; tanpa deploy. |
| T-003 | Siapkan rencana demo UTS dan gerbang izin | dokumen, R1 | M | todo | docs/T-003-uts | T-002 | Rencana perubahan minimum, cakupan demo, akses GitHub, risiko, rollback dan permintaan izin deployment konkret. |
| T-004 | Audit kebutuhan data penyerahan dan manual | F1, F3 | M | todo | docs/T-004-data | T-001 | Bandingkan kolom existing dengan alamat/metode/WhatsApp nullable/kesepakatan/penyerahan; usulkan penyesuaian tanpa migration otomatis. |
| T-005 | Simpan pengajuan pelanggan secara idempotent | F1 | L | todo | feat/T-005-pengajuan | T-004 | Setelah perubahan data diizinkan: pending tanpa alokasi, snapshot, token hash, key unik dan tes retry. Pecah lanjut bila melampaui setengah hari. |
| T-006 | Konfirmasi kuota atomik dan uji MySQL | F2, F3 | L | todo | feat/T-006-kuota | T-005 | Transaksi/locking serta konfirmasi ulang aman; concurrency dibuktikan DB testing MySQL atau laporkan belum teruji. |
| T-007 | Input manual dan pencarian admin | F3 | L | todo | feat/T-007-manual | T-005 | Sumber benar, WhatsApp manual opsional, pending tanpa alokasi, cari kode/nama dan role diperiksa. |
| T-008 | Tracking kode dan token privat | F4 | L | todo | feat/T-008-tracking | T-005 | Token salah/antar pesanan ditolak, detail/timeline sesuai server, internal tidak bocor; lost token tunggu kebijakan. |
| T-009 | Pencatatan tunai idempotent | F5 | L | todo | feat/T-009-tunai | T-006 | Penerimaan integer sesuai transaksi, action key unik, status pembayaran terpisah dan audit; tanpa transfer. |
| T-010 | Produksi, penyerahan dan penyelesaian | F3, F5 | L | todo | feat/T-010-penyerahan | T-009 | Transisi sah, penyerahan dicatat, Selesai ditolak sebelum diterima dan lunas. |
| T-011 | Dashboard dan laporan terfilter | F6 | L | todo | feat/T-011-laporan | T-010 | Scope Should ditinjau setelah Must; agregat cocok rinci, format ekspor dikonfirmasi sebelum implementasi. |
| T-012 | Evaluasi dan pasang browser automation setelah izin | setup, F1, F2, F3, F4, F5 | M | todo | test/T-012-browser | T-001 | Verifikasi alat/versi/biaya, usulkan dependency dan perintah; jangan klaim E2E tersedia sebelum dipasang dan diuji. |
| T-013 | UAT dengan pemilik dan pelanggan uji | F1, F2, F3, F4, F5, R3 | L | todo | test/T-013-uat | T-010, T-019 | Checklist dijalankan, hasil/kendala dicatat tanpa data pribadi; perbaikan menjadi task terpisah. |
| T-014 | Integrasi formulir publik dengan service pesanan | F1 | L | todo | feat/T-014-form | T-005 | Metode penyerahan dan alamat bersyarat, total server, error/loading/sukses; desain existing dipertahankan. |
| T-015 | Tinjau aturan pembatalan dan perubahan jumlah | F2, F3, F5 | M | todo | docs/T-015-pembatalan | T-004 | Mahasiswa memutuskan penerapan aturan PRD lama pada tunai; tidak menambah refund sebelum keputusan. |
| T-016 | Evaluasi regresi batch existing | F2 | M | todo | test/T-016-batch | T-006 | Kapasitas di bawah alokasi ditolak, satu batch menerima, kapasitas terkunci saat produksi dan penutupan sesuai aturan. |
| T-017 | Implementasi perubahan demo yang disetujui | setup | L | todo | feat/T-017-demo | T-003 | Hanya perubahan minimum yang secara terpisah diizinkan; tes relevan dan tinjauan sebelum deploy. |
| T-018 | Verifikasi link UTS sesudah deployment berizin | setup, R1 | M | todo | docs/T-018-link | T-017 | Sebelum 10 November 2026: GitHub dapat diakses dosen, URL Vercel HTTPS/routing/aset/session/fungsi demo diuji; kegagalan dilaporkan. |
| T-019 | Uji browser alur utama desktop dan mobile | F1, F2, F3, F4, F5 | L | todo | test/T-019-e2e | T-012, T-014, T-008, T-010 | Login, batch, pengajuan, konfirmasi, tracking dan penyelesaian diuji; simpan bukti tersamarkan. |
| T-020 | Review dokumentasi, README dan refleksi akhir | dokumen, R2, R3 | M | todo | docs/T-020-final | T-013 | README akurat dengan izin overwrite, laporan hasil, video/rencana presentasi, refleksi mahasiswa sendiri dan LESSONS diperbarui. |
| T-021 | Penyempurnaan dan rilis Capstone | setup, F1, F2, F3, F4, F5 | L | todo | docs/T-021-rilis | T-020 | Minggu 16: gerbang akhir, rollback/backup ditinjau, rilis hanya berizin; tanggal dosen dikonfirmasi. |

Nama dan prioritas fitur dari SPEC:

- F1 — Pre-Order Pelanggan (must).
- F2 — Manajemen Batch dan Kuota Produksi (must).
- F3 — Manajemen Pesanan Admin (must).
- F4 — Tracking Pesanan Privat (must).
- F5 — Pencatatan dan Verifikasi Pembayaran (must).
- F6 — Dashboard, Pencarian, dan Laporan (should).

Kendali risiko resmi:

- R1: "Memprioritaskan fitur Must. Menyelesaikan fitur pemesanan, manajemen pesanan, pengelolaan kuota batch, tracking privat, dan pencatatan pembayaran manual sebelum mengembangkan fitur tambahan berprioritas Should." (F1–F6)
- R2: "Menerapkan satu tugas utama per siklus Loop. Setiap pekerjaan dilakukan melalui tahap Plan → Code → Test → Review → Approval, dengan hasil pengujian dan perubahan yang terdokumentasi. Maksimal dua percobaan perbaikan otomatis untuk setiap kegagalan sebelum meminta arahan." (F1–F6)
- R3: "Melakukan evaluasi progres mingguan. Membandingkan hasil pengembangan dengan roadmap minggu ke-7 sampai minggu ke-16. Setiap laporan mingguan harus mencatat pekerjaan selesai, hasil pengujian, kendala, dan prioritas minggu berikutnya." (F1–F6)

## Backlog

F6 berprioritas Should; T-011 hanya dijalankan setelah kapasitas waktu dan format laporan ditinjau. Detail perubahan jumlah/pembatalan/pengembalian memerlukan keputusan T-015 sebelum task implementasi ditambahkan. Tidak ada backlog AI, varian, gateway atau integrasi kurir yang diam-diam menjadi komitmen proyek.
