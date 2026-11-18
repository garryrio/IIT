<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CitizenSubmission extends Model
{
    public const CATEGORIES = [
        'potensi' => 'Potensi lokal',
        'kegiatan' => 'Kegiatan masyarakat',
        'infrastruktur' => 'Infrastruktur',
        'lingkungan' => 'Lingkungan',
        'lainnya' => 'Informasi lainnya',
    ];

    public const STATUSES = ['pending' => 'Menunggu verifikasi', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['reviewed_at' => 'datetime', 'latitude' => 'float', 'longitude' => 'float'];
    }

    public function publishedPost(): BelongsTo
    {
        return $this->belongsTo(InformationPost::class, 'published_post_id');
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }
}
