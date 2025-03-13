<?php

namespace Modules\Url\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Modules\Url\Actions\DecodeUrlAction;
use Modules\Url\Actions\EncodeUrlAction;
use Modules\Url\DTO\DecodeUrlDTO;
use Modules\Url\DTO\EncodeUrlDTO;
use Modules\Url\Http\Requests\V1\DecodeUrlRequest;
use Modules\Url\Http\Requests\V1\EncodeUrlRequest;
use Modules\Url\Http\Resource\UrlResource;

class UrlController extends Controller
{
    public function encode(EncodeUrlRequest $request)
    {
        return new UrlResource(app(EncodeUrlAction::class)->execute(new EncodeUrlDTO($request->longUrl)));
    }

    public function decode(DecodeUrlRequest $request)
    {
        return new UrlResource(app(DecodeUrlAction::class)->execute(new DecodeUrlDTO($request->shortUrl)));
    }
}
