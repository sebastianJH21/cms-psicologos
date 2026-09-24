<?php

namespace App\Support;

class PhoneHelper
{
    public static function normalize(?string $raw): ?string
    {
        if ($raw === null) {
            return null;
        }

        $trimmed = trim($raw);
        if ($trimmed === '') {
            return null;
        }

        $hasPlus = str_starts_with($trimmed, '+');
        $digits = preg_replace('/\D+/', '', $trimmed);

        if ($digits === '' || $digits === null) {
            return null;
        }

        return $hasPlus ? '+' . $digits : $digits;
    }
}
