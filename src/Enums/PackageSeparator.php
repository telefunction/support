<?php

namespace Telefunction\Support\Enums;

enum PackageSeparator
{
    case Path;
    case Key;
    case Slug;

    public function separator(): string
    {
        return match ($this) {
            self::Path => '/',
            self::Key => '.',
            self::Slug => '-',
        };
    }
}
