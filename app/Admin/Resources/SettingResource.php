<?php

namespace App\Admin\Resources;

use App\Admin\Field;
use App\Admin\Resource;
use App\Models\SiteSetting;

class SettingResource extends Resource
{
    public static string $model = SiteSetting::class;

    public static string $slug = 'pengaturan';

    public static string $label = 'Pengaturan Situs';

    public static string $plural = 'Pengaturan Situs';

    public static string $icon = 'settings';

    public static bool $singleton = true;

    public static string $titleField = 'village_name';

    public static function fields(): array
    {
        return [
            Field::make('village_name', 'Nama desa')->required()->section('Identitas situs'),
            Field::make('tagline', 'Tagline'),
            Field::make('short_description', 'Deskripsi singkat', 'textarea')->help('Tampil di footer dan metadata SEO.'),
            Field::make('hero_title', 'Judul hero beranda')->required()->section('Hero beranda'),
            Field::make('hero_description', 'Deskripsi hero', 'textarea'),
            Field::make('hero_cta_primary', 'Teks tombol utama')->placeholder('Jelajahi Desa'),
            Field::make('hero_cta_secondary', 'Teks tombol kedua')->placeholder('Lihat Potensi'),
            Field::make('hero_image_url', 'Foto hero', 'image'),
            Field::make('address', 'Alamat kantor desa')->full()->section('Kontak & lokasi'),
            Field::make('phone', 'Telepon'),
            Field::make('whatsapp', 'WhatsApp')->placeholder('0812xxxxxxx'),
            Field::make('email', 'Email', 'email'),
            Field::make('service_hours', 'Jam layanan')->placeholder('Senin–Jumat, 08.00–15.00 WIB'),
            Field::latlng('latitude', 'longitude', 'Titik kantor desa'),
            Field::make('social_links', 'Media sosial', 'keyvalue')->options([
                'instagram' => 'Instagram', 'facebook' => 'Facebook', 'youtube' => 'YouTube', 'tiktok' => 'TikTok', 'website' => 'Website',
            ])->help('Isi URL lengkap, mis. https://instagram.com/...'),
            Field::make('contribution_cta_title', 'Judul ajakan kontribusi')->section('Ajakan & catatan'),
            Field::make('contribution_cta_text', 'Teks ajakan kontribusi', 'textarea'),
            Field::make('demo_notice', 'Catatan data (footer)', 'textarea')->help('Mis. keterangan bahwa data masih berupa data contoh.'),
            Field::make('show_income_summary', 'Tampilkan ringkasan agregat kelas penghasilan di halaman Data Desa', 'boolean'),
        ];
    }
}
