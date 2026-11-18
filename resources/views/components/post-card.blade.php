@props(['post', 'compact' => false])
@php
    $tone = ['news' => 'bg-forest-100 text-forest-900', 'announcement' => 'bg-sun-200 text-ink', 'event' => 'bg-[#dbeef3] text-[#174f63]', 'service' => 'bg-[#ece3f3] text-[#563670]'][$post->type] ?? 'bg-stone-100';
@endphp
<article {{ $attributes->merge(['class' => 'group card card-hover relative flex flex-col overflow-hidden']) }}>
    @if (! $compact && $post->cover_image_url)
        <div class="aspect-[16/10] overflow-hidden bg-forest-50">
            <img src="{{ $post->cover_image_url }}" alt="" loading="lazy" decoding="async" width="800" height="500" class="size-full object-cover transition duration-500 group-hover:scale-[1.03]">
        </div>
    @endif
    <div class="flex flex-1 flex-col p-5">
        <div class="flex flex-wrap items-center gap-2 text-sm">
            <span class="badge {{ $tone }}">{{ $post->typeLabel() }}</span>
            @if ($post->isExpired())<span class="badge bg-stone-200 text-stone-700">Berakhir</span>@endif
            @if ($post->isPastEvent())<span class="badge bg-stone-200 text-stone-700">Selesai</span>@endif
            @if ($post->type === 'announcement' && $post->priority > 0 && ! $post->isExpired())<span class="badge bg-clay-600 text-white">{{ \App\Models\InformationPost::PRIORITIES[$post->priority] }}</span>@endif
            @if ($date = $post->displayDate())
                <time datetime="{{ $date->toDateString() }}" class="text-ink-soft">{{ $date->translatedFormat('d M Y') }}</time>
            @endif
        </div>
        <h3 class="mt-3 text-lg leading-snug font-bold">
            <a href="{{ $post->url() }}" class="after:absolute after:inset-0 focus:outline-none focus-visible:underline">{{ $post->title }}</a>
        </h3>
        @if ($post->excerpt)<p class="mt-2 line-clamp-3 text-[0.97rem] text-ink-soft">{{ $post->excerpt }}</p>@endif
        @if ($post->type === 'event' && ($post->location || $post->timeRange()))
            <p class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-sm font-medium text-ink-soft">
                @if ($post->timeRange())<span class="inline-flex items-center gap-1.5"><x-lucide-clock class="size-4" aria-hidden="true" />{{ $post->timeRange() }}</span>@endif
                @if ($post->location)<span class="inline-flex items-center gap-1.5"><x-lucide-map-pin class="size-4" aria-hidden="true" />{{ $post->location }}</span>@endif
            </p>
        @endif
        <span class="mt-auto inline-flex items-center gap-1 pt-4 text-sm font-bold text-forest-700">Baca selengkapnya <x-lucide-arrow-right class="size-4 transition group-hover:translate-x-0.5" aria-hidden="true" /></span>
    </div>
</article>
