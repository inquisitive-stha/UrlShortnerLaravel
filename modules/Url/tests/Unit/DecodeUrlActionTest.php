<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;

use Modules\Url\Models\Url;
use Modules\Url\Services\UrlService;
use Modules\Url\Actions\DecodeUrlAction;
use Modules\Url\DTO\DecodeUrlDTO;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseCount;

uses(
    \Tests\TestCase::class,
    DatabaseMigrations::class,
);

beforeEach(function () {
    $this->urlService = new UrlService();
    $this->action = new DecodeUrlAction($this->urlService);
});

it('can decode a valid short URL', function (Url $url, string $shortUrl) {
    // Arrange
    $dto = new DecodeUrlDTO($shortUrl);

    // Act
    $result = $this->action->execute($dto);

    // Assert
    expect($result->is($url))->toBeTrue();
    expect($result->short_code)->toBe($url->short_code);
})->with([
    'with_http' => [fn() => Url::factory()->create([
        'long_url'   => 'https://www.example.com/some/long/path?param1=value1&param2=value3',
        'short_code' => 'aBcd3f'
    ]), 'http://short.est/aBcd3f'],
    'with_https' => [fn() => Url::factory()->create([
        'long_url'   => 'https://www.thisisalongdomain.com/',
        'short_code' => 's1mp13'
    ]), 'https://short.est/s1mp13'],
]);



it('can decode a valid short URL with www subdomain', function (Url $url, string $shortUrl) {
    // Arrange
    $dto = new DecodeUrlDTO($shortUrl);

    // Act
    $result = $this->action->execute($dto);

    // Assert
    expect($result->is($url))->toBeTrue();
})->with([
    'with_http' => [fn() => Url::factory()->create([
        'long_url'   => 'https://www.example.com/some/long/path?param1=value1&param2=value3',
        'short_code' => 'abc123'
    ]), 'http://www.short.est/abc123'],
]);

it('throws an exception when URL not found in database', function () {
    $this->withoutExceptionHandling();

    // Arrange
    $shortUrl = 'http://short.est/abc123';
    $dto = new DecodeUrlDTO($shortUrl);

    // Act & Assert
    expect(fn() => $this->action->execute($dto))->toThrow(ModelNotFoundException::class, 'Short URL not found');

});

it('throws an exception when URL format is invalid', function (string $invalidUrl) {
    $this->withoutExceptionHandling();

    // Arrange
    $dto = new DecodeUrlDTO($invalidUrl);

    // Act & Assert
    expect(fn() => $this->action->execute($dto))->toThrow(ModelNotFoundException::class, 'Short URL not found');
})->with([
    'invalid_url' => 'invalid-url-format',
    'missing_protocol' => 'short.est/abc123', // No protocol
]);

it('throws an exception when URL domain is not short.est', function () {
    $this->withoutExceptionHandling();

    // Arrange
    $shortUrl = 'http://different-domain.com/abc123';
    $dto = new DecodeUrlDTO($shortUrl);

    // Act & Assert
    expect(fn() => $this->action->execute($dto))->toThrow(ModelNotFoundException::class, 'Short URL not found');
});

it('throws an exception when URL path is empty or just a slash', function (string $invalidUrl) {
    $this->withoutExceptionHandling();

    // Arrange
    $dto = new DecodeUrlDTO($invalidUrl);

    // Act & Assert
    expect(fn() => $this->action->execute($dto))->toThrow(ModelNotFoundException::class, 'Short URL not found');
})->with([
    'no_path' => 'http://short.est',
    'only_slash' => 'http://short.est/',
]);

it('throws an exception when valid short code is not found in database', function () {
    $this->withoutExceptionHandling();

    $nonExistentShortCode = 'abcdef';
    $shortUrl = 'http://short.est/' . $nonExistentShortCode;
    $dto = new DecodeUrlDTO($shortUrl);

    // Act & Assert
    expect(fn() => $this->action->execute($dto))->toThrow(ModelNotFoundException::class, 'Short URL not found');
});
