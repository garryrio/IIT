<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = config('village.admin');
        if ($admin['email'] && $admin['password']) {
            User::updateOrCreate(['email' => $admin['email']], [
                'name' => $admin['name'],
                'password' => $admin['password'],
                'is_admin' => true,
            ]);
            $this->command?->info("Admin dibuat: {$admin['email']}");
        } else {
            $this->command?->warn('ADMIN_EMAIL / ADMIN_PASSWORD belum diisi — jalankan `php artisan admin:create` untuk membuat admin.');
        }

        $this->call(VillageSeeder::class);
    }
}
