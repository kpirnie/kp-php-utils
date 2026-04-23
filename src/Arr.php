<?php

/**
 * Array Functions
 *
 * This is our primary array utility class
 *
 * @since      8.2
 * @author     Kevin Pirnie <me@kpirnie.com>
 * @package    KP Library
 */

declare(strict_types=1);

// namespace this class
namespace KPT;

// make sure the class does not already exist
if (! class_exists('\KPT\Arr')) {

    /**
     * Arr
     *
     * A modern PHP 8.2+ array utility class providing multi-needle search,
     * key subset matching, multi-dimensional sorting, and object conversion.
     *
     * @package    KP Library
     * @author     Kevin Pirnie <me@kpirnie.com>
     * @copyright  2026 Kevin Pirnie
     * @license    MIT
     */
    class Arr
    {
        // -------------------------------------------------------------------------
        // Search
        // -------------------------------------------------------------------------

        /**
         * Check whether any element in an array contains the given substring.
         *
         * Search is case-insensitive and uses partial matching — an element
         * matches if it contains the needle anywhere within it.  Provides an
         * 8.2-compatible fallback for the PHP 8.4 array_any() function.
         *
         * @param  string  $needle    The substring to search for.
         * @param  array   $haystack  The array to search within.
         * @return bool
         */
        public static function findInArray(string $needle, array $haystack): bool
        {
            // PHP 8.4+: delegate to the native function
            if (function_exists('array_any')) {
                return array_any(
                    $haystack,
                    fn(mixed $item): bool => stripos((string) $item, $needle) !== false
                );
            }

            // PHP 8.2 / 8.3 fallback: early-return foreach
            foreach ($haystack as $item) {
                if (stripos((string) $item, $needle) !== false) {
                    return true;
                }
            }

            return false;
        }

        /**
         * Find the first array element whose key contains a given substring.
         *
         * Provides an 8.2-compatible fallback for the PHP 8.4 array_find_key()
         * function.  Returns the matched element's value, or false when no key
         * contains the subset.
         *
         * @param  array   $array          The array to search.
         * @param  string  $subset         The substring to look for in keys.
         * @param  bool    $caseSensitive  Whether the key search is case-sensitive.
         * @return mixed                   The matched element value, or false.
         */
        public static function arrayKeyContainsSubset(
            array $array,
            string $subset,
            bool $caseSensitive = true
        ): mixed {
            if (empty($array)) {
                return false;
            }

            // PHP 8.4+: delegate to the native function
            if (function_exists('array_find_key')) {
                $matchingKey = array_find_key(
                    $array,
                    fn(mixed $value, mixed $key): bool => $caseSensitive
                        ? str_contains((string) $key, $subset)
                        : stripos((string) $key, $subset) !== false
                );

                return $matchingKey !== null ? $array[$matchingKey] : false;
            }

            // PHP 8.2 / 8.3 fallback: early-return foreach over keys
            foreach (array_keys($array) as $key) {
                $matched = $caseSensitive
                    ? str_contains((string) $key, $subset)
                    : stripos((string) $key, $subset) !== false;

                if ($matched) {
                    return $array[$key];
                }
            }

            return false;
        }

        // -------------------------------------------------------------------------
        // Sorting
        // -------------------------------------------------------------------------

        /**
         * Sort a multi-dimensional array by a shared subkey.
         *
         * Numeric subkey values use spaceship comparison; all other values
         * are compared case-insensitively as strings.  The array is sorted
         * in place via usort().
         *
         * @param  array   &$array   The array to sort (modified in place).
         * @param  string  $subkey   The key to sort by (default 'id').
         * @param  bool    $sortAsc  True for ascending, false for descending.
         * @return void
         */
        public static function sortMultiDim(array &$array, string $subkey = 'id', bool $sortAsc = true): void
        {
            usort($array, function (mixed $a, mixed $b) use ($subkey): int {
                $valA = $a[$subkey] ?? null;
                $valB = $b[$subkey] ?? null;

                // Use numeric spaceship comparison when both values are numeric
                if (is_numeric($valA) && is_numeric($valB)) {
                    return $valA <=> $valB;
                }

                // Fall back to case-insensitive string comparison
                return strcasecmp((string) $valA, (string) $valB);
            });

            // Reverse after sort rather than during to keep the comparator simple
            if (! $sortAsc) {
                $array = array_reverse($array);
            }
        }

        // -------------------------------------------------------------------------
        // Conversion
        // -------------------------------------------------------------------------

        /**
         * Recursively convert an object (or nested objects) to an array.
         *
         * Arrays nested within the object are walked recursively so that any
         * objects they contain are also converted.
         *
         * @param  object  $value  The object to convert.
         * @return array
         */
        public static function objectToArray(object $value): array
        {
            $result = [];

            foreach ($value as $key => $val) {
                if (is_object($val)) {
                    // Recurse directly into nested objects
                    $result[$key] = self::objectToArray($val);
                } elseif (is_array($val)) {
                    // Walk nested arrays so any objects inside are also converted
                    $result[$key] = array_map(
                        fn(mixed $item): mixed => is_object($item) ? self::objectToArray($item) : $item,
                        $val
                    );
                } else {
                    $result[$key] = $val;
                }
            }

            return $result;
        }
    }
}
