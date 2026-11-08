# **PRD Sistem Pre Order 4 Saudara** 

## **Product Requirements Document** 

Versi 2.0 | 12 Oktober 2026 | Acuan pengembangan dan pengujian 

Website ini memperkenalkan satu rasa khas Keripik Singkong 4 Saudara, menerima pengajuan preorder, dan membantu pengelola menerima pesanan sesuai kuota produksi. Dokumen menetapkan fitur, alur, desain, teknologi, data, keamanan, dan kriteria pengujian dalam satu acuan. 

## **1 Tujuan dan informasi produk** 

Tujuannya menyatukan pesanan dari website, WhatsApp, kunjungan langsung, dan titipan; mencatat pembayaran; serta menjaga jumlah yang dikonfirmasi tetap sesuai kapasitas setiap kelompok produksi atau batch. 

|**Informasi**|**Ketentuan**|
|---|---|
|Usaha dan produk|4 Saudara; satu produk keripik singkong dengan satu rasa khas.|
|Harga dan satuan|Harga awal Rp5.000 per pouch. Jumlah pesanan dan kuota memakai pouch.|
|Perhitungan|Total = jumlah pouch × harga satuan pesanan. Contoh 3 pouch = Rp15.000.|
|Nama dan bahasa|Merek tetap 4 Saudara. SaudaraOrder adalah nama kerja aplikasi. Antarmuka<br>memakai Bahasa Indonesia.|



## **2 Pengguna dan batasan versi awal** 

|**Pengguna**|**Hak dan manfaat**|
|---|---|
|Konsumen|Tanpa akun; melihat produk, mengajukan pesanan, melihat status pribadi, mengunggah<br>bukti setelah konfirmasi, dan meminta pembatalan sebelum produksi.|
|Pengelola|Login; memasukkan dan memproses pesanan, pembayaran, produksi, serta melihat<br>dashboard dan laporan.|
|Pemilik|Seluruh hak pengelola; mengatur produk, harga, batch, kuota, informasi usaha, dan akun<br>pengelola.|



Cakupan awal: satu usaha, satu rasa, satu satuan pouch, satu batch menerima pengajuan pada satu waktu, dan kuota ditetapkan manual oleh pemilik. Batch sebelumnya boleh tetap diproduksi setelah penerimaannya ditutup. 

Cara penyerahan awal: ambil di lokasi. Cara bayar awal: transfer manual atau bayar saat ambil. Keduanya dikonfirmasi bersama pemilik sebelum pemakaian. Foto, nomor WhatsApp, lokasi, rekening, kuota, dan estimasi siap diisi pengelola; data demonstrasi diberi label Data contoh. 

1 

## **3 Fitur dan prioritas pengembangan** 

Must have berarti wajib tersedia pada versi awal. Seluruh tindakan harus memakai data server; pesan sukses baru ditampilkan setelah data berhasil disimpan. ID fitur dipakai kembali pada pengujian dan serah terima. 

|**ID**|**Fitur**|**Perilaku dan hasil yang wajib tersedia**|
|---|---|---|
|F01|Etalase produk|Satu produk; foto, deskripsi, harga Rp5.000 per pouch, cara pesan, kontak,<br>batch dan sisa kuota. Tombol pesan nonaktif jika tidak ada batch terbuka atau<br>kuota habis.|
|F02|Pengajuan<br>pesanan|Nama, WhatsApp, jumlah bulat minimal satu pouch, cara bayar dan catatan.<br>Total dihitung server. Kunci pengajuan unik mencegah pesanan ganda saat klik<br>atau kirim ulang.|
|F03|Status pribadi|Kode dan tautan pelacakan pribadi; timeline, total, estimasi siap, pembayaran,<br>unggah bukti setelah konfirmasi dan pengajuan pembatalan. Tautan tidak<br>memakai kode pesanan biasa sebagai otorisasi.|
|F04|Akun admin|Login dengan username/password, logout dan ubah kata sandi. Hak diperiksa<br>server. Pemilik membuat, mengatur ulang kata sandi, atau menonaktifkan akun<br>pengelola.|
|F05|Produk dan usaha|Pemilik mengatur satu produk, foto, deskripsi, harga, kontak, lokasi dan<br>rekening. Satuan versi awal tetap pouch. Harga pesanan lama tidak berubah.|
|F06|Batch dan kuota|Buat batch, tetapkan kapasitas pouch, buka/tutup penerimaan, isi estimasi siap<br>dan tandai selesai. Kuota terpakai dan sisa dihitung serta diperiksa server.|
|F07|Kelola pesanan|Cari/filter, detail, input Website/WhatsApp/Langsung/Titipan, konfirmasi atau<br>tolak dengan alasan, ubah jumlah dan putuskan pembatalan sebelum produksi.|
|F08|Pembayaran|Unggah dan verifikasi/tolak bukti; catat penerimaan transfer atau tunai, jumlah<br>dan waktu. Catat pengembalian manual dan cegah pencatatan ganda.|
|F09|Produksi|Perbarui Dikonfirmasi, Diproduksi, Siap Diambil dan Selesai sesuai aturan.<br>Konsumen melihat riwayat yang relevan, tanpa catatan internal admin.|
|F10|Dashboard BI|Ringkasan pesanan, pouch, nilai pesanan, penerimaan bersih, pembatalan dan<br>penggunaan kuota. Grafik dan filter periode, batch, sumber dan status sesuai<br>data rinci.|
|F11|Laporan|Ekspor CSV dan cetak sesuai filter. Pesanan menurut tanggal pengajuan; kas<br>menurut waktu transaksi; kuota menurut batch.|
|F12|Riwayat dan<br>kontak|Catat pelaku, waktu, perubahan kuota, jumlah, status dan pembayaran.<br>Tampilkan pesan gagal yang jelas. WhatsApp membuka pesan yang dikirim<br>sendiri oleh pengguna.|



Should have: pengingat visual untuk pengajuan belum diproses dan pengembalian belum selesai; view data mart untuk kebutuhan praktik BI. Could have: testimoni nyata dengan izin pelanggan. Won't have: banyak rasa, keranjang multi-produk, akun konsumen, payment gateway, ongkir otomatis, bot WhatsApp, AI, atau jadwal produksi otomatis. 

2 

## **4 Aturan kuota dan perubahan pesanan** 

**Sisa kuota = kapasitas batch − alokasi pesanan Dikonfirmasi, Diproduksi, Siap Diambil, dan Selesai. Pesanan Menunggu Konfirmasi, Ditolak, dan Batal tidak memakai alokasi.** 

- Konfirmasi hanya berhasil saat penerimaan batch terbuka dan kuota cukup. Pemeriksaan, perubahan alokasi, status dan riwayat disimpan dalam satu transaksi dengan penguncian baris batch. Konfirmasi bersamaan tidak boleh melampaui kapasitas. 

- Jumlah boleh diubah saat Menunggu Konfirmasi atau Dikonfirmasi. Penambahan pada pesanan terkonfirmasi memeriksa sisa kuota; pengurangan mengembalikan selisih. Total memakai harga satuan yang tersimpan pada pesanan. 

- Permintaan pembatalan belum melepas kuota. Admin dapat menyetujui sebelum produksi; pelepasan alokasi terjadi tepat sekali. Sejak Diproduksi, perubahan jumlah dan pembatalan ditutup. Selesai tidak mengembalikan kuota. 

- Kapasitas harus positif dan tidak lebih kecil daripada alokasi yang terikat. Kapasitas dikunci setelah pesanan pertama dalam batch mulai diproduksi. Penerimaan ditutup setelah seluruh pengajuan tersisa diputuskan; batch selesai setelah semua pesanan dan pembayaran, termasuk pengembalian, tuntas. 

- Unggah bukti belum berarti Lunas. Transfer harus diverifikasi dan selisihnya diselesaikan sebelum produksi. Bayar saat ambil dapat diproduksi sebelum pembayaran; pelunasan wajib dicatat sebelum Selesai. 

- Setelah perubahan jumlah atau pembatalan, kelebihan pembayaran ditandai Perlu Pengembalian. Transaksi asli tetap ada; pengembalian dicatat sebagai transaksi baru. Harga baru hanya berlaku untuk pengajuan baru. 

## **5 Alur pelanggan dan pengelola** 

Konsumen membuka beranda, memilih jumlah pouch, mengisi identitas dan cara bayar, lalu menerima kode dan tautan pribadi. Halaman menampilkan bahwa kuota baru dipastikan setelah konfirmasi admin. Pengelola memproses pesanan sesuai kapasitas dan mengisi estimasi siap. 

**Urutan normal: Menunggu Konfirmasi → Dikonfirmasi → Diproduksi → Siap Diambil → Selesai. Cabang akhir: Ditolak atau Batal. Sistem memeriksa perpindahan status di server; tidak tersedia tombol untuk melompati tahapan.** 

|**Situasi**|**Tindakan berikutnya**|
|---|---|
|Transfer bank|Setelah konfirmasi, konsumen melihat rekening dan mengunggah bukti. Admin<br>memverifikasi uang masuk sebelum produksi.|
|Bayar saat ambil|Admin mencatat uang diterima saat penyerahan. Pesanan menjadi Selesai setelah<br>barang diserahkan dan pembayaran tuntas.|
|Pesanan kanal lain|Pengelola memasukkan pesanan dengan sumber WhatsApp, Langsung atau Titipan.<br>Selanjutnya mengikuti aturan yang sama.|
|Tautan hilang|Pengelola memverifikasi kontak pelanggan, lalu menerbitkan tautan baru dan<br>mengirimkannya manual. Tautan lama dinonaktifkan.|



Status pembayaran terpisah: Belum Lunas, Menunggu Verifikasi, Lunas, Perlu Pengembalian, dan Dikembalikan. Detail menampilkan uang diterima, uang dikembalikan, sisa tagihan atau pengembalian yang masih diperlukan. 

3 

## **6 Susunan layar dan desain interaksi** 

### **Halaman konsumen** 

Beranda memakai etalase dengan navigasi horizontal, foto utama lebar, judul yang menonjol, tombol pemesanan dan bagian produk di bawahnya. Foto, warna, dan tulisan mengikuti acuan desain yang dibagikan untuk keripik 4 Saudara. Galeri boleh memperlihatkan produk yang sama dari beberapa sudut; tidak menjadi daftar varian. 

|**Layar**|**Isi dan tujuan**|
|---|---|
|Beranda|Logo, Produk/Cara Pesan/Kontak, foto utama, harga, batch dan kuota, FAQ, tombol<br>Ajukan Pre-Order serta akses Cek Pesanan.|
|Form pre-order|Nama, WhatsApp, jumlah, cara bayar, catatan dan total. Tombol minus/plus dan<br>input angka; tidak ada pilihan rasa.|
|Pengajuan berhasil|Kode, ringkasan, Menunggu Konfirmasi, tautan pribadi yang dapat disalin dan tombol<br>menuju status.|
|Cek dan status|Input tautan/kunci pelacakan pribadi; detail, timeline, pembayaran dan pembatalan<br>sebelum produksi. Kode pesanan biasa tidak cukup untuk membuka detail.|
|Kontak|WhatsApp, lokasi pengambilan dan petunjuk kehilangan tautan. Nomor dan alamat<br>berasal dari pengaturan usaha.|



### **Halaman admin** 

Login membuka Dashboard, Pesanan, Batch Produksi, Pembayaran, Laporan dan Pengaturan. Sidebar dipakai pada desktop; menu ringkas dan kartu menggantikan tabel lebar pada ponsel. Riwayat tindakan ada di detail pesanan dan halaman pengaturan sesuai hak akses. 

### **Identitas dan animasi** 

Gunakan foto asli, ruang kosong cukup, kartu membulat dan bayangan lembut. Acuan visual terlampir diprioritaskan. Palet cadangan: putih/krem, teks cokelat gelap, aksen oranye tua. Harga Rp5.000 per pouch ditampilkan sebagai harga nyata. Judul promosi yang dapat digunakan: Satu rasa khas. Nikmatnya harus kamu coba. 

|**Bagian**|**Interaksi yang dirancang**|
|---|---|
|Pembuka dan galeri|Judul masuk halus; foto bergerak ringan; konten muncul sekali saat digulir.|
|Tombol dan formulir|Efek hover/tekan, indikator pengiriman, kesalahan di dekat kolom, ringkasan total<br>berubah saat jumlah diubah.|
|Status dan grafik|Transisi ringan saat data dimuat. Informasi status tetap terbaca tanpa animasi.|
|Aksesibilitas|Label, fokus keyboard, kontras jelas dan reduced motion. Tombol tetap mudah<br>dijangkau tanpa menutupi formulir.|



Jangan tampilkan testimoni, sertifikasi, rasa tambahan atau klaim usaha rekaan. Foto dan data demonstrasi diberi penanda; seluruh kuota, harga dan status saat dipakai berasal dari server. 

4 

## **7 Teknologi dan arsitektur pengembangan** 

Gunakan satu aplikasi Laravel dengan pola MVC, yaitu pemisahan data, logika proses dan tampilan. Pilihan berikut menjadi spesifikasi awal pengembangan. Gunakan pembaruan patch yang kompatibel, simpan composer.lock dan package-lock.json, serta catat versi yang benar-benar dipasang. Rujukan resmi ada di bagian 13. 

|**Kebutuhan**|**Pilihan**|**Peran dalam project**|
|---|---|---|
|Framework|Laravel 13.x|Routing, validasi, otorisasi, proses pesanan dan transaksi. [T1]|
|Bahasa server|PHP 8.4.x|Versi yang dipilih berada dalam dukungan Laravel 13. [T1]|
|Database|MySQL 8.4 LTS|Tabel memakai InnoDB, foreign key dan transaksi; charset<br>utf8mb4. [T2]|
|Template|Blade|Halaman konsumen dan admin dengan komponen yang dapat<br>dipakai ulang.|
|Tampilan|Tailwind CSS 4.x|Layout responsif, warna, tipografi dan komponen. [T3]|
|Interaksi|Alpine.js 3.x|Modal, jumlah pouch, menu, indikator dan state ringan. [T4]|
|Animasi|CSS dan JS|Transform, opacity dan IntersectionObserver; tidak memerlukan<br>library animasi tambahan.|
|Grafik BI|Chart.js 4.x|Grafik pesanan, sumber dan kuota. Selalu sertakan ringkasan<br>angka/tabel. [T5]|
|Build aset|Vite|Integrasi bawaan Laravel; versi kompatibel dikunci dalam<br>package-lock.json. [T6]|
|Pengelola paket|Composer 2.x|Dependensi PHP; npm dari Node.js 24 LTS untuk aset. [T6]|
|Perangkat lokal|VS Code dan Herd|Windows 10/11; PHP 8.4 dan Node 24. MySQL Community 8.4<br>dipasang terpisah dari Herd gratis. [T7]|
|Versi kode|Git|Riwayat perubahan; rahasia, data produksi dan bukti<br>pembayaran tidak masuk repository.|
|Pengujian|PHPUnit bawaan|php artisan test; database pengujian MySQL terpisah. Lighthouse<br>dan k6 untuk kinerja. [T9]|



### **Pembagian tanggung jawab kode** 

Browser → route dan middleware → controller serta Form Request → service pesanan/pembayaran → Eloquent dan MySQL. Blade membaca hasil server; Alpine mengelola interaksi. Nilai harga, kuota, total, status dan hak akses tidak dipercayakan kepada JavaScript. 

Pisahkan proses menjadi OrderService, BatchService, PaymentService dan ReportService. Semua perubahan kuota memakai transaksi dan urutan penguncian yang sama: pengaturan batch aktif, batch, lalu pesanan. Rollback membatalkan seluruh perubahan jika gagal. Pemeriksaan permintaan ulang dilakukan di server. [T2] 

Konfigurasi awal: SESSION_DRIVER=database, CACHE_STORE=file dan QUEUE_CONNECTION=sync; tanpa Redis atau worker khusus. Aset di-build lokal lalu dipasang pada server. Waktu disimpan UTC dan ditampilkan serta difilter menurut Asia/Jakarta. 

5 

## **8 Database dan definisi laporan** 

Nama database kerja: preorder_4_saudara. Struktur berikut adalah dasar ERD dan migration. Gunakan primary key, foreign key dan indeks; transaksi lama tidak dihapus melalui tindakan operasional. 

|**Tabel**|**Data utama dan relasi**|
|---|---|
|users|Nama, username unik, password hash, role pemilik/pengelola, is_active. Akun<br>konsumen tidak dibuat.|
|products|Satu produk aktif: nama, satu rasa, deskripsi, foto, price_rupiah dan satuan pouch.|
|business_settings|Satu baris: kontak, lokasi, rekening dan active_batch_id. Dikunci saat mengganti<br>batch agar penerimaan tidak terbuka ganda.|
|production_batches|product_id, nama, capacity_pouches, allocated_pouches, status penerimaan,<br>estimasi siap, started_at dan completed_at.|
|orders|batch_id, kode unik, idempotency_key unik, pelanggan, sumber,<br>quantity_pouches, unit_price_rupiah, total_rupiah, status, cara bayar, alokasi dan<br>estimasi. Simpan hash token pelacakan.|
|payment_proofs|order_id, lokasi berkas privat, status verifikasi, catatan, pengunggah dan<br>pemeriksa. Bukti baru tetap terpisah dari uang diterima.|
|cash_transactions|order_id, jenis penerimaan/pengembalian, nominal rupiah positif, waktu dan<br>admin; action_key unik. payment_proof_id unik jika ada mencegah bukti tercatat<br>dua kali.|
|cancellation_requests|order_id, alasan, status permintaan dan keputusan admin sebelum produksi.|
|audit_logs|Pelaku, order/batch terkait, tindakan, nilai sebelum/sesudah dan waktu. Log tidak<br>menyimpan token atau kata sandi.|



### **Integritas data** 

Tabel sessions memakai migration bawaan Laravel. Nominal uang berupa bilangan bulat rupiah, misalnya BIGINT UNSIGNED; jumlah pouch berupa integer positif. Total memakai harga snapshot. CHECK dan validasi menjaga alokasi antara nol dan kapasitas, sesuai jumlah alokasi pesanan. Indeks mencakup kode/token, tanggal, status, batch dan sumber. 

Token pelacakan berasal dari 32 byte acak kriptografis; hash disimpan untuk pencarian. Respons pengajuan dapat diputar ulang untuk session pengaju yang sama. Kunci pengajuan sama dengan isi berbeda ditolak. Tautan baru dapat diterbitkan admin setelah verifikasi kontak; token lama dinonaktifkan. 

### **Definisi dashboard dan Business Intelligence** 

Pesanan masuk dihitung dari pengajuan dalam periode. Nilai pesanan dan pouch terkonfirmasi memakai pesanan Dikonfirmasi sampai Selesai, dengan tanggal pengajuan dan status saat laporan dibuat. Penerimaan bersih = uang diterima terverifikasi − uang dikembalikan dalam periode transaksi. Pembatalan dan penolakan dihitung terpisah. Pemakaian kuota = alokasi batch dibagi kapasitasnya. 

Grafik melakukan roll-up harian, mingguan dan bulanan; filter periode/batch/sumber/status menyediakan slice dan dice. Klik ringkasan membuka detail untuk drill-down. Pengembangan Should have: view fact_order dan fact_cash dengan dimensi tanggal, batch dan sumber. Hindari penggandaan total karena satu pesanan dapat memiliki beberapa transaksi. 

6 

## **9 Keamanan dan kebutuhan operasional** 

- Login memakai session Laravel, password hash dan pembatasan percobaan. Session diganti saat login dan dihapus saat logout; cookie produksi Secure, HttpOnly dan SameSite. Role dan akun aktif diperiksa setiap request. Pemilik terakhir tidak dapat dinonaktifkan. Tidak ada registrasi admin publik. 

- Form memakai perlindungan pemalsuan permintaan, validasi server dan pembatasan frekuensi pengajuan. Harga atau status yang dikirim browser tidak dipercaya. Nilai keluaran di-escape; query memakai parameter atau Eloquent. 

- Status konsumen hanya dapat dibuka dengan token pribadi. Tautan tidak boleh masuk sitemap, indeks mesin pencari, log aplikasi atau analytics. Kode pesanan biasa tidak dapat dipakai untuk mencari data orang lain. 

- Bukti transfer JPG/PNG maksimal 5 MB; periksa MIME, ukuran dan isi berkas di server. Nama berkas dibuat server, disimpan privat, serta hanya dibuka melalui route yang memeriksa hak admin atau token pesanan terkait. 

- Nomor WhatsApp dan data pesanan hanya dipakai untuk pemrosesan order. Jangan tampilkan data pelanggan di halaman publik. Ekspor CSV melindungi kolom teks dari eksekusi rumus spreadsheet. Riwayat transaksi dan audit tetap dapat ditelusuri. 

- Gunakan pagination, indeks, query agregasi terpisah, foto terkompresi dan lazy loading. Kuota diperbarui saat halaman dimuat ulang dan setelah tindakan admin; setiap keputusan tetap memeriksa data terbaru di server. 

Browser sasaran: Chrome/Edge 111 atau lebih baru, Firefox 128 atau lebih baru, Safari 16.4 atau lebih baru, termasuk browser ponsel yang setara. Form, informasi harga dan status tetap bisa dipakai saat animasi dikurangi. [T3] 

## **10 Hosting pencadangan dan pemasangan** 

Kebutuhan server: hosting yang mendukung PHP 8.4, MySQL 8.4, HTTPS, penjadwalan backup, penyimpanan privat dan document root ke folder public. Pilihan awal adalah server Linux dengan Nginx dan PHP-FPM; penyedia dipilih menurut anggaran saat pemasangan. Ekstensi PHP: Ctype, cURL, DOM, Fileinfo, Filter, Hash, Mbstring, OpenSSL, PCRE, PDO/pdo_mysql, Session, Tokenizer dan XML. [T8] 

Sebelum pemasangan: backup, akun database aplikasi, .env privat, APP_KEY, APP_ENV=production dan APP_DEBUG=false; pasang paket dari lockfile, migration, build aset dan optimasi. Folder storage dan bootstrap/cache dapat ditulis proses web. Upload PHP 8M dan post 10M mendukung batas bukti 5 MB. Jangan jalankan migration fresh pada data usaha. [T8] 

Backup harian mencakup database, berkas privat, foto dan kunci konfigurasi yang disimpan aman. Salinan berada di tempat berbeda dari server aplikasi. Uji pemulihan pada lingkungan terpisah sebelum digunakan rutin; catat keberhasilan dan waktu pemulihan. Jika pembaruan gagal, gunakan versi kode sebelumnya dan pemulihan data yang telah diuji. 

Uji penerimaan mencakup login, satu alur pesanan lengkap, pembayaran, kuota dan CSV/cetak pada lingkungan demo. Bersihkan data contoh sebelum operasional. Kredensial pemilik dibuat melalui proses pemasangan; tidak ada password tetap di source code. 

7 

## **11 Pengujian dan kriteria siap digunakan** 

Gunakan black-box untuk seluruh F01 sampai F12 dan feature test untuk transaksi yang memengaruhi kuota/pembayaran. Uji konkurensi memakai MySQL, dua koneksi nyata dan database pengujian terpisah; SQLite tidak dipakai sebagai bukti perilaku penguncian MySQL. 

|**Kasus utama**|**Hasil yang harus dibuktikan**|
|---|---|
|Harga dan validasi|3 pouch menghasilkan Rp15.000; jumlah nol/negatif/pecahan dan manipulasi<br>harga ditolak. Harga berubah menampilkan ringkasan baru sebelum persetujuan<br>pelanggan.|
|Pengajuan ulang|Klik dua kali atau kirim ulang dengan kunci sama menghasilkan satu pesanan dan<br>tidak mengunci kuota.|
|Konfirmasi|Alokasi hanya bertambah saat konfirmasi berhasil. Permintaan konfirmasi ulang<br>tidak menambah alokasi lagi.|
|Konfirmasi bersamaan|Contoh uji: sisa 5 pouch, dua pesanan masing-masing 5 dikonfirmasi bersamaan.<br>Satu berhasil, satu mendapat pesan kuota tidak cukup.|
|Ubah jumlah|Kenaikan yang melebihi sisa ditolak; penurunan melepas selisih. Harga historis<br>tetap dipakai.|
|Pembatalan|Permintaan belum melepas kuota. Persetujuan melepas tepat sekali;<br>pembatalan/perubahan setelah produksi ditolak.|
|Kapasitas dan<br>penutupan|Penurunan di bawah alokasi ditolak; kapasitas terkunci setelah produksi; batch<br>ditutup hanya setelah pengajuan diputuskan. Selesai tidak melepas kuota.|
|Pembayaran|Unggah belum berarti lunas; verifikasi bukti berulang tidak menggandakan kas;<br>jumlah kurang/lebih terlihat dan dituntaskan sesuai cara bayar.|
|Pengembalian|Pembatalan pesanan berbayar menandai pengembalian; pencatatan ulang<br>dicegah; uang asli dan pengembaliannya sama-sama tercatat.|
|Akses dan berkas|Role salah, akun nonaktif, kode biasa/token salah dan berkas tidak valid ditolak.<br>Bukti pesanan lain tidak dapat dibuka.|
|Laporan dan pemulihan|Dashboard, CSV dan cetak cocok dengan data rinci pada periode/zona waktu yang<br>sama. Backup dapat dipulihkan.|



### **Metrik evaluasi yang dilaporkan** 

Laporkan persentase kasus uji berhasil, selisih alokasi terhadap perhitungan acuan, pelanggaran kuota, kesesuaian laporan, waktu pemulihan dan keberhasilan tugas pengelola. Lighthouse mencatat skor performa, LCP, CLS dan TBT; k6 mencatat waktu respons median/p95, throughput dan tingkat kesalahan. Simpan spesifikasi perangkat, jaringan, data dan beban uji. [T9] 

Metrik penelitian tidak diberi target angka. Kebenaran total, alokasi, hak akses dan pembayaran merupakan aturan fungsi yang wajib dipenuhi. Pemilik dan pengelola mencoba tugas utama; usability tambahan dengan SUS dapat menjadi evaluasi opsional sesuai kebutuhan penelitian. 

Keadaan antarmuka yang harus diuji: batch belum dibuka/ditutup, kuota habis, jaringan gagal, formulir invalid, loading, sukses, tabel kosong, tautan hilang, bukti ditolak, serta permintaan pembatalan atau pengembalian belum selesai. Pesan gagal menjelaskan tindakan berikutnya dan input yang sudah diisi tetap tersedia. 

8 

## **12 Tahap pengembangan dan serah terima** 

Estimasi awal enam minggu, disesuaikan jadwal kuliah dan masukan pemilik. Penyelesaian tahap ditentukan oleh hasil yang dapat diperiksa, bukan sekadar tampilan halaman. 

|**Tahap**|**Pekerjaan**|**Hasil yang diperiksa**|
|---|---|---|
|Minggu 1|Validasi kebutuhan, isi informasi<br>usaha, PRD dan ERD.|Aturan pemilik, ERD, daftar layar, rancangan Stitch<br>dan rencana uji.|
|Minggu 2|Siapkan Laravel/MySQL, migration,<br>akun dan batch.|Login/hak akses, satu produk Rp5.000, pembukaan<br>batch dan pengaturan kuota.|
|Minggu 3|Pengajuan, pesanan kanal lain dan<br>pengendalian kuota.|Alur pengajuan sampai konfirmasi; idempotensi,<br>perubahan jumlah dan uji konkurensi.|
|Minggu 4|Pembayaran, pembatalan, produksi<br>dan pelacakan.|Bukti privat, pencatatan kas/pengembalian dan<br>timeline yang konsisten.|
|Minggu 5|Dashboard BI, laporan, desain dan<br>animasi.|Ringkasan cocok dengan detail, filter/roll-up,<br>CSV/cetak dan tampilan ponsel.|
|Minggu 6|Pengujian, pemulihan, demo dan<br>perbaikan.|Hasil black-box/kinerja, backup berhasil, masukan<br>pengelola dan panduan penggunaan.|



### **Bahan untuk memulai implementasi** 

Acuan awal pengembang adalah PRD, desain Stitch, foto asli, informasi usaha, ERD dan rencana uji. Hasil implementasi diserahkan bersama source code, migration, test case dan README. README memuat versi, pemasangan, .env.example tanpa rahasia, akun, build, pengujian, deployment dan pemulihan. Desain dipetakan ke Blade, Tailwind dan Alpine; aturan diproses Laravel. 

Data yang perlu dilengkapi pemilik sebelum uji operasional adalah foto, nomor WhatsApp, lokasi ambil, rekening, kapasitas batch dan estimasi siap. Harga Rp5.000 per pouch dan satu rasa telah menjadi ketentuan produk. Nama kerja aplikasi dapat ditetapkan kembali tanpa mengubah nama usaha 4 Saudara. 

## **13 Rujukan teknis resmi** 

Dokumentasi diakses 12 Oktober 2026. Nomor T merujuk sumber kompatibilitas dan perilaku teknis; pilihan arsitektur serta aturan bisnis ditetapkan untuk project ini. 

- [T1] Laravel 13 release notes dan dukungan PHP 

- [T2] MySQL 8.4 rilis LTS 

- [T2] MySQL 8.4 InnoDB locking reads 

- [T3] Tailwind CSS kompatibilitas browser 

- [T4] Alpine.js instalasi 

- [T5] Chart.js dokumentasi 

- [T6] Vite kebutuhan Node.js 

- [T6] Node.js 24 LTS 

- [T7] Herd Windows layanan database 

- [T8] Laravel deployment dan kebutuhan server 

- [T9] Lighthouse penilaian performa 

- [T9] Grafana k6 pengujian beban 

9 

