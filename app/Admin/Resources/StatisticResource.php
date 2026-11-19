<?php

namespace App\Admin\Resources;

use App\Admin\Field;
use App\Admin\Resource;
use App\Models\StatCategory;
use App\Models\VillageStatistic;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rule;

class StatisticResource extends Resource
{
    public static string $model = VillageStatistic::class;

    public static string $slug = 'statistik';

    public static string $label = 'Data Statistik';

    public static string $plural = 'Data & Statistik Desa';

    public static string $icon = 'bar-chart-3';

    public static string $titleField = 'indicator';

    public static function fields(): array
    {
        $help = 'Indikator dengan kategori & tahun yang sama tampil dalam satu grafik. ';
        $help .= auth()->user()?->isSuperAdmin()
            ? 'Kategori baru dapat ditambahkan di menu Kategori statistik.'
            : 'Kategori baru hanya dapat ditambahkan oleh super admin.';

        return [
            Field::make('category', 'Kategori / kelompok grafik', 'select')->options(StatCategory::options())->required()->help($help),
            Field::make('indicator', 'Indikator')->required()->placeholder('mis. Laki-laki'),
            Field::make('value', 'Nilai', 'number')->required()->rules(['min:0']),
            Field::make('unit', 'Satuan')->placeholder('jiwa, ha, unit, orang'),
            Field::make('year', 'Tahun data', 'number')->required()->rules(['integer', 'between:2000,2100'])->default((int) date('Y')),
            Field::make('chart_type', 'Jenis visualisasi', 'select')->options(VillageStatistic::CHART_TYPES)->required()->default('bar')
                ->help('Dipakai untuk seluruh kategori (diambil dari indikator pertama).'),
            Field::make('source', 'Sumber data')->placeholder('mis. Profil Desa (data contoh)'),
            Field::make('display_order', 'Urutan', 'number')->default(0)->rules(['integer', 'min:0']),
            Field::make('is_highlight', 'Tampilkan sebagai statistik singkat di beranda', 'boolean'),
            Field::make('is_published', 'Terbitkan', 'boolean')->default(true),
        ];
    }

    public static function columns(): array
    {
        return ['category' => 'Kategori', 'indicator' => 'Indikator', 'value' => 'Nilai', 'year' => 'Tahun', 'is_published' => 'Terbit'];
    }

    public static function cell($m, string $key): string|HtmlString
    {
        return $key === 'value' ? $m->formattedValue().' '.$m->unit : parent::cell($m, $key);
    }

    public static function searchable(): array
    {
        return ['category', 'indicator', 'source'];
    }

    public static function filters(): array
    {
        return [
            'category' => ['label' => 'Kategori', 'options' => StatCategory::options()],
            'year' => ['label' => 'Tahun', 'options' => VillageStatistic::query()->distinct()->orderByDesc('year')->pluck('year', 'year')->all()],
        ];
    }

    public static function sort(): array
    {
        return ['category', 'asc'];
    }

    public static function extraRules(?int $ignoreId): array
    {
        return ['indicator' => [Rule::unique('village_statistics')->where(fn ($q) => $q
            ->where('category', request('category'))->where('year', request('year')))->ignore($ignoreId)]];
    }
}
