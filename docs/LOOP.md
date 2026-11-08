# Loop Engineering: SaudaraOrder

## Loop Definition

Satu putaran SaudaraOrder: pilih satu task yang diizinkan → Plan membaca SPEC/kode → Code sesuai scope → Test dengan gerbang relevan → Review diff/hasil → Approval mahasiswa. Untuk kode, gunakan `php vendor/bin/pint --test`, `php artisan test`, dan `npm run build` sesuai kebutuhan serta keamanan database. Untuk dokumentasi, cek struktur/konsistensi dan diff tanpa menjalankan aplikasi. Commit dan PR tidak otomatis dilakukan setelah gerbang hijau.

## Roles

| Peran | Masukan | Keluaran | Batas |
|---|---|---|---|
| Planner | SPEC, TASKS, kondisi repo | Rencana satu tugas dan risiko | Tidak mengubah kode |
| Coder | Rencana disetujui | Perubahan fokus dan ringkasan | Tidak memperluas kebutuhan |
| Tester | Acceptance dan diff | Bukti tes serta keterbatasan | Tidak mereset DB pengembangan |
| Reviewer | Diff, EVALS, REVIEW | Temuan dan rekomendasi | Tidak memperbaiki diam-diam |

Peran ini dapat dijalankan berurutan oleh satu agen. Tidak ada perintah untuk spawn sub-agent; delegasi hanya jika mahasiswa meminta. Semua peran tunduk pada AGENTS dan GUARDRAILS.

## Stop Conditions

| Kondisi | Tindakan |
|---|---|
| Gerbang relevan lulus | Laporkan dan minta review/approval |
| Dua perbaikan otomatis gagal pada masalah sama | Hentikan bagian terkait dan minta arahan |
| Scope meluas atau pekerjaan melebihi ukuran task | Pecah rencana, minta keputusan |
| Perlu izin skema, CI, overwrite, commit atau deploy | Siapkan bahan konkret lalu tunggu |
| Batas biaya/kuota layanan tidak jelas | Jangan mengaktifkan layanan |
| Dokumen bertentangan | Jelaskan konflik, jangan memilih kebijakan sendiri |
| Rahasia/data nyata ditemukan | Jangan mencetak atau menyebarkan; laporkan aman |
| Iterasi tanpa kemajuan | Catat bukti, berhenti untuk review |

## Iteration Limits and Budget

Satu tugas utama per siklus; maksimal dua percobaan perbaikan otomatis per kegagalan. Batas durasi total dan budget alat belum ditetapkan mahasiswa, sehingga angka contoh lima iterasi/tiga percobaan dari template tidak diadopsi. Gunakan layanan gratis sesuai pilihan, tetapi periksa batas dan keberlanjutan sebelum mengaktifkan. P0 UTS dekat bukan alasan melewati review atau mengubah .env. Setelah task selesai, jangan otomatis berpindah ke implementasi berikutnya yang belum diizinkan.

## Hand-off Between Agents

Serah terima melalui PROGRESS, TASKS, DECISIONS dan LESSONS, ditambah diff/branch/PR jika telah diizinkan. Format: task dan fitur; keadaan terakhir; file berubah; perintah dan hasil; kendala; keputusan yang masih menunggu; langkah berikutnya. Jangan menyertakan kredensial, token tracking atau data pelanggan. Kondisi working tree existing harus disebut agar penerus tidak menghapus perubahan pengguna.

## Human Checkpoints

Mahasiswa memutuskan cakupan, meninjau rencana berisiko, perubahan schema/arsitektur, dependency baru, dan hasil akhir Loop. PRD/README existing perlu draf/diff sebelum overwrite. Commit, push dan deployment memerlukan izin terpisah. Mahasiswa menjawab Author Comprehension Check sendiri; asisten tidak mengisi refleksi.

## Observability

Gunakan Session Log, hasil validator/test, diff dan log CI jika sudah tersedia. Riwayat commit bukan bukti pada sesi yang tidak mengizinkan commit. Catat pemeriksaan belum dijalankan secara eksplisit. Mahasiswa dapat menghentikan putaran; agen menghentikan pekerjaan terkait dan melaporkan perubahan yang telah dibuat. Untuk UTS, bukti URL hidup, routing, aset dan fungsi demo harus dapat ditinjau, bukan klaim berdasarkan build saja.
