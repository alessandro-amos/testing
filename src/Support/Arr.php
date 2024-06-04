<?php

namespace Alms\Testing\Support;

class Arr
{
    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string|int|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public static function get($array, $key, $default = null): mixed
    {
        if (! static::accessible($array)) {
            return Util::value($default);
        }

        if (is_null($key)) {
            return $array;
        }

        if (static::exists($array, $key)) {
            return $array[$key];
        }

        if (! str_contains($key, '.')) {
            return $array[$key] ?? Util::value($default);
        }

        foreach (explode('.', $key) as $segment) {
            if (static::accessible($array) && static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return Util::value($default);
            }
        }

        return $array;
    }
    
    public static function collapse(array $array): array
    {
        $results = [];

        foreach ($array as $values)
        {
            if (!is_array($values))
            {
                continue;
            }

            $results[] = $values;
        }

        return array_merge([], ...$results);
    }

    public static function accessible(mixed $value): bool
    {
        return is_array($value) || $value instanceof \ArrayAccess;
    }

    public static function exists(mixed $array, string|int|float $key): bool
    {
        if ($array instanceof \ArrayAccess)
        {
            return $array->offsetExists($key);
        }

        if (is_float($key))
        {
            $key = (string)$key;
        }

        return array_key_exists($key, $array);
    }

    public static function sortRecursive(array $array, int $options = SORT_REGULAR,bool $descending = false): array
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = static::sortRecursive($value, $options, $descending);
            }
        }

        if (! array_is_list($array)) {
            $descending
                ? krsort($array, $options)
                : ksort($array, $options);
        } else {
            $descending
                ? rsort($array, $options)
                : sort($array, $options);
        }

        return $array;
    }

    public static function data_get($target, $key, $default = null)
    {
        if (is_null($key))
        {
            return $target;
        }

        $key = is_array($key) ? $key : explode('.', $key);

        foreach ($key as $i => $segment)
        {
            unset($key[$i]);

            if (is_null($segment))
            {
                return $target;
            }

            if ($segment === '*')
            {
                if (!is_iterable($target))
                {
                    return Util::value($default);
                }

                $result = [];

                foreach ($target as $item)
                {
                    $result[] = Arr::data_get($item, $key);
                }

                return in_array('*', $key) ? Arr::collapse($result) : $result;
            }

            if (Arr::accessible($target) && Arr::exists($target, $segment))
            {
                $target = $target[$segment];
            } elseif (is_object($target) && isset($target->{$segment}))
            {
                $target = $target->{$segment};
            } else
            {
                return Util::value($default);
            }
        }

        return $target;
    }

    public static function has(array|object $array, string|array $keys): bool
    {
        $keys = (array) $keys;

        if (! $array || $keys === []) {
            return false;
        }

        foreach ($keys as $key) {
            $subKeyArray = $array;

            if (static::exists($array, $key)) {
                continue;
            }

            foreach (explode('.', $key) as $segment) {
                if (static::accessible($subKeyArray) && static::exists($subKeyArray, $segment)) {
                    $subKeyArray = $subKeyArray[$segment];
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    public static function dot(array $array, string $prepend = ''): array
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && ! empty($value)) {
                $results[] = static::dot($value, $prepend.$key.'.');
            } else {
                $results[] = [$prepend.$key => $value];
            }
        }

        return array_merge(...$results);
    }

    public static function set(array &$array, string|null $key,mixed $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        foreach ($keys as $i => $key) {
            if (count($keys) === 1) {
                break;
            }

            unset($keys[$i]);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    public static function isAssoc(array $array): bool
    {
        return ! array_is_list($array);
    }
}