# Workflow: SaudaraOrder

## Cycle

Plan → Code → Test → Review → Approval adalah proses yang disetujui. Branch, commit, PR dan merge merupakan langkah Git pendukung setelah izin yang sesuai, bukan tindakan otomatis.

Plan membaca SPEC, task dan kode existing. Pilih satu pekerjaan dengan hasil yang terukur. Code menerapkan perubahan yang telah diizinkan pada branch task; untuk dokumentasi hanya berkas dokumen yang disentuh. Test menjalankan gerbang relevan pada database terisolasi jika aplikasi berubah. Review membandingkan diff terhadap acceptance, lalu melaporkan keterbatasan. Approval berasal dari mahasiswa sebelum tindakan penting berikutnya. Dokumen existing yang dilindungi memerlukan draf/diff sebelum diganti.

## Session Start Ritual

1. Baca AGENTS, PROGRESS, TASKS, GUARDRAILS dan pelajaran yang relevan.
2. Periksa working tree tanpa membuang perubahan existing; kondisi kotor tidak memberi izin reset.
3. Pilih satu task todo yang dependensinya selesai dan scope-nya disetujui.
4. Catat doing hanya ketika benar-benar mulai; pastikan branch task sesuai sebelum implementasi.
5. Dahulukan P0 kesiapan link UTS 10 November 2026; evaluasi tidak berarti izin deployment.

## Session End Ritual

1. Jalankan pemeriksaan relevan; tulis perintah serta hasil sebenarnya.
2. Periksa diff dan pastikan tidak ada rahasia atau perubahan di luar tugas.
3. Perbarui PROGRESS, TASKS dan keputusan baru; jangan menulis tes lulus jika belum dijalankan.
4. Siapkan ringkasan dan bahan review; commit/push/PR hanya bila diizinkan mahasiswa.
5. Berhenti pada batas persetujuan; jangan mengubah status done sebelum review hasil diterima.

## Branching Strategy

Gunakan branch satu tugas, misalnya docs/T-001-audit atau feat/T-005-pengajuan. Main diharapkan stabil; proteksi GitHub belum diverifikasi. Tidak commit langsung ke main atau force push. Integrasi perubahan main ke branch menggunakan merge sebagai usulan teknis untuk review, bukan rewrite history otomatis. Penghapusan branch setelah merge bukan kewajiban agen tanpa izin. Proyek individu tetap memerlukan mahasiswa meninjau hasil agen.

## Commit and Pull Request Rules

Ikuti CODE_STYLE. PR menyebut ID task, fitur, masalah, perubahan perilaku, bukti tes dan batasan. Sertakan tampilan bila UI berubah. Satu PR satu tujuan; pemecahan disesuaikan review, tidak menetapkan batas jumlah file baru yang belum disepakati. .github/pull_request_template.md belum dibuat oleh tahap dokumentasi ini. Merge memerlukan pemeriksaan manusia serta gerbang terkait; pengaturan CI belum dianggap aktif.

## Human-in-the-Loop Checkpoints

| Titik | Penanggung jawab | Pemeriksaan |
|---|---|---|
| Akhir Loop | Mahasiswa | Scope, hasil, risiko, langkah berikutnya |
| Dokumen existing | Mahasiswa | Draf dan diff sebelum overwrite |
| Skema/arsitektur | Mahasiswa | Kebutuhan, integritas, dampak data |
| Dependency/hosting | Mahasiswa | Kompatibilitas, biaya dan batas layanan |
| Commit/push/deploy | Mahasiswa | Perubahan konkret dan hasil tes |
| Data nyata/destruktif | Mahasiswa/pemilik | Izin spesifik; larangan penghapusan tetap berlaku |

## Handling Blockers

Maksimal dua percobaan perbaikan otomatis per kegagalan, sesuai keputusan mahasiswa yang mengesampingkan contoh tiga percobaan template. Catat gejala, versi, langkah yang dicoba dan bukti tersamarkan. Jika tetap gagal, hentikan bagian terkait dan minta arahan; jangan mematikan tes atau mengurangi keamanan. Kendala Vercel dicatat untuk keputusan mahasiswa/dosen; Render bukan substitusi otomatis kewajiban UTS.

## Level Notes

- Dasar: gerbang lokal/manual sebagai pembanding, bukan level aktif.
- Menengah: CI dapat membantu setelah disetujui, bukan bukti otomatis sudah berjalan.
- Loop — AKTIF: satu tugas, maksimal dua perbaikan otomatis, lalu review dan approval; detail pada LOOP.md.
