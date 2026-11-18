<x-layouts.public title="Potensi Desa" description="Jelajahi potensi Desa Marga Mulya: pertanian, UMKM, kuliner, kerajinan, wisata, dan jasa — lengkap dengan peta dan kontak.">
    <x-page-hero title="Jelajah Potensi Desa" eyebrow="Katalog & peta potensi" lead="Temukan produk, usaha, dan ruang publik yang dikelola warga Marga Mulya. Hubungi pengelola langsung melalui WhatsApp." :crumbs="['Potensi Desa' => null]" />

    <div class="container-page section pt-10" x-data="catalog({{ $items->count() }})">
        <div class="card flex flex-col gap-4 p-4 sm:p-5">
            <div class="relative">
                <label for="cari-potensi" class="sr-only">Cari potensi</label>
                <x-lucide-search class="pointer-events-none absolute top-1/2 left-4 size-5 -translate-y-1/2 text-ink-soft" aria-hidden="true" />
                <input id="cari-potensi" type="search" x-model.debounce.200ms="q" placeholder="Cari nama, produk, atau pengelola…" class="input !rounded-full !pl-12">
            </div>
            <div class="flex flex-wrap gap-2" role="group" aria-label="Filter kategori">
                <button type="button" class="chip" :aria-pressed="(category === 'all').toString()" @click="category = 'all'">Semua <span class="opacity-70">({{ $items->count() }})</span></button>
                @foreach ($categories as $key => $label)
                    <button type="button" class="chip" :aria-pressed="(category === '{{ $key }}').toString()" @click="category = '{{ $key }}'">
                        <span class="size-2.5 rounded-full" style="background: {{ \App\Models\Potential::COLORS[$key] ?? '#2f6b3a' }}"></span>
                        {{ $label }} <span class="opacity-70">({{ $items->where('category', $key)->count() }})</span>
                    </button>
                @endforeach
            </div>
        </div>

        <p class="mt-6 text-ink-soft" aria-live="polite"><span class="font-bold text-ink" x-text="shown">{{ $items->count() }}</span> potensi ditampilkan</p>

        @if ($items->isEmpty())
            <x-empty-state class="mt-6" icon="sprout" title="Belum ada potensi yang diterbitkan" />
        @else
            <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($items as $item)
                    <x-potential-card :item="$item" data-item data-category="{{ $item->category }}"
                        data-search="{{ \Illuminate\Support\Str::lower($item->name.' '.$item->summary.' '.$item->owner_or_manager.' '.$item->categoryLabel()) }}" />
                @endforeach
            </div>
            <div x-show="shown === 0" x-cloak class="mt-6">
                <x-empty-state icon="search-x" title="Tidak ada potensi yang cocok" text="Coba kata kunci lain atau pilih kategori Semua.">
                    <button type="button" class="btn btn-outline mt-4" @click="reset()">Atur ulang pencarian</button>
                </x-empty-state>
            </div>
        @endif

        <section class="mt-16" aria-labelledby="peta-potensi-judul">
            <h2 id="peta-potensi-judul" class="section-title">Peta potensi</h2>
            <p class="section-lead mb-6">Penanda di peta mengikuti filter kategori di atas. Klik penanda untuk melihat ringkasan dan tautan detail.</p>
            <x-village-map :markers="$markers" :layers="['potensi' => 'Potensi']" height="h-[480px]" label="Peta potensi Desa Marga Mulya" />
        </section>
    </div>
</x-layouts.public>
