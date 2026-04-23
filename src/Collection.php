<?php

/**
 * Collection
 *
 * A fluent, immutable array wrapper
 *
 * @since      8.2
 * @author     Kevin Pirnie <me@kpirnie.com>
 * @package    KP Library
 */

declare(strict_types=1);

// namespace this class
namespace KPT;

// make sure the class does not already exist
if (! class_exists('\KPT\Collection')) {

    /**
     * Collection
     *
     * A modern PHP 8.2+ immutable fluent array wrapper implementing Countable,
     * IteratorAggregate, and ArrayAccess.  All transformation methods return a
     * new Collection instance — the original is never modified.
     *
     * @package    KP Library
     * @author     Kevin Pirnie <me@kpirnie.com>
     * @copyright  2026 Kevin Pirnie
     * @license    MIT
     */
    class Collection implements \Countable, \IteratorAggregate, \ArrayAccess
    {
        // -------------------------------------------------------------------------
        // Internal state
        // -------------------------------------------------------------------------

        /** @var array The wrapped items */
        private array $items;

        /**
         * Private constructor — use Collection::make() to instantiate.
         *
         * @param  array  $items
         */
        private function __construct(array $items = [])
        {
            $this->items = $items;
        }

        // -------------------------------------------------------------------------
        // Factory
        // -------------------------------------------------------------------------

        /**
         * Create a new Collection from an array.
         *
         * @param  array  $items
         * @return static
         */
        public static function make(array $items = []): static
        {
            return new static($items);
        }

        // -------------------------------------------------------------------------
        // Transformation — each returns a new Collection
        // -------------------------------------------------------------------------

        /**
         * Filter items through a callback.
         *
         * When no callback is provided, removes all falsy values.
         *
         * @param  callable|null  $callback  fn(mixed $value, mixed $key): bool
         * @return static
         */
        public function filter(?callable $callback = null): static
        {
            return new static(
                $callback
                    ? array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH)
                    : array_filter($this->items)
            );
        }

        /**
         * Apply a callback to every item.
         *
         * @param  callable  $callback  fn(mixed $value, mixed $key): mixed
         * @return static
         */
        public function map(callable $callback): static
        {
            return new static(array_map($callback, $this->items, array_keys($this->items)));
        }

        /**
         * Reduce the collection to a single value.
         *
         * @param  callable  $callback  fn(mixed $carry, mixed $item): mixed
         * @param  mixed     $initial
         * @return mixed
         */
        public function reduce(callable $callback, mixed $initial = null): mixed
        {
            return array_reduce($this->items, $callback, $initial);
        }

        /**
         * Chunk the collection into smaller Collections of a given size.
         *
         * Returns a Collection of Collections.
         *
         * @param  int  $size
         * @return static
         */
        public function chunk(int $size): static
        {
            return new static(array_map(
                fn(array $chunk): static => new static($chunk),
                array_chunk($this->items, max(1, $size), true)
            ));
        }

        /**
         * Group items into Collections keyed by a field or callback result.
         *
         * Returns a Collection of Collections.
         *
         * @param  string|callable  $key  Field name or fn(mixed $value): mixed
         * @return static
         */
        public function groupBy(string|callable $key): static
        {
            $groups = [];

            foreach ($this->items as $item) {
                $groupKey = is_callable($key) ? $key($item) : (is_array($item) ? $item[$key] : $item->$key);

                $groups[$groupKey][] = $item;
            }

            return new static(array_map(fn(array $group): static => new static($group), $groups));
        }

        /**
         * Pluck values for a given field, optionally keyed by another field.
         *
         * @param  string       $value  Field to pluck as the value.
         * @param  string|null  $key    Field to use as the key (optional).
         * @return static
         */
        public function pluck(string $value, ?string $key = null): static
        {
            $results = [];

            foreach ($this->items as $item) {
                $val = is_array($item) ? $item[$value] : $item->$value;

                if ($key !== null) {
                    $k           = is_array($item) ? $item[$key] : $item->$key;
                    $results[$k] = $val;
                } else {
                    $results[] = $val;
                }
            }

            return new static($results);
        }

        /**
         * Sort the collection using an optional callback.
         *
         * When no callback is provided, uses the default PHP comparison.
         *
         * @param  callable|null  $callback  fn(mixed $a, mixed $b): int
         * @return static
         */
        public function sort(?callable $callback = null): static
        {
            $items = $this->items;

            $callback ? usort($items, $callback) : sort($items);

            return new static($items);
        }

        /**
         * Sort the collection by a field value.
         *
         * @param  string  $key        The field to sort by.
         * @param  bool    $ascending  True for ASC, false for DESC.
         * @return static
         */
        public function sortBy(string $key, bool $ascending = true): static
        {
            $items = $this->items;

            usort($items, function (mixed $a, mixed $b) use ($key): int {
                $valA = is_array($a) ? $a[$key] : $a->$key;
                $valB = is_array($b) ? $b[$key] : $b->$key;

                return is_numeric($valA) && is_numeric($valB)
                    ? $valA <=> $valB
                    : strcasecmp((string) $valA, (string) $valB);
            });

            return new static($ascending ? $items : array_reverse($items));
        }

        /**
         * Reverse the order of items.
         *
         * @return static
         */
        public function reverse(): static
        {
            return new static(array_reverse($this->items, true));
        }

        /**
         * Return only unique items, optionally de-duplicated by field.
         *
         * @param  string|null  $key  Field to use for uniqueness comparison.
         * @return static
         */
        public function unique(?string $key = null): static
        {
            if ($key === null) {
                return new static(array_unique($this->items));
            }

            $seen  = [];
            $items = [];

            foreach ($this->items as $k => $item) {
                $val = is_array($item) ? $item[$key] : $item->$key;

                if (! in_array($val, $seen, true)) {
                    $seen[]   = $val;
                    $items[$k] = $item;
                }
            }

            return new static($items);
        }

        /**
         * Flatten nested arrays to a single level or to a given depth.
         *
         * @param  float  $depth  Depth limit (INF for full flattening).
         * @return static
         */
        public function flatten(float $depth = INF): static
        {
            return new static(self::flattenArray($this->items, $depth));
        }

        /**
         * Merge additional items into a new Collection.
         *
         * @param  array|self  $items
         * @return static
         */
        public function merge(array|self $items): static
        {
            return new static(array_merge(
                $this->items,
                $items instanceof self ? $items->toArray() : $items
            ));
        }

        /**
         * Return a new Collection with one or more values appended.
         *
         * @param  mixed  ...$values
         * @return static
         */
        public function push(mixed ...$values): static
        {
            return new static(array_merge($this->items, $values));
        }

        /**
         * Return a new Collection with a value prepended.
         *
         * @param  mixed  $value
         * @param  mixed  $key    Optional key for associative prepend.
         * @return static
         */
        public function prepend(mixed $value, mixed $key = null): static
        {
            $items = $this->items;

            if ($key !== null) {
                $items = [$key => $value] + $items;
            } else {
                array_unshift($items, $value);
            }

            return new static($items);
        }

        /**
         * Take the first N items.
         *
         * @param  int  $limit
         * @return static
         */
        public function take(int $limit): static
        {
            return new static(array_slice($this->items, 0, $limit, true));
        }

        /**
         * Skip the first N items.
         *
         * @param  int  $offset
         * @return static
         */
        public function skip(int $offset): static
        {
            return new static(array_slice($this->items, $offset, null, true));
        }

        /**
         * Return a new Collection with keys reset to sequential integers.
         *
         * @return static
         */
        public function values(): static
        {
            return new static(array_values($this->items));
        }

        /**
         * Return a new Collection containing only the keys.
         *
         * @return static
         */
        public function keys(): static
        {
            return new static(array_keys($this->items));
        }

        /**
         * Return only the items whose keys are in the given list.
         *
         * @param  array  $keys
         * @return static
         */
        public function only(array $keys): static
        {
            return new static(array_intersect_key($this->items, array_flip($keys)));
        }

        /**
         * Return all items except those whose keys are in the given list.
         *
         * @param  array  $keys
         * @return static
         */
        public function except(array $keys): static
        {
            return new static(array_diff_key($this->items, array_flip($keys)));
        }

        /**
         * Filter items where a field equals a given value.
         *
         * @param  string  $key
         * @param  mixed   $value
         * @param  bool    $strict  Use strict comparison.
         * @return static
         */
        public function where(string $key, mixed $value, bool $strict = false): static
        {
            return $this->filter(function (mixed $item) use ($key, $value, $strict): bool {
                $field = is_array($item) ? ($item[$key] ?? null) : ($item->$key ?? null);

                return $strict ? $field === $value : $field == $value;
            });
        }

        /**
         * Zip the collection together with one or more arrays.
         *
         * Each item in the result is a Collection of corresponding elements.
         *
         * @param  array  ...$arrays
         * @return static
         */
        public function zip(array ...$arrays): static
        {
            $zipped = array_map(
                fn(mixed $item, mixed ...$rest): static => new static([$item, ...$rest]),
                $this->items,
                ...$arrays
            );

            return new static($zipped);
        }

        // -------------------------------------------------------------------------
        // Aggregation — return scalar / mixed values
        // -------------------------------------------------------------------------

        /**
         * Get the first item, or the first item matching a callback.
         *
         * @param  callable|null  $callback  fn(mixed $value, mixed $key): bool
         * @param  mixed          $default   Returned when no match is found.
         * @return mixed
         */
        public function first(?callable $callback = null, mixed $default = null): mixed
        {
            if ($callback === null) {
                return ! empty($this->items) ? reset($this->items) : $default;
            }

            foreach ($this->items as $key => $item) {
                if ($callback($item, $key)) {
                    return $item;
                }
            }

            return $default;
        }

        /**
         * Get the last item, or the last item matching a callback.
         *
         * @param  callable|null  $callback  fn(mixed $value, mixed $key): bool
         * @param  mixed          $default   Returned when no match is found.
         * @return mixed
         */
        public function last(?callable $callback = null, mixed $default = null): mixed
        {
            if ($callback === null) {
                return ! empty($this->items) ? end($this->items) : $default;
            }

            $match = $default;

            foreach ($this->items as $key => $item) {
                if ($callback($item, $key)) {
                    $match = $item;
                }
            }

            return $match;
        }

        /**
         * Sum the values, or the values of a given field.
         *
         * @param  string|callable|null  $key
         * @return int|float
         */
        public function sum(string|callable|null $key = null): int|float
        {
            return array_sum($this->resolveValues($key));
        }

        /**
         * Average the values, or the values of a given field.
         *
         * Returns 0.0 when the collection is empty.
         *
         * @param  string|callable|null  $key
         * @return float
         */
        public function avg(string|callable|null $key = null): float
        {
            $count = $this->count();

            return $count > 0 ? $this->sum($key) / $count : 0.0;
        }

        /**
         * Get the minimum value, or the minimum value of a given field.
         *
         * @param  string|callable|null  $key
         * @return mixed
         */
        public function min(string|callable|null $key = null): mixed
        {
            return min($this->resolveValues($key));
        }

        /**
         * Get the maximum value, or the maximum value of a given field.
         *
         * @param  string|callable|null  $key
         * @return mixed
         */
        public function max(string|callable|null $key = null): mixed
        {
            return max($this->resolveValues($key));
        }

        /**
         * Check whether the collection contains a value or a matching item.
         *
         * @param  mixed        $value  A plain value or callable fn(mixed $item): bool
         * @param  string|null  $key    Optionally check a field rather than the whole item.
         * @return bool
         */
        public function contains(mixed $value, ?string $key = null): bool
        {
            if (is_callable($value)) {
                foreach ($this->items as $k => $item) {
                    if ($value($item, $k)) {
                        return true;
                    }
                }

                return false;
            }

            if ($key !== null) {
                return $this->pluck($key)->contains($value);
            }

            return in_array($value, $this->items, true);
        }

        /**
         * Apply a callback to each item for side effects.
         *
         * Returns the same Collection to allow continued chaining.
         * Returning false from the callback stops iteration.
         *
         * @param  callable  $callback  fn(mixed $value, mixed $key): mixed
         * @return static
         */
        public function each(callable $callback): static
        {
            foreach ($this->items as $key => $item) {
                if ($callback($item, $key) === false) {
                    break;
                }
            }

            return $this;
        }

        /**
         * Check whether the collection is empty.
         *
         * @return bool
         */
        public function isEmpty(): bool
        {
            return empty($this->items);
        }

        /**
         * Check whether the collection is not empty.
         *
         * @return bool
         */
        public function isNotEmpty(): bool
        {
            return ! $this->isEmpty();
        }

        // -------------------------------------------------------------------------
        // Conversion
        // -------------------------------------------------------------------------

        /**
         * Convert the collection to a plain array.
         *
         * Nested Collections are recursively converted.
         *
         * @return array
         */
        public function toArray(): array
        {
            return array_map(
                fn(mixed $item): mixed => $item instanceof self ? $item->toArray() : $item,
                $this->items
            );
        }

        /**
         * Convert the collection to a JSON string.
         *
         * @param  int  $flags  json_encode flags.
         * @return string
         */
        public function toJson(int $flags = 0): string
        {
            return json_encode($this->toArray(), $flags);
        }

        // -------------------------------------------------------------------------
        // Countable
        // -------------------------------------------------------------------------

        /**
         * Return the number of items in the collection.
         *
         * @return int
         */
        public function count(): int
        {
            return count($this->items);
        }

        // -------------------------------------------------------------------------
        // IteratorAggregate
        // -------------------------------------------------------------------------

        /**
         * Return an iterator for use in foreach loops.
         *
         * @return \ArrayIterator
         */
        public function getIterator(): \ArrayIterator
        {
            return new \ArrayIterator($this->items);
        }

        // -------------------------------------------------------------------------
        // ArrayAccess
        // -------------------------------------------------------------------------

        /**
         * @param  mixed  $offset
         * @return bool
         */
        public function offsetExists(mixed $offset): bool
        {
            return isset($this->items[$offset]);
        }

        /**
         * @param  mixed  $offset
         * @return mixed
         */
        public function offsetGet(mixed $offset): mixed
        {
            return $this->items[$offset];
        }

        /**
         * Mutation is not permitted on an immutable Collection.
         *
         * @param  mixed  $offset
         * @param  mixed  $value
         * @return void
         *
         * @throws \LogicException
         */
        public function offsetSet(mixed $offset, mixed $value): void
        {
            throw new \LogicException('Collection is immutable — use push() or merge() to add items.');
        }

        /**
         * Mutation is not permitted on an immutable Collection.
         *
         * @param  mixed  $offset
         * @return void
         *
         * @throws \LogicException
         */
        public function offsetUnset(mixed $offset): void
        {
            throw new \LogicException('Collection is immutable — use filter() or except() to remove items.');
        }

        // -------------------------------------------------------------------------
        // Private helpers
        // -------------------------------------------------------------------------

        /**
         * Resolve a flat list of values from the items using a key or callback.
         *
         * @param  string|callable|null  $key
         * @return array
         */
        private function resolveValues(string|callable|null $key): array
        {
            if ($key === null) {
                return $this->items;
            }

            return array_map(
                fn(mixed $item): mixed => is_callable($key)
                    ? $key($item)
                    : (is_array($item) ? $item[$key] : $item->$key),
                $this->items
            );
        }

        /**
         * Recursively flatten an array to a given depth.
         *
         * @param  array  $array
         * @param  float  $depth
         * @return array
         */
        private static function flattenArray(array $array, float $depth): array
        {
            $result = [];

            foreach ($array as $item) {
                if (is_array($item) && $depth > 0) {
                    $result = array_merge($result, self::flattenArray($item, $depth - 1));
                } else {
                    $result[] = $item;
                }
            }

            return $result;
        }
    }
}
