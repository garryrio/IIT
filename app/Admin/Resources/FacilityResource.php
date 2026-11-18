<?php

namespace App\Admin\Resources;

use App\Admin\Field;
use App\Admin\Resource;
use App\Models\Facility;
use Illuminate\Support\HtmlString;

class FacilityResource extends Resource
{
    public static string $model = Facility::class;

    public static string $slug = 'fasilitas';

    public static string $label = 'Fasilitas';

    public static string $plural = 'Fasilitas Umum';

    public static string $icon = 'building-2';

    public static string $titleField = 'name';

    public static function fields(): array
    {
        return [
            Field::make('name', 'Nama fasilitas')->required(),
            Field::make('type', 'Jenis', 'select')->options(Facility::TYPES)->required(),
            Field::make('address', 'Alamat')->full(),
            Field::make('description', 'Deskripsi', 'textarea'),
            Field::latlng(),
            Field::make('display_order', 'Urutan tampil', 'number')->default(0)->rules(['integer', 'min:0']),
            Field::make('status', 'Status', 'status')->options(self::STATUS)->required()->default('published'),
            Field::make('image_url', 'Foto', 'image'),
        ];
    }

    public static function columns(): array
    {
        return ['name' => 'Nama', 'type' => 'Jenis', 'status' => 'Status', 'updated_at' => 'Diperbarui'];
    }

    public static function cell($m, string $key): string|HtmlString
    {
        return $key === 'type' ? $m->typeLabel() : parent::cell($m, $key);
    }

    public static function filters(): array
    {
        return ['type' => ['label' => 'Jenis', 'options' => Facility::TYPES]] + parent::filters();
    }

    public static function searchable(): array
    {
        return ['name', 'address', 'description'];
    }
}
