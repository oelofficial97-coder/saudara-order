<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class CreateFirstOwner extends Command
{
    protected $signature = 'saudara:owner';

    protected $description = 'Buat akun pemilik pertama secara interaktif tanpa password tetap';

    public function handle(): int
    {
        if (! $this->input->isInteractive()) {
            $this->error('Perintah ini wajib dijalankan secara interaktif.');

            return self::FAILURE;
        }
        if (User::where('role', 'pemilik')->exists()) {
            $this->error('Akun pemilik sudah tersedia. Perintah hanya untuk pemilik pertama.');

            return self::FAILURE;
        }

        $name = $this->ask('Nama pemilik');
        $username = Str::lower(trim((string) $this->ask('Username (huruf, angka, titik, garis bawah atau tanda hubung)')));
        $password = $this->secret('Kata sandi (minimal 12 karakter, huruf besar/kecil, angka dan simbol)', false);
        $confirmation = $this->secret('Ulangi kata sandi', false);
        $validator = Validator::make([
            'name' => $name, 'username' => $username,
            'password' => $password, 'password_confirmation' => $confirmation,
        ], [
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/\A[a-z0-9._-]+\z/', 'unique:users,username'],
            'password' => ['required', 'string', 'max:255', 'confirmed', Password::min(12)->mixedCase()->numbers()->symbols()],
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }

        $created = DB::transaction(function () use ($name, $username, $password) {
            // The seeded product supplies a stable provisioning mutex before any owner exists.
            if (! Product::where('singleton_key', 1)->lockForUpdate()->first()) {
                $this->error('Produk belum tersedia. Jalankan ProductSeeder terlebih dahulu.');

                return false;
            }
            if (User::where('role', 'pemilik')->exists()) {
                $this->error('Akun pemilik sudah dibuat oleh proses lain.');

                return false;
            }
            $user = new User(['name' => $name, 'username' => $username, 'password' => $password]);
            $user->role = 'pemilik';
            $user->is_active = true;
            $user->save();

            return true;
        }, 3);

        if (! $created) {
            return self::FAILURE;
        }
        $this->info('Akun pemilik berhasil dibuat. Masuk melalui /admin/login.');

        return self::SUCCESS;
    }
}
