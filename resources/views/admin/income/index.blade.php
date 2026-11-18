@php
    $dist = $a->distribution();
    $byRw = $a->byRw();
    $cov = $a->coverage();
    $rp = fn ($v) => 'Rp'.number_format($v, 0, ',', '.');
    $donut = ['type' => 'donut', 'labels' => $dist->map(fn ($d) => $d['class']->name)->all(), 'unit' => 'KK',
        'datasets' => [['label' => 'KK', 'data' => $dist->pluck('count')->all(), 'colors' => $dist->map(fn ($d) => $d['class']->color)->all()]]];
    $stacked = ['type' => 'stacked', 'labels' => $byRw['labels'], 'unit' => 'KK',
        'datasets' => collect($byRw['datasets'])->map(fn ($d) => ['label' => $d['label'], 'data' => $d['data'], 'color' => $d['color']])->all()];
    $rwOptions = collect($byRw['labels'])->mapWithKeys(fn ($l) => [(int) substr($l, 3) => $l]);
@endphp
<x-layouts.admin title="Analisis Kelas Penghasilan">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <p class="max-w-3xl text-stone-600">Klasifikasi otomatis berdasarkan <strong>penghasilan per kapita</strong> (penghasilan keluarga ÷ jumlah anggota) dan ambang batas pada menu <a href="{{ route('admin.resource.index', 'kelas-penghasilan') }}" class="font-semibold text-forest-700 underline">Kelas Penghasilan</a>. Hasil ini adalah alat bantu — keputusan akhir penyaluran tetap melalui verifikasi lapangan & musyawarah desa.</p>
        <a href="{{ route('admin.resource.create', 'data-kk') }}" class="btn btn-primary btn-sm"><x-lucide-plus class="size-4" aria-hidden="true" /> Tambah data KK</a>
    </div>

    <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach ([['KK terdata', $a->total(), 'house'], ['Rata-rata per kapita', $rp($a->averagePerCapita()), 'wallet'], ['Median per kapita', $rp($a->medianPerCapita()), 'chart-column'], ['Cakupan bantuan sasaran', $cov['percent'].'%', 'hand-heart']] as [$l, $v, $i])
            <div class="rounded-2xl border border-stone-200 bg-white p-5">
                <span class="flex size-10 items-center justify-center rounded-xl bg-forest-50 text-forest-800"><x-dynamic-component :component="'lucide-'.$i" class="size-5" aria-hidden="true" /></span>
                <p class="mt-3 text-2xl font-bold">{{ $v }}</p><p class="text-sm text-stone-600">{{ $l }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-5">
        <section class="rounded-2xl border border-stone-200 bg-white p-5 xl:col-span-2" aria-labelledby="dist-judul">
            <h2 id="dist-judul" class="font-bold">Sebaran kelas penghasilan</h2>
            <div x-data="villageChart(@js($donut))" class="relative mt-3 h-64 w-full overflow-hidden"><canvas x-ref="canvas" role="img" aria-label="Diagram donat jumlah KK per kelas penghasilan"></canvas></div>
            <div class="mt-4 overflow-x-auto"><table class="table-clean">
                <thead><tr><th scope="col">Kelas</th><th scope="col">Rentang/kapita</th><th scope="col" class="text-right">KK</th><th scope="col" class="text-right">Menerima</th></tr></thead>
                <tbody>
                    @foreach ($dist as $d)
                        <tr><th scope="row" class="font-semibold"><span class="mr-1.5 inline-block size-2.5 rounded-full" style="background: {{ $d['class']->color }}"></span>{{ $d['class']->name }}@if ($d['class']->aid_priority)<span class="badge ml-1 bg-sun-200 text-ink">P{{ $d['class']->aid_priority }}</span>@endif</th>
                            <td class="text-xs">{{ $d['class']->rangeLabel() }}</td><td class="text-right tabular-nums">{{ $d['count'] }} ({{ $d['percent'] }}%)</td><td class="text-right tabular-nums">{{ $d['receiving'] }}</td></tr>
                    @endforeach
                </tbody>
            </table></div>
        </section>
        <section class="min-w-0 rounded-2xl border border-stone-200 bg-white p-5 xl:col-span-3" aria-labelledby="rw-judul">
            <h2 id="rw-judul" class="font-bold">Komposisi kelas per RW</h2>
            <p class="text-sm text-stone-600">Membantu menentukan wilayah prioritas program.</p>
            <div x-data="villageChart(@js($stacked))" class="relative mt-3 h-72 w-full overflow-hidden"><canvas x-ref="canvas" role="img" aria-label="Grafik batang bertumpuk jumlah KK per kelas penghasilan untuk setiap RW"></canvas></div>
            <div class="mt-4 grid grid-cols-3 gap-2 text-sm sm:grid-cols-6">
                @foreach ($byRw['labels'] as $i => $l)
                    <div class="rounded-xl bg-stone-50 p-2 text-center"><p class="font-semibold">{{ $l }}</p><p class="text-xs text-stone-600">{{ $rp($byRw['avg'][$i]) }}/kapita</p></div>
                @endforeach
            </div>
        </section>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-stone-200 bg-white p-5"><p class="text-sm text-stone-600">KK sasaran (kelas prioritas)</p><p class="text-2xl font-bold">{{ $cov['targets'] }}</p></div>
        <div class="rounded-2xl border border-sun-400 bg-sun-100 p-5"><p class="text-sm">KK sasaran <strong>belum</strong> menerima bantuan</p><p class="text-2xl font-bold">{{ $cov['uncovered'] }}</p></div>
        <div class="rounded-2xl border border-red-200 bg-red-50 p-5"><p class="text-sm">Penerima di luar kelas sasaran (perlu verifikasi)</p><p class="text-2xl font-bold">{{ $cov['outside'] }}</p></div>
    </div>

    <section class="mt-6 rounded-2xl border border-stone-200 bg-white p-5" aria-labelledby="prioritas-judul">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div><h2 id="prioritas-judul" class="font-bold">Daftar prioritas penyaluran bantuan</h2><p class="text-sm text-stone-600">Diurutkan berdasarkan skor prioritas (kelas penghasilan, tanggungan, kondisi rumah, status bantuan).</p></div>
            <a href="{{ route('admin.income.export', request()->only('rw', 'class', 'aid')) }}" class="btn btn-outline btn-sm"><x-lucide-download class="size-4" aria-hidden="true" /> Ekspor CSV</a>
        </div>
        <form method="GET" class="mt-4 flex flex-wrap items-end gap-3">
            <div><label for="rw" class="label">RW</label><select id="rw" name="rw" class="input"><option value="">Semua</option>@foreach ($rwOptions as $k => $l)<option value="{{ $k }}" @selected(($filters['rw'] ?? null) == $k)>{{ $l }}</option>@endforeach</select></div>
            <div><label for="class" class="label">Kelas</label><select id="class" name="class" class="input"><option value="">Semua sasaran</option>@foreach ($classes->where('aid_priority', '>', 0) as $c)<option value="{{ $c->id }}" @selected(($filters['class'] ?? null) == $c->id)>{{ $c->name }}</option>@endforeach</select></div>
            <div><label for="aid" class="label">Status bantuan</label><select id="aid" name="aid" class="input"><option value="">Semua</option><option value="0" @selected(($filters['aid'] ?? null) === '0')>Belum menerima</option><option value="1" @selected(($filters['aid'] ?? null) === '1')>Sudah menerima</option></select></div>
            <button class="btn btn-outline">Terapkan</button>
        </form>
        <div class="mt-4 overflow-x-auto">
            <table class="table-clean">
                <thead class="bg-stone-50"><tr><th scope="col">#</th><th scope="col">Kode KK</th><th scope="col">Kepala keluarga</th><th scope="col">RT/RW</th><th scope="col" class="text-right">Anggota</th><th scope="col" class="text-right">Per kapita</th><th scope="col">Kelas</th><th scope="col">Rumah</th><th scope="col">Bantuan</th><th scope="col" class="text-right">Skor</th></tr></thead>
                <tbody>
                    @forelse ($priority as $i => $h)
                        @php $c = $h->incomeClass(); $score = $h->priorityScore(); @endphp
                        <tr>
                            <td class="tabular-nums">{{ $i + 1 }}</td>
                            <td><a href="{{ route('admin.resource.edit', ['data-kk', $h->id]) }}" class="font-semibold hover:underline">{{ $h->household_code }}</a></td>
                            <td>{{ $h->head_name }}</td>
                            <td class="tabular-nums">{{ sprintf('%03d/%03d', $h->rt, $h->rw) }}</td>
                            <td class="text-right tabular-nums">{{ $h->members_count }}</td>
                            <td class="text-right tabular-nums">{{ $rp($h->perCapita()) }}</td>
                            <td><span class="inline-flex items-center gap-1.5"><span class="size-2.5 rounded-full" style="background: {{ $c?->color }}"></span>{{ $c?->name }}</span></td>
                            <td>{{ \App\Models\Household::HOUSE_CONDITIONS[$h->house_condition] ?? '—' }}</td>
                            <td>{!! $h->receives_aid ? '<span class="badge bg-forest-100 text-forest-900">'.e($h->aid_program ?: 'Ya').'</span>' : '<span class="badge bg-sun-200 text-ink">Belum</span>' !!}</td>
                            <td class="text-right"><span class="inline-flex min-w-10 justify-center rounded-full px-2 py-0.5 font-bold tabular-nums {{ $score >= 70 ? 'bg-red-100 text-red-800' : ($score >= 50 ? 'bg-sun-200 text-ink' : 'bg-stone-100') }}">{{ $score }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="p-6 text-center text-stone-500">Tidak ada KK sasaran untuk filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if ($review->isNotEmpty())
        <section class="mt-6 rounded-2xl border border-red-200 bg-white p-5" aria-labelledby="review-judul">
            <h2 id="review-judul" class="font-bold">Perlu verifikasi ulang ({{ $review->count() }})</h2>
            <p class="text-sm text-stone-600">KK yang tercatat menerima bantuan, tetapi penghasilan per kapitanya berada di kelas bukan sasaran.</p>
            <ul class="mt-3 divide-y divide-stone-100">
                @foreach ($review as $h)
                    <li class="flex flex-wrap items-center justify-between gap-2 py-2.5"><a href="{{ route('admin.resource.edit', ['data-kk', $h->id]) }}" class="font-semibold hover:underline">{{ $h->household_code }} · {{ $h->head_name }}</a><span class="text-sm text-stone-600">{{ $rp($h->perCapita()) }}/kapita · {{ $h->incomeClass()?->name }} · {{ $h->aid_program }}</span></li>
                @endforeach
            </ul>
        </section>
    @endif
</x-layouts.admin>
