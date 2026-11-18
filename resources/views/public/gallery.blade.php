@php
    $data = $items->map(fn ($g) => ['id' => $g->id, 'title' => $g->title, 'description' => $g->description, 'image' => $g->image_url, 'alt' => $g->alt_text,
        'category' => $g->category, 'categoryLabel' => $g->categoryLabel(), 'date' => $g->event_date?->translatedFormat('d F Y'), 'location' => $g->location])->values();
@endphp
<x-layouts.public title="Galeri" description="Galeri kegiatan, lingkungan, UMKM, dan pembangunan di Desa Marga Mulya.">
    <x-page-hero title="Galeri Desa" eyebrow="Dokumentasi" lead="Potret kegiatan warga, lingkungan, UMKM, pembangunan, dan fasilitas Desa Marga Mulya." :crumbs="['Galeri' => null]" />

    <div class="container-page section pt-10" x-data="gallery(@js($data), {{ (int) $open }})">
        <div class="flex flex-wrap gap-2" role="group" aria-label="Filter kategori galeri">
            <button type="button" class="chip" :aria-pressed="(category === 'all').toString()" @click="category = 'all'">Semua</button>
            @foreach ($categories as $key => $label)
                <button type="button" class="chip" :aria-pressed="(category === '{{ $key }}').toString()" @click="category = '{{ $key }}'">{{ $label }}</button>
            @endforeach
        </div>
        <p class="sr-only" aria-live="polite" x-text="filtered.length + ' foto ditampilkan'"></p>

        @if ($items->isEmpty())
            <x-empty-state class="mt-8" icon="images" title="Belum ada foto di galeri" />
        @else
            <ul class="mt-8 columns-1 gap-5 sm:columns-2 lg:columns-3">
                @foreach ($items as $g)
                    <li class="mb-5 break-inside-avoid" x-show="visible({ category: '{{ $g->category }}' })" x-transition.opacity>
                        <button type="button" @click="showById({{ $g->id }})" class="group block w-full overflow-hidden rounded-[var(--radius-card)] border border-line bg-paper text-left shadow-[var(--shadow-soft)] transition hover:shadow-[var(--shadow-lift)]">
                            <span class="block overflow-hidden bg-forest-50"><img src="{{ $g->image_url }}" alt="{{ $g->alt_text }}" loading="lazy" decoding="async" width="1200" height="800" class="w-full object-cover transition duration-500 group-hover:scale-[1.03] {{ $loop->index % 3 === 1 ? 'aspect-[4/5]' : 'aspect-[3/2]' }}"></span>
                            <span class="block p-4">
                                <span class="badge bg-forest-50 text-forest-800">{{ $g->categoryLabel() }}</span>
                                <span class="mt-2 block font-bold">{{ $g->title }}</span>
                                @if ($g->event_date)<span class="text-sm text-ink-soft">{{ $g->event_date->translatedFormat('d F Y') }}@if ($g->location) · {{ $g->location }}@endif</span>@endif
                            </span>
                        </button>
                    </li>
                @endforeach
            </ul>
        @endif

        <dialog x-ref="dialog" @close="onClose()" @keydown.arrow-right="step(1)" @keydown.arrow-left="step(-1)" aria-labelledby="lightbox-judul"
                class="m-auto w-[min(64rem,calc(100vw-1.5rem))] max-w-none overflow-hidden rounded-3xl bg-paper p-0 backdrop:bg-forest-950/80 backdrop:backdrop-blur-sm">
            <template x-if="current">
                <div class="grid grid-cols-1 max-h-[90vh] lg:grid-cols-3">
                    <div class="relative bg-forest-950 lg:col-span-2">
                        <img :src="current.image" :alt="current.alt" class="max-h-[60vh] w-full object-contain lg:h-full lg:max-h-[90vh]">
                        <button type="button" @click="step(-1)" class="absolute top-1/2 left-3 inline-flex size-11 -translate-y-1/2 items-center justify-center rounded-full bg-paper/90 hover:bg-paper" aria-label="Foto sebelumnya"><x-lucide-chevron-left class="size-6" aria-hidden="true" /></button>
                        <button type="button" @click="step(1)" class="absolute top-1/2 right-3 inline-flex size-11 -translate-y-1/2 items-center justify-center rounded-full bg-paper/90 hover:bg-paper" aria-label="Foto berikutnya"><x-lucide-chevron-right class="size-6" aria-hidden="true" /></button>
                    </div>
                    <div class="overflow-y-auto p-6">
                        <div class="flex items-start justify-between gap-3">
                            <span class="badge bg-forest-50 text-forest-800" x-text="current.categoryLabel"></span>
                            <button type="button" @click="hide()" class="-mt-2 -mr-2 inline-flex size-11 items-center justify-center rounded-full hover:bg-cream" aria-label="Tutup"><x-lucide-x class="size-5" aria-hidden="true" /></button>
                        </div>
                        <h2 id="lightbox-judul" class="mt-3 font-display text-2xl font-semibold" x-text="current.title"></h2>
                        <p class="mt-2 text-sm text-ink-soft" x-text="[current.date, current.location].filter(Boolean).join(' · ')"></p>
                        <p class="mt-4" x-text="current.description"></p>
                        <p class="mt-6 text-sm text-ink-soft"><span x-text="index + 1"></span> / <span x-text="filtered.length"></span> · Gunakan tombol panah untuk berpindah</p>
                    </div>
                </div>
            </template>
        </dialog>
    </div>
</x-layouts.public>
