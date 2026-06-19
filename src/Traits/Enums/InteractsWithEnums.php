<?php

namespace Telefunction\Support\Traits\Enums;

use BackedEnum;
use InvalidArgumentException;
use UnitEnum;

/**
 * @mixin BackedEnum
 */
trait InteractsWithEnums
{
    public static function values(): array
    {
        return array_map(
            static fn (BackedEnum $case): int|string => $case->value,
            self::cases()
        );
    }

    public static function names(): array
    {
        return array_map(
            static fn (UnitEnum $case): string => $case->name,
            self::cases()
        );
    }

    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = method_exists($case, 'label')
                ? $case->label()
                : $case->name;
        }

        return $options;
    }

    public static function toArray(): array
    {
        return self::values();
    }

    public static function hasValue(int|string|BackedEnum $value): bool
    {
        return self::fromValue($value) !== null;
    }

    public static function fromValue(int|string|BackedEnum $value): ?self
    {
        if ($value instanceof BackedEnum) {
            $value = $value->value;
        }

        return self::tryFrom($value);
    }

    public static function fromName(string $name): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }

        return null;
    }

    public static function hasName(string $name): bool
    {
        return self::fromName($name) !== null;
    }

    public static function random(): self
    {
        $cases = self::cases();

        return $cases[array_rand($cases)];
    }

    public static function first(): self
    {
        return self::cases()[0];
    }

    public static function last(): self
    {
        $cases = self::cases();

        return $cases[array_key_last($cases)];
    }

    public static function count(): int
    {
        return count(self::cases());
    }

    public function label(): string
    {
        return $this->name;
    }

    public function toString(): string
    {
        return (string) $this->value;
    }

    public function equals(self|BackedEnum $other): bool
    {
        return $this === $other;
    }

    public function in(self|BackedEnum ...$cases): bool
    {
        foreach ($cases as $case) {
            if ($this->equals($case)) {
                return true;
            }
        }

        return false;
    }

    public function is(self|BackedEnum $case): bool
    {
        return $this->equals($case);
    }

    public function isNot(self|BackedEnum $case): bool
    {
        return ! $this->is($case);
    }

    public function throwIfNot(self|BackedEnum ...$cases): self
    {
        if (! $this->in(...$cases)) {
            throw new InvalidArgumentException(sprintf(
                'Enum case [%s] is not allowed.',
                $this->name
            ));
        }

        return $this;
    }

    public static function validationRule(): string
    {
        return 'in:'.implode(',', self::values());
    }
}
