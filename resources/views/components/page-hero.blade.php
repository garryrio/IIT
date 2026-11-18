@props(['title', 'eyebrow' => null, 'lead' => null, 'crumbs' => []])
<section class="relative overflow-hidden border-b border-line bg-gradient-to-b from-leaf-100/70 to-cream">
    <svg class="pointer-events-none absolute -right-24 -bottom-10 h-72 w-[38rem] text-leaf-200" viewBox="0 0 600 280" fill="none" aria-hidden="true">
        @for ($i = 0; $i < 7; $i++)
            <path d="M0 {{ 280 - $i * 28 }} Q300 {{ 200 - $i * 30 }} 600 {{ 260 - $i * 26 }}" stroke="currentColor" stroke-width="{{ 10 - $i }}" />
        @endfor
    </svg>
    <div class="container-page relative py-12 sm:py-16">
        @if ($crumbs)
            <nav aria-label="Breadcrumb" class="mb-5">
                <ol class="flex flex-wrap items-center gap-1.5 text-sm text-ink-soft">
                    <li><a href="{{ route('home') }}" class="hover:text-forest-800 hover:underline">Beranda</a></li>
                    @foreach ($crumbs as $label => $url)
                        <li aria-hidden="true"><x-lucide-chevron-right class="size-3.5" /></li>
                        <li>@if ($url)<a href="{{ $url }}" class="hover:text-forest-800 hover:underline">{{ $label }}</a>@else<span aria-current="page" class="font-semibold text-ink">{{ $label }}</span>@endif</li>
                    @endforeach
                </ol>
            </nav>
        @endif
        @if ($eyebrow)<p class="eyebrow" data-reveal>{{ $eyebrow }}</p>@endif
        <h1 class="mt-2 max-w-4xl font-display text-4xl font-semibold tracking-tight sm:text-5xl" data-reveal>{{ $title }}</h1>
        @if ($lead)<p class="mt-4 max-w-2xl text-lg text-ink-soft" data-reveal data-reveal-delay="0.08">{{ $lead }}</p>@endif
        {{ $slot }}
    </div>
</section>
