<?php

if (!function_exists('versioned_asset')) {
    /**
     * Generate a versioned asset URL to bust browser cache.
     *
     * @param string $path
     * @param bool $secure
     * @return string
     */
    function versioned_asset($path, $secure = null)
    {
        $version = config('app.asset_version', '1.0.0');
        $assetUrl = asset($path, $secure);

        return $assetUrl . '?v=' . $version;
    }
}
