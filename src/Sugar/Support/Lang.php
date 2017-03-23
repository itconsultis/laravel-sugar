<?php

namespace ITC\Laravel\Sugar\Support;

class Lang
{
    /**
     * This function accepts an associative array and returns an array
     * containing key-value pairs.
     * ```php
     * $assoc = ['foo'=>1, 'bar'=>2];
     * $pairs = Lang::pairs($assoc);
     * >>> [['foo', 1], ['bar', 2]]
     * @param array $assoc - associative array
     * @return array
     */
    public static function pairs(array $assoc): array
    {
        $sort = $sort ?? 'sort';
        $pairs = [];
        foreach ($assoc as $key => $value) {
            $pairs[] = [$key, $value];
        }
        return $pairs;
    }
}
