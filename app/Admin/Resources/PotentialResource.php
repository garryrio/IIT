<?php

namespace App\Admin\Resources;

use App\Admin\Field;
use App\Admin\Resource;
use App\Models\Potential;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class PotentialResource extends Resource
{
    public static string $model = Potential::class;

    public static string $slug = 'potensi';

    public static string $label = 'Potensi';

    public static string $plural = 'Potensi Desa';

    public static string $icon = 'sprout';

    public static string $titleField = 'name';

    public static function fields(): array
    {
        return [
            Field::make('name', 'Nama potensi / usaha')->required()->full()->section('Informasi utama'),
            Field::make('slug', 'Slug URL', 'slug')->help('Kosongkan untuk dibuat otomatis.'),
            Field::make('category', 'Kategori', 'select')->options(Potential::CATEGORIES)->required(),
            Field::make('summary', 'Ringkasan singkat', 'textarea')->rules(['max:300']),
            Field::make('description', 'Deskripsi lengkap', 'richtext'),
            Field::make('image_url', 'Foto', 'image'),
            Field::make('owner_or_manager', 'Pemilik / pengelola')->section('Kontak & operasional'),
            Field::make('contact', 'Kontak (telepon)'),
            Field::make('whatsapp', 'WhatsApp'),
            Field::make('operating_hours', 'Jam operasional'),
            Field::make('price_range', 'Kisaran harga'),
            Field::make('address', 'Alamat')->full(),
            Field::latlng(),
            Field::make('social_links', 'Media sosial', 'keyvalue')->options([
                'instagram' => 'Instagram', 'facebook' => 'Facebook', 'tiktok' => 'TikTok', 'marketplace' => 'Marketplace', 'website' => 'Website',
            ]),
            Field::make('is_featured', 'Tampilkan sebagai unggulan di beranda', 'boolean')->section('Publikasi'),
            Field::make('status', 'Status', 'status')->options(self::STATUS)->required()->default('draft'),
        ];
    }

    public static function columns(): array
    {
        return ['name' => 'Nama', 'category' => 'Kategori', 'is_featured' => 'Unggulan', 'status' => 'Status', 'updated_at' => 'Diperbarui'];
    }

    public static function cell(Model $m, string $key): string|HtmlString
    {
        return $key === 'category' ? $m->categoryLabel() : parent::cell($m, $key);
    }

    public static function searchable(): array
    {
        return ['name', 'summary', 'owner_or_manager', 'address'];
    }

    public static function filters(): array
    {
        return ['category' => ['label' => 'Kategori', 'options' => Potential::CATEGORIES]] + parent::filters();
    }

    public static function publicUrl(Model $m): ?string
    {
        return $m->status === 'published' ? route('potentials.show', $m->slug) : null;
    }
}
