@php
    $site = \App\Models\SiteSetting::current();
    $collage = $gallery->take(2)->values();
    $population = $stats->firstWhere('indicator', 'Jumlah penduduk') ?? $stats->first();
@endphp
<x-layouts.public>
    {{-- HERO — pola "editorial collage hero" (diadaptasi dari referensi 21st.dev ke Blade + framer-motion) --}}
    <section class="relative overflow-hidden">
        <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-[38rem] bg-[radial-gradient(60%_60%_at_80%_10%,var(--color-sun-100),transparent_70%),radial-gradient(50%_60%_at_10%_20%,var(--color-leaf-100),transparent_70%)]"></div>
        <div class="container-page grid grid-cols-1 items-center gap-12 pt-10 pb-16 lg:grid-cols-12 lg:pt-16 lg:pb-24">
            <div class="lg:col-span-6">
                <p class="eyebrow" data-reveal>
                    <span class="inline-block size-2 rounded-full bg-sun-400"></span>
                    {{ $site->village_name }}@if ($profile?->regency) · {{ $profile->regency }}@endif
                </p>
                <h1 class="mt-4 font-display text-[2.6rem] leading-[1.05] font-semibold tracking-tight sm:text-6xl" data-reveal data-reveal-delay="0.05">
                    {{ $site->hero_title }}
                </h1>
                @if ($site->hero_description)
                    <p class="mt-5 max-w-xl text-lg text-ink-soft sm:text-xl" data-reveal data-reveal-delay="0.12">{{ $site->hero_description }}</p>
                @endif
                <div class="mt-8 flex flex-wrap gap-3" data-reveal data-reveal-delay="0.18">
                    <a href="{{ route('profile') }}" class="btn btn-primary">{{ $site->hero_cta_primary ?: 'Jelajahi Desa' }} <x-lucide-arrow-right class="size-5" aria-hidden="true" /></a>
                    <a href="{{ route('potentials.index') }}" class="btn btn-outline">{{ $site->hero_cta_secondary ?: 'Lihat Potensi' }}</a>
                </div>
                <button type="button" @click="$dispatch('open-search')" data-reveal data-reveal-delay="0.24"
                        class="mt-8 flex min-h-12 w-full max-w-md items-center gap-3 rounded-full border border-line bg-paper px-4 text-left text-ink-soft shadow-[var(--shadow-soft)] hover:border-forest-400">
                    <x-lucide-search class="size-5 text-forest-700" aria-hidden="true" />
                    <span class="flex-1">Cari layanan, agenda, atau potensi…</span>
                    <kbd class="hidden rounded-md border border-line px-1.5 text-xs sm:inline">Ctrl K</kbd>
                </button>
            </div>

            <div class="relative lg:col-span-6" data-reveal="fade" data-reveal-delay="0.1">
                <div class="relative grid grid-cols-6 grid-rows-6 gap-3 sm:gap-4" style="aspect-ratio: 6 / 5">
                    <div class="col-span-4 row-span-6 overflow-hidden rounded-[2rem] bg-forest-100 shadow-[var(--shadow-lift)]">
                        @if ($site->hero_image_url)
                            <img src="{{ $site->hero_image_url }}" alt="Pemandangan {{ $site->village_name }}" width="1200" height="800" fetchpriority="high" data-parallax="-30" class="h-[110%] w-full object-cover">
                        @endif
                    </div>
                    @foreach ($collage as $i => $g)
                        <div class="col-span-2 row-span-3 overflow-hidden rounded-3xl bg-forest-100 shadow-[var(--shadow-soft)]">
                            <img src="{{ $g->image_url }}" alt="{{ $g->alt_text }}" loading="lazy" width="600" height="600" class="size-full object-cover">
                        </div>
                    @endforeach
                </div>
                @if ($population)
                    <div class="absolute -bottom-6 left-4 rounded-2xl border border-line bg-paper/95 px-5 py-3.5 shadow-[var(--shadow-lift)] backdrop-blur sm:left-8">
                        <p class="text-sm font-semibold text-ink-soft">{{ $population->indicator }} ({{ $population->year }})</p>
                        <p class="font-display text-3xl font-semibold text-forest-900"><span data-count="{{ $population->value }}">{{ $population->formattedValue() }}</span> <span class="text-base font-sans text-ink-soft">{{ $population->unit }}</span></p>
                    </div>
                @endif
                @if ($announcements->first())
                    <a href="{{ $announcements->first()->url() }}" class="absolute -top-4 right-2 flex max-w-[16rem] items-center gap-2 rounded-full bg-sun-400 px-4 py-2 text-sm font-bold text-ink shadow-[var(--shadow-lift)] hover:bg-sun-300 sm:right-6">
                        <x-lucide-megaphone class="size-4 shrink-0" aria-hidden="true" /><span class="truncate">{{ $announcements->first()->title }}</span>
                    </a>
                @endif
            </div>
        </div>
    </section>

    {{-- STATISTIK SINGKAT --}}
    @if ($stats->isNotEmpty())
        <section aria-labelledby="statistik-judul" class="border-y border-line bg-forest-900 text-white">
            <h2 id="statistik-judul" class="sr-only">Statistik singkat desa</h2>
            <div class="container-page grid grid-cols-2 divide-white/10 py-8 md:grid-cols-4 md:divide-x" data-stagger>
                @foreach ($stats as $s)
                    <div class="px-2 py-3 md:px-6">
                        <p class="font-display text-3xl font-semibold text-sun-300 sm:text-4xl">
                            <span data-count="{{ $s->value }}" data-decimals="{{ floor($s->value) == $s->value ? 0 : 1 }}">{{ $s->formattedValue() }}</span>
                            <span class="font-sans text-base font-semibold text-forest-200">{{ $s->unit }}</span>
                        </p>
                        <p class="mt-1 text-forest-100">{{ $s->indicator }}</p>
                    </div>
                @endforeach
            </div>
            <div class="container-page pb-6"><a href="{{ route('data') }}" class="inline-flex min-h-11 items-center gap-1.5 font-semibold text-sun-200 hover:text-white">Lihat seluruh data desa <x-lucide-arrow-right class="size-4" aria-hidden="true" /></a></div>
        </section>
    @endif

    {{-- PENGUMUMAN AKTIF --}}
    <section class="section pb-0" aria-labelledby="pengumuman-judul">
        <div class="container-page">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="eyebrow"><x-lucide-megaphone class="size-4" aria-hidden="true" /> Pengumuman aktif</p>
                    <h2 id="pengumuman-judul" class="section-title mt-2">Yang perlu warga ketahui</h2>
                </div>
                <a href="{{ route('information.index', ['jenis' => 'announcement']) }}" class="btn btn-ghost">Semua pengumuman <x-lucide-arrow-right class="size-4" aria-hidden="true" /></a>
            </div>
            @if ($announcements->isEmpty())
                <x-empty-state class="mt-8" icon="megaphone" title="Tidak ada pengumuman aktif saat ini" />
            @else
                <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-3" data-stagger>
                    @foreach ($announcements as $a)
                        <article class="relative flex flex-col rounded-[var(--radius-card)] border p-5 {{ $a->priority >= 2 ? 'border-clay-500/40 bg-sun-100' : 'border-line bg-paper' }}">
                            <div class="flex items-center gap-2 text-sm">
                                <span class="badge {{ $a->priority >= 2 ? 'bg-clay-600 text-white' : ($a->priority == 1 ? 'bg-sun-300 text-ink' : 'bg-forest-100 text-forest-900') }}">{{ \App\Models\InformationPost::PRIORITIES[$a->priority] }}</span>
                                @if ($a->end_date)<span class="text-ink-soft">s.d. {{ $a->end_date->translatedFormat('d M Y') }}</span>@endif
                            </div>
                            <h3 class="mt-3 text-lg font-bold"><a href="{{ $a->url() }}" class="after:absolute after:inset-0 hover:underline">{{ $a->title }}</a></h3>
                            <p class="mt-2 line-clamp-3 text-ink-soft">{{ $a->excerpt }}</p>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- POTENSI UNGGULAN (bento grid) --}}
    <section class="section" aria-labelledby="potensi-judul">
        <div class="container-page">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="eyebrow"><x-lucide-sprout class="size-4" aria-hidden="true" /> Potensi unggulan</p>
                    <h2 id="potensi-judul" class="section-title mt-2">Dari sawah sampai usaha rumahan</h2>
                    <p class="section-lead">Produk dan potensi lokal yang dikelola warga, lengkap dengan lokasi dan kontak.</p>
                </div>
                <a href="{{ route('potentials.index') }}" class="btn btn-outline">Jelajah potensi <x-lucide-arrow-right class="size-4" aria-hidden="true" /></a>
            </div>
            @if ($potentials->isEmpty())
                <x-empty-state class="mt-8" icon="sprout" title="Belum ada potensi unggulan" />
            @else
                <div class="mt-8 grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3 lg:grid-rows-2" data-stagger>
                    @foreach ($potentials as $i => $p)
                        <x-potential-card :item="$p" :large="$i === 0" class="{{ $i === 0 ? 'lg:row-span-2' : '' }}" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- AGENDA TERDEKAT --}}
    <section class="section bg-leaf-100/60" aria-labelledby="agenda-judul">
        <div class="container-page grid grid-cols-1 gap-10 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <p class="eyebrow"><x-lucide-calendar-days class="size-4" aria-hidden="true" /> Agenda terdekat</p>
                <h2 id="agenda-judul" class="section-title mt-2">Kegiatan warga yang akan datang</h2>
                <p class="section-lead">Simpan agenda ke kalender ponsel Anda langsung dari halaman detail kegiatan.</p>
                <a href="{{ route('information.index', ['jenis' => 'event', 'tampilan' => 'kalender']) }}" class="btn btn-primary mt-6">Buka kalender agenda <x-lucide-calendar-days class="size-5" aria-hidden="true" /></a>
            </div>
            <div class="lg:col-span-8">
                @if ($events->isEmpty())
                    <x-empty-state icon="calendar-days" title="Belum ada agenda mendatang" />
                @else
                    <ol class="grid gap-3" data-stagger>
                        @foreach ($events as $e)
                            <li class="group card relative flex items-center gap-4 p-4 sm:gap-6 sm:p-5">
                                <div class="flex w-16 shrink-0 flex-col items-center rounded-2xl bg-forest-800 py-2 text-white sm:w-20">
                                    <span class="text-xs font-bold tracking-wider text-sun-300 uppercase">{{ $e->event_date->translatedFormat('M') }}</span>
                                    <span class="font-display text-3xl leading-none font-semibold">{{ $e->event_date->format('d') }}</span>
                                    <span class="text-xs text-forest-200">{{ $e->event_date->translatedFormat('D') }}</span>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-lg font-bold"><a href="{{ $e->url() }}" class="after:absolute after:inset-0 group-hover:underline">{{ $e->title }}</a></h3>
                                    <p class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-sm text-ink-soft">
                                        @if ($e->timeRange())<span class="inline-flex items-center gap-1.5"><x-lucide-clock class="size-4" aria-hidden="true" />{{ $e->timeRange() }}</span>@endif
                                        @if ($e->location)<span class="inline-flex items-center gap-1.5"><x-lucide-map-pin class="size-4" aria-hidden="true" />{{ $e->location }}</span>@endif
                                    </p>
                                </div>
                                <x-lucide-arrow-up-right class="ml-auto hidden size-5 shrink-0 text-forest-700 sm:block" aria-hidden="true" />
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>
        </div>
    </section>

    {{-- PETA DESA --}}
    <section class="section" aria-labelledby="peta-judul">
        <div class="container-page">
            <p class="eyebrow"><x-lucide-map class="size-4" aria-hidden="true" /> Peta desa</p>
            <h2 id="peta-judul" class="section-title mt-2">Temukan lokasi penting di Marga Mulya</h2>
            <p class="section-lead mb-6">Aktifkan atau sembunyikan lapisan peta: potensi lokal, fasilitas umum, dan lokasi agenda.</p>
            <x-village-map :markers="$markers" :layers="['potensi' => 'Potensi', 'fasilitas' => 'Fasilitas', 'agenda' => 'Agenda']" height="h-[440px] sm:h-[520px]" :show-center="true" />
        </div>
    </section>

    {{-- BERITA TERBARU --}}
    <section class="section pt-0" aria-labelledby="berita-judul">
        <div class="container-page">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="eyebrow"><x-lucide-newspaper class="size-4" aria-hidden="true" /> Berita & kegiatan</p>
                    <h2 id="berita-judul" class="section-title mt-2">Kabar terbaru dari desa</h2>
                </div>
                <a href="{{ route('information.index', ['jenis' => 'news']) }}" class="btn btn-ghost">Semua berita <x-lucide-arrow-right class="size-4" aria-hidden="true" /></a>
            </div>
            @if ($news->isEmpty())
                <x-empty-state class="mt-8" icon="newspaper" title="Belum ada berita" />
            @else
                <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-3" data-stagger>
                    @foreach ($news as $n)<x-post-card :post="$n" />@endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- CERITA MARGA MULYA --}}
    @if ($stories->isNotEmpty())
        <section class="section bg-sand/60" aria-labelledby="cerita-judul">
            <div class="container-page">
                <p class="eyebrow"><x-lucide-message-square-quote class="size-4" aria-hidden="true" /> Cerita Marga Mulya</p>
                <h2 id="cerita-judul" class="section-title mt-2">Suara warga</h2>
                <div class="mt-8 grid grid-cols-1 gap-5 md:grid-cols-3" data-stagger>
                    @foreach ($stories as $st)
                        <figure class="card flex flex-col p-6">
                            <x-lucide-quote class="size-8 text-sun-400" aria-hidden="true" />
                            <blockquote class="mt-3 flex-1 text-lg leading-relaxed">“{{ $st->quote }}”</blockquote>
                            <figcaption class="mt-5 flex items-center gap-3">
                                @if ($st->photo_url)
                                    <img src="{{ $st->photo_url }}" alt="" class="size-11 rounded-full object-cover" loading="lazy">
                                @else
                                    <span class="flex size-11 items-center justify-center rounded-full bg-forest-800 font-bold text-white" aria-hidden="true">{{ mb_substr($st->name, 0, 1) }}</span>
                                @endif
                                <span><span class="block font-bold">{{ $st->name }}</span><span class="text-sm text-ink-soft">{{ $st->role }}</span></span>
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- CTA KONTRIBUSI --}}
    <section class="section">
        <div class="container-page">
            <div class="relative overflow-hidden rounded-[2rem] bg-forest-800 px-6 py-12 text-white sm:px-12 sm:py-14" data-reveal>
                <svg aria-hidden="true" class="absolute -right-10 -bottom-16 h-64 w-96 text-forest-700" viewBox="0 0 400 260" fill="none">
                    @for ($i = 0; $i < 6; $i++)<path d="M0 {{ 260 - $i * 30 }} Q200 {{ 180 - $i * 32 }} 400 {{ 240 - $i * 28 }}" stroke="currentColor" stroke-width="12" />@endfor
                </svg>
                <div class="relative grid grid-cols-1 items-center gap-8 lg:grid-cols-3">
                    <div class="lg:col-span-2">
                        <p class="eyebrow text-sun-300">Citizen to Village</p>
                        <h2 class="mt-2 font-display text-3xl font-semibold text-white sm:text-4xl">{{ $site->contribution_cta_title ?: 'Punya informasi tentang desa?' }}</h2>
                        @if ($site->contribution_cta_text)<p class="mt-3 max-w-2xl text-lg text-forest-100">{{ $site->contribution_cta_text }}</p>@endif
                    </div>
                    <div class="flex flex-wrap gap-3 lg:justify-end">
                        <a href="{{ route('contact') }}#kontribusi" class="btn btn-accent">Kirim kontribusi <x-lucide-arrow-right class="size-5" aria-hidden="true" /></a>
                        @if ($wa = $site->whatsappLink('Halo admin Desa Marga Mulya'))
                            <a href="{{ $wa }}" target="_blank" rel="noopener" class="btn border border-white/30 text-white hover:bg-white/10">WhatsApp <span class="sr-only">(tab baru)</span></a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
