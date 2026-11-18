<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasStatus
{
    public function scopePublished(Builder $query): Builder
    {
        return $query->where($this->getTable().'.status', 'published');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}
