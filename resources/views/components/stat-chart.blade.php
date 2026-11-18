@props(['group'])
@use('App\Models\VillageStatistic')
@php
    $rows = $group['rows'];
    $isLine = $group['type'] === 'line';
    $labels = $isLine ? $rows->pluck('year')->map(fn ($y) => (string) $y)->all() : $rows->pluck('indicator')->all();
    $config = [
        'type' => $group['type'],
        'labels' => $labels,
        'unit' => $group['unit'],
        'datasets' => [['label' => $group['category'], 'data' => $rows->pluck('value')->all()]],
    ];
    $max = $rows->sortByDesc('value')->first();
    $years = $group['years'];
    $summary = $isLine
        ? sprintf('%s naik dari %s %s pada %d menjadi %s %s pada %d.', $group['category'], VillageStatistic::format($rows->first()->value), $group['unit'], $rows->first()->year, VillageStatistic::format($rows->last()->value), $group['unit'], $rows->last()->year)
        : sprintf('Nilai tertinggi: %s (%s %s%s).', $max->indicator, $max->formattedValue(), $max->unit, $group['total'] ? ', '.round($max->value / max(1, $group['total']) * 100, 1).'% dari total' : '');
    if ($isLine && $rows->last()->value < $rows->first()->value) {
        $summary = str_replace(' naik ', ' turun ', $summary);
    }
    $id = 'grafik-'.\Illuminate\Support\Str::slug($group['category']);
@endphp
<figure {{ $attributes->merge(['class' => 'card flex flex-col p-5 sm:p-6']) }} aria-labelledby="{{ $id }}-judul">
    <div class="flex flex-wrap items-start justify-between gap-2">
        <div>
            <h3 id="{{ $id }}-judul" class="text-lg font-bold">{{ $group['category'] }}</h3>
            <p class="text-sm text-ink-soft">{{ $isLine ? $years->first().'–'.$years->last() : 'Tahun '.$years->implode(', ') }}@if ($group['total']) · Total {{ VillageStatistic::format($group['total']) }} {{ $group['unit'] }}@endif</p>
        </div>
        <span class="badge bg-forest-50 text-forest-800">{{ VillageStatistic::CHART_TYPES[$group['type']] ?? 'Grafik' }}</span>
    </div>

    <div x-data="villageChart(@js($config))" class="relative mt-4 w-full overflow-hidden {{ $group['type'] === 'horizontal' ? 'h-[22rem]' : 'h-72' }}">
        <canvas x-ref="canvas" role="img" aria-label="Grafik {{ $group['category'] }}. {{ $summary }}"></canvas>
        <p x-show="failed" x-cloak class="absolute inset-0 flex items-center justify-center text-center text-sm text-ink-soft">Grafik tidak dapat dimuat — lihat tabel data di bawah.</p>
    </div>

    <figcaption class="mt-4 text-[0.95rem] text-ink-soft">
        <p><span class="font-semibold text-ink">Ringkasan:</span> {{ $summary }}</p>
        @if ($group['source'])<p class="mt-1 text-sm">Sumber: {{ $group['source'] }}</p>@endif
    </figcaption>

    <details class="group mt-3 rounded-xl border border-line">
        <summary class="flex min-h-11 cursor-pointer list-none items-center justify-between px-3 text-sm font-semibold">
            Lihat tabel data <x-lucide-chevron-down class="size-4 transition group-open:rotate-180" aria-hidden="true" />
        </summary>
        <div class="overflow-x-auto px-1 pb-2">
            <table class="table-clean">
                <caption class="sr-only">Tabel data {{ $group['category'] }}</caption>
                <thead><tr><th scope="col">{{ $isLine ? 'Tahun' : 'Indikator' }}</th><th scope="col" class="text-right">Nilai</th>@if ($group['total'])<th scope="col" class="text-right">Persentase</th>@endif</tr></thead>
                <tbody>
                    @foreach ($rows as $r)
                        <tr>
                            <th scope="row" class="font-medium text-ink">{{ $isLine ? $r->year : $r->indicator }}</th>
                            <td class="text-right tabular-nums">{{ $r->formattedValue() }} {{ $r->unit }}</td>
                            @if ($group['total'])<td class="text-right tabular-nums">{{ number_format($r->value / $group['total'] * 100, 1, ',', '.') }}%</td>@endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </details>
</figure>
