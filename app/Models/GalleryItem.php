<?php

namespace App\Models;

use App\Models\Concerns\HasStatus;
use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    use HasStatus;

    public const CATEGORIES = [
        'kegiatan' => 'Kegiatan Warga',
        'pemerintahan' => 'Pemerintahan',
        'umkm' => 'UMKM',
        'lingkungan' => 'Lingkungan',
        'pembangunan' => 'Pembangunan',
        'fasilitas' => 'Fasilitas',
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['event_date' => 'date'];
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst($this->category);
    }
}
