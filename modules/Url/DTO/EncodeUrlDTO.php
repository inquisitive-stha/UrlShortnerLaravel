<?php

namespace Modules\Url\DTO;

use InvalidArgumentException;

class EncodeUrlDTO
{
    public function __construct(
        public string $longUrl
    ) {
        $this->validateUrl();
    }

    /**
     * Validate that the URL is properly formatted
     *
     * @throws InvalidArgumentException
     * @return void
     */
    private function validateUrl(): void
    {
        // Simple validation using filter_var
        if (!filter_var($this->longUrl, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid URL format provided');
        }

         $this->longUrl = $this->normalizeUrl($this->longUrl);
    }

    /**
     * Normalize URL to prevent duplicates
     *
     * @param string $url
     * @return string
     */
    private function normalizeUrl(string $url): string
    {
        // Remove trailing slashes
        return rtrim($url, '/');
    }

}
