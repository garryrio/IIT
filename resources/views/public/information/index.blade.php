@php
    $tabs = ['' => 'Semua'] + \App\Models\InformationPost::TYPES;
    $base = array_filter(['q' => $filters['q'] ?? null, 'dari' => $filters['dari'] ?? null, 'sampai' => $filters['sampai'] ?? null, 'status' => $filters['status'] ?? null]);
@endphp
<x-layouts.public title="Informasi Desa" description="Berita, pengumuman, agenda kegiatan, dan informasi layanan Desa Marga Mulya.">
    <x-page-hero title="Informasi Desa" eyebrow="Berita · Pengumuman · Agenda · Layanan" lead="Semua kabar resmi desa dalam satu tempat. Gunakan filter atau tampilan kalender untuk menemukan kegiatan." :crumbs="['Informasi' => null]" />

    <div class="container-page section pt-8">
        @foreach ($priority as $p)
            <a href="{{ $p->url() }}" class="mb-3 flex items-start gap-3 rounded-2xl border border-clay-500/30 bg-sun-100 p-4 hover:bg-sun-200" data-reveal>
                <x-lucide-triangle-alert class="mt-0.5 size-5 shrink-0 text-clay-700" aria-hidden="true" />
                <span><span class="font-bold">{{ \App\Models\InformationPost::PRIORITIES[$p->priority] }}: {{ $p->title }}</span>@if ($p->end_date)<span class="block text-sm text-ink-soft">Berlaku hingga {{ $p->end_date->translatedFormat('d F Y') }}</span>@endif</span>
            </a>
        @endforeach

        <nav aria-label="Jenis informasi" class="mt-6 flex gap-2 overflow-x-auto pb-1 [scrollbar-width:none]">
            @foreach ($tabs as $key => $label)
                <a href="{{ route('information.index', array_filter(['jenis' => $key ?: null, 'tampilan' => $view === 'kalender' ? 'kalender' : null]) + $base) }}" class="chip whitespace-nowrap" @if (($type ?? '') === $key) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
        </nav>

        <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
            <div class="inline-flex rounded-full border border-line bg-paper p-1" role="group" aria-label="Pilih tampilan">
                <a href="{{ route('information.index', array_filter(['jenis' => $type]) + $base) }}" class="inline-flex min-h-10 items-center gap-1.5 rounded-full px-4 text-sm font-semibold {{ $view === 'daftar' ? 'bg-forest-800 text-white' : 'text-ink-soft' }}" @if ($view === 'daftar') aria-current="page" @endif><x-lucide-list class="size-4" aria-hidden="true" /> Daftar</a>
                <a href="{{ route('information.index', array_filter(['jenis' => $type, 'tampilan' => 'kalender']) + $base) }}" class="inline-flex min-h-10 items-center gap-1.5 rounded-full px-4 text-sm font-semibold {{ $view === 'kalender' ? 'bg-forest-800 text-white' : 'text-ink-soft' }}" @if ($view === 'kalender') aria-current="page" @endif><x-lucide-calendar-days class="size-4" aria-hidden="true" /> Kalender agenda</a>
            </div>
        </div>

        @if ($view === 'kalender')
            @include('public.information.calendar')
        @else
            <form method="GET" action="{{ route('information.index') }}" class="card mt-6 grid grid-cols-1 gap-4 p-4 sm:p-5 md:grid-cols-2 lg:grid-cols-12 lg:items-end" aria-label="Filter informasi">
                @if ($type)<input type="hidden" name="jenis" value="{{ $type }}">@endif
                <div class="lg:col-span-4">
                    <label for="q" class="label">Kata kunci</label>
                    <input id="q" name="q" type="search" value="{{ $filters['q'] ?? '' }}" class="input" placeholder="mis. posyandu, pelatihan">
                </div>
                <div class="lg:col-span-2"><label for="dari" class="label">Dari tanggal</label><input id="dari" name="dari" type="date" value="{{ $filters['dari'] ?? '' }}" class="input"></div>
                <div class="lg:col-span-2"><label for="sampai" class="label">Sampai tanggal</label><input id="sampai" name="sampai" type="date" value="{{ $filters['sampai'] ?? '' }}" class="input"></div>
                <div class="lg:col-span-2">
                    <label for="status" class="label">Status</label>
                    <select id="status" name="status" class="input">
                        <option value="">Semua</option>
                        <option value="aktif" @selected(($filters['status'] ?? '') === 'aktif')>Aktif / mendatang</option>
                        <option value="arsip" @selected(($filters['status'] ?? '') === 'arsip')>Arsip / selesai</option>
                    </select>
                </div>
                <div class="flex gap-2 lg:col-span-2">
                    <button class="btn btn-primary flex-1">Terapkan</button>
                    @if ($base)<a href="{{ route('information.index', array_filter(['jenis' => $type])) }}" class="btn btn-ghost" aria-label="Hapus filter"><x-lucide-x class="size-5" aria-hidden="true" /></a>@endif
                </div>
                @if ($errors->any())<p class="field-error md:col-span-2 lg:col-span-12">{{ $errors->first() }}</p>@endif
            </form>

            <div class="mt-8 grid grid-cols-1 gap-10 lg:grid-cols-12">
                <div class="lg:col-span-8">
                    <p class="mb-4 text-ink-soft" role="status">{{ $posts->total() }} informasi ditemukan</p>
                    @if ($posts->isEmpty())
                        <x-empty-state icon="search-x" title="Tidak ada informasi yang cocok" text="Ubah kata kunci, rentang tanggal, atau jenis informasi." />
                    @else
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            @foreach ($posts as $post)<x-post-card :post="$post" />@endforeach
                        </div>
                        {{ $posts->links('partials.pagination') }}
                    @endif
                </div>
                <aside class="lg:col-span-4" aria-labelledby="agenda-samping">
                    <div class="card p-5 lg:sticky lg:top-24">
                        <h2 id="agenda-samping" class="flex items-center gap-2 text-lg font-bold"><x-lucide-calendar-days class="size-5 text-forest-700" aria-hidden="true" /> Agenda mendatang</h2>
                        @forelse ($upcoming as $e)
                            <a href="{{ $e->url() }}" class="mt-3 flex gap-3 rounded-2xl p-2 hover:bg-cream">
                                <span class="flex w-12 shrink-0 flex-col items-center rounded-xl bg-forest-50 py-1.5 text-forest-900"><span class="text-[0.7rem] font-bold uppercase">{{ $e->event_date->translatedFormat('M') }}</span><span class="text-xl leading-none font-bold">{{ $e->event_date->format('d') }}</span></span>
                                <span><span class="block leading-snug font-semibold">{{ $e->title }}</span><span class="text-sm text-ink-soft">{{ $e->timeRange() }}</span></span>
                            </a>
                        @empty
                            <p class="mt-3 text-ink-soft">Belum ada agenda mendatang.</p>
                        @endforelse
                    </div>
                </aside>
            </div>
        @endif
    </div>
</x-layouts.public>
