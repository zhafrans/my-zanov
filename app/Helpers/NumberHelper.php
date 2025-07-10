<?php

namespace App\Helpers;

class NumberHelper
{
    public static function decimal(int|float $value, bool $shouldRounded = false): string
    {
        return number_format(
            num: $shouldRounded ? explode('.', (string) $value)[0] : $value,
            decimals: 2,
            thousands_separator: ''
        );
    }

    public static function randomDigit(int $digit): string
    {
        $number = '';

        for ($i = 1; $i <= $digit; $i++) {
            $number .= rand(0, 9);
        }

        return $number;
    }

    public static function roman(int $number): string
    {
        $map = [
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1,
        ];

        $result = '';

        foreach ($map as $roman => $value) {
            $matches = intval($number / $value);
            $result .= str_repeat($roman, $matches);
            $number = $number % $value;
        }

        return $result;
    }
}
