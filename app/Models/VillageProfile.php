<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VillageProfile extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['area_hectare' => 'float'];
    }

    public static function current(): ?self
    {
        return once(fn () => static::query()->first());
    }

    /** @return list<string> */
    public function missionList(): array
    {
        $text = preg_replace('/<\/(p|li|div)>|<br\s*\/?>/i', "\n", (string) $this->mission);

        return array_values(array_filter(array_map(
            fn ($l) => trim(html_entity_decode(preg_replace('/^\s*(\d+[\.\)]|[-*•])\s*/u', '', $l))),
            preg_split('/\r?\n/', strip_tags($text))
        )));
    }
}
