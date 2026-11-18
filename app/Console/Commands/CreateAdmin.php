<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create {email?} {--name=Admin Desa} {--password=}';

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

        User::updateOrCreate(['email' => $email], ['name' => $this->option('name'), 'password' => $password, 'is_admin' => true]);
        $this->info("Admin {$email} siap digunakan. Login di /admin/login");

        return self::SUCCESS;
    }
}
