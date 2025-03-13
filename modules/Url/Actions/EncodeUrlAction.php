<?php

namespace Modules\Url\Actions;

use Illuminate\Support\Str;
use Modules\Url\DTO\EncodeUrlDTO;
use Modules\Url\Services\UrlService;
use \Modules\Url\Models\Url;

class EncodeUrlAction
{
    /**
     * Create a new action instance
     *
     * @param UrlService $urlService
     * @param int $codeLength
     */
    public function __construct(
        protected UrlService $urlService,
        protected int $codeLength = 6
    ) {
    }

    /**
     * Execute the action
     *
     * @param EncodeUrlDTO $dto
     * @return Url
     */
    public function execute(EncodeUrlDTO $dto): Url
    {
        $existingUrl = $this->urlService->findByLongUrl($dto->longUrl);

        if ($existingUrl) {
            return $existingUrl;
        }

        // Generate a unique short code
        $shortCode = $this->generateUniqueCode();

        // Create new URL record
        return $this->urlService->createUrl($dto->longUrl, $shortCode);
    }

    /**
     * Generate a unique short code
     *
     * @return string
     */
    protected function generateUniqueCode(): string
    {
        do {
            $code = $this->generateRandomCode();
        } while ($this->urlService->shortCodeExists($code));

        return $code;
    }

    /**
     * Generate a random code
     *
     * @return string
     */
    protected function generateRandomCode(): string
    {
        return Str::random($this->codeLength);
    }

}
