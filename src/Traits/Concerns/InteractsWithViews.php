<?php

namespace Telefunction\Support\Traits\Concerns;

use Illuminate\Support\Str;
use Illuminate\Contracts\View\{Factory, View};
use Telefunction\Support\Enums\PackageSeparator;

trait InteractsWithViews
{
    use ResolvesPackageNames;

    protected function view($view, $data, $mergeData): Factory|View
    {
        return view(static::viewKey($view), $data, $mergeData);
    }

    protected static function viewKey(string $key): string
    {
        return Str::contains($key, '::')
            ? $key
            : static::packageIdentifier(PackageSeparator::Slug) . '::' . $key;
    }
}
