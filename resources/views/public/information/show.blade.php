@php $site = \App\Models\SiteSetting::current(); @endphp
<x-layouts.public :title="$post->title" :description="$post->excerpt" :image="$post->cover_image_url">
    <x-page-hero :title="$post->title" :eyebrow="$post->typeLabel()" :crumbs="['Informasi' => route('information.index'), $post->typeLabel() => route('information.index', ['jenis' => $post->type]), \Illuminate\Support\Str::limit($post->title, 40) => null]">
        <div class="mt-5 flex flex-wrap gap-x-5 gap-y-2 text-ink-soft">
            @if ($post->published_at)<span class="inline-flex items-center gap-1.5"><x-lucide-calendar class="size-4" aria-hidden="true" />Diterbitkan <time datetime="{{ $post->published_at->toIso8601String() }}">{{ $post->published_at->translatedFormat('d F Y') }}</time></span>@endif
            @if ($post->location)<span class="inline-flex items-center gap-1.5"><x-lucide-map-pin class="size-4" aria-hidden="true" />{{ $post->location }}</span>@endif
        </div>
    </x-page-hero>

    <div class="container-page section pt-10">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">
            <article class="lg:col-span-2">
                @if ($post->isExpired())
                    <p class="mb-6 flex gap-2 rounded-2xl border border-line bg-stone-100 p-4 font-semibold text-stone-700" role="note"><x-lucide-info class="mt-0.5 size-5 shrink-0" aria-hidden="true" />Pengumuman ini sudah berakhir pada {{ $post->end_date->translatedFormat('d F Y') }} dan disimpan sebagai arsip.</p>
                @elseif ($post->isPastEvent())
                    <p class="mb-6 flex gap-2 rounded-2xl border border-line bg-stone-100 p-4 font-semibold text-stone-700" role="note"><x-lucide-info class="mt-0.5 size-5 shrink-0" aria-hidden="true" /><span>Agenda ini telah selesai dilaksanakan.@if (($report = $post->report) && $report->isPublished() && (! $report->published_at || $report->published_at->isPast())) <a href="{{ $report->url() }}" class="link">Baca laporan kegiatannya →</a>@endif</span></p>
                @endif
                @if ($post->cover_image_url)
                    <img src="{{ $post->cover_image_url }}" alt="Gambar sampul: {{ $post->title }}" width="1200" height="800" class="aspect-[16/9] w-full rounded-[2rem] object-cover shadow-[var(--shadow-lift)]">
                @endif
                @if ($post->excerpt)<p class="mt-8 text-xl leading-relaxed font-medium">{{ $post->excerpt }}</p>@endif
                <div class="prose-village mt-4 text-lg">{!! \App\Support\Html::clean($post->content) !!}</div>

                <div class="mt-10 flex flex-wrap items-center gap-2 border-t border-line pt-6" x-data="{ copied: false }">
                    <span class="mr-2 font-semibold">Bagikan:</span>
                    <a href="https://wa.me/?text={{ rawurlencode($post->title.' — '.$post->url()) }}" target="_blank" rel="noopener" class="chip"><x-lucide-message-circle class="size-4" aria-hidden="true" /> WhatsApp <span class="sr-only">(tab baru)</span></a>
                    <button type="button" class="chip" @click="navigator.clipboard.writeText('{{ $post->url() }}').then(() => { copied = true; setTimeout(() => copied = false, 2000) })">
                        <x-lucide-link class="size-4" aria-hidden="true" /><span x-text="copied ? 'Tautan tersalin' : 'Salin tautan'">Salin tautan</span>
                    </button>
                </div>
            </article>

            <aside class="space-y-5">
                @if ($post->type === 'event' && $post->event_date)
                    {{-- Kartu detail + peta lokasi menempel bersama sebagai satu blok agar tidak saling menimpa --}}
                    <div class="space-y-5 lg:sticky lg:top-24">
                    <div class="card p-6">
                        <h2 class="text-lg font-bold">Detail agenda</h2>
                        <dl class="mt-4 space-y-3">
                            <div class="flex gap-3"><x-lucide-calendar-days class="mt-0.5 size-5 shrink-0 text-forest-700" aria-hidden="true" /><div><dt class="text-sm text-ink-soft">Tanggal</dt><dd class="font-semibold">{{ $post->event_date->translatedFormat('l, d F Y') }}</dd></div></div>
                            @if ($post->timeRange())<div class="flex gap-3"><x-lucide-clock class="mt-0.5 size-5 shrink-0 text-forest-700" aria-hidden="true" /><div><dt class="text-sm text-ink-soft">Waktu</dt><dd class="font-semibold">{{ $post->timeRange() }}</dd></div></div>@endif
                            @if ($post->location)<div class="flex gap-3"><x-lucide-map-pin class="mt-0.5 size-5 shrink-0 text-forest-700" aria-hidden="true" /><div><dt class="text-sm text-ink-soft">Lokasi</dt><dd class="font-semibold">{{ $post->location }}</dd></div></div>@endif
                            @if ($post->organizer)<div class="flex gap-3"><x-lucide-users class="mt-0.5 size-5 shrink-0 text-forest-700" aria-hidden="true" /><div><dt class="text-sm text-ink-soft">Penyelenggara</dt><dd class="font-semibold">{{ $post->organizer }}</dd></div></div>@endif
                        </dl>
                        @unless ($post->isPastEvent())
                            <div class="mt-6 grid gap-2">
                                <a href="{{ $calendarUrl }}" target="_blank" rel="noopener" class="btn btn-primary"><x-lucide-calendar-plus class="size-5" aria-hidden="true" /> Tambah ke Google Calendar <span class="sr-only">(tab baru)</span></a>
                                <a href="{{ route('information.ics', $post->slug) }}" class="btn btn-outline"><x-lucide-download class="size-5" aria-hidden="true" /> Unduh .ics (kalender ponsel)</a>
                            </div>
                        @endunless
                    </div>
                    @if ($post->latitude)
                        <x-village-map :markers="[['layer' => 'agenda', 'category' => 'agenda', 'categoryLabel' => 'Lokasi agenda', 'color' => '#c48a12', 'lat' => $post->latitude, 'lng' => $post->longitude, 'title' => $post->location ?: $post->title, 'text' => null, 'url' => null, 'image' => null]]" :center="[$post->latitude, $post->longitude]" :zoom="16" height="h-56" :list="false" label="Peta lokasi agenda" />
                    @endif
                    </div>
                @elseif ($post->type === 'announcement')
                    <div class="card p-6">
                        <h2 class="text-lg font-bold">Masa berlaku</h2>
                        <dl class="mt-4 space-y-2">
                            <div><dt class="text-sm text-ink-soft">Mulai</dt><dd class="font-semibold">{{ $post->start_date?->translatedFormat('d F Y') ?? 'Sejak diterbitkan' }}</dd></div>
                            <div><dt class="text-sm text-ink-soft">Berakhir</dt><dd class="font-semibold">{{ $post->end_date?->translatedFormat('d F Y') ?? 'Tidak ditentukan' }}</dd></div>
                            <div><dt class="text-sm text-ink-soft">Prioritas</dt><dd class="font-semibold">{{ \App\Models\InformationPost::PRIORITIES[$post->priority] ?? 'Normal' }}</dd></div>
                        </dl>
                    </div>
                @endif
                <div class="card p-6">
                    <h2 class="text-lg font-bold">Butuh bantuan?</h2>
                    <p class="mt-2 text-ink-soft">Hubungi kantor desa pada jam layanan{{ $site->service_hours ? ' ('.$site->service_hours.')' : '' }}.</p>
                    <a href="{{ route('contact') }}" class="btn btn-outline mt-4 w-full">Kontak desa</a>
                </div>
            </aside>
        </div>

        @if ($related->isNotEmpty())
            <section class="mt-16" aria-labelledby="terkait-judul">
                <h2 id="terkait-judul" class="section-title">{{ $post->typeLabel() }} lainnya</h2>
                <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-3">@foreach ($related as $r)<x-post-card :post="$r" />@endforeach</div>
            </section>
        @endif
    </div>
</x-layouts.public>
