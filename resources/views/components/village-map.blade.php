@props([
    'markers' => [],
    'layers' => [],
    'height' => 'h-[420px]',
    'label' => 'Peta interaktif Desa Marga Mulya',
    'center' => null,
    'zoom' => 15,
    'showCenter' => false,
    'centerLabel' => 'Kantor Desa',
    'list' => true,
])
@php
    $center = $center ?? \App\Support\MapData::center();
    $config = ['center' => $center, 'zoom' => $zoom, 'markers' => array_values($markers), 'layers' => $layers, 'showCenter' => $showCenter, 'centerLabel' => $centerLabel];
@endphp
<div x-data="villageMap(@js($config))" {{ $attributes }}>
    @if (count($layers) > 1)
        <div class="mb-3 flex flex-wrap gap-2" role="group" aria-label="Lapisan peta">
            @foreach ($layers as $key => $layerLabel)
                <button type="button" class="chip" :aria-pressed="activeLayers.includes('{{ $key }}').toString()" aria-pressed="true" @click="toggleLayer('{{ $key }}')">
                    <span class="size-2.5 rounded-full" style="background: {{ ['potensi' => '#c46a16', 'fasilitas' => '#1f6f8b', 'agenda' => '#c48a12'][$key] ?? '#1f4d2b' }}"></span>
                    {{ $layerLabel }} <span class="opacity-70" x-text="'(' + layerCount('{{ $key }}') + ')'"></span>
                </button>
            @endforeach
        </div>
    @endif
    <div class="map-frame {{ $height }}">
        <div x-ref="map" class="absolute inset-0 z-0" role="region" aria-label="{{ $label }}" tabindex="0"></div>
        <div x-show="status !== 'ready'" class="absolute inset-0 z-10 flex flex-col items-center justify-center gap-2 bg-forest-50 p-6 text-center text-ink-soft">
            <x-lucide-map class="size-8 text-forest-600" aria-hidden="true" />
            <p x-show="status !== 'error'" role="status">Memuat peta…</p>
            <p x-show="status === 'error'" x-cloak role="alert">Peta tidak dapat dimuat. Gunakan daftar lokasi di bawah peta.</p>
        </div>
    </div>
    @if ($list && count($markers))
        <details class="group mt-3 rounded-2xl border border-line bg-paper">
            <summary class="flex min-h-11 cursor-pointer list-none items-center justify-between px-4 py-2 font-semibold">
                <span>Daftar lokasi ({{ count($markers) }})</span>
                <x-lucide-chevron-down class="size-5 transition group-open:rotate-180" aria-hidden="true" />
            </summary>
            <ul class="grid grid-cols-1 gap-1 px-2 pb-3 sm:grid-cols-2">
                @foreach ($markers as $m)
                    <li class="flex items-start justify-between gap-2 rounded-xl px-2 py-2 hover:bg-cream">
                        <span>
                            <span class="block font-semibold">{{ $m['title'] }}</span>
                            <span class="text-sm text-ink-soft">{{ $m['categoryLabel'] }}</span>
                        </span>
                        <span class="flex shrink-0 gap-1">
                            <button type="button" class="btn btn-ghost btn-sm" @click="focusMarker({{ $m['lat'] }}, {{ $m['lng'] }})">Lihat <span class="sr-only">{{ $m['title'] }} di peta</span></button>
                        </span>
                    </li>
                @endforeach
            </ul>
        </details>
    @endif
</div>
