<?php

namespace App\Support;

class AssetPath
{
    public static function local(?string $value, string $fallback): string
    {
        $path = preg_replace('#^(?:\.\./|\./|/)+#', '', trim($value ?? ''));
        if (!$path || str_contains($path, '..') || !is_file(public_path($path))) return $fallback;
        return $path;
    }
}
