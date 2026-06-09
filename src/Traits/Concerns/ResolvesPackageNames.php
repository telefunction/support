<?php

namespace Telefunction\Support\Traits\Concerns;

use Illuminate\Support\Str;
use Telefunction\Support\Enums\PackageSeparator;

trait ResolvesPackageNames
{
    /**
     * @return array<int, string>
     */
    final public static function namespaceParts(): array
    {
        return explode('\\', static::class);
    }

    final public static function vendorName(bool $raw = false): string
    {
        return static::namePart(0, 'Vendor', $raw);
    }

    final public static function packageName(bool $raw = false): string
    {
        return static::namePart(1, 'Package', $raw);
    }

    final public static function className(bool $raw = false): string
    {
        $class = class_basename(static::class);

        return $raw ? $class : Str::kebab($class);
    }

    /**
     * @param array<string> $segments
     */
    final public static function packageIdentifier(
        PackageSeparator $separator,
        ?string $root = null,
        ?string $package = null,
        array $segments = []
    ): string
    {
        return implode($separator->separator(), [
            $root ?? static::vendorName(),
            $package ?? static::packageName(),
            ...$segments
        ]);
    }

    final public static function namePart(int $index, string $fallback, bool $raw = false): string
    {
        $value = static::namespaceParts()[$index] ?? $fallback;

        return $raw ? $value : Str::kebab($value);
    }
}
