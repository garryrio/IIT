<?php

namespace App\Models;

use App\Models\Concerns\HasStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class InformationPost extends Model
{
    use HasStatus;

    public const TYPES = [
        'news' => 'Berita',
        'announcement' => 'Pengumuman',
        'event' => 'Agenda',
        'service' => 'Layanan',
    ];

    public const PRIORITIES = [0 => 'Normal', 1 => 'Penting', 2 => 'Sangat penting'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'start_date' => 'date',
            'end_date' => 'date',
            'published_at' => 'datetime',
            'priority' => 'integer',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    /** Konten terbit = status published dan waktu terbit sudah lewat. */
    public function scopePublished(Builder $q): Builder
    {
        return $q->where('status', 'published')
            ->where(fn ($w) => $w->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function scopeActiveAnnouncements(Builder $q): Builder
    {
        $today = Carbon::today()->toDateString();

        return $q->published()->where('type', 'announcement')
            ->where(fn ($w) => $w->whereNull('start_date')->orWhere('start_date', '<=', $today))
            ->where(fn ($w) => $w->whereNull('end_date')->orWhere('end_date', '>=', $today))
            ->orderByDesc('priority')->orderByDesc('published_at');
    }

    public function scopeUpcomingEvents(Builder $q): Builder
    {
        return $q->published()->where('type', 'event')
            ->whereDate('event_date', '>=', Carbon::today())
            ->orderBy('event_date')->orderBy('event_time');
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function isExpired(): bool
    {
        return $this->type === 'announcement' && $this->end_date && $this->end_date->lt(Carbon::today());
    }

    public function isPastEvent(): bool
    {
        return $this->type === 'event' && $this->event_date && $this->event_date->lt(Carbon::today());
    }

    public function timeRange(): ?string
    {
        if (! $this->event_time) {
            return null;
        }
        $s = substr($this->event_time, 0, 5);

        return $this->event_end_time ? $s.'–'.substr($this->event_end_time, 0, 5).' WIB' : $s.' WIB';
    }

    public function displayDate(): ?Carbon
    {
        return $this->type === 'event' ? $this->event_date : ($this->published_at ?? $this->created_at);
    }

    public function url(): string
    {
        return route('information.show', $this->slug);
    }

    /** Penanda isi yang harus diganti admin sebelum berita laporan kegiatan boleh terbit. */
    public const REPORT_PLACEHOLDER = '[Tuliskan jalannya kegiatan di sini]';

    /** Agenda asal (untuk berita yang dibuat dari agenda yang sudah selesai). */
    public function sourceEvent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'source_event_id');
    }

    /** Berita laporan yang dibuat dari agenda ini. */
    public function report(): HasOne
    {
        return $this->hasOne(self::class, 'source_event_id');
    }

    /** Agenda yang sudah lewat dan dapat dijadikan berita. */
    public function canBecomeNews(): bool
    {
        return $this->type === 'event' && $this->isPastEvent();
    }
}
