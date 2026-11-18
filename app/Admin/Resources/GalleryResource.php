<?php

namespace App\Admin\Resources;

use App\Admin\Field;
use App\Admin\Resource;
use App\Models\GalleryItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class GalleryResource extends Resource
{
    public static string $model = GalleryItem::class;

    public static string $slug = 'galeri';

    public static string $label = 'Foto Galeri';

    public static string $plural = 'Galeri';

    public static string $icon = 'images';

    public static function fields(): array
    {
        return [
            Field::make('title', 'Judul foto')->required()->full(),
            Field::make('image_url', 'Foto', 'image')->required()->help('Wajib diisi. JPG/PNG/WebP, maks 2 MB (otomatis diperkecil).'),
            Field::make('alt_text', 'Teks alternatif (alt text)')->required()->full()->help('Jelaskan isi foto untuk pengguna pembaca layar.'),
            Field::make('description', 'Deskripsi', 'textarea'),
            Field::make('category', 'Kategori', 'select')->options(GalleryItem::CATEGORIES)->required(),
            Field::make('event_date', 'Tanggal', 'date'),
            Field::make('location', 'Lokasi'),
            Field::make('status', 'Status', 'status')->options(self::STATUS)->required()->default('draft'),
        ];
    }

    public static function columns(): array
    {
        return ['thumb' => 'Foto', 'title' => 'Judul', 'category' => 'Kategori', 'status' => 'Status', 'updated_at' => 'Diperbarui'];
    }

    public static function cell(Model $m, string $key): string|HtmlString
    {
        return match ($key) {
            'thumb' => new HtmlString('<img src="'.e($m->image_url).'" alt="" class="h-12 w-16 rounded-md object-cover bg-stone-100" loading="lazy">'),
            'category' => $m->categoryLabel(),
            default => parent::cell($m, $key),
        };
    }

    public static function searchable(): array
    {
        return ['title', 'description', 'location'];
    }

    public static function filters(): array
    {
        return ['category' => ['label' => 'Kategori', 'options' => GalleryItem::CATEGORIES]] + parent::filters();
    }
}
