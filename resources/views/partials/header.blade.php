@php
    $nav = [
        ['home', 'Beranda', 'home'],
        ['profile', 'Profil', 'profile'],
        ['data', 'Data Desa', 'data'],
        ['potentials.index', 'Potensi', 'potentials.*'],
        ['information.index', 'Informasi', 'information.*'],
        ['gallery', 'Galeri', 'gallery'],
        ['contact', 'Kontak', 'contact'],
    ];
    $site = \App\Models\SiteSetting::current();
@endphp
<header x-data="siteHeader" class="sticky top-0 z-40 transition-colors duration-300"
        :class="scrolled || open ? 'bg-cream/95 shadow-[0_1px_0_var(--color-line)] backdrop-blur' : 'bg-cream/60 backdrop-blur-sm'">
    <div class="container-page flex h-[4.5rem] items-center justify-between gap-3">
        <a href="{{ route('home') }}" class="group flex min-h-11 items-center gap-2.5 rounded-lg" aria-label="{{ $site->village_name }} — Beranda">
            <x-brand-mark class="size-10 shrink-0" />
            <span class="leading-tight">
                <span class="block font-display text-lg font-semibold text-forest-900">Marga Mulya</span>
                <span class="block text-xs font-semibold tracking-wider text-forest-700 uppercase">Village Hub</span>
            </span>
        </a>

        <nav aria-label="Navigasi utama" class="hidden xl:block">
            <ul class="flex items-center gap-0.5">
                @foreach ($nav as [$route, $label, $pattern])
                    @php $active = request()->routeIs($pattern); @endphp
                    <li>
                        <a href="{{ route($route) }}" @if ($active) aria-current="page" @endif
                           class="relative inline-flex min-h-11 items-center whitespace-nowrap rounded-full px-3.5 text-[0.95rem] font-semibold transition {{ $active ? 'bg-forest-800 text-white' : 'text-ink-soft hover:bg-forest-50 hover:text-forest-900' }}">
                            {{ $label }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="flex items-center gap-1.5">
            <button type="button" @click="$dispatch('open-search')"
                    class="inline-flex min-h-11 items-center gap-2 rounded-full border border-line bg-paper px-3.5 text-sm font-semibold text-ink-soft transition hover:border-forest-400 hover:text-forest-900"
                    aria-label="Cari informasi (Ctrl + K)">
                <x-lucide-search class="size-[1.15rem]" aria-hidden="true" />
                <span class="hidden xl:inline">Cari</span>
                <kbd class="hidden rounded-md border border-line bg-cream px-1.5 py-0.5 font-sans text-[0.7rem] text-ink-soft xl:inline">Ctrl K</kbd>
            </button>
            <a href="{{ route('contact') }}#kontribusi" class="btn btn-primary btn-sm hidden sm:inline-flex">
                <x-lucide-hand-heart class="size-4" aria-hidden="true" /> Kontribusi
            </a>
            <button type="button" class="inline-flex size-11 items-center justify-center rounded-full text-forest-900 hover:bg-forest-50 xl:hidden"
                    @click="toggle()" :aria-expanded="open.toString()" aria-controls="menu-seluler" aria-expanded="false">
                <span class="sr-only" x-text="open ? 'Tutup menu' : 'Buka menu'">Buka menu</span>
                <x-lucide-menu class="size-6" x-show="!open" aria-hidden="true" />
                <x-lucide-x class="size-6" x-show="open" x-cloak aria-hidden="true" />
            </button>
        </div>
    </div>

    <div id="menu-seluler" x-show="open" x-cloak x-collapse @keydown.escape.window="close()"
         class="border-t border-line bg-cream xl:hidden">
        <nav aria-label="Navigasi seluler" class="container-page max-h-[calc(100dvh-4.5rem)] overflow-y-auto py-4">
            <ul class="grid gap-1">
                @foreach ($nav as [$route, $label, $pattern])
                    @php $active = request()->routeIs($pattern); @endphp
                    <li>
                        <a href="{{ route($route) }}" @if ($active) aria-current="page" @endif @click="close()"
                           class="flex min-h-12 items-center justify-between rounded-2xl px-4 text-lg font-semibold {{ $active ? 'bg-forest-800 text-white' : 'text-ink hover:bg-forest-50' }}">
                            {{ $label }} <x-lucide-chevron-right class="size-5 opacity-60" aria-hidden="true" />
                        </a>
                    </li>
                @endforeach
            </ul>
            <a href="{{ route('contact') }}#kontribusi" @click="close()" class="btn btn-accent mt-4 w-full">
                <x-lucide-hand-heart class="size-5" aria-hidden="true" /> Kirim Kontribusi Warga
            </a>
        </nav>
    </div>
</header>
