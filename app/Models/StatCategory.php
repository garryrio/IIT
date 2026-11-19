<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Kategori baku untuk data statistik (dikelola super admin, dipilih admin lewat dropdown). */
class StatCategory extends Model
{
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        // Saat nama kategori diganti, seluruh indikator yang memakainya ikut diperbarui.
        static::updated(function (StatCategory $c) {
            if ($c->wasChanged('name')) {
                VillageStatistic::where('category', $c->getOriginal('name'))->update(['category' => $c->name]);
            }
        });
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return static::query()->orderBy('display_order')->orderBy('name')->pluck('name', 'name')->all();
    }

    public function statisticsCount(): int
    {
        return VillageStatistic::where('category', $this->name)->count();
    }
}
