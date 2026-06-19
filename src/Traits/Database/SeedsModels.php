<?php

namespace Telefunction\Support\Traits\Database;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

/**
 * @mixin Seeder
 */
trait SeedsModels
{
    /**
     * @return array<class-string<Model>, array<int, array<string, mixed>>>
     */
    protected static function seeds(): array
    {
        return [];
    }

    protected static function forceSeed(): bool
    {
        return false;
    }

    public function run(): void
    {
        foreach (static::seeds() as $model => $records) {
            $this->seedModel($model, $records);
        }
    }

    /**
     * @param  class-string<Model>  $model
     * @param  array<int, array<string, mixed>>  $records
     */
    protected function seedModel(string $model, array $records): void
    {
        foreach ($records as $attributes) {
            static::forceSeed()
                ? $model::query()->forceCreate($attributes)
                : $model::query()->create($attributes);
        }
    }
}
