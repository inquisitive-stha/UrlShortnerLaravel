<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Modules\Url\Models\Url;
use Modules\Url\Services\UrlService;

uses(\Tests\TestCase::class, DatabaseMigrations::class);

beforeEach(function () {
    $this->apiPrefix = '/api/v1/urls';
});

it('can encode a url', function () {
    // Arrange
    $longUrl = 'https://www.example.com/some/long/path';

    // Act
    $response = $this->postJson($this->apiPrefix.'/encode', [
        'longUrl' => $longUrl
    ]);

    // Assert
    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'shortUrl',
                    'longUrl',
                    'created_at',
                    'updated_at'
                ]
            ]
        ]);

    $this->assertDatabaseHas('urls', [
        'long_url' => $longUrl
    ]);

    // Verify the response follows the expected JSON:API format
    $responseData = $response->json('data');
    expect($responseData['type'])->toBe('url');
    expect($responseData['attributes']['longUrl'])->toBe($longUrl);
    expect($responseData['attributes']['shortUrl'])->toStartWith('http://short.est/');
});


it('validates long url input', function ($payload, $errorSource, $errorMessage) {
    // Act
    $response = $this->postJson($this->apiPrefix.'/encode', $payload);

    // Assert
    $response->assertStatus(200)
        ->assertJson([
            'errors' => [
                [
                    'status' => 422,
                    'message' => $errorMessage,
                    'source' => $errorSource
                ]
            ]
        ]);
})->with([
    'missing_url' => [[], 'longUrl', 'The long url field is required.'],
    'invalid_url' => [['longUrl' => 'not-a-valid-url'], 'longUrl', 'The long url field must be a valid URL.'],
    'empty_url' => [['longUrl' => ''], 'longUrl', 'The long url field is required.']
]);


it('can decode a url', function (Url $url, $shortUrl) {

    // Act
    $response = $this->postJson($this->apiPrefix.'/decode', [
        'shortUrl' => $shortUrl
    ]);

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'shortUrl',
                    'longUrl',
                    'created_at',
                    'updated_at'
                ]
            ]
        ]);

    $responseData = $response->json('data');
    expect($responseData['id'])->toBe($url->id);
    expect($responseData['attributes']['longUrl'])->toBe($url->long_url);
    expect($responseData['attributes']['shortUrl'])->toBe($shortUrl);
})->with([
    'with_params' => [fn() => Url::factory()->create([
        'long_url'   => 'https://www.example.com/some/long/path?param1=value1&param2=value3',
        'short_code' => 'aBcd3f'
    ]), 'http://short.est/aBcd3f'],
    'without_params' => [fn() => Url::factory()->create([
        'long_url'   => 'https://www.thisisalongdomain.com/',
        'short_code' => 's1mp13'
    ]), 'http://short.est/s1mp13'],
]);


it('validates short url input', function ($payload, $errorSource, $errorMessage) {
    // Act
    $response = $this->postJson($this->apiPrefix.'/decode', $payload);

    // Assert
    $response->assertStatus(200)
        ->assertJson([
            'errors' => [
                [
                    'status' => 422,
                    'message' => $errorMessage,
                    'source' => $errorSource
                ]
            ]
        ]);

})->with([
    'missing_url' => [[], 'shortUrl', 'The short url field is required.'],
    'invalid_url' => [['shortUrl' => 'not-a-valid-url'], 'shortUrl', 'The short url field must be a valid URL.'],
    'empty_url' => [['shortUrl' => ''], 'shortUrl', 'The short url field is required.']
]);

it('returns 404 when short url not found', function () {
    // Arrange
    $shortUrl = 'http://short.est/nonexistent';

    // Act
    $response = $this->postJson($this->apiPrefix.'/decode', [
        'shortUrl' => $shortUrl
    ]);

    // Assert
    $response->assertStatus(200)
        ->assertJson([
            'errors' => [
                    'status' => 404,
                    'message' => 'Short URL not found',
                    'source' => ''
            ]
        ]);
});
