<?php

/**
 * DateTime Functions
 *
 * This is our primary date and time utility class
 *
 * @since      8.2
 * @author     Kevin Pirnie <me@kpirnie.com>
 * @package    KP Library
 */

declare(strict_types=1);

// namespace this class
namespace KPT;

// make sure the class does not already exist
if (! class_exists('\KPT\DateTime')) {

    /**
     * DateTime
     *
     * A modern PHP 8.2+ date and time utility class providing human-readable
     * time differences and WordPress-compatible time constants.
     *
     * @package    KP Library
     * @author     Kevin Pirnie <me@kpirnie.com>
     * @copyright  2026 Kevin Pirnie
     * @license    MIT
     */
    class DateTime
    {
        // -------------------------------------------------------------------------
        // Time constants
        // -------------------------------------------------------------------------

        /** Seconds in one minute */
        const MINUTE_IN_SECONDS = 60;

        /** Seconds in one hour */
        const HOUR_IN_SECONDS = self::MINUTE_IN_SECONDS * 60;

        /** Seconds in one day */
        const DAY_IN_SECONDS = self::HOUR_IN_SECONDS * 24;

        /** Seconds in one week */
        const WEEK_IN_SECONDS = self::DAY_IN_SECONDS * 7;

        /** Seconds in one month (30 days) */
        const MONTH_IN_SECONDS = self::DAY_IN_SECONDS * 30;

        /** Seconds in one year (365 days) */
        const YEAR_IN_SECONDS = self::DAY_IN_SECONDS * 365;

        // -------------------------------------------------------------------------
        // Formatting
        // -------------------------------------------------------------------------

        /**
         * Return a human-readable "time ago" string for a given datetime.
         *
         * Resolves differences from seconds through years, with singular/plural
         * labels.  Falls back to a formatted date string for differences beyond
         * one year.  Returns an empty string when the input cannot be parsed.
         *
         * @param  string  $datetime   Any datetime string parseable by strtotime().
         * @param  string  $fallback   Date format used when diff exceeds one year.
         * @return string
         */
        public static function timeAgo(string $datetime, string $fallback = 'M j, Y'): string
        {
            $time = strtotime($datetime);

            // strtotime returns false for unparseable input
            if ($time === false) {
                return '';
            }

            $diff = time() - $time;

            // Seconds
            if ($diff < self::MINUTE_IN_SECONDS) {
                $n = $diff;
                return $n . ' ' . ($n === 1 ? 'Second' : 'Seconds') . ' Ago';
            }

            // Minutes
            if ($diff < self::HOUR_IN_SECONDS) {
                $n = (int) floor($diff / self::MINUTE_IN_SECONDS);
                return $n . ' ' . ($n === 1 ? 'Minute' : 'Minutes') . ' Ago';
            }

            // Hours
            if ($diff < self::DAY_IN_SECONDS) {
                $n = (int) floor($diff / self::HOUR_IN_SECONDS);
                return $n . ' ' . ($n === 1 ? 'Hour' : 'Hours') . ' Ago';
            }

            // Days
            if ($diff < self::WEEK_IN_SECONDS) {
                $n = (int) floor($diff / self::DAY_IN_SECONDS);
                return $n . ' ' . ($n === 1 ? 'Day' : 'Days') . ' Ago';
            }

            // Weeks
            if ($diff < self::MONTH_IN_SECONDS) {
                $n = (int) floor($diff / self::WEEK_IN_SECONDS);
                return $n . ' ' . ($n === 1 ? 'Week' : 'Weeks') . ' Ago';
            }

            // Months
            if ($diff < self::YEAR_IN_SECONDS) {
                $n = (int) floor($diff / self::MONTH_IN_SECONDS);
                return $n . ' ' . ($n === 1 ? 'Month' : 'Months') . ' Ago';
            }

            // Beyond a year — fall back to a formatted date string
            return (new \DateTimeImmutable('@' . $time))->format($fallback);
        }
    }
}
