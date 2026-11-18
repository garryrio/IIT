<?php

namespace App\Admin\Resources;

use App\Admin\Field;
use App\Admin\Resource;
use App\Models\InformationPost;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;

class PostResource extends Resource
{
    public static string $model = InformationPost::class;

    public static string $slug = 'informasi';

    public static string $label = 'Informasi';

    public static string $plural = 'Informasi Desa';

    public static string $icon = 'newspaper';

    public static function fields(): array
    {
        return [
            Field::make('type', 'Jenis informasi', 'select')->options(InformationPost::TYPES)->required()->default('news')->section('Konten'),
            Field::make('status', 'Status', 'status')->options(self::STATUS)->required()->default('draft'),
            Field::make('title', 'Judul')->required()->full(),
            Field::make('slug', 'Slug URL', 'slug')->help('Kosongkan untuk dibuat otomatis dari judul.')->full(),
            Field::make('excerpt', 'Ringkasan', 'textarea')->rules(['max:400']),
            Field::make('content', 'Isi', 'richtext'),
            Field::make('cover_image_url', 'Gambar sampul', 'image'),
            Field::make('published_at', 'Waktu terbit', 'datetime')->help('Kosongkan untuk memakai waktu saat diterbitkan. Isi tanggal mendatang untuk terbit terjadwal.'),
            Field::make('priority', 'Prioritas pengumuman', 'select')->options(InformationPost::PRIORITIES)->default(0)->section('Khusus pengumuman'),
            Field::make('start_date', 'Mulai tampil', 'date'),
            Field::make('end_date', 'Berakhir', 'date')->rules(['after_or_equal:start_date'])->help('Setelah tanggal ini, pengumuman tidak lagi aktif.'),
            Field::make('event_date', 'Tanggal agenda', 'date')->section('Khusus agenda'),
            Field::make('event_time', 'Jam mulai', 'time'),
            Field::make('event_end_time', 'Jam selesai', 'time'),
            Field::make('organizer', 'Penyelenggara'),
            Field::make('location', 'Lokasi'),
            Field::latlng('latitude', 'longitude', 'Titik lokasi agenda (opsional)'),
        ];
    }

    public static function columns(): array
    {
        return ['title' => 'Judul', 'type' => 'Jenis', 'when' => 'Tanggal', 'status' => 'Status', 'updated_at' => 'Diperbarui'];
    }

    public static function cell(Model $m, string $key): string|HtmlString
    {
        /** @var InformationPost $m */
        return match ($key) {
            'type' => $m->typeLabel().($m->isExpired() ? ' (kedaluwarsa)' : '').($m->isPastEvent() ? ' (selesai)' : ''),
            'when' => $m->displayDate()?->translatedFormat('d M Y') ?? '—',
            default => parent::cell($m, $key),
        };
    }

    public static function searchable(): array
    {
        return ['title', 'excerpt', 'location', 'organizer'];
    }

    public static function filters(): array
    {
        return ['type' => ['label' => 'Jenis', 'options' => InformationPost::TYPES]] + parent::filters();
    }

    public static function heading(Request $request): string
    {
        return InformationPost::TYPES[$request->query('type')] ?? static::$plural;
    }

    public static function context(Request $request): array
    {
        return array_key_exists($request->query('type'), InformationPost::TYPES) ? ['type' => $request->query('type')] : [];
    }

    public static function newModel(Request $request): Model
    {
        $m = parent::newModel($request);
        if (array_key_exists($request->query('type'), InformationPost::TYPES)) {
            $m->type = $request->query('type');
        }

        return $m;
    }

    public static function sort(): array
    {
        return ['updated_at', 'desc'];
    }

    public static function query(Request $request): Builder
    {
        return InformationPost::query();
    }

    public static function mutate(array $data, ?Model $model): array
    {
        $data = parent::mutate($data, $model);
        if (($data['status'] ?? null) === 'published' && empty($data['published_at'])) {
            $data['published_at'] = $model?->published_at ?? now();
        }
        if (($data['type'] ?? '') !== 'announcement') {
            $data['priority'] = 0;
        }

        return $data;
    }

    public static function publicUrl(Model $m): ?string
    {
        return $m->status === 'published' ? route('information.show', $m->slug) : null;
    }
}
