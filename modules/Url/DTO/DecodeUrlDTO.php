<?php

namespace Modules\Url\DTO;

class DecodeUrlDTO
{
    public function __construct(
        public string $shortUrl
    ) {
    }
}
