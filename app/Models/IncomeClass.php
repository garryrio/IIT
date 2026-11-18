<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class IncomeClass extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['min_per_capita' => 'integer', 'max_per_capita' => 'integer', 'aid_priority' => 'integer'];
    }

    /** @return Collection<int, IncomeClass> */
    public static function ordered(): Collection
    {
        return once(fn () => static::query()->orderBy('min_per_capita')->get());
    }

    public static function classify(float $perCapita): ?self
    {
        $perCapita = floor($perCapita); // batas kelas berupa rupiah bulat

        return static::ordered()->first(fn (self $c) => $perCapita >= $c->min_per_capita
            && ($c->max_per_capita === null || $perCapita <= $c->max_per_capita));
    }

    public function rangeLabel(): string
    {
        $min = 'Rp'.number_format($this->min_per_capita, 0, ',', '.');
        if ($this->max_per_capita === null) {
            return '≥ '.$min;
        }

        return $min.' – Rp'.number_format($this->max_per_capita, 0, ',', '.');
    }
}
