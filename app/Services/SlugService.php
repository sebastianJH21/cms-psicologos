<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SlugService
{
    public function uniqueSlug(string $base, string $modelClass, string $column = 'slug', ?int $ignoreId = null): string
    {
        $slug = Str::slug($base);
        if ($slug === '') {
            $slug = 'item';
        }

        $original = $slug;
        $i = 2;

        while ($this->exists($modelClass, $column, $slug, $ignoreId)) {
            $slug = $original . '-' . $i;
            $i++;
        }

        return $slug;
    }

    private function exists(string $modelClass, string $column, string $slug, ?int $ignoreId): bool
    {
        /** @var Model $modelClass */
        $query = $modelClass::query()->where($column, $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }
        return $query->exists();
    }
}
