<?php

namespace App\Admin\Resources;

use App\Admin\Field;
use App\Admin\Resource;
use App\Models\StatCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rule;

/** Daftar kategori baku untuk data statistik — hanya super admin yang dapat menambah/mengubah. */
class StatCategoryResource extends Resource
{
    public static string $model = StatCategory::class;

    public static string $slug = 'kategori-statistik';

    public static string $label = 'Kategori Statistik';

    public static string $plural = 'Kategori Statistik';

    public static string $icon = 'tags';

    public static bool $superAdminOnly = true;

    public static string $titleField = 'name';

    public static function fields(): array
    {
        return [
            Field::make('name', 'Nama kategori / kelompok grafik')->required()->rules(['max:80'])->full()
                ->help('Mengubah nama akan ikut memperbarui seluruh indikator yang memakai kategori ini.'),
            Field::make('description', 'Keterangan (opsional)', 'textarea')->rules(['max:500']),
            Field::make('display_order', 'Urutan di dropdown', 'number')->default(0)->rules(['integer', 'min:0']),
        ];
    }

    public static function columns(): array
    {
        return ['name' => 'Kategori', 'usage' => 'Jumlah indikator', 'display_order' => 'Urutan'];
    }

    public static function cell(Model $m, string $key): string|HtmlString
    {
        /** @var StatCategory $m */
        return $key === 'usage' ? (string) $m->statisticsCount() : parent::cell($m, $key);
    }

    public static function searchable(): array
    {
        return ['name', 'description'];
    }

    public static function sort(): array
    {
        return ['display_order', 'asc'];
    }

    public static function extraRules(?int $ignoreId): array
    {
        return ['name' => [Rule::unique('stat_categories', 'name')->ignore($ignoreId)]];
    }

    public static function deleteBlockedReason(Model $m): ?string
    {
        /** @var StatCategory $m */
        $n = $m->statisticsCount();

        return $n > 0 ? 'Kategori "'.$m->name.'" masih dipakai '.$n.' indikator. Pindahkan atau hapus indikatornya terlebih dahulu.' : null;
    }
}
