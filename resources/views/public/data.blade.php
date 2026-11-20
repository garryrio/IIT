<x-layouts.public title="Data Desa" description="Village Data Hub Desa Marga Mulya: kependudukan, pekerjaan, pendidikan, penggunaan lahan, dan pemerintahan dalam visualisasi yang mudah dipahami.">
    <x-page-hero title="Village Data Hub" eyebrow="Data desa" lead="Data desa disajikan dalam grafik yang mudah dipahami — lengkap dengan ringkasan teks dan tabel untuk setiap visualisasi." :crumbs="['Data Desa' => null]" />

    <div class="container-page section pt-10">
        <form method="GET" action="{{ route('data') }}" class="card flex flex-col gap-4 p-4 sm:p-5 lg:flex-row lg:items-end" aria-label="Filter data">
            <div class="lg:w-48">
                <label for="tahun" class="label">Tahun data</label>
                <select id="tahun" name="tahun" class="input" onchange="this.form.submit()">
                    <option value="" @selected(! $year)>Data terbaru</option>
                    @foreach ($years as $y)<option value="{{ $y }}" @selected($y === $year)>{{ $y }}</option>@endforeach
                </select>
            </div>
            <div class="flex-1">
                <span class="label" id="kategori-label">Kategori</span>
                <div class="flex flex-wrap gap-2" role="group" aria-labelledby="kategori-label">
                    <a href="{{ route('data', array_filter(['tahun' => $year])) }}" class="chip" @if (! $category) aria-current="page" @endif>Semua</a>
                    @foreach ($categories as $c)
                        <a href="{{ route('data', array_filter(['tahun' => $year, 'kategori' => $c])) }}" class="chip" @if ($category === $c) aria-current="page" @endif>{{ $c }}</a>
                    @endforeach
                </div>
            </div>
            <noscript><button class="btn btn-primary">Terapkan</button></noscript>
        </form>

        @if ($highlights->isNotEmpty() && ! $category)
            <section aria-labelledby="ringkasan-judul" class="mt-10">
                <h2 id="ringkasan-judul" class="text-xl font-bold">{{ $year ? 'Ringkasan '.$year : 'Ringkasan data terbaru' }}</h2>
                <dl class="mt-4 grid grid-cols-2 gap-4 lg:grid-cols-4" data-stagger>
                    @foreach ($highlights as $h)
                        <div class="card p-5">
                            <dt class="text-sm font-semibold text-ink-soft">{{ $h->indicator }}@unless ($year) ({{ $h->year }})@endunless</dt>
                            <dd class="mt-1 font-display text-3xl font-semibold text-forest-900 sm:text-4xl"><span data-count="{{ $h->value }}" data-decimals="{{ floor($h->value) == $h->value ? 0 : 1 }}">{{ $h->formattedValue() }}</span> <span class="font-sans text-base text-ink-soft">{{ $h->unit }}</span></dd>
                        </div>
                    @endforeach
                </dl>
            </section>
        @endif

        <section aria-label="Grafik data desa" class="mt-10">
            @php $charts = $groups->where('type', '!=', 'cards'); $cards = $groups->where('type', 'cards')->filter(fn ($g) => $category || $g['category'] !== 'Ringkasan Desa'); @endphp
            @if ($charts->isEmpty() && $cards->isEmpty())
                <x-empty-state icon="bar-chart-3" title="Belum ada data untuk filter ini" text="Coba pilih tahun atau kategori lain." />
            @endif
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                @foreach ($charts as $g)
                    <x-stat-chart :group="$g" data-reveal />
                @endforeach
            </div>
            @foreach ($cards as $g)
                <div class="card mt-6 p-5 sm:p-6">
                    <h3 class="text-lg font-bold">{{ $g['category'] }}</h3>
                    <p class="text-sm text-ink-soft">Tahun {{ $g['years']->implode(', ') }}@if ($g['source']) · Sumber: {{ $g['source'] }}@endif</p>
                    <dl class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-5">
                        @foreach ($g['rows'] as $r)
                            <div class="rounded-2xl bg-cream p-4"><dt class="text-sm text-ink-soft">{{ $r->indicator }}</dt><dd class="font-display text-2xl font-semibold text-forest-900">{{ $r->formattedValue() }} <span class="font-sans text-sm text-ink-soft">{{ $r->unit }}</span></dd></div>
                        @endforeach
                    </dl>
                </div>
            @endforeach
        </section>

        @if ($income && ! $category && $income['total'] > 0)
            <section class="mt-14" aria-labelledby="kesejahteraan-judul">
                <div class="card overflow-hidden">
                    <div class="grid grid-cols-1 gap-8 p-6 sm:p-8 lg:grid-cols-5">
                        <div class="lg:col-span-2">
                            <p class="eyebrow"><x-lucide-wallet class="size-4" aria-hidden="true" /> Kesejahteraan keluarga</p>
                            <h2 id="kesejahteraan-judul" class="mt-2 font-display text-2xl font-semibold">Sebaran kelas penghasilan KK terdata</h2>
                            <p class="mt-3 text-ink-soft">Data agregat dari {{ number_format($income['total'], 0, ',', '.') }} KK hasil pendataan ekonomi. Data per keluarga bersifat rahasia dan hanya digunakan petugas desa untuk perencanaan bantuan yang tepat sasaran.</p>
                        </div>
                        <div class="lg:col-span-3">
                            <div class="flex h-5 w-full overflow-hidden rounded-full" role="img" aria-label="Proporsi KK per kelas penghasilan: {{ $income['distribution']->map(fn ($d) => $d['class']->name.' '.$d['percent'].' persen')->implode(', ') }}">
                                @foreach ($income['distribution'] as $d)
                                    @if ($d['count'])<span style="width: {{ $d['percent'] }}%; background: {{ $d['class']->color }}"></span>@endif
                                @endforeach
                            </div>
                            <table class="table-clean mt-5">
                                <caption class="sr-only">Jumlah KK per kelas penghasilan</caption>
                                <thead><tr><th scope="col">Kelas</th><th scope="col">Penghasilan per kapita/bulan</th><th scope="col" class="text-right">KK</th><th scope="col" class="text-right">%</th></tr></thead>
                                <tbody>
                                    @foreach ($income['distribution'] as $d)
                                        <tr>
                                            <th scope="row" class="font-semibold text-ink"><span class="mr-2 inline-block size-3 rounded-full align-middle" style="background: {{ $d['class']->color }}"></span>{{ $d['class']->name }}</th>
                                            <td>{{ $d['class']->rangeLabel() }}</td>
                                            <td class="text-right tabular-nums">{{ $d['count'] }}</td>
                                            <td class="text-right tabular-nums">{{ number_format($d['percent'], 1, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <p class="mt-10 flex gap-2 text-sm text-ink-soft"><x-lucide-info class="mt-0.5 size-4 shrink-0" aria-hidden="true" />Seluruh angka dikelola perangkat desa melalui CMS. Klik "Lihat tabel data" pada setiap grafik untuk versi teks yang ramah pembaca layar.</p>
    </div>
</x-layouts.public>
