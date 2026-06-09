<?php

namespace Telefunction\Support\Traits\Concerns;

use InvalidArgumentException;

trait ConvertsAlphabetIndexes
{
    final protected function numberToLetters(int $number): string
    {
        if ($number < 1) {
            throw new InvalidArgumentException('Number must be greater than or equal to 1.');
        }

        $result = '';

        while ($number > 0) {
            $number--;
            $result = chr(65 + ($number % 26)) . $result;
            $number = intdiv($number, 26);
        }

        return $result;
    }

    final protected function lettersToNumber(string $letters): int
    {
        $letters = strtoupper(trim($letters));

        if ($letters === '' || ! preg_match('/^[A-Z]+$/', $letters)) {
            throw new InvalidArgumentException('Letters must contain only A-Z characters.');
        }

        $result = 0;

        foreach (str_split($letters) as $char) {
            $result = ($result * 26) + (ord($char) - 64);
        }

        return $result;
    }
}
