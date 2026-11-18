<?php

namespace App\Support;

use App\Models\Facility;
use App\Models\InformationPost;
use App\Models\Potential;
use App\Models\SiteSetting;

/** Menyusun data marker peta (semua berasal dari database/CMS). */
class MapData
{
    public static function center(): array
    {
        $s = SiteSetting::current();

        return [$s->latitude ?? -6.035507, $s->longitude ?? 106.5230712];
    }

    /** @return list<array<string, mixed>> */
    public static function potentials(): array
    {
        return Potential::published()->whereNotNull('latitude')->whereNotNull('longitude')->orderBy('name')->get()
            ->map(fn (Potential $p) => [
                'layer' => 'potensi',
                'category' => $p->category,
                'categoryLabel' => $p->categoryLabel(),
                'color' => Potential::COLORS[$p->category] ?? '#2f6b3a',
                'lat' => $p->latitude,
                'lng' => $p->longitude,
                'title' => $p->name,
                'text' => $p->summary,
                'url' => $p->url(),
                'image' => $p->image_url,
            ])->all();
    }

    /** @return list<array<string, mixed>> */
    public static function facilities(): array
    {
        return Facility::published()->whereNotNull('latitude')->whereNotNull('longitude')->orderBy('display_order')->get()
            ->map(fn (Facility $f) => [
                'layer' => 'fasilitas',
                'category' => $f->type,
                'categoryLabel' => $f->typeLabel(),
                'color' => Facility::COLORS[$f->type] ?? '#1f4d2b',
                'lat' => $f->latitude,
                'lng' => $f->longitude,
                'title' => $f->name,
                'text' => Html::excerpt($f->description, 110),
                'url' => route('profile').'#fasilitas',
                'image' => $f->image_url,
            ])->all();
    }

    /** @return list<array<string, mixed>> */
    public static function events(): array
    {
        return InformationPost::upcomingEvents()->whereNotNull('latitude')->whereNotNull('longitude')->limit(20)->get()
            ->map(fn (InformationPost $p) => [
                'layer' => 'agenda',
                'category' => 'agenda',
                'categoryLabel' => 'Agenda · '.$p->event_date?->translatedFormat('d M Y'),
                'color' => '#c48a12',
                'lat' => $p->latitude,
                'lng' => $p->longitude,
                'title' => $p->title,
                'text' => $p->location,
                'url' => $p->url(),
                'image' => $p->cover_image_url,
            ])->all();
    }
}
