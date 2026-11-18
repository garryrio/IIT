@props(['title' => 'Belum ada data', 'text' => null, 'icon' => 'inbox'])
<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center rounded-[var(--radius-card)] border border-dashed border-forest-300 bg-paper/70 px-6 py-12 text-center']) }} role="status">
    <span class="flex size-14 items-center justify-center rounded-full bg-forest-50 text-forest-700">
        <x-dynamic-component :component="'lucide-'.$icon" class="size-7" aria-hidden="true" />
    </span>
    <p class="mt-4 text-lg font-semibold">{{ $title }}</p>
    @if ($text)<p class="mt-1 max-w-md text-ink-soft">{{ $text }}</p>@endif
    {{ $slot }}
</div>
