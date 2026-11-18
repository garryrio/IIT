@php
    $site = \App\Models\SiteSetting::current();
    $wa = \App\Models\SiteSetting::waLink($item->whatsapp, 'Halo, saya tertarik dengan '.$item->name.' (dari website Desa Marga Mulya).');
@endphp
<x-layouts.public :title="$item->name" :description="$item->summary" :image="$item->image_url">
    <x-page-hero :title="$item->name" :eyebrow="$item->categoryLabel()" :lead="$item->summary" :crumbs="['Potensi Desa' => route('potentials.index'), $item->name => null]" />

    <div class="container-page section pt-10">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">
            <div class="lg:col-span-2">
                @if ($item->image_url)
                    <img src="{{ $item->image_url }}" alt="Gambar {{ $item->name }}" width="1200" height="800" class="aspect-[3/2] w-full rounded-[2rem] object-cover shadow-[var(--shadow-lift)]" data-reveal>
                @endif
                <div class="prose-village mt-8 text-lg">{!! \App\Support\Html::clean($item->description) !!}</div>
            </div>

            <aside class="space-y-5" aria-label="Informasi kontak dan lokasi">
                <div class="card p-6 lg:sticky lg:top-24">
                    <h2 class="text-lg font-bold">Informasi</h2>
                    <dl class="mt-4 space-y-3">
                        @foreach ([['users', 'Pengelola', $item->owner_or_manager], ['clock', 'Jam operasional', $item->operating_hours], ['wallet', 'Kisaran harga', $item->price_range], ['map-pin', 'Alamat', $item->address], ['phone', 'Telepon', $item->contact]] as [$icon, $k, $v])
                            @if (filled($v))
                                <div class="flex gap-3"><x-dynamic-component :component="'lucide-'.$icon" class="mt-0.5 size-5 shrink-0 text-forest-700" aria-hidden="true" /><div><dt class="text-sm text-ink-soft">{{ $k }}</dt><dd class="font-semibold">{{ $v }}</dd></div></div>
                            @endif
                        @endforeach
                    </dl>
                    <div class="mt-6 grid gap-2">
                        @if ($wa)<a href="{{ $wa }}" target="_blank" rel="noopener" class="btn btn-primary"><x-lucide-message-circle class="size-5" aria-hidden="true" /> Hubungi via WhatsApp <span class="sr-only">(tab baru)</span></a>@endif
                        @if ($item->latitude)
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $item->latitude }},{{ $item->longitude }}" target="_blank" rel="noopener" class="btn btn-outline"><x-lucide-navigation class="size-5" aria-hidden="true" /> Petunjuk arah <span class="sr-only">(tab baru)</span></a>
                        @endif
                    </div>
                    @if ($item->socials())
                        <ul class="mt-5 flex flex-wrap gap-2">
                            @foreach ($item->socials() as $k => $url)
                                <li><a href="{{ $url }}" target="_blank" rel="noopener" class="chip"><x-social-icon :name="$k" class="size-4" /> {{ ucfirst($k) }} <span class="sr-only">(tab baru)</span></a></li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </aside>
        </div>

        @if ($item->latitude)
            <section class="mt-14" aria-labelledby="lokasi-judul">
                <h2 id="lokasi-judul" class="section-title">Lokasi</h2>
                <x-village-map class="mt-6" :markers="collect($markers)->where('url', $item->url())->values()->all()" :center="[$item->latitude, $item->longitude]" :zoom="16" height="h-[380px]" :list="false" label="Peta lokasi {{ $item->name }}" />
            </section>
        @endif

        @if ($related->isNotEmpty())
            <section class="mt-16" aria-labelledby="terkait-judul">
                <h2 id="terkait-judul" class="section-title">Potensi serupa</h2>
                <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">@foreach ($related as $r)<x-potential-card :item="$r" />@endforeach</div>
            </section>
        @endif
    </div>
</x-layouts.public>
