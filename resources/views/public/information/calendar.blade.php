<section x-data="calendar(@js($calendarEvents))" class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-12" aria-labelledby="kalender-judul">
    <div class="card p-4 sm:p-6 lg:col-span-8">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 id="kalender-judul" class="font-display text-2xl font-semibold" x-text="title" aria-live="polite"></h2>
            <div class="flex items-center gap-1">
                <button type="button" class="btn btn-ghost btn-sm" @click="goToday()">Hari ini</button>
                <button type="button" class="inline-flex size-11 items-center justify-center rounded-full hover:bg-forest-50" @click="move(-1)" aria-label="Bulan sebelumnya"><x-lucide-chevron-left class="size-5" aria-hidden="true" /></button>
                <button type="button" class="inline-flex size-11 items-center justify-center rounded-full hover:bg-forest-50" @click="move(1)" aria-label="Bulan berikutnya"><x-lucide-chevron-right class="size-5" aria-hidden="true" /></button>
            </div>
        </div>
        <div class="mt-4 grid grid-cols-7 gap-1 text-center text-xs font-bold tracking-wider text-ink-soft uppercase" aria-hidden="true">
            <template x-for="d in days" :key="d"><div class="py-2" x-text="d"></div></template>
        </div>
        <div class="grid grid-cols-7 gap-1" role="grid" aria-labelledby="kalender-judul">
            <template x-for="(c, i) in cells" :key="i">
                <div role="gridcell">
                    <template x-if="c">
                        <button type="button" @click="selected = c.date" :aria-label="cellLabel(c)" :aria-pressed="(selected === c.date).toString()"
                                class="flex aspect-square w-full flex-col items-center justify-start gap-1 rounded-xl border p-1 text-sm transition sm:aspect-[1.15] sm:items-start sm:p-2"
                                :class="{
                                    'border-forest-800 bg-forest-800 text-white': selected === c.date,
                                    'border-sun-400 bg-sun-100': selected !== c.date && c.date === today,
                                    'border-line bg-paper hover:border-forest-400': selected !== c.date && c.date !== today,
                                }">
                            <span class="font-semibold" x-text="c.day"></span>
                            <template x-if="c.events.length">
                                <span class="flex w-full flex-col gap-0.5">
                                    <span class="mx-auto size-2 rounded-full sm:hidden" :class="selected === c.date ? 'bg-sun-300' : 'bg-clay-500'"></span>
                                    <template x-for="ev in c.events.slice(0, 2)" :key="ev.url">
                                        <span class="hidden truncate rounded-md px-1.5 py-0.5 text-left text-[0.7rem] leading-tight font-semibold sm:block"
                                              :class="selected === c.date ? 'bg-white/20' : 'bg-sun-200 text-ink'" x-text="ev.title"></span>
                                    </template>
                                </span>
                            </template>
                        </button>
                    </template>
                </div>
            </template>
        </div>
        <p class="mt-4 flex flex-wrap gap-4 text-sm text-ink-soft"><span class="inline-flex items-center gap-1.5"><span class="size-3 rounded border border-sun-400 bg-sun-100"></span> Hari ini</span><span class="inline-flex items-center gap-1.5"><span class="size-3 rounded bg-sun-200"></span> Ada agenda</span></p>
    </div>

    <div class="lg:col-span-4">
        <div class="card p-5 lg:sticky lg:top-24" aria-live="polite">
            <h3 class="font-bold" x-text="longDate(selected)"></h3>
            <template x-if="!eventsOn(selected).length"><p class="mt-3 text-ink-soft">Tidak ada agenda pada tanggal ini.</p></template>
            <ul class="mt-3 space-y-3">
                <template x-for="ev in eventsOn(selected)" :key="ev.url">
                    <li><a :href="ev.url" class="block rounded-2xl border border-line p-4 hover:border-forest-400 hover:bg-cream">
                        <span class="block font-bold" x-text="ev.title"></span>
                        <span class="mt-1 block text-sm text-ink-soft" x-text="[ev.time, ev.location].filter(Boolean).join(' · ')"></span>
                    </a></li>
                </template>
            </ul>
            <h3 class="mt-6 text-sm font-bold tracking-wider text-forest-700 uppercase">Semua agenda bulan ini</h3>
            <template x-if="!monthEvents.length"><p class="mt-2 text-sm text-ink-soft">Belum ada agenda bulan ini.</p></template>
            <ul class="mt-2 space-y-1">
                <template x-for="ev in monthEvents" :key="'m' + ev.url">
                    <li><button type="button" class="flex min-h-10 w-full items-center gap-3 rounded-xl px-2 text-left hover:bg-cream" @click="selected = ev.date">
                        <span class="w-8 text-center font-bold text-forest-800" x-text="Number(ev.date.slice(8))"></span><span class="text-sm font-semibold" x-text="ev.title"></span>
                    </button></li>
                </template>
            </ul>
        </div>
    </div>
</section>
