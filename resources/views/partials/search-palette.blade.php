<div x-data="searchPalette('{{ route('search') }}')" @keydown.escape.window="open && hide()">
    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-start justify-center p-3 pt-[8vh] sm:p-6 sm:pt-[10vh]"
         role="dialog" aria-modal="true" aria-labelledby="cari-judul" x-trap.noscroll.inert="open">
        <div class="absolute inset-0 bg-forest-950/60 backdrop-blur-sm" @click="hide()" x-show="open" x-transition.opacity></div>

        <div class="relative flex max-h-[84vh] w-full max-w-2xl flex-col overflow-hidden rounded-3xl border border-line bg-paper shadow-2xl"
             x-show="open" x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="opacity-0 translate-y-3 scale-[.98]" x-transition:enter-end="opacity-100 translate-y-0 scale-100">
            <h2 id="cari-judul" class="sr-only">Pencarian pintar desa</h2>
            <div class="flex items-center gap-2 border-b border-line px-4 py-3">
                <x-lucide-search class="size-5 shrink-0 text-forest-700" aria-hidden="true" />
                <label for="cari-input" class="sr-only">Kata kunci</label>
                <input id="cari-input" x-ref="input" x-model="q" @input="debounced()" type="search" autocomplete="off"
                       @keydown.arrow-down.prevent="move(1)" @keydown.arrow-up.prevent="move(-1)" @keydown.enter.prevent="go()"
                       role="combobox" aria-autocomplete="list" aria-controls="hasil-cari" :aria-expanded="(!!results).toString()"
                       :aria-activedescendant="active >= 0 ? 'hasil-' + active : null"
                       placeholder="Cari pengumuman, agenda, potensi, layanan…" class="min-h-11 w-full bg-transparent text-lg outline-none placeholder:text-stone-500">
                <button type="button" @click="showFilters = !showFilters" :aria-expanded="showFilters.toString()" aria-controls="cari-filter"
                        class="relative inline-flex min-h-11 shrink-0 items-center gap-1.5 rounded-full px-3 text-sm font-semibold text-forest-800 hover:bg-forest-50">
                    <x-lucide-sliders-horizontal class="size-4" aria-hidden="true" /> <span class="hidden sm:inline">Filter</span>
                    <span x-show="filterCount" x-text="filterCount" class="badge bg-sun-400 text-ink"></span>
                </button>
                <button type="button" @click="hide()" class="inline-flex size-11 shrink-0 items-center justify-center rounded-full hover:bg-forest-50" aria-label="Tutup pencarian">
                    <x-lucide-x class="size-5" aria-hidden="true" />
                </button>
            </div>

            <div id="cari-filter" x-show="showFilters" x-collapse class="border-b border-line bg-cream/70 px-4 py-4">
                <fieldset>
                    <legend class="text-sm font-bold text-ink">Jenis konten</legend>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <template x-for="(label, key) in typeOptions" :key="key">
                            <button type="button" class="chip !min-h-9" :aria-pressed="types.includes(key).toString()" @click="toggleType(key)" x-text="label"></button>
                        </template>
                    </div>
                </fieldset>
                <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div><label for="cari-dari" class="label">Dari tanggal</label><input id="cari-dari" type="date" x-model="from" @change="search()" class="input"></div>
                    <div><label for="cari-sampai" class="label">Sampai tanggal</label><input id="cari-sampai" type="date" x-model="to" @change="search()" class="input"></div>
                    <div><label for="cari-urut" class="label">Urutkan</label>
                        <select id="cari-urut" x-model="sort" @change="search()" class="input"><option value="relevance">Paling relevan</option><option value="newest">Terbaru</option></select>
                    </div>
                </div>
                <button type="button" @click="resetFilters()" class="btn-ghost btn btn-sm mt-3">Atur ulang filter</button>
            </div>

            <div class="overflow-y-auto px-2 py-3" id="hasil-cari" role="listbox" aria-label="Hasil pencarian">
                <p class="sr-only" aria-live="polite" x-text="results ? results.total + ' hasil ditemukan' : ''"></p>

                <template x-if="!results && !loading">
                    <div class="px-3 py-4">
                        <p class="text-sm font-semibold text-ink-soft">Coba cari:</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach (['posyandu', 'pengumuman', 'UMKM', 'surat domisili', 'sawah', 'pelatihan'] as $s)
                                <button type="button" class="chip !min-h-9" @click="quick('{{ $s }}')">{{ $s }}</button>
                            @endforeach
                        </div>
                        <p class="mt-4 text-sm text-ink-soft">Tip: tekan <kbd class="rounded border border-line px-1">Ctrl</kbd> + <kbd class="rounded border border-line px-1">K</kbd> atau <kbd class="rounded border border-line px-1">/</kbd> dari halaman mana pun.</p>
                    </div>
                </template>

                <div x-show="loading" class="px-4 py-6 text-center text-ink-soft" role="status">Mencari…</div>
                <div x-show="error" class="px-4 py-6 text-center text-red-700" role="alert">Pencarian gagal dimuat. Periksa koneksi lalu coba lagi.</div>

                <template x-if="results && !loading && results.total === 0">
                    <div class="px-4 py-8 text-center">
                        <p class="font-semibold">Tidak ada hasil untuk "<span x-text="results.query"></span>".</p>
                        <p class="mt-1 text-sm text-ink-soft">Coba kata kunci lain atau hapus sebagian filter.</p>
                    </div>
                </template>

                <template x-for="group in (results && !loading ? results.groups : [])" :key="group.key">
                    <section class="mb-2" :aria-label="group.label">
                        <h3 class="flex items-center justify-between px-3 pt-2 pb-1 text-xs font-bold tracking-wider text-forest-700 uppercase">
                            <span x-text="group.label"></span><span class="text-ink-soft" x-text="group.count"></span>
                        </h3>
                        <template x-for="item in group.items" :key="item.url + item.title">
                            <a :href="item.url" :id="'hasil-' + indexOf(item)" role="option" :aria-selected="(active === indexOf(item)).toString()"
                               @mouseenter="active = indexOf(item)"
                               class="flex gap-3 rounded-2xl px-3 py-2.5" :class="active === indexOf(item) ? 'bg-forest-50 ring-1 ring-forest-200' : ''">
                                <template x-if="item.image"><img :src="item.image" alt="" class="size-12 shrink-0 rounded-xl object-cover"></template>
                                <span class="min-w-0">
                                    <span class="block font-semibold text-ink" x-text="item.title"></span>
                                    <span class="block truncate text-sm text-ink-soft" x-text="item.snippet"></span>
                                    <span class="mt-0.5 flex flex-wrap items-center gap-2 text-xs font-semibold text-forest-700">
                                        <span x-text="item.meta"></span>
                                        <span x-show="item.expired" class="badge bg-stone-200 text-stone-700">Arsip</span>
                                    </span>
                                </span>
                            </a>
                        </template>
                    </section>
                </template>
            </div>
            <div class="hidden items-center gap-4 border-t border-line px-4 py-2.5 text-xs text-ink-soft sm:flex">
                <span><kbd class="rounded border border-line px-1">↑</kbd> <kbd class="rounded border border-line px-1">↓</kbd> pilih</span>
                <span><kbd class="rounded border border-line px-1">Enter</kbd> buka</span>
                <span><kbd class="rounded border border-line px-1">Esc</kbd> tutup</span>
            </div>
        </div>
    </div>
</div>
