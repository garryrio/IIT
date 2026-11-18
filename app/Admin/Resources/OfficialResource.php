<?php

namespace App\Admin\Resources;

use App\Admin\Field;
use App\Admin\Resource;
use App\Models\Official;

class OfficialResource extends Resource
{
    public static string $model = Official::class;

    public static string $slug = 'perangkat';

    public static string $label = 'Perangkat Desa';

    public static string $plural = 'Perangkat Desa';

    public static string $icon = 'users';

    public static string $titleField = 'name';

    public static function fields(): array
    {
        return [
            Field::make('name', 'Nama')->required(),
            Field::make('position', 'Jabatan')->required(),
            Field::make('unit', 'Unit', 'select')->options([
                'Pemerintah Desa' => 'Pemerintah Desa', 'BPD' => 'BPD', 'Kecamatan' => 'Aparat Kecamatan', 'Lembaga Desa' => 'Lembaga Desa',
            ])->required()->default('Pemerintah Desa'),
            Field::make('employment_status', 'Status kepegawaian', 'select')->options(['PNS' => 'PNS', 'Non-PNS' => 'Non-PNS', 'Perangkat Desa' => 'Perangkat Desa']),
            Field::make('gender', 'Jenis kelamin', 'select')->options(['Laki-laki' => 'Laki-laki', 'Perempuan' => 'Perempuan']),
            Field::make('education', 'Pendidikan terakhir', 'select')->options(['SD' => 'SD', 'SMP' => 'SMP', 'SMA/SMK' => 'SMA/SMK', 'D3' => 'D3', 'S1' => 'S1', 'S2' => 'S2']),
            Field::make('display_order', 'Urutan tampil', 'number')->default(0)->rules(['integer', 'min:0']),
            Field::make('status', 'Status', 'status')->options(self::STATUS)->required()->default('published'),
            Field::make('photo_url', 'Foto', 'image'),
        ];
    }

    public static function columns(): array
    {
        return ['name' => 'Nama', 'position' => 'Jabatan', 'unit' => 'Unit', 'status' => 'Status'];
    }

    public static function searchable(): array
    {
        return ['name', 'position'];
    }

    public static function sort(): array
    {
        return ['display_order', 'asc'];
    }
}
