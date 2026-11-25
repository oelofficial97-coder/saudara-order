# Panduan Screenshot SaudaraOrder

Direktori ini menyimpan tangkapan layar asli aplikasi **SaudaraOrder** untuk keperluan showcase `README.md` dan dokumentasi Capstone.

## 1. Screenshot yang Sudah Tersedia

### Tampilan Live Terbaru (`https://saudara-order.vercel.app`)

| Berkas | Halaman / Bagian | Route | Resolusi | Keterangan |
|---|---|---|---|---|
| `homepage-live.png` | Landing Page / Hero | `/` | 1440×920 | Tampilan hero utama dengan status sistem pre-order aktif |
| `product-live.png` | Pengenalan Produk | `/#produk` | 1440×920 | Kartu produk tunggal Keripik Singkong 4 Saudara Rp5.000/pouch |
| `preorder-live.png` | Formulir Pre-Order | `/pre-order` | 1440×1280 | Form pemesanan live beserta ringkasan batch dan sisa kuota |
| `tracking-live.png` | Pelacakan Pesanan Privat | `/cek-pesanan` | 1440×1080 | Formulir kunci akses pribadi 32 karakter |
| `admin-login.png` | Login Administrator | `/admin/login` | 1440×900 | Halaman masuk ruang kerja pemilik dan pengelola |

### Arsip Tampilan Awal (Minggu 3)

Berkas historis berikut tetap disimpan sebagai catatan perkembangan UI awal:
- `beranda-desktop.png`
- `beranda-mobile.png`
- `form-preorder.png`
- `cek-pesanan.png`

---

## 2. Screenshot Admin Terautentikasi yang Perlu Diambil

Screenshot berikut memerlukan login akun `pemilik` yang aktif dan **tidak dibuat menggunakan data palsu/dummy produksi**:

| Nama Berkas Target | Halaman | Route | Catatan Pengambilan |
|---|---|---|---|
| `admin-dashboard.png` | Admin Dashboard | `/admin/dashboard` | Pastikan metrik dan ringkasan batch tampil utuh (resolusi rekomendasi `1440×900`) |
| `admin-batches.png` | Manajemen Batch Produksi | `/admin/batches` | Tampilkan tabel daftar batch, alokasi, sisa kuota, dan tombol aksi |
| `admin-batch-capacity.png` | Pengaturan Kapasitas Batch | `/admin/batches/create` atau `/admin/batches/{id}/edit` | Tampilkan formulir kapasitas pouch & estimasi siap WIB |
| `admin-settings.png` | Pengaturan Usaha | `/admin/settings` | Samarkan data pribadi sensitif jika perlu sebelum diunggah |
| `admin-orders.png` | Manajemen Pesanan Admin | `/admin/orders` | Opsional namun direkomendasikan untuk menunjukkan manajemen pesanan yang sudah aktif |

---

## 3. Cara Menambahkan ke `README.md`

Setelah menyimpan berkas di folder `docs/screenshots/`, buka blok komentar pada bagian **Admin Interface** di `README.md` atau gunakan format tabel berikut:

```markdown
| Admin Dashboard | Manajemen Batch Produksi |
| :---: | :---: |
| ![Admin Dashboard](docs/screenshots/admin-dashboard.png) | ![Batch Production](docs/screenshots/admin-batches.png) |
| *Ringkasan kuota batch aktif dan status penerimaan (`/admin/dashboard`).* | *Daftar batch produksi, status terbuka/tertutup, dan kuota (`/admin/batches`).* |

| Pengaturan Kapasitas Batch | Pengaturan Informasi Usaha |
| :---: | :---: |
| ![Capacity Management](docs/screenshots/admin-batch-capacity.png) | ![Business Settings](docs/screenshots/admin-settings.png) |
| *Formulir kapasitas pouch dan estimasi siap WIB (`/admin/batches/create`).* | *Pengelolaan kontak resmi, lokasi ambil, dan rekening opsional (`/admin/settings`).* |
```
