<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'social_links' => 'array',
            'latitude' => 'float',
            'longitude' => 'float',
            'show_income_summary' => 'boolean',
        ];
    }

    public static function current(): self
    {
        return once(fn () => static::query()->first() ?? new static([
            'village_name' => 'Desa Marga Mulya',
            'hero_title' => 'Desa Marga Mulya',
        ]));
    }

    public function whatsappLink(?string $text = null): ?string
    {
        return self::waLink($this->whatsapp, $text);
    }

    public static function waLink(?string $number, ?string $text = null): ?string
    {
        if (! $number) {
            return null;
        }
        $digits = preg_replace('/\D+/', '', $number);
        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        }

        return 'https://wa.me/'.$digits.($text ? '?text='.rawurlencode($text) : '');
    }

    /** @return array<string, string> */
    public function socials(): array
    {
        return array_filter((array) ($this->social_links ?? []));
    }
}
