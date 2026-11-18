<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex">
    <title>Masuk CMS · Marga Mulya Hub</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
</head>
<body class="grid grid-cols-1 min-h-screen bg-cream lg:grid-cols-2">
    <div class="relative hidden overflow-hidden bg-forest-900 lg:block">
        <img src="/images/seed/hero-sawah.svg" alt="" class="absolute inset-0 size-full object-cover opacity-50">
        <div class="relative flex h-full flex-col justify-end p-12 text-white">
            <x-brand-mark class="size-14" />
            <p class="mt-6 font-display text-4xl font-semibold">Kelola informasi desa tanpa menyentuh kode.</p>
            <p class="mt-3 max-w-md text-forest-100">Berita, agenda, data desa, potensi, galeri, dan kontribusi warga — semuanya dari satu dashboard.</p>
        </div>
    </div>
    <main class="flex items-center justify-center p-6">
        <div class="w-full max-w-sm">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-forest-700 hover:underline"><x-lucide-arrow-left class="size-4" aria-hidden="true" /> Kembali ke website</a>
            <h1 class="mt-6 font-display text-3xl font-semibold">Masuk ke CMS</h1>
            <p class="mt-2 text-ink-soft">Khusus admin dan perangkat desa.</p>
            @if (session('success'))<p class="mt-4 rounded-xl bg-forest-50 px-4 py-3 text-forest-900" role="status">{{ session('success') }}</p>@endif
            <form method="POST" action="{{ route('admin.login.attempt') }}" class="mt-8 grid gap-5" novalidate>
                @csrf
                <div>
                    <label for="email" class="label">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" autofocus class="input" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                    @error('email')<p id="email-error" class="field-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div x-data="{ show: false }">
                    <label for="password" class="label">Kata sandi</label>
                    <div class="relative">
                        <input id="password" name="password" :type="show ? 'text' : 'password'" type="password" required autocomplete="current-password" class="input pr-12" @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                        <button type="button" @click="show = !show" class="absolute top-1/2 right-1 inline-flex size-10 -translate-y-1/2 items-center justify-center rounded-full hover:bg-stone-100" :aria-label="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'" aria-label="Tampilkan kata sandi">
                            <x-lucide-eye class="size-5" x-show="!show" aria-hidden="true" /><x-lucide-eye-off class="size-5" x-show="show" x-cloak aria-hidden="true" />
                        </button>
                    </div>
                    @error('password')<p id="password-error" class="field-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <label class="flex min-h-11 items-center gap-3"><input type="checkbox" name="remember" class="size-5 rounded accent-forest-800"> Ingat saya</label>
                <button class="btn btn-primary w-full">Masuk</button>
            </form>
        </div>
    </main>
</body>
</html>
