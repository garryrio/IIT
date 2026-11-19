<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create {email?} {--name=Admin Desa} {--password=} {--role= : super_admin atau admin}';

    protected $description = 'Membuat atau memperbarui akun admin CMS';

    public function handle(): int
    {
        $email = $this->argument('email') ?? $this->ask('Email admin');
        $password = $this->option('password') ?: $this->secret('Kata sandi (min. 10 karakter)');

        $v = Validator::make(compact('email', 'password'), ['email' => 'required|email', 'password' => 'required|string|min:10']);
        if ($v->fails()) {
            foreach ($v->errors()->all() as $e) {
                $this->error($e);
            }

            return self::FAILURE;
        }

        $role = $this->option('role');
        if ($role !== null && ! array_key_exists($role, User::ROLES)) {
            $this->error('Peran harus super_admin atau admin.');

            return self::FAILURE;
        }

        $user = User::firstOrNew(['email' => $email]);
        // Tanpa --role: akun lama mempertahankan perannya; akun baru jadi super admin bila belum ada super admin.
        $role ??= $user->exists ? $user->role : (User::where('role', 'super_admin')->exists() ? 'admin' : 'super_admin');
        $user->fill(['name' => $this->option('name'), 'password' => $password, 'is_admin' => true, 'role' => $role])->save();
        $this->info("Akun {$email} ({$user->roleLabel()}) siap digunakan. Login di /admin/login");

        return self::SUCCESS;
    }
}
