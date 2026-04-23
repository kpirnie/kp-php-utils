<?php

/**
 * Number Functions
 *
 * This is our primary number utility class
 *
 * @since      8.2
 * @author     Kevin Pirnie <me@kpirnie.com>
 * @package    KP Library
 */

declare(strict_types=1);

// namespace this class
namespace KPT;

// make sure the class does not already exist
if (! class_exists('\KPT\Num')) {

    /**
     * Num
     *
     * A modern PHP 8.2+ number utility class providing ordinal formatting
     * and human-readable byte conversion.
     *
     * @package    KP Library
     * @author     Kevin Pirnie <me@kpirnie.com>
     * @copyright  2026 Kevin Pirnie
     * @license    MIT
     */
    class Num
    {
        // -------------------------------------------------------------------------
        // Formatting
        // -------------------------------------------------------------------------

        /**
         * Format an integer as an ordinal number string.
         *
         * Correctly handles the 11th/12th/13th edge cases that naive
         * modulo-10 implementations get wrong.
         *
         * Examples: 1 → '1st', 11 → '11th', 22 → '22nd', 103 → '103rd'
         *
         * @param  int  $value  The number to format.
         * @return string
         */
        public static function ordinal(int $value): string
        {
            $abs    = abs($value);
            $mod100 = $abs % 100;
            $mod10  = $abs % 10;

            // 11, 12, 13 are exceptions — they always use 'th' regardless of mod10
            $suffix = match (true) {
                $mod100 >= 11 && $mod100 <= 13 => 'th',
                $mod10 === 1                    => 'st',
                $mod10 === 2                    => 'nd',
                $mod10 === 3                    => 'rd',
                default                         => 'th',
            };

            return $value . $suffix;
        }

        /**
         * Format a byte count as a human-readable string.
         *
         * Scales automatically from bytes through to petabytes.
         * Returns '0 B' for zero or negative input.
         *
         * Examples: 1024 → '1 KB', 1536 → '1.5 KB', 1073741824 → '1 GB'
         *
         * @param  int  $size       Size in bytes.
         * @param  int  $precision  Decimal places in the output (default 2).
         * @return string
         */
        public static function formatBytes(int $size, int $precision = 2): string
        {
            if ($size <= 0) {
                return '0 B';
            }

            $suffixes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
            $base     = log($size, 1024);
            $index    = (int) min(floor($base), count($suffixes) - 1);

            return round(pow(1024, $base - $index), $precision) . ' ' . $suffixes[$index];
        }
    }
}
