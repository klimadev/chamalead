<?php
declare(strict_types=1);

function quiz_asset_url(string $relativePath): string
{
    static $cache = [];

    if (isset($cache[$relativePath])) {
        return $cache[$relativePath];
    }

    $normalizedPath = ltrim($relativePath, '/');
    $absolutePath = dirname(__DIR__) . '/' . $normalizedPath;
    $version = is_file($absolutePath) ? (string) filemtime($absolutePath) : '1';

    $cache[$relativePath] = '/quiz/' . $normalizedPath . '?v=' . $version;

    return $cache[$relativePath];
}
