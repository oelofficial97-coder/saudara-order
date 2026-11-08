# Lessons: SaudaraOrder

## How to Use

- Tambahkan temuan review, kesalahan berulang atau galat yang menghabiskan waktu; jangan mengarang pengalaman mahasiswa.
- Gunakan tanggal, gejala, akar masalah, aturan dan lokasi penerapan.
- Setelah diterima, ubah pelajaran menjadi aturan CODE_STYLE, GUARDRAILS atau EVALS yang relevan.
- Tulis singkat tanpa menyalahkan; bedakan risiko awal dari kejadian yang teramati.

## Lessons Log

| Tanggal | Kesalahan atau gejala | Akar masalah | Aturan baru | Diterapkan di |
|---|---|---|---|---|
| Awal (belum terjadi di proyek ini) | Menganggap SQLite membuktikan concurrency MySQL | Perbedaan mesin/locking | Wajib bukti MySQL untuk klaim concurrency | TESTING.md |
| Awal (belum terjadi di proyek ini) | Build Vercel hijau dianggap aplikasi berjalan | Tidak menguji runtime/session/DB | Periksa URL dan fitur demo nyata | EVALS.md |
| Awal (belum terjadi di proyek ini) | Mengganti PRD existing tanpa review | Template dianggap izin overwrite | Draf/diff dan persetujuan wajib | GUARDRAILS.md |
| Awal (belum terjadi di proyek ini) | Mematikan tes agar demo cepat | Tekanan deadline | Laporkan gagal dan batasi dua perbaikan otomatis | LOOP.md |

Baris awal merupakan antisipasi, bukan laporan bahwa mahasiswa atau agen telah melakukan kesalahan tersebut.

## Patterns to Watch

- Laravel API dari versi lain: cocokkan dengan dependency existing dan dokumentasi resmi sebelum coding.
- Runtime PHP komunitas: periksa pemeliharaan dan kompatibilitas, jangan percaya contoh konfigurasi lama.
- Total/kuota dipercaya dari browser: tinjau service dan tes manipulasi input.
- Pengajuan ulang menggandakan pesanan: uji idempotency key yang sama dengan payload sama maupun berbeda.
- Data simulasi menjadi aturan permanen: cocokkan keputusan dengan SPEC dan hasil wawancara.

## Reflection Prompts

Pertanyaan berikut dijawab mahasiswa sendiri pada evaluasi akhir:

1. Keputusan apa yang berubah setelah melihat kondisi usaha, dan mengapa?
2. Bagian pekerjaan AI mana yang membantu dan mana yang perlu diperbaiki?
3. Bagaimana Anda membuktikan kuota dan pembayaran tidak tercatat ganda?
4. Apa pelajaran dari evaluasi Vercel dan deadline UTS?
5. Apa yang akan Anda lakukan berbeda pada proyek berikutnya?

Refleksi C-14/C-15 yang sudah diberikan tetap berada di SPEC; pertanyaan lanjutan ini belum dijawab.
