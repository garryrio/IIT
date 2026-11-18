<?php

namespace App\Admin\Resources;

use App\Admin\Field;
use App\Admin\Resource;
use App\Models\IncomeClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class IncomeClassResource extends Resource
{
    public static string $model = IncomeClass::class;

    public static string $slug = 'kelas-penghasilan';

    public static string $label = 'Kelas Penghasilan';

    public static string $plural = 'Kelas Penghasilan';

    public static string $icon = 'layers';

    public static string $titleField = 'name';

    public static function fields(): array
    {
        return [
            Field::make('name', 'Nama kelas')->required(),
            Field::make('color', 'Warna grafik', 'color')->required()->default('#2f6b3a'),
            Field::make('min_per_capita', 'Batas bawah (Rp/kapita/bulan)', 'number')->required()->rules(['integer', 'min:0']),
            Field::make('max_per_capita', 'Batas atas (Rp/kapita/bulan)', 'number')->rules(['integer', 'gt:min_per_capita'])->help('Kosongkan untuk kelas tertinggi.'),
            Field::make('aid_priority', 'Prioritas bantuan', 'select')->options([
                '1' => '1 — Prioritas utama', '2' => '2 — Prioritas kedua', '3' => '3 — Dipertimbangkan', '0' => 'Bukan sasaran bantuan',
            ])->required()->default('0'),
            Field::make('display_order', 'Urutan', 'number')->default(0)->rules(['integer', 'min:0']),
            Field::make('description', 'Keterangan', 'textarea'),
        ];
    }

    public static function columns(): array
    {
        return ['name' => 'Kelas', 'range' => 'Rentang per kapita', 'aid_priority' => 'Prioritas bantuan'];
    }

    public static function cell(Model $m, string $key): string|HtmlString
    {
        return match ($key) {
            'range' => $m->rangeLabel(),
            'aid_priority' => $m->aid_priority ? 'Prioritas '.$m->aid_priority : 'Bukan sasaran',
            'name' => new HtmlString('<span class="inline-flex items-center gap-2"><span class="size-3 rounded-full" style="background:'.e($m->color).'"></span>'.e($m->name).'</span>'),
            default => parent::cell($m, $key),
        };
    }

    public static function sort(): array
    {
        return ['min_per_capita', 'asc'];
    }
}
