<?php

namespace App\Admin\Resources;

use App\Admin\Field;
use App\Admin\Resource;
use App\Models\InformationPost;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;

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

    public static function mutate(array $data, ?Model $model): array
    {
        $data = parent::mutate($data, $model);

        // Berita laporan kegiatan (dari agenda) baru boleh terbit setelah ada foto dokumentasi & isi lengkap.
        if ($model instanceof InformationPost && $model->source_event_id && ($data['status'] ?? null) === 'published') {
            $errors = [];
            $cover = array_key_exists('cover_image_url', $data) ? $data['cover_image_url'] : $model->cover_image_url;
            if (! $cover) {
                $errors['cover_image_url_file'] = 'Unggah foto dokumentasi kegiatan sebelum menerbitkan berita ini.';
            }
            if (str_contains((string) ($data['content'] ?? ''), InformationPost::REPORT_PLACEHOLDER)) {
                $errors['content'] = 'Ganti teks "'.InformationPost::REPORT_PLACEHOLDER.'" dengan cerita jalannya kegiatan.';
            }
            if ($errors) {
                throw ValidationException::withMessages($errors);
            }
        }

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

    public static function rowActions(Model $m): array
    {
        /** @var InformationPost $m */
        if (! $m->canBecomeNews()) {
            return [];
        }
        if ($m->report) {
            return [['label' => 'Buka berita', 'url' => route('admin.resource.edit', ['informasi', $m->report->id, 'type' => 'news']), 'method' => 'get']];
        }

        return [['label' => 'Jadikan berita', 'url' => route('admin.events.report', $m->id), 'method' => 'post']];
    }

    public static function query(Request $request): Builder
    {
        return InformationPost::query()->with('report:id,source_event_id');
    }
}
