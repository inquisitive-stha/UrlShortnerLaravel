<?php

namespace Modules\Url\Services;

use Modules\Url\Models\Url;

class UrlService
{
    /**
     * Find a URL by its short code
     *
     * @param string $shortCode
     * @return Url|null
     */
    public function findByShortCode(string $shortCode): ?Url
    {
        return Url::where('short_code', $shortCode)->first();
    }

    /**
     * Find a URL by its long URL
     *
     * @param string $longUrl
     * @return Url|null
     */
    public function findByLongUrl(string $longUrl): ?Url
    {
        return Url::where('long_url', $longUrl)->first();
    }

    /**
     * Create a new URL record
     *
     * @param string $longUrl
     * @param string $shortCode
     * @return Url
     */
    public function createUrl(string $longUrl, string $shortCode): Url
    {
        return Url::create([
            'long_url'   => $longUrl,
            'short_code' => $shortCode,
        ]);
    }

    /**
     * Check if a short code already exists
     *
     * @param string $shortCode
     * @return bool
     */
    public function shortCodeExists(string $shortCode): bool
    {
        return Url::where('short_code', $shortCode)->exists();
    }
}
