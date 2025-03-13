<?php

namespace Modules\Url\Actions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Url\DTO\DecodeUrlDTO;
use Modules\Url\Models\Url;
use Modules\Url\Services\UrlService;

class DecodeUrlAction
{
    public function __construct(
        protected UrlService $urlService,
        protected string $baseUrl = 'http://short.est/'
    ) {
    }


    /**
     * Execute the action to decode a short URL
     *
     * @param DecodeUrlDTO $dto
     * @return Url
     * @throws ModelNotFoundException
     */
    public function execute(DecodeUrlDTO $dto): Url
    {
        // Extract the short code from the URL
        $shortCode = $this->extractShortCode($dto->shortUrl);

        if (!$shortCode) {
            throw new ModelNotFoundException('Short URL not found');
        }

        // Find the URL by short code
        $url = $this->urlService->findByShortCode($shortCode);
        if (!$url) {
            throw new ModelNotFoundException('Short URL not found');
        }

        return $url;
    }

    /**
     * Extract the short code from a URL
     *
     * @param string $shortUrl
     * @return string|null
     */
    private function extractShortCode(string $shortUrl): ?string
    {
        // Parse the URL to get its components
        $parsedUrl = parse_url($shortUrl);

        if (!isset($parsedUrl['host']) || !isset($parsedUrl['path'])) {
            return null;
        }

        // Check if the host is short.est or www.short.est
        $host = $parsedUrl['host'];
        if ($host !== 'short.est' && $host !== 'www.short.est') {
            return null;
        }

        // Extract the path component and remove the leading slash
        $path = $parsedUrl['path'];
        if (strlen($path) <= 1) {
            return null;
        }

        // Return the code (everything after the first slash)
        return substr($path, 1);
    }
}
