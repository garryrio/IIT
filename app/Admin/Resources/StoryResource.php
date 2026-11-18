<?php

namespace App\Admin\Resources;

use App\Admin\Field;
use App\Admin\Resource;
use App\Models\Story;

class StoryResource extends Resource
{
    public static string $model = Story::class;

    public static string $slug = 'cerita';

    public static string $label = 'Cerita Warga';

    public static string $plural = 'Cerita Marga Mulya';

    public static string $icon = 'message-square-quote';

    public static string $titleField = 'name';

    public static function fields(): array
    {
        return [
            Field::make('name', 'Nama warga')->required(),
            Field::make('role', 'Peran / keterangan')->placeholder('mis. Pelaku UMKM, Kader Posyandu'),
            Field::make('quote', 'Cerita singkat', 'textarea')->required()->rules(['max:600']),
            Field::make('display_order', 'Urutan', 'number')->default(0)->rules(['integer', 'min:0']),
            Field::make('status', 'Status', 'status')->options(self::STATUS)->required()->default('draft'),
            Field::make('photo_url', 'Foto (opsional)', 'image'),
        ];
    }

    public static function columns(): array
    {
        return ['name' => 'Nama', 'role' => 'Peran', 'quote' => 'Cerita', 'status' => 'Status'];
    }

    public static function searchable(): array
    {
        return ['name', 'role', 'quote'];
    }
}
