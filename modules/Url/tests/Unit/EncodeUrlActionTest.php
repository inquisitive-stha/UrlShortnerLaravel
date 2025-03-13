<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;

use Modules\Url\Models\Url;
use Modules\Url\Services\UrlService;
use Modules\Url\Actions\EncodeUrlAction;
use Modules\Url\DTO\EncodeUrlDTO;
use Illuminate\Support\Str;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseCount;

uses(
    \Tests\TestCase::class,
    DatabaseMigrations::class,
);

beforeEach(function () {
    $this->urlService = new UrlService();
    $this->action = new EncodeUrlAction($this->urlService);
});

it('can encode new url', function () {
    // Arrange
    $longUrl = 'https://example.com/encode-new';
    $dto = new EncodeUrlDTO($longUrl);

    // Act
    $result = $this->action->execute($dto);

    // Assert
    expect($result)->toBeInstanceOf(Url::class)
        ->and($result->long_url)->toBe($longUrl)
        ->and($result->short_code)->not->toBeEmpty()
        ->and(strlen($result->short_code))->toBe(6);

    // Verify it's in the database
    assertDatabaseHas('urls', [
        'long_url'   => $longUrl,
        'short_code' => $result->short_code
    ]);
});

it('returns existing url for duplicate', function () {
    // Arrange
    $longUrl = 'https://example.com/duplicate-url';
    $existingUrl = Url::factory()->create([
        'long_url'   => $longUrl,
        'short_code' => 'same12'
    ]);

    $dto = new EncodeUrlDTO($longUrl);

    // Act
    $result = $this->action->execute($dto);

    // Assert
    expect($result->id)->toBe($existingUrl->id)
        ->and($result->short_code)->toBe('same12');

    // Verify no duplicate was created
    assertDatabaseCount('urls', 1);
});

it('retries if generated code already exists', function () {
    // Arrange
    $existingCode = 'abcde1';
    Url::factory()->create([
        'long_url'   => 'https://www.example.com/some/long/path?param1=value1&param2=value3',
        'short_code' => $existingCode
    ]);

    $dto = new EncodeUrlDTO('https://example.com/another/long/url?param1=value1&param2=value3');

    // Create a partial mock of EncodeUrlAction
    $mockAction = Mockery::mock(EncodeUrlAction::class, [app(UrlService::class), 6])
        ->shouldAllowMockingProtectedMethods()
        ->makePartial();

    // First call to `generateRandomCode` returns a duplicate code
    $mockAction->shouldReceive('generateRandomCode')
        ->once()
        ->andReturn($existingCode);

    // Second call to `generateRandomCode` returns a new unique code
    $mockAction->shouldReceive('generateRandomCode')
        ->once()
        ->andReturn('newone');

    // Act
    $result = $mockAction->execute($dto);

    // Assert
    expect($result->short_code)->toBe('newone');
});

it('can generate codes of different lengths', function () {
    // Arrange
    $longUrl = 'https://example.com/different-length';
    $customLengthAction = new EncodeUrlAction($this->urlService, 8); // 8 character code
    $dto = new EncodeUrlDTO($longUrl);

    // Act
    $result = $customLengthAction->execute($dto);

    // Assert
    expect($result)->toBeInstanceOf(Url::class)
        ->and($result->long_url)->toBe($longUrl)
        ->and(strlen($result->short_code))->toBe(8);
});

it('throws exception for invalid URL format', function () {
    // Arrange
    $invalidUrl = 'not-a-valid-url';

    // Act & Assert
    expect(fn() => new EncodeUrlDTO($invalidUrl))
        ->toThrow(InvalidArgumentException::class);
});

it('normalizes URLs correctly', function () {
    // Arrange & Act
    $dto1 = new EncodeUrlDTO('https://example.com/path/');
    $dto2 = new EncodeUrlDTO('https://example.com/path');

    // Assert
    expect($dto1->longUrl)->toBe('https://example.com/path')
        ->and($dto2->longUrl)->toBe('https://example.com/path');
});
