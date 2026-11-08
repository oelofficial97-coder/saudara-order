# Product Requirements Document: SaudaraOrder

## Overview

| Field | Isi |
|---|---|
| Nama produk | SaudaraOrder |
| Platform | web existing |
| Level proses | loop |
| Tagline | Pesanan terpusat, produksi terkendali, status dapat dipantau. |
| Tim dan waktu | 1 mahasiswa, sisa 10 minggu |

Satu produk Keripik Singkong 4 Saudara, satu rasa khas, dengan harga Rp5.000 per pouch. Proyek sudah memiliki frontend, autentikasi pemilik, dashboard, pengaturan usaha, manajemen batch, sembilan entitas inti, dan pengujian. Halaman pre-order dan tracking publik masih prototipe; status implementasi terbaru dicatat terpisah dari kebutuhan. Keputusan terbaru pada SPEC.json mengatasi perbedaan kebutuhan tanpa menghapus riwayat PRD versi 2.0.

## Problem

Pencatatan pesanan Keripik Singkong 4 Saudara masih mengandalkan ingatan dan catatan manual sehingga pesanan dapat terlupakan atau tidak tersampaikan. Belum ada sistem terintegrasi untuk antrean produksi, kapasitas, status, dan penyelesaian transaksi. Pemesanan melalui WhatsApp, datang langsung, atau perantara; produksi setelah ada pesanan dengan perkiraan 1–3 hari dan pembayaran saat produk diterima. Orang yang paling terbantu adalah ibu/pengelola usaha yang selama ini harus mengingat pesanan. Belum ada kutipan atau survei pengguna yang membuktikan angka dampak.

## Goals and Non-Goals

### Goals

- Memusatkan pesanan website, WhatsApp, Langsung, dan Perantara agar dapat dicocokkan dengan daftar pesanan admin.
- Mengendalikan kapasitas batch tanpa kelebihan alokasi dan menyediakan tracking privat yang sesuai status pada admin.

### Non-Goals

- Tidak membuat multi-produk atau variasi rasa; satu produk Rp5.000 per pouch.
- Tidak mewajibkan akun pelanggan atau membuat aplikasi native.
- Tidak mengaktifkan transfer, unggahan bukti, atau payment gateway pada tahap awal.
- Tidak mengintegrasikan WhatsApp API, kurir eksternal, GPS, atau perhitungan ongkir otomatis.
- Tidak mengganti stack, membangun ulang proyek, atau menghapus fitur existing.
- Tidak menambahkan AI dalam aplikasi, PWA, offline, multi-bahasa, atau mode gelap terpisah.
- Tidak menambahkan email transaksional, realtime, peta, analitik pihak ketiga, atau penyimpanan eksternal tanpa persetujuan.

Batasan menjaga waktu proyek individu dan menghindari integrasi yang belum dibutuhkan atau disetujui.

## Target Users

| Persona | Kebutuhan | Kutipan |
|---|---|---|
| Pelanggan masyarakat umum | Memesan tanpa akun dan memantau pesanan sendiri secara privat. | Belum ada kutipan langsung dari pengguna. |
| Ibu/pengelola usaha | Mencatat dan memproses seluruh pesanan secara terpusat sesuai hak akses. | Belum ada kutipan langsung dari pengguna. |
| Pemilik usaha | Mengatur batch, kapasitas, pengaturan usaha, dan memantau transaksi. | Belum ada kutipan langsung dari pengguna. |

- Utamakan smartphone, juga mendukung tablet dan desktop.
- Koneksi lambat atau terputus dan perangkat rendah merupakan pertimbangan desain, bukan hasil survei khusus.
- Bahasa Indonesia sederhana; formulir ringkas, teks dan tombol mudah dibaca, animasi ringan.
- Loading, empty, dan error state jelas; sukses hanya setelah server mengonfirmasi penyimpanan.

## Core Features

### F1 - Pre-Order Pelanggan (must)

Pengajuan pesanan website tanpa akun pada batch yang menerima pengajuan, dengan pilihan pengambilan atau pengantaran.

Acceptance Criteria:

- Pengajuan valid menghasilkan kode dan token tracking privat, status Menunggu Konfirmasi, tanpa alokasi kuota.
- Server menghitung 3 pouch sebagai Rp15.000 dan menyimpan snapshot harga; jumlah bukan integer positif ditolak.
- Pengantaran tanpa alamat ditolak; pengambilan tidak mewajibkan alamat; WhatsApp website wajib.
- Idempotency key sama tidak menggandakan pesanan; payload berbeda dengan key sama ditolak.
- Tidak menampilkan keberhasilan sebelum penyimpanan dikonfirmasi server.

### F2 - Manajemen Batch dan Kuota Produksi (must)

Pertahankan manajemen batch existing dan kembangkan alokasi yang aman terhadap konkurensi.

Acceptance Criteria:

- Kuota hanya dialokasikan ketika konfirmasi berhasil; konfirmasi ulang tidak menambah alokasi lagi.
- Dua konfirmasi bersamaan tidak boleh mengalokasikan melebihi kapasitas; buktikan dengan MySQL testing terpisah.
- Kapasitas di bawah alokasi ditolak; kapasitas terkunci setelah produksi dimulai.
- Hanya satu batch menerima pengajuan pada satu waktu; batch tertutup menolak pengajuan baru.
- Selesai tidak melepas kuota; penerimaan batch baru boleh ditutup setelah seluruh pengajuan Menunggu Konfirmasi pada batch itu diputuskan.

### F3 - Manajemen Pesanan Admin (must)

Kelola pesanan lintas saluran, konfirmasi, penolakan, produksi, penyerahan, dan penyelesaian sesuai hak akses.

Acceptance Criteria:

- Pemilik/pengelola berwenang memasukkan pesanan WhatsApp, Langsung, atau Perantara; WhatsApp manual boleh kosong; pesanan menunggu konfirmasi tanpa alokasi.
- Pencarian kode atau nama mengembalikan pesanan yang sesuai; akses tanpa izin ditolak.
- Pengantaran belum disepakati dengan pelanggan tidak dapat dikonfirmasi atau mengalokasikan kuota.
- Pesanan hanya dapat menjadi Selesai setelah produk diterima dan pembayaran lunas; penyerahan tanpa pelunasan dicatat terpisah.
- Penolakan mempunyai alasan dan setiap perubahan penting menyimpan audit pelaku, waktu, dan perubahan.

### F4 - Tracking Pesanan Privat (must)

Pelanggan melihat pesanan sendiri menggunakan kode dan token privat tanpa akun.

Acceptance Criteria:

- Kode dan token valid membuka pesanan terkait; kode saja, token salah, atau token pesanan lain ditolak.
- Detail menampilkan status, jumlah pouch, total, batch, estimasi kesiapan, pembayaran, dan timeline sesuai database.
- Token disimpan sebagai hash; catatan internal admin dan rahasia tidak ditampilkan pada pelanggan.

### F5 - Pencatatan dan Verifikasi Pembayaran (must)

Pencatatan manual tunai saat penyerahan, terpisah dari status pesanan, dengan riwayat yang dapat diaudit.

Acceptance Criteria:

- Admin berwenang mencatat pembayaran tunai dengan nominal sesuai transaksi yang disepakati; nominal rupiah berupa integer.
- Pengulangan tindakan pembayaran tidak menggandakan transaksi penerimaan.
- Status pembayaran terpisah; produk diserahkan tetapi belum lunas tidak menjadi Selesai.
- Transfer, gateway, dan unggahan bukti tidak diaktifkan; struktur existing tetap dipertahankan.

### F6 - Dashboard, Pencarian, dan Laporan (should)

Kembangkan dashboard existing dengan rekap operasional, filter, dan ekspor berdasarkan data sebenarnya.

Acceptance Criteria:

- Ringkasan pesanan, pembayaran, dan kuota cocok dengan data rinci; tanpa data menampilkan empty state, bukan statistik palsu.
- Filter menghasilkan data sesuai kriteria; laporan unduhan sesuai data rinci dan filter yang sama.
- Pencarian dasar kode/nama tetap Must pada F3; perluasan laporan tidak menunda fitur inti.

## User Flows

1. F1 → F4, **pengambilan langsung**: pelanggan website mengisi nama, WhatsApp, jumlah pouch dan catatan bila perlu, lalu memilih pengambilan. Alamat pengantaran tidak diwajibkan. Setelah server menyimpan, pelanggan menerima kode dan token; pesanan tetap Menunggu Konfirmasi dan belum mengurangi kuota. Lokasi/jadwal pengambilan mengikuti informasi usaha dan komunikasi operasional yang tersedia; tidak ditetapkan jadwal baru dalam dokumen ini.
2. F1 → F3 → F2, **pengantaran**: pelanggan memilih pengantaran dan wajib mengisi alamat. Admin memeriksa kelayakan lokasi, jadwal, dan ketentuan penyerahan melalui komunikasi manual; biaya pengantaran belum menjadi kebijakan dan tidak dihitung otomatis. Sebelum kesepakatan tercapai, pesanan tetap Menunggu Konfirmasi, tidak dapat dikonfirmasi dan tidak mengalokasikan kuota. Setelah kesepakatan, konfirmasi tetap memeriksa kapasitas dalam transaksi; kuota tidak cukup menghasilkan pesan gagal tanpa perubahan sebagian.
3. F3 → F5 → F4: pemilik/pengelola memasukkan pesanan WhatsApp, Langsung, atau Perantara; nomor WhatsApp manual boleh kosong jika tidak tersedia. Pesanan menggunakan alur konfirmasi yang sama. Saat produk diserahkan melalui pengambilan atau pengantaran, admin mencatat pembayaran tunai manual. Jika sudah diserahkan tetapi belum lunas, status penyerahan dan pembayaran tetap terpisah dan pesanan belum menjadi Selesai. Pelanggan melacak status dengan kode dan token; jaringan gagal tidak ditampilkan sebagai keberhasilan.

### Fondasi existing yang dipertahankan

Login/logout session, role pemilik/pengelola, dashboard, pengaturan usaha, dan manajemen batch yang sudah ada tetap dipakai. Sembilan entitas inti, termasuk `payment_proofs`, `cash_transactions`, dan `cancellation_requests`, tidak dihapus meskipun transfer serta unggahan bukti belum aktif. Admin yang berwenang tetap mengelola status produksi dan audit tindakan penting. Pengelolaan akun pengelola, penyuntingan produk/harga/foto, perubahan jumlah, pembatalan, pengembalian, dan pemulihan token yang disebut PRD versi 2.0 **tidak dinyatakan telah terimplementasi** oleh dokumen ini; aturan atau prioritas lanjutannya memerlukan konfirmasi sebelum tugas baru dijadwalkan. Harga pesanan yang sudah dibuat tetap memakai snapshot; harga produk saat ini Rp5.000/pouch.

### Aturan kuota yang tetap menjadi acuan

Sisa kuota batch adalah kapasitas dikurangi alokasi pesanan yang sudah Dikonfirmasi, Diproduksi, siap diserahkan, atau Selesai. Menunggu Konfirmasi, Ditolak, dan Batal tidak memakai alokasi. Konfirmasi hanya boleh pada batch yang menerima pengajuan dan mempunyai sisa kapasitas cukup; pemeriksaan, perubahan counter, status, dan audit dilakukan atomik agar permintaan bersamaan tidak melampaui kapasitas. Kapasitas tidak boleh turun di bawah alokasi dan terkunci setelah produksi dimulai. Selesai tidak melepaskan kuota. Batch yang penerimaannya ditutup tidak menerima pengajuan baru; batch lama boleh masih diproduksi. Penutupan penerimaan menunggu semua pengajuan tertunda pada batch itu diputuskan.

**Permintaan pembatalan tidak otomatis melepaskan kuota.** Jika pesanan sudah terkonfirmasi, alokasinya tetap tercatat selama permintaan menunggu keputusan. Aturan lama menyebut pelepasan hanya setelah persetujuan admin sebelum produksi, tepat sekali; cara mengajukan, memutuskan, mengomunikasikan, serta menangani pembayaran pada alur baru tetap Open Question dan tidak diaktifkan otomatis oleh dokumen ini.

Label operasional **Perantara** merujuk sumber pesanan melalui orang lain. Kolom `orders.order_source` existing menggunakan nilai `titipan`, bukan `perantara`. Pemetaan label Perantara ke `titipan` merupakan penyesuaian teknis yang perlu disetujui saat implementasi; jangan menambah nilai enum atau mengubah data lama tanpa keputusan. Status `siap_diambil` existing juga perlu pemetaan tampilan untuk pesanan pengantaran sebelum perubahan skema/status apa pun.

## AI Features

Tidak ada fitur AI dalam aplikasi; alat AI hanya mendampingi pengembangan.

## Non-Functional Requirements

| Kategori | Persyaratan |
|---|---|
| Performa | Aset ringan, pagination dan query agregasi; target angka belum disepakati |
| Aksesibilitas | Keyboard, label, kontras, target sentuh, dan reduced motion dievaluasi; WCAG 2.2 AA hanya acuan evaluasi sampai disetujui sebagai persyaratan dan diuji, belum klaim kepatuhan |
| Koneksi | Error jelas dan idempotensi saat pengajuan diulang |
| Bahasa | Bahasa Indonesia, rupiah integer, tampilan tanggal Asia/Jakarta |
| Privasi | Data sendiri melalui kode/token; data operasional hanya staf berwenang |
| Ketersediaan | Uji URL demo nyata; tidak menjanjikan SLA paket gratis |

## Success Metrics

| Metrik | Target | Cara Ukur |
|---|---|---|
| Ketepatan pencatatan pesanan | Target angka belum ditentukan. | Bandingkan pesanan masuk dengan database/dashboard sebelum implementasi dan setelah penggunaan. |
| Ketepatan pengendalian kuota | Tidak boleh terjadi kelebihan alokasi kuota. | Uji transaksi, perubahan status, dan konfirmasi bersamaan pada MySQL testing sebelum implementasi operasional. |
| Keberhasilan tracking privat | Target angka belum ditentukan. | Uji token valid/tidak valid dan kesesuaian status sebelum implementasi dan saat UAT. |

Milestone P0 UTS: Selasa, 10 November 2026, link repository GitHub dan website Vercel dapat diperiksa dosen. Evaluasi kompatibilitas segera didahulukan; pelaksanaan deployment memerlukan izin terpisah. Ini bukan tenggat seluruh fitur. Final mengikuti minggu ke-16; tanggal resmi belum ditentukan.

## Risks and Open Questions

### Risiko menurut mahasiswa

Risiko terbesar adalah keterbatasan waktu pengembangan.

### Kendali risiko yang disetujui mahasiswa

- R1: "Memprioritaskan fitur Must. Menyelesaikan fitur pemesanan, manajemen pesanan, pengelolaan kuota batch, tracking privat, dan pencatatan pembayaran manual sebelum mengembangkan fitur tambahan berprioritas Should." (F1–F6)
- R2: "Menerapkan satu tugas utama per siklus Loop. Setiap pekerjaan dilakukan melalui tahap Plan → Code → Test → Review → Approval, dengan hasil pengujian dan perubahan yang terdokumentasi. Maksimal dua percobaan perbaikan otomatis untuk setiap kegagalan sebelum meminta arahan." (F1–F6)
- R3: "Melakukan evaluasi progres mingguan. Membandingkan hasil pengembangan dengan roadmap minggu ke-7 sampai minggu ke-16. Setiap laporan mingguan harus mencatat pekerjaan selesai, hasil pengujian, kendala, dan prioritas minggu berikutnya." (F1–F6)

Risiko kompatibilitas Vercel dan konkurensi MySQL dikendalikan dengan evaluasi nyata, bukan asumsi keberhasilan. Seluruh pertanyaan terbuka berikut memerlukan keputusan sebelum implementasi terkait:

- Jam batas pengumpulan UTS dan cakupan minimum demo dosen.
- Biaya, wilayah dan jadwal pengantaran; tidak ada ongkir otomatis.
- Hosting final dan kompatibilitas Vercel; Render perlu keputusan terpisah jika Vercel terkendala.
- Alat browser automation dan database MySQL testing yang tersedia.
- Target angka pencatatan dan tracking.
- Pemulihan token pelanggan tanpa WhatsApp.
- Penerapan perubahan jumlah, pembatalan dan pengembalian dari PRD lama terhadap tunai saat penyerahan.
- PRD versi 2.0 mengizinkan perubahan jumlah saat Menunggu Konfirmasi atau Dikonfirmasi, dengan pemeriksaan selisih kuota; sejak Diproduksi, perubahan jumlah dan pembatalan ditutup. Apakah batas waktu dan prosedur ini tetap berlaku pada alur pengambilan serta pengantaran yang baru?
- PRD versi 2.0 menyatakan permintaan pembatalan belum melepas kuota dan persetujuan admin melepasnya tepat sekali sebelum produksi. Bagaimana pembatalan diminta, diputuskan, dan diinformasikan pada alur tunai saat penyerahan?
- PRD versi 2.0 meminta kelebihan pembayaran dicatat sebagai Perlu Pengembalian, transaksi asli dipertahankan dan pengembalian dicatat terpisah. Karena pembayaran awal sekarang tunai saat penyerahan, kapan situasi ini berlaku dan siapa yang berwenang mencatatnya?
- Apakah pengelolaan akun pengelola dan penyuntingan harga/foto produk dari PRD versi 2.0 masuk cakupan tahap berikutnya? Struktur dan fitur existing tidak dihapus sambil menunggu keputusan.
- Detail pemetaan sumber Perantara ke nilai existing Titipan dan status siap diambil untuk pengantaran.
- Format dan cakupan laporan lanjutan; PRD lama menyebut CSV/cetak.
- Konfirmasi detail teknis klasifikasi data dan SEO: sensitive dan seo=false adalah pemetaan dokumentasi, bukan penambahan data atau penghapusan SEO existing.

Perubahan kebutuhan dicatat pada `SPEC.json` bagian `requirements_changes` dan `docs/DECISIONS.md`. Aturan lama yang belum dikonfirmasi tetap terdokumentasi di atas sebagai pertanyaan terbuka; tidak boleh dianggap fitur yang sudah berjalan atau kebijakan baru yang disahkan.
