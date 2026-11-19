@props(['title' => 'Dashboard'])
@use('App\Models\CitizenSubmission')
@use('App\Models\ContactMessage')
@php
    $pending = CitizenSubmission::where('status', 'pending')->count();
    $unread = ContactMessage::where('status', 'unread')->count();
    $menu = [
        'Ringkasan' => [
            ['Dashboard', route('admin.dashboard'), 'layout-dashboard', request()->routeIs('admin.dashboard'), null],
        ],
        'Konten desa' => [
            ['Pengaturan situs', route('admin.resource.singleton', 'pengaturan'), 'settings', request()->is('admin/kelola/pengaturan*'), null],
            ['Profil desa', route('admin.resource.singleton', 'profil'), 'landmark', request()->is('admin/kelola/profil*'), null],
            ['Perangkat desa', route('admin.resource.index', 'perangkat'), 'users', request()->is('admin/kelola/perangkat*'), null],
            ['Fasilitas umum', route('admin.resource.index', 'fasilitas'), 'building-2', request()->is('admin/kelola/fasilitas*'), null],
            ['Data & statistik', route('admin.resource.index', 'statistik'), 'chart-column', request()->is('admin/kelola/statistik*'), null],
            ...(auth()->user()->isSuperAdmin() ? [['Kategori statistik', route('admin.resource.index', 'kategori-statistik'), 'tags', request()->is('admin/kelola/kategori-statistik*'), null]] : []),
        ],
        'Informasi' => collect(\App\Models\InformationPost::TYPES)->map(fn ($l, $k) => [
            $l, route('admin.resource.index', ['informasi', 'type' => $k]), ['news' => 'newspaper', 'announcement' => 'megaphone', 'event' => 'calendar-days', 'service' => 'file-text'][$k],
            request()->is('admin/kelola/informasi*') && request('type') === $k, null,
        ])->values()->all(),
        'Potensi & media' => [
            ['Potensi desa', route('admin.resource.index', 'potensi'), 'sprout', request()->is('admin/kelola/potensi*'), null],
            ['Galeri', route('admin.resource.index', 'galeri'), 'images', request()->is('admin/kelola/galeri*'), null],
            ['Cerita warga', route('admin.resource.index', 'cerita'), 'message-square-quote', request()->is('admin/kelola/cerita*'), null],
        ],
        'Partisipasi' => [
            ['Kontribusi warga', route('admin.submissions.index'), 'hand-heart', request()->routeIs('admin.submissions.*'), $pending],
            ['Pesan kontak', route('admin.messages.index'), 'mail', request()->routeIs('admin.messages.*'), $unread],
        ],
        'Kesejahteraan' => [
            ['Analisis penghasilan', route('admin.income.index'), 'wallet', request()->routeIs('admin.income.*'), null],
            ['Data ekonomi KK', route('admin.resource.index', 'data-kk'), 'house', request()->is('admin/kelola/data-kk*'), null],
            ['Kelas penghasilan', route('admin.resource.index', 'kelas-penghasilan'), 'layers', request()->is('admin/kelola/kelas-penghasilan*'), null],
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }} · CMS Marga Mulya Hub</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
</head>
<body class="min-h-screen bg-stone-100 text-[0.97rem]" x-data="adminShell">
    <a href="#konten-admin" class="fixed top-3 left-3 z-[60] -translate-y-24 rounded-full bg-forest-900 px-5 py-3 font-semibold text-white focus:translate-y-0">Lewati ke konten</a>

    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <div x-show="nav" x-cloak class="fixed inset-0 z-40 bg-black/40 lg:hidden" @click="nav = false"></div>
        <aside class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col bg-forest-950 text-forest-100 transition-transform lg:sticky lg:top-0 lg:h-screen lg:shrink-0 lg:translate-x-0"
               :class="nav && 'translate-x-0'" aria-label="Menu CMS">
            <div class="flex h-16 items-center gap-3 px-5">
                <x-brand-mark class="size-9" />
                <div class="leading-tight"><p class="font-display font-semibold text-white">Marga Mulya</p><p class="text-xs text-forest-300">CMS Desa</p></div>
                <button type="button" class="ml-auto inline-flex size-10 items-center justify-center rounded-full hover:bg-white/10 lg:hidden" @click="nav = false" aria-label="Tutup menu"><x-lucide-x class="size-5" aria-hidden="true" /></button>
            </div>
            <nav data-sidebar-scroll class="scrollbar-dark relative min-h-0 flex-1 overflow-y-auto overscroll-contain px-3 pb-10 [mask-image:linear-gradient(to_bottom,black_calc(100%-2.5rem),transparent)]">
                @foreach ($menu as $group => $items)
                    <p class="mt-5 mb-1 px-3 text-[0.7rem] font-bold tracking-widest text-forest-400 uppercase">{{ $group }}</p>
                    <ul>
                        @foreach ($items as [$label, $url, $icon, $active, $badge])
                            <li>
                                <a href="{{ $url }}" @if ($active) aria-current="page" @endif
                                   class="flex min-h-10 items-center gap-3 rounded-xl px-3 text-sm font-semibold {{ $active ? 'bg-white/12 text-white' : 'text-forest-200 hover:bg-white/5 hover:text-white' }}">
                                    <x-dynamic-component :component="'lucide-'.$icon" class="size-[1.1rem] shrink-0" aria-hidden="true" />
                                    <span class="flex-1">{{ $label }}</span>
                                    @if ($badge)<span class="badge bg-sun-400 text-ink">{{ $badge }}<span class="sr-only"> baru</span></span>@endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            </nav>
            <div class="border-t border-white/10 p-3">
                <a href="{{ route('home') }}" target="_blank" class="flex min-h-10 items-center gap-3 rounded-xl px-3 text-sm font-semibold text-forest-200 hover:bg-white/5 hover:text-white"><x-lucide-external-link class="size-4" aria-hidden="true" /> Lihat website</a>
                <form method="POST" action="{{ route('admin.logout') }}">@csrf
                    <button class="flex min-h-10 w-full items-center gap-3 rounded-xl px-3 text-sm font-semibold text-forest-200 hover:bg-white/5 hover:text-white"><x-lucide-log-out class="size-4" aria-hidden="true" /> Keluar</button>
                </form>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-stone-200 bg-white/90 px-4 backdrop-blur sm:px-6">
                <button type="button" class="inline-flex size-10 items-center justify-center rounded-full hover:bg-stone-100 lg:hidden" @click="nav = true" aria-label="Buka menu"><x-lucide-menu class="size-5" aria-hidden="true" /></button>
                <h1 class="truncate text-lg font-bold">{{ $title }}</h1>
                <div class="ml-auto flex items-center gap-2 text-sm">
                    <span class="hidden text-right leading-tight text-stone-600 sm:block">{{ auth()->user()->name }}<span class="block text-xs text-stone-500">{{ auth()->user()->roleLabel() }}</span></span>
                    <span class="flex size-9 items-center justify-center rounded-full bg-forest-800 font-bold text-white" aria-hidden="true">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                </div>
            </header>

            <main id="konten-admin" class="mx-auto w-full max-w-7xl flex-1 p-4 sm:p-6 lg:p-8" tabindex="-1">
                @if (session('success'))
                    <div class="mb-5 flex items-start gap-3 rounded-2xl border border-forest-300 bg-forest-50 px-4 py-3 text-forest-900" role="status" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)">
                        <x-lucide-circle-check class="mt-0.5 size-5 shrink-0" aria-hidden="true" /><p class="flex-1 font-semibold">{{ session('success') }}</p>
                        <button type="button" @click="show = false" aria-label="Tutup pesan"><x-lucide-x class="size-4" aria-hidden="true" /></button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-5 rounded-2xl border border-red-300 bg-red-50 px-4 py-3 text-red-900" role="alert" tabindex="-1" x-init="$el.focus()">
                        <p class="flex items-center gap-2 font-bold"><x-lucide-circle-alert class="size-5" aria-hidden="true" /> {{ $errors->has('hapus') ? 'Tindakan tidak dapat diproses:' : 'Gagal menyimpan — periksa '.$errors->count().' isian berikut:' }}</p>
                        <ul class="mt-2 list-disc pl-8 text-sm">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif
                {{ $slot }}
            </main>
        </div>
    </div>

    <dialog x-ref="confirmDialog" class="m-auto w-[min(28rem,calc(100vw-2rem))] rounded-3xl bg-white p-6 backdrop:bg-black/50" aria-labelledby="konfirmasi-judul">
        <div class="flex gap-4">
            <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-700"><x-lucide-triangle-alert class="size-5" aria-hidden="true" /></span>
            <div>
                <h2 id="konfirmasi-judul" class="text-lg font-bold">Konfirmasi</h2>
                <p class="mt-1 text-stone-700" x-text="confirm.message"></p>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-2">
            <button type="button" class="btn btn-ghost" @click="$refs.confirmDialog.close()" autofocus>Batal</button>
            <button type="button" class="btn btn-danger" @click="accept()">Ya, lanjutkan</button>
        </div>
    </dialog>
</body>
</html>
