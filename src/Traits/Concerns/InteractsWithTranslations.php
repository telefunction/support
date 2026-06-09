<?php

namespace Telefunction\Support\Traits\Concerns;

use Illuminate\Support\Str;
use Telefunction\Support\Enums\PackageSeparator;

trait InteractsWithTranslations
{
    use ResolvesPackageNames;

    final protected function translate(
        string $key,
        array $replace = [],
        ?string $locale = null
    ): array|string|null {
        return static::trans($key, $replace, $locale);
    }

    final protected static function trans(
        string $key,
        array $replace = [],
        ?string $locale = null
    ): array|string|null {
        return __(
            static::translationKey($key),
            $replace,
            $locale
        );
    }

    final protected static function translationKey(string $key): string
    {
        return Str::contains($key, '::')
            ? $key
            : static::packageIdentifier(PackageSeparator::Slug) . '::' . $key;
    }
}
