<?php

/*
|--------------------------------------------------------------------------
| Mbstring Fallback Functions
|--------------------------------------------------------------------------
|
| Pure-PHP fallbacks for the few mbstring functions that are not covered
| by symfony/polyfill-mbstring. They are only defined when the runtime
| (e.g. the php-fpm pool) does not have ext-mbstring loaded, keeping
| features like rendering verification emails working everywhere.
|
*/

if (! function_exists('mb_strcut')) {
    /**
     * Cut a string by byte length without splitting multi-byte characters.
     */
    function mb_strcut(string $string, int $start, ?int $length = null, ?string $encoding = null): string
    {
        $bytes = strlen($string);

        if ($start < 0) {
            $start = max(0, $bytes + $start);
        } elseif ($start > $bytes) {
            return '';
        }

        if ($length === null) {
            $length = $bytes - $start;
        } elseif ($length < 0) {
            $length = max(0, $bytes - $start + $length);
        }

        $slice = substr($string, $start, $length);

        while ($slice !== '' && ! preg_match('//u', $slice)) {
            $slice = substr($slice, 0, -1);
        }

        return $slice;
    }
}

if (! function_exists('mb_strimwidth')) {
    /**
     * Trim a string to a given display width, appending a marker when truncated.
     */
    function mb_strimwidth(string $string, int $start, int $width, string $trim_marker = '', ?string $encoding = null): string
    {
        if ($width <= 0) {
            return '';
        }

        $chars = preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY);

        if ($chars === false) {
            $chars = str_split($string);
        }

        if ($start < 0) {
            $start = max(0, count($chars) + $start);
        }

        $chars = array_slice($chars, $start);

        $markerWidth = 0;

        foreach (preg_split('//u', $trim_marker, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $char) {
            $markerWidth += mb_strimwidth_char_width($char);
        }

        $result = '';
        $usedWidth = 0;
        $limit = max(0, $width - $markerWidth);

        foreach ($chars as $char) {
            $charWidth = mb_strimwidth_char_width($char);

            if ($usedWidth + $charWidth > $limit) {
                return $result.$trim_marker;
            }

            $result .= $char;
            $usedWidth += $charWidth;
        }

        return $result;
    }
}

if (! function_exists('mb_strimwidth_char_width')) {
    /**
     * Approximate the terminal display width of a single UTF-8 character.
     */
    function mb_strimwidth_char_width(string $char): int
    {
        static $decoder;

        $codepoint = ($decoder ??= function (string $char): int {
            $byte = ord($char[0]);
            $length = strlen($char);

            if ($length === 1) {
                return $byte;
            }

            if ($length === 2) {
                return ($byte & 0x1F) << 6 | (ord($char[1]) & 0x3F);
            }

            if ($length === 3) {
                return ($byte & 0x0F) << 12 | (ord($char[1]) & 0x3F) << 6 | (ord($char[2]) & 0x3F);
            }

            return ($byte & 0x07) << 18 | (ord($char[1]) & 0x3F) << 12 | (ord($char[2]) & 0x3F) << 6 | (ord($char[3]) & 0x3F);
        })($char);

        return match (true) {
            $codepoint >= 0x1100 && $codepoint <= 0x115F,
            $codepoint >= 0x2E80 && $codepoint <= 0xA4CF,
            $codepoint >= 0xAC00 && $codepoint <= 0xD7A3,
            $codepoint >= 0xF900 && $codepoint <= 0xFAFF,
            $codepoint >= 0xFE30 && $codepoint <= 0xFE4F,
            $codepoint >= 0xFF00 && $codepoint <= 0xFF60,
            $codepoint >= 0xFFE0 && $codepoint <= 0xFFE6,
            $codepoint >= 0x20000 && $codepoint <= 0x3FFFD => 2,
            default => 1,
        };
    }
}
