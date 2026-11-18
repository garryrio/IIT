<?php

namespace App\Models;

use App\Models\Concerns\HasStatus;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasStatus;

    public const TYPES = [
        'pemerintahan' => 'Pemerintahan',
        'pendidikan' => 'Pendidikan',
        'kesehatan' => 'Kesehatan',
        'keamanan' => 'Keamanan',
        'olahraga' => 'Olahraga & Ruang Publik',
        'umum' => 'Fasilitas Umum',
    ];

    public const COLORS = [
        'pemerintahan' => '#1f4d2b',
        'pendidikan' => '#1f6f8b',
        'kesehatan' => '#b5452f',
        'keamanan' => '#4a4a4a',
        'olahraga' => '#5b6b2f',
        'umum' => '#7a4e9a',
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['latitude' => 'float', 'longitude' => 'float'];
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }
}
