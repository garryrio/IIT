<?php

namespace App\Admin;

class Registry
{
    /** @return array<string, class-string<resource>> */
    public static function all(): array
    {
        return collect([
            Resources\SettingResource::class,
            Resources\ProfileResource::class,
            Resources\OfficialResource::class,
            Resources\FacilityResource::class,
            Resources\StatisticResource::class,
            Resources\PostResource::class,
            Resources\PotentialResource::class,
            Resources\GalleryResource::class,
            Resources\StoryResource::class,
            Resources\IncomeClassResource::class,
            Resources\HouseholdResource::class,
        ])->mapWithKeys(fn ($c) => [$c::$slug => $c])->all();
    }

    /** @return class-string<resource> */
    public static function find(string $slug): string
    {
        return self::all()[$slug] ?? abort(404);
    }
}
