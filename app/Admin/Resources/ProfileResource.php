<?php

namespace App\Admin\Resources;

use App\Admin\Field;
use App\Admin\Resource;
use App\Models\VillageProfile;

class ProfileResource extends Resource
{
    public static string $model = VillageProfile::class;

    public static string $slug = 'profil';

    public static string $label = 'Profil Desa';

    public static string $plural = 'Profil Desa';

    public static string $icon = 'landmark';

    public static bool $singleton = true;

    public static string $titleField = 'official_name';

    public static function fields(): array
    {
        return [
            Field::make('official_name', 'Nama resmi desa')->required()->section('Identitas'),
            Field::make('village_code', 'Kode desa'),
            Field::make('district', 'Kecamatan'),
            Field::make('regency', 'Kabupaten/Kota'),
            Field::make('province', 'Provinsi'),
            Field::make('postal_code', 'Kode pos'),
            Field::make('description', 'Deskripsi desa', 'textarea'),
            Field::make('image_url', 'Foto utama profil', 'image'),
            Field::make('history', 'Sejarah desa', 'richtext')->section('Sejarah, visi & misi'),
            Field::make('vision', 'Visi', 'textarea'),
            Field::make('mission', 'Misi', 'textarea')->help('Satu misi per baris.'),
            Field::make('area_hectare', 'Luas wilayah (ha)', 'number')->section('Kondisi geografis'),
            Field::make('rw_count', 'Jumlah RW', 'number')->rules(['integer', 'min:0']),
            Field::make('rt_count', 'Jumlah RT', 'number')->rules(['integer', 'min:0']),
            Field::make('border_north', 'Batas utara'),
            Field::make('border_east', 'Batas timur'),
            Field::make('border_south', 'Batas selatan'),
            Field::make('border_west', 'Batas barat'),
            Field::make('geography_notes', 'Catatan geografis', 'textarea'),
            Field::make('structure_image_url', 'Bagan struktur organisasi (opsional)', 'image')->section('Pemerintahan'),
        ];
    }
}
