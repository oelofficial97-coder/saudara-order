# SaudaraOrder — Paket Frontend Minggu 3

Tujuan: memasang tampilan sinematik di project Laravel 13 SaudaraOrder yang SUDAH ada. Jangan membuat ulang project Laravel.

## Instalasi Windows / Antigravity IDE

1. Backup project atau buat commit terlebih dahulu:
   `git status` dan pastikan perubahan lokal aman.
2. Buka hasil ekstraksi `saudaraorder-frontend-minggu3.zip`.
3. Salin **isi folder** paket ke `C:\laragon8\www\saudara-order\` dan izinkan menimpa `routes/web.php` jika project masih Laravel standar. Jika ada route lain yang sudah dikembangkan, **gabungkan** tiga route baru, jangan menimpa route milikmu.
4. Jangan menyalin folder paket sebagai subfolder baru di project.
5. Pada Antigravity terminal (di folder root Laravel), jalankan:
   ```powershell
   php artisan route:clear
   php artisan view:clear
   php artisan serve
   ```
6. Buka `http://127.0.0.1:8000`.
7. Cek `http://127.0.0.1:8000/pre-order` dan `http://127.0.0.1:8000/cek-pesanan`.
8. Periksa responsif dengan browser DevTools (F12 → toggle device toolbar).

## File yang dipasang

- `routes/web.php` — tiga route
- `resources/views/layouts/saudara.blade.php` — layout, HTML, metadata, footer
- `resources/views/pages/home.blade.php` — landing page
- `resources/views/pages/preorder.blade.php` — pratinjau form dengan perhitungan total
- `resources/views/pages/tracking.blade.php` — halaman placeholder pelacakan
- `public/saudara/css/site.css` — CSS responsif
- `public/saudara/js/site.js` — menu, animasi, jumlah pouch
- `public/saudara/images/hero-keripik-konsep.webp` — ilustrasi visual original untuk prototipe

## Penting untuk integritas PRD

- **Belum ada fitur pesanan yang tersimpan**; tombol kirim form sengaja nonaktif.
- **Belum ada batch/kuota real-time**; halaman tidak mengarang angka.
- **Belum ada pelacakan privat**; halaman informasi tidak membuka data pelanggan.
- Foto ilustrasi AI tidak boleh digunakan sebagai foto produk asli tanpa penjelasan.
- Form masih tampilan awal; PHP/controller, MySQL, validasi server, token, kuota, status, dan keamanan implementasi pada tahap berikutnya.
- Paket memakai HTML/Blade, CSS, JS ringan; Tailwind/Alpine tetap direncanakan sesuai PRD saat komponen diperluas. Tidak mengubah `package.json`, dependensi, atau `.env`.
- Tidak ada alamat rekening, WhatsApp, atau lokasi rekaan.

## Upload ke GitHub setelah dicek

```powershell
git status
git add routes/web.php resources/views/layouts/saudara.blade.php resources/views/pages/home.blade.php resources/views/pages/preorder.blade.php resources/views/pages/tracking.blade.php public/saudara docs/PANDUAN_PASANG_FRONTEND.md
git commit -m "feat: cinematic SaudaraOrder landing page prototype"
git push origin main
```

## Ide evaluasi presentasi

Perlihatkan beranda, navigasi, komponen FAQ, versi mobile, halaman `/pre-order` dengan perhitungan 3 x Rp5.000 = Rp15.000, dan halaman `/cek-pesanan` yang menjelaskan token pribadi. Tegaskan bahwa alur bisnis belum diaktifkan sampai backend siap.
