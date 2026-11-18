<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class VillageStatistic extends Model
{
    public const CHART_TYPES = [
        'bar' => 'Batang',
        'horizontal' => 'Batang horizontal',
        'donut' => 'Donat',
        'line' => 'Garis (tren antar tahun)',
        'cards' => 'Kartu angka',
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['value' => 'float', 'is_published' => 'boolean', 'is_highlight' => 'boolean', 'year' => 'integer'];
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true);
    }

    public static function format(float $v): string
    {
        $decimals = floor($v) == $v ? 0 : 2;

        return number_format($v, $decimals, ',', '.');
    }

    public function formattedValue(): string
    {
        return self::format($this->value);
    }
}
