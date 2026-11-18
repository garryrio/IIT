@props(['key', 'type' => 'success'])
@if (session($key))
    <div {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-2xl border px-4 py-3 '.($type === 'success' ? 'border-forest-300 bg-forest-50 text-forest-900' : 'border-red-300 bg-red-50 text-red-900')]) }} role="status" tabindex="-1" x-data x-init="$el.focus()">
        <x-lucide-circle-check class="mt-0.5 size-5 shrink-0" aria-hidden="true" />
        <p class="font-semibold">{{ session($key) }}</p>
    </div>
@endif
