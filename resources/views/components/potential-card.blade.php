@props(['item', 'large' => false])
<article {{ $attributes->merge(['class' => 'group card card-hover relative flex flex-col overflow-hidden']) }}>
    <div class="relative overflow-hidden bg-forest-50 {{ $large ? 'aspect-[16/11] lg:aspect-auto lg:flex-1' : 'aspect-[4/3]' }}">
        @if ($item->image_url)
            <img src="{{ $item->image_url }}" alt="" loading="lazy" decoding="async" width="800" height="600" class="size-full object-cover transition duration-500 group-hover:scale-[1.04]">
        @endif
        <span class="badge absolute top-3 left-3 bg-paper/95 text-ink shadow-sm">
            <span class="size-2 rounded-full" style="background: {{ \App\Models\Potential::COLORS[$item->category] ?? '#2f6b3a' }}"></span>
            {{ $item->categoryLabel() }}
        </span>
    </div>
    <div class="flex flex-col p-5">
        <h3 class="{{ $large ? 'font-display text-2xl' : 'text-lg' }} leading-snug font-bold">
            <a href="{{ $item->url() }}" class="after:absolute after:inset-0 focus:outline-none focus-visible:underline">{{ $item->name }}</a>
        </h3>
        @if ($item->summary)<p class="mt-2 line-clamp-2 text-[0.97rem] text-ink-soft">{{ $item->summary }}</p>@endif
        <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-sm text-ink-soft">
            @if ($item->operating_hours)<span class="inline-flex items-center gap-1.5"><x-lucide-clock class="size-4" aria-hidden="true" />{{ $item->operating_hours }}</span>@endif
            @if ($item->owner_or_manager)<span class="inline-flex items-center gap-1.5"><x-lucide-users class="size-4" aria-hidden="true" />{{ $item->owner_or_manager }}</span>@endif
        </div>
    </div>
</article>
