# Langkah 6: file pendukung git (tanpa model)

1. Salin `.github/pull_request_template.md` ke root proyekmu.
2. Buka `ekosistem/<ekosistem-mu>/` (sesuai `stack.ecosystems` di SPEC.json, mis. `node`, `python`, `php`, `dart-flutter`, `go`, `rust`, `jvm`, `dotnet`, `platformio`, `other`) dan salin `.gitignore` serta folder `.github/` ke root proyek.
3. **Wajib:** di `ci.yml`, ganti setiap baris berisi `GANTI-PERINTAH` dengan perintah dari `stack.commands` (install, lint, test, build). Sebelum diganti, CI sengaja gagal agar tidak lupa.
4. Memakai lebih dari satu ekosistem (mis. Tauri = node + rust, Laravel + Vite = php + node)? Gabungkan langkah setup dari kedua `ci.yml` dan baris `.gitignore`/`dependabot.yml`-nya. Lebih mudah: bila Python terpasang, jalankan `python kit/tools/scaffold_extras.py`, yang membuat semuanya otomatis dari SPEC.json.
5. Buat `.env.example` berisi NAMA variabel lingkungan tanpa nilai rahasia, sesuai `docs/ARCHITECTURE.md`.
6. Memakai Claude Code atau Gemini CLI? Buat `CLAUDE.md` / `GEMINI.md` berisi satu baris `@AGENTS.md`.
