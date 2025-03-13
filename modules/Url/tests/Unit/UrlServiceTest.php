<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Modules\Url\Models\Url;
use Modules\Url\Services\UrlService;

uses(\Tests\TestCase::class, DatabaseMigrations::class);

beforeEach(function () {
    $this->urlService = new UrlService();
});

it('can find url by short code', function (Url $url) {
    $foundUrl = $this->urlService->findByShortCode($url->short_code);

    expect($foundUrl)->not->toBeNull();
    expect($foundUrl->long_url)->toBe($url->long_url);
    expect($foundUrl->short_code)->toBe($url->short_code);
})->with([
    'with_params'    => [
        fn() => Url::factory()->create([
            'long_url'   => 'https://www.example.com/some/long/path?param1=value1&param2=value3',
            'short_code' => 'aBcd3f'
        ])
    ],
    'without_params' => [
        fn() => Url::factory()->create([
            'long_url'   => 'https://www.thisisalongdomain.com/',
            'short_code' => 's1mp13'
        ])
    ],
]);


it('returns null when finding a non-existent short code', function () {
    // Act
    $result = $this->urlService->findByShortCode('non-existent-code');

    // Assert
    expect($result)->toBeNull();
});


it('can find a URL by long URL', function (Url $url) {
    // Act
    $foundUrl = $this->urlService->findByLongUrl($url->long_url);

    // Assert
    expect($foundUrl)->not->toBeNull();
    expect($foundUrl->id)->toBe($url->id);
    expect($foundUrl->long_url)->toBe($url->long_url);
})->with([
    'with_http'  => [
        fn() => Url::factory()->create([
            'long_url'   => 'https://www.example.com/some/long/path?param1=value1&param2=value3',
            'short_code' => 'aBcd3f'
        ])
    ],
    'with_https' => [
        fn() => Url::factory()->create([
            'long_url'   => 'https://www.thisisalongdomain.com/',
            'short_code' => 's1mp13'
        ])
    ],
]);


it('returns null when finding a non-existent long URL', function () {
    // Act
    $result = $this->urlService->findByLongUrl('https://example.com/non-existent');

    // Assert
    expect($result)->toBeNull();
});


it('can create a new URL record', function () {
    // Arrange
    $longUrl = 'https://example.com/new/page';
    $shortCode = 'new123';

    // Act
    $createdUrl = $this->urlService->createUrl($longUrl, $shortCode);

    // Assert
    expect($createdUrl)->not->toBeNull();
    expect($createdUrl->long_url)->toBe($longUrl);
    expect($createdUrl->short_code)->toBe($shortCode);

    // Verify the record exists in the database
    $this->assertDatabaseHas('urls', [
        'long_url'   => $longUrl,
        'short_code' => $shortCode
    ]);
});

it('can check if a short code exists', function (Url $url) {
    // Act & Assert
    expect($this->urlService->shortCodeExists($url->short_code))->toBeTrue();
    expect($this->urlService->shortCodeExists('non-existent'))->toBeFalse();
})->with([
    fn() => Url::factory()->create([
        'long_url'   => 'https://www.example.com/some/long/path?param1=value1&param2=value3',
        'short_code' => 'aBcd3f'
    ])
]);

it('handles case sensitivity correctly for short codes', function (Url $url) {
    // Act & Assert
    $lowercaseResult = $this->urlService->findByShortCode(strtolower($url->short_code));

    if ($lowercaseResult) {
        // Case-insensitive database (like MySQL by default)
        expect($lowercaseResult->short_code)->toBe($url->short_code);
    } else {
        // Case-sensitive database (like PostgreSQL)
        expect($lowercaseResult)->toBeNull();
        expect($this->urlService->findByShortCode($url->short_code))->not->toBeNull();
    }
})->with([
    fn() => Url::factory()->create([
        'long_url'   => 'https://www.example.com/some/long/path?param1=value1&param2=value3',
        'short_code' => 'ABCDEF'
    ])
]);
