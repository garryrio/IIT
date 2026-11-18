<?php

namespace App\Models;

use App\Models\Concerns\HasStatus;
use Illuminate\Database\Eloquent\Model;

class Potential extends Model
{
    use HasStatus;

    public const CATEGORIES = [
        'pertanian' => 'Pertanian & Perkebunan',
        'umkm' => 'UMKM & Produk Lokal',
        'kuliner' => 'Kuliner',
        'kerajinan' => 'Kerajinan',
        'wisata' => 'Wisata & Ruang Terbuka',
        'jasa' => 'Jasa & Pelatihan',
    ];

    public const COLORS = [
        'pertanian' => '#2f6b3a',
        'umkm' => '#b05a0e',
        'kuliner' => '#b5452f',
        'kerajinan' => '#7a4e9a',
        'wisata' => '#1f6f8b',
        'jasa' => '#5b6b2f',
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['social_links' => 'array', 'is_featured' => 'boolean', 'latitude' => 'float', 'longitude' => 'float'];
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst($this->category);
    }

    /** @return array<string, string> */
    public function socials(): array
    {
        return array_filter((array) ($this->social_links ?? []));
    }

    public function url(): string
    {
        return route('potentials.show', $this->slug);
    }
}
