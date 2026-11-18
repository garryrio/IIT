<?php

namespace App\Support;

use App\Models\Facility;
use App\Models\GalleryItem;
use App\Models\InformationPost;
use App\Models\Potential;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Smart Search: mencari berita, pengumuman, agenda, layanan, potensi, fasilitas, dan galeri.
 * Mendukung beberapa kata (semua kata harus cocok), filter jenis, kategori, rentang tanggal,
 * dan pengurutan berdasarkan relevansi/terbaru. Hasil dikelompokkan per jenis konten.
 */
class Search
{
    public const GROUPS = [
        'announcement' => 'Pengumuman',
        'event' => 'Agenda',
        'news' => 'Berita',
        'service' => 'Layanan',
        'potential' => 'Potensi Desa',
        'facility' => 'Fasilitas',
        'gallery' => 'Galeri',
    ];

    /**
     * @param  array{q?:string,types?:array<int,string>,category?:string,from?:string,to?:string,sort?:string}  $params
     * @return array{query:string,total:int,groups:list<array{key:string,label:string,items:list<array<string,mixed>>}>}
     */
    public static function run(array $params, int $perGroup = 6): array
    {
        $q = trim(Str::limit((string) ($params['q'] ?? ''), 100, ''));
        $terms = collect(preg_split('/\s+/', Str::lower($q)))->filter(fn ($t) => mb_strlen($t) >= 2)->unique()->take(6)->values();
        $types = collect($params['types'] ?? [])->intersect(array_keys(self::GROUPS))->values();
        if ($types->isEmpty()) {
            $types = collect(array_keys(self::GROUPS));
        }
        $from = self::date($params['from'] ?? null);
        $to = self::date($params['to'] ?? null);
        $category = $params['category'] ?? null;
        $sort = ($params['sort'] ?? 'relevance') === 'newest' ? 'newest' : 'relevance';

        if ($terms->isEmpty() && ! $from && ! $to && ! $category) {
            return ['query' => $q, 'total' => 0, 'groups' => []];
        }

        $groups = [];
        foreach (['announcement', 'event', 'news', 'service'] as $type) {
            if (! $types->contains($type)) {
                continue;
            }
            $query = InformationPost::published()->where('type', $type);
            self::match($query, $terms, ['title', 'excerpt', 'content', 'location', 'organizer']);
            $dateCol = $type === 'event' ? 'event_date' : 'published_at';
            if ($from) {
                $query->whereDate($dateCol, '>=', $from);
            }
            if ($to) {
                $query->whereDate($dateCol, '<=', $to);
            }
            $items = $query->limit(50)->get()->map(fn (InformationPost $p) => [
                'title' => $p->title,
                'url' => $p->url(),
                'snippet' => Html::excerpt($p->excerpt ?: $p->content, 140),
                'meta' => trim(($p->displayDate()?->translatedFormat('d M Y') ?? '').($p->location ? ' · '.$p->location : '')),
                'date' => $p->displayDate()?->timestamp ?? 0,
                'score' => self::score($terms, $p->title, $p->excerpt.' '.$p->content),
                'expired' => $p->isExpired() || $p->isPastEvent(),
            ]);
            $groups[$type] = $items;
        }

        if ($types->contains('potential') && ! $from && ! $to) {
            $query = Potential::published();
            self::match($query, $terms, ['name', 'summary', 'description', 'address', 'owner_or_manager']);
            if ($category && isset(Potential::CATEGORIES[$category])) {
                $query->where('category', $category);
            }
            $groups['potential'] = $query->limit(50)->get()->map(fn (Potential $p) => [
                'title' => $p->name,
                'url' => $p->url(),
                'snippet' => Html::excerpt($p->summary ?: $p->description, 140),
                'meta' => $p->categoryLabel(),
                'date' => $p->updated_at?->timestamp ?? 0,
                'score' => self::score($terms, $p->name, $p->summary.' '.$p->description),
            ]);
        }

        if ($types->contains('facility') && ! $from && ! $to && ! $category) {
            $query = Facility::published();
            self::match($query, $terms, ['name', 'description', 'address']);
            $groups['facility'] = $query->limit(30)->get()->map(fn (Facility $f) => [
                'title' => $f->name,
                'url' => route('profile').'#fasilitas',
                'snippet' => Html::excerpt($f->description, 120),
                'meta' => $f->typeLabel(),
                'date' => 0,
                'score' => self::score($terms, $f->name, (string) $f->description),
            ]);
        }

        if ($types->contains('gallery') && ! $category) {
            $query = GalleryItem::published();
            self::match($query, $terms, ['title', 'description', 'location']);
            if ($from) {
                $query->whereDate('event_date', '>=', $from);
            }
            if ($to) {
                $query->whereDate('event_date', '<=', $to);
            }
            $groups['gallery'] = $query->limit(30)->get()->map(fn (GalleryItem $g) => [
                'title' => $g->title,
                'url' => route('gallery').'?foto='.$g->id,
                'snippet' => Html::excerpt($g->description, 120),
                'meta' => trim($g->categoryLabel().($g->event_date ? ' · '.$g->event_date->translatedFormat('d M Y') : '')),
                'date' => $g->event_date?->timestamp ?? 0,
                'score' => self::score($terms, $g->title, (string) $g->description),
                'image' => $g->image_url,
            ]);
        }

        $out = [];
        $total = 0;
        foreach (self::GROUPS as $key => $label) {
            /** @var Collection<int, array<string, mixed>>|null $items */
            $items = $groups[$key] ?? null;
            if (! $items || $items->isEmpty()) {
                continue;
            }
            $items = $sort === 'newest'
                ? $items->sortByDesc('date')
                : $items->sortBy([['score', 'desc'], ['date', 'desc']]);
            $total += $items->count();
            $out[] = ['key' => $key, 'label' => $label, 'count' => $items->count(),
                'items' => $items->take($perGroup)->map(fn ($i) => collect($i)->except(['score', 'date'])->all())->values()->all()];
        }

        return ['query' => $q, 'total' => $total, 'groups' => $out];
    }

    /** @param Collection<int, string> $terms */
    private static function match(Builder $query, Collection $terms, array $columns): void
    {
        foreach ($terms as $term) {
            $like = '%'.addcslashes($term, '%_\\').'%';
            $query->where(function (Builder $w) use ($columns, $like) {
                foreach ($columns as $col) {
                    $w->orWhere($col, 'like', $like);
                }
            });
        }
    }

    /** @param Collection<int, string> $terms */
    private static function score(Collection $terms, ?string $title, ?string $body): int
    {
        $title = Str::lower((string) $title);
        $body = Str::lower(strip_tags((string) $body));
        $score = 0;
        foreach ($terms as $t) {
            if (str_starts_with($title, $t)) {
                $score += 6;
            } elseif (str_contains($title, $t)) {
                $score += 4;
            }
            $score += min(3, substr_count($body, $t));
        }

        return $score;
    }

    private static function date(?string $v): ?string
    {
        if (! $v) {
            return null;
        }
        try {
            return Carbon::createFromFormat('Y-m-d', $v)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}
