<x-layouts.admin title="Dashboard">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-stone-600">Selamat datang, {{ auth()->user()->name }} 👋</p>
            <h2 class="font-display text-2xl font-semibold">Ringkasan Marga Mulya Hub</h2>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.resource.create', ['informasi', 'type' => 'news']) }}" class="btn btn-primary btn-sm"><x-lucide-plus class="size-4" aria-hidden="true" /> Berita</a>
            <a href="{{ route('admin.resource.create', ['informasi', 'type' => 'announcement']) }}" class="btn btn-outline btn-sm"><x-lucide-plus class="size-4" aria-hidden="true" /> Pengumuman</a>
            <a href="{{ route('admin.resource.create', ['informasi', 'type' => 'event']) }}" class="btn btn-outline btn-sm"><x-lucide-plus class="size-4" aria-hidden="true" /> Agenda</a>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach ($counts as $c)
            <a href="{{ $c['url'] }}" class="group rounded-2xl border bg-white p-5 transition hover:shadow-md {{ ! empty($c['alert']) && $c['value'] ? 'border-sun-400' : 'border-stone-200' }}">
                <div class="flex items-center justify-between">
                    <span class="flex size-10 items-center justify-center rounded-xl {{ ! empty($c['alert']) && $c['value'] ? 'bg-sun-200 text-ink' : 'bg-forest-50 text-forest-800' }}"><x-dynamic-component :component="'lucide-'.$c['icon']" class="size-5" aria-hidden="true" /></span>
                    <x-lucide-arrow-up-right class="size-4 text-stone-400 group-hover:text-forest-700" aria-hidden="true" />
                </div>
                <p class="mt-4 text-3xl font-bold">{{ $c['value'] }}</p>
                <p class="text-sm text-stone-600">{{ $c['label'] }}</p>
            </a>
        @endforeach
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
        <section class="rounded-2xl border border-stone-200 bg-white p-5" aria-labelledby="d-kontribusi">
            <div class="flex items-center justify-between"><h3 id="d-kontribusi" class="font-bold">Kontribusi menunggu verifikasi</h3><a href="{{ route('admin.submissions.index', ['status' => 'pending']) }}" class="text-sm font-semibold text-forest-700 hover:underline">Semua</a></div>
            <ul class="mt-3 divide-y divide-stone-100">
                @forelse ($pending as $s)
                    <li><a href="{{ route('admin.submissions.show', $s) }}" class="flex flex-col py-2.5 hover:text-forest-800"><span class="font-semibold">{{ $s->title }}</span><span class="text-sm text-stone-500">{{ $s->categoryLabel() }} · {{ $s->created_at->diffForHumans() }}</span></a></li>
                @empty
                    <li class="py-3 text-stone-500">Tidak ada kontribusi yang menunggu. 🎉</li>
                @endforelse
            </ul>
        </section>
        <section class="rounded-2xl border border-stone-200 bg-white p-5" aria-labelledby="d-agenda">
            <div class="flex items-center justify-between"><h3 id="d-agenda" class="font-bold">Agenda mendatang</h3><a href="{{ route('admin.resource.index', ['informasi', 'type' => 'event']) }}" class="text-sm font-semibold text-forest-700 hover:underline">Kelola</a></div>
            <ul class="mt-3 divide-y divide-stone-100">
                @forelse ($events as $e)
                    <li class="flex gap-3 py-2.5"><span class="w-14 shrink-0 rounded-lg bg-forest-50 py-1 text-center text-sm font-bold text-forest-900">{{ $e->event_date->translatedFormat('d M') }}</span><a href="{{ route('admin.resource.edit', ['informasi', $e->id]) }}" class="font-semibold hover:underline">{{ $e->title }}</a></li>
                @empty
                    <li class="py-3 text-stone-500">Belum ada agenda mendatang.</li>
                @endforelse
            </ul>
        </section>
        <section class="rounded-2xl border border-stone-200 bg-white p-5" aria-labelledby="d-pengumuman">
            <div class="flex items-center justify-between"><h3 id="d-pengumuman" class="font-bold">Pengumuman aktif</h3><a href="{{ route('admin.resource.index', ['informasi', 'type' => 'announcement']) }}" class="text-sm font-semibold text-forest-700 hover:underline">Kelola</a></div>
            <ul class="mt-3 divide-y divide-stone-100">
                @forelse ($announcements as $a)
                    <li class="py-2.5"><a href="{{ route('admin.resource.edit', ['informasi', $a->id]) }}" class="font-semibold hover:underline">{{ $a->title }}</a><p class="text-sm text-stone-500">{{ \App\Models\InformationPost::PRIORITIES[$a->priority] }} · s.d. {{ $a->end_date?->translatedFormat('d M Y') ?? '—' }}</p></li>
                @empty
                    <li class="py-3 text-stone-500">Tidak ada pengumuman aktif.</li>
                @endforelse
            </ul>
        </section>
    </div>

    <section class="mt-6 rounded-2xl border border-stone-200 bg-white p-5" aria-labelledby="d-aktivitas">
        <h3 id="d-aktivitas" class="font-bold">Aktivitas & konten terbaru</h3>
        <div class="mt-3 overflow-x-auto">
            <table class="table-clean">
                <thead><tr><th scope="col">Konten</th><th scope="col">Jenis</th><th scope="col">Status</th><th scope="col">Diperbarui</th></tr></thead>
                <tbody>
                    @foreach ($recent as $r)
                        <tr><td><a href="{{ $r['url'] }}" class="font-semibold hover:underline">{{ $r['title'] }}</a></td><td>{{ $r['type'] }}</td><td>@include('admin.partials.status', ['status' => $r['status']])</td><td class="whitespace-nowrap text-stone-500">{{ $r['at']->diffForHumans() }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="mt-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-forest-200 bg-forest-50 p-5">
        <div><h3 class="font-bold">Analisis kesejahteraan</h3><p class="text-sm text-stone-600">{{ $households }} KK terdata. Lihat kelas penghasilan dan daftar prioritas penyaluran bantuan.</p></div>
        <a href="{{ route('admin.income.index') }}" class="btn btn-primary btn-sm">Buka analisis <x-lucide-arrow-right class="size-4" aria-hidden="true" /></a>
    </section>
</x-layouts.admin>
