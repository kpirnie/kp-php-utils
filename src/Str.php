<?php

/**
 * String Functions
 *
 * This is our primary string utility class
 *
 * @since      8.2
 * @author     Kevin Pirnie <me@kpirnie.com>
 * @package    KP Library
 */

declare(strict_types=1);

// namespace this class
namespace KPT;

// make sure the class does not already exist
if (! class_exists('\KPT\Str')) {

    /**
     * Str
     *
     * A modern PHP 8.2+ string utility class providing multi-needle search,
     * regex search, whole-word matching, and common string inspection helpers.
     *
     * @package    KP Library
     * @author     Kevin Pirnie <me@kpirnie.com>
     * @copyright  2026 Kevin Pirnie
     * @license    MIT
     */
    class Str
    {
        // -------------------------------------------------------------------------
        // Search
        // -------------------------------------------------------------------------

        /**
         * Check whether a string contains any of the given substrings.
         *
         * Search is case-insensitive.  Provides an 8.2-compatible fallback
         * for the PHP 8.4 array_any() function.
         *
         * @param  string  $haystack  The string to search within.
         * @param  array   $needles   Substrings to search for.
         * @return bool
         */
        public static function strContainsAny(string $haystack, array $needles): bool
        {
            // PHP 8.4+: delegate to the native function
            if (function_exists('array_any')) {
                return array_any(
                    $needles,
                    fn(string $n): bool => str_contains(strtolower($haystack), strtolower($n))
                );
            }

            // PHP 8.2 / 8.3 fallback: early-return foreach
            foreach ($needles as $needle) {
                if (str_contains(strtolower($haystack), strtolower($needle))) {
                    return true;
                }
            }

            return false;
        }

        /**
         * Check whether a string matches any of the given regex patterns.
         *
         * Patterns are matched case-insensitively.  Provides an 8.2-compatible
         * fallback for the PHP 8.4 array_any() function.
         *
         * @param  string  $haystack  The string to search within.
         * @param  array   $patterns  PCRE pattern bodies without delimiters.
         * @return bool
         */
        public static function strContainsAnyRegex(string $haystack, array $patterns): bool
        {
            // PHP 8.4+: delegate to the native function
            if (function_exists('array_any')) {
                return array_any(
                    $patterns,
                    fn(string $p): bool => (bool) preg_match('~' . $p . '~i', $haystack)
                );
            }

            // PHP 8.2 / 8.3 fallback: early-return foreach
            foreach ($patterns as $pattern) {
                if ((bool) preg_match('~' . $pattern . '~i', $haystack)) {
                    return true;
                }
            }

            return false;
        }

        /**
         * Check whether a string contains a given word.
         *
         * Uses lookahead and lookbehind assertions to match whole words only,
         * respecting common punctuation and non-ASCII characters.  A plain
         * substring check (str_contains) is intentionally not used here as
         * it would match partial words.
         *
         * @param  string  $string  The string to search within.
         * @param  string  $word    The whole word to search for.
         * @return bool
         */
        public static function containsWord(string $string, string $word): bool
        {
            // Lookahead/lookbehind boundaries cover whitespace and common punctuation
            return (bool) preg_match(
                '/(?<=[\s,.:;"\']|^)' . preg_quote($word, '/') . '(?=[\s,.:;"\']|$)/',
                $string
            );
        }

        // -------------------------------------------------------------------------
        // Inspection
        // -------------------------------------------------------------------------

        /**
         * Check whether a value is empty, null, or the literal string 'null'.
         *
         * Useful for sanitizing form input or API responses where downstream
         * services may return the string 'null' instead of a true null value.
         *
         * @param  mixed  $value
         * @return bool
         */
        public static function isEmpty(mixed $value): bool
        {
            // empty() already covers null, 0, '', [], false — the string 'null' needs explicit check
            return empty($value) || $value === 'null';
        }

        /**
         * Check whether a value is strictly blank.
         *
         * Unlike isEmpty(), this treats 0, '0', and false as non-blank.
         * Only null, the literal string 'null', and empty/whitespace-only
         * strings are considered blank.
         *
         * @param  mixed  $value
         * @return bool
         */
        public static function isBlank(mixed $value): bool
        {
            // Preserve 0, '0', and false as non-blank
            if ($value === 0 || $value === '0' || $value === false) {
                return false;
            }

            // Whitespace-only strings are blank
            if (is_string($value)) {
                return trim($value) === '' || $value === 'null';
            }

            return is_null($value);
        }
    }
}
