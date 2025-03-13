<?php

namespace Modules\Url\Http\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UrlResource extends JsonResource
{

    /**
     * Create a new resource instance.
     *
     * @param mixed $resource
     * @param string $baseUrl
     * @return void
     */
    public function __construct($resource, protected string $baseUrl = 'http://short.est/')
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type'       => 'url',
            'id'         => $this->id,
            'attributes' => [
                'shortUrl' => $this->baseUrl . $this->short_code,
                'longUrl' => $this->long_url,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
/*            'links'      => [
                'self' => route('api.v1.urls.show', ['url' => $this->id]),
            ],*/
        ];
    }
}
