<?php

namespace App\Support;

use App\Models\Household;
use App\Models\IncomeClass;
use Illuminate\Support\Collection;

/** Analisis kelas penghasilan KK untuk membantu penyaluran bantuan tepat sasaran (khusus admin). */
class IncomeAnalytics
{
    /** @param Collection<int, Household> $households */
    public function __construct(public Collection $households) {}

    public function total(): int
    {
        return $this->households->count();
    }

    public function averagePerCapita(): float
    {
        return $this->total() ? $this->households->avg(fn (Household $h) => $h->perCapita()) : 0;
    }

    public function medianPerCapita(): float
    {
        return $this->total() ? (float) $this->households->map(fn (Household $h) => $h->perCapita())->median() : 0;
    }

    /** @return Collection<int, array{class:IncomeClass,count:int,percent:float,receiving:int}> */
    public function distribution(): Collection
    {
        return IncomeClass::ordered()->map(function (IncomeClass $c) {
            $members = $this->households->filter(fn (Household $h) => $h->incomeClass()?->id === $c->id);

            return [
                'class' => $c,
                'count' => $members->count(),
                'percent' => $this->total() ? round($members->count() / $this->total() * 100, 1) : 0,
                'receiving' => $members->where('receives_aid', true)->count(),
            ];
        });
    }

    /** Jumlah KK per kelas untuk setiap RW (untuk grafik bertumpuk & peta sebaran). */
    public function byRw(): array
    {
        $rws = $this->households->pluck('rw')->unique()->sort()->values();
        $classes = IncomeClass::ordered();

        return [
            'labels' => $rws->map(fn ($rw) => 'RW '.sprintf('%03d', $rw))->all(),
            'datasets' => $classes->map(fn (IncomeClass $c) => [
                'label' => $c->name,
                'color' => $c->color,
                'data' => $rws->map(fn ($rw) => $this->households->filter(fn (Household $h) => $h->rw === $rw && $h->incomeClass()?->id === $c->id)->count())->all(),
            ])->values()->all(),
            'avg' => $rws->map(fn ($rw) => round($this->households->where('rw', $rw)->avg(fn (Household $h) => $h->perCapita())))->all(),
        ];
    }

    public function coverage(): array
    {
        $targets = $this->households->filter(fn (Household $h) => ($h->incomeClass()?->aid_priority ?? 0) > 0);
        $covered = $targets->where('receives_aid', true)->count();

        return [
            'targets' => $targets->count(),
            'covered' => $covered,
            'uncovered' => $targets->count() - $covered,
            'percent' => $targets->count() ? round($covered / $targets->count() * 100) : 0,
            'outside' => $this->households->where('receives_aid', true)->filter(fn (Household $h) => ($h->incomeClass()?->aid_priority ?? 0) === 0)->count(),
        ];
    }

    /**
     * Daftar KK sasaran yang diurutkan berdasarkan skor prioritas.
     *
     * @return Collection<int, Household>
     */
    public function priorityList(array $filters = [], ?int $limit = 25): Collection
    {
        $list = $this->households
            ->filter(fn (Household $h) => ($h->incomeClass()?->aid_priority ?? 0) > 0)
            ->when(isset($filters['rw']), fn ($c) => $c->where('rw', (int) $filters['rw']))
            ->when(isset($filters['class']), fn ($c) => $c->filter(fn (Household $h) => $h->incomeClass()?->id === (int) $filters['class']))
            ->when(isset($filters['aid']), fn ($c) => $c->where('receives_aid', (bool) $filters['aid']))
            ->sortBy([fn (Household $a, Household $b) => $b->priorityScore() <=> $a->priorityScore(), fn ($a, $b) => $a->perCapita() <=> $b->perCapita()])
            ->values();

        return $limit ? $list->take($limit) : $list;
    }

    /** KK yang menerima bantuan padahal berada di kelas bukan sasaran — perlu verifikasi ulang. */
    public function needsReview(): Collection
    {
        return $this->households->where('receives_aid', true)
            ->filter(fn (Household $h) => ($h->incomeClass()?->aid_priority ?? 0) === 0)
            ->sortByDesc(fn (Household $h) => $h->perCapita())->values();
    }
}
