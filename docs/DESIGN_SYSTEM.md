# Design System: SaudaraOrder

## Principles

1. Pertahankan identitas SaudaraOrder: halaman pesanan baru menggunakan karakter landing dan admin yang sudah ada.
2. Utamakan smartphone: formulir pelanggan tidak memerlukan akun atau data yang tidak dibutuhkan.
3. Tampilkan informasi nyata: kuota dan total berasal dari server, bukan angka simulasi.
4. Berikan umpan balik yang jujur: koneksi gagal tidak menghasilkan layar sukses.
5. Hindari beban tambahan: animasi ringan dan aset terkompresi menjaga pengalaman perangkat rendah.

## Color Tokens

| Token | Nilai existing | Penggunaan |
|---|---|---|
| Public forest | #1C221A | Warna primer publik |
| Public ink | #182019 | Teks |
| Public sage | #D6E5AA | Aksen |
| Public cream | #E9E7DD | Latar krem |
| Public paper | #F7F4EB | Permukaan |
| Admin green | #173C32 | Sidebar dan tombol |
| Admin sage | #DCE7D9 | Permukaan lembut |
| Admin cream | #F7F6F0 | Latar admin |
| Admin muted | #64736A | Teks pendukung |
| Admin line | #E1E6DC | Batas |
| Admin focus | #B68B48 | Fokus keyboard |
| Admin success | #28553B | Teks sukses |
| Admin error | #8C3D28 | Teks galat |

Nilai dibaca dari CSS existing. Rasio kontras belum dihitung; evaluasi target teks 4,5:1 dan komponen 3:1 sesuai acuan template, bukan klaim kepatuhan. Warna peringatan baru tidak ditetapkan; gunakan pola notice existing dan teks yang jelas. Tidak membuat tema gelap terpisah.

## Typography

| Gaya | Ukuran existing | Bobot/tinggi | Penggunaan |
|---|---|---|---|
| Judul publik | Responsif, lihat site.css | DM Serif Display/Georgia | Judul halaman |
| Judul admin | 36px desktop, 31px breakpoint kecil | normal/1.2 Georgia | Judul dashboard |
| Isi admin | 14px | normal/1.6 Arial/Helvetica | Isi panel |
| Input jumlah publik | 16px | 700 | Jumlah pouch |
| Keterangan admin | 11–13px | sesuai komponen | Bantuan |

Template menyarankan isi web 16px; existing admin 14px. Catat sebagai evaluasi keterbacaan sebelum perubahan, bukan mengubah desain secara otomatis. Ukuran dan font publik rinci tetap mengikuti CSS; jangan menambah font eksternal baru tanpa kebutuhan.

## Spacing and Layout

Pertahankan breakpoint publik 1100/760/420px dan admin 1150/800/580px dari CSS existing. Admin memakai sidebar desktop, navigasi mobile, panel dan tabel yang dapat digulir. Main admin memiliki max-width 1580px; padding menyesuaikan breakpoint. Tidak memperkenalkan skala jarak baru: gunakan spacing komponen existing. Uji form alamat panjang, nama pelanggan panjang, dan pesan error tanpa overflow horizontal pada ponsel.

## Components

- Tombol utama: aksi simpan/konfirmasi; default, hover/fokus, disabled dan loading harus jelas; label berubah hanya bila proses benar-benar berlangsung.
- Input nama/WhatsApp/jumlah: label tetap terlihat, invalid disertai pesan; catatan opsional diberi label opsional.
- Pilihan penyerahan: pengambilan/pengantaran; alamat wajib hanya pengantaran. Jangan menambahkan biaya otomatis.
- Panel ringkasan: jumlah dan total tampil, tanpa statistik palsu.
- Tabel batch/pesanan: status teks, kapasitas dan alokasi; empty state menyediakan petunjuk.
- Notice sukses/galat: sukses setelah server menyimpan, galat tidak membocorkan SQL.
- Navigasi: gunakan Blade dan CSS existing, bukan pustaka UI baru.

## Accessibility

Periksa label terhubung dengan input, urutan tab sesuai alur, fokus keyboard terlihat, teks alternatif foto, status tidak hanya dibedakan warna, serta error dapat dipahami pembaca layar. Acuan WCAG 2.2 AA dari template dipakai untuk evaluasi; belum ada hasil audit. Target sentuh minimum acuan 24×24 CSS px dan rekomendasi mobile 44–48px perlu diperiksa pada komponen existing. Reduced motion harus mempertahankan seluruh informasi. Pertimbangan perangkat rendah berasal dari sasaran pengguna, bukan survei aksesibilitas khusus.

## Motion and Feedback

Pertahankan timing CSS existing; jangan menambah animasi berat. Hormati prefers-reduced-motion. Loading menonaktifkan pengiriman berulang secara visual, sementara idempotensi tetap diperiksa server. Empty: 'Belum ada pesanan pada filter ini.' Error F1: 'Alamat pengantaran wajib diisi.' Error F2: 'Kuota tidak cukup untuk mengonfirmasi pesanan ini.' Error jaringan: 'Penyimpanan belum dapat dipastikan. Periksa koneksi dan coba kembali.' Pesan adalah usulan redaksi implementasi, bukan bukti UI sudah berubah.

## Content and Voice

Gunakan Bahasa Indonesia singkat dan tidak menyalahkan pelanggan. Istilah: pouch = satuan produk; batch = kelompok produksi; Menunggu Konfirmasi = belum mendapat kuota; Dikonfirmasi = kuota dialokasikan; Diproduksi = proses produksi; Siap Diserahkan = label kebutuhan penyerahan yang pemetaannya ke status existing perlu ditinjau; Lunas = status pembayaran; Selesai = diterima dan lunas. Format uang Rp5.000 dan tanggal menurut Asia/Jakarta. Estimasi 1–3 hari bukan janji waktu pasti.
