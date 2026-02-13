<?php

namespace Photo\Infrastructure\Http;

use App\Photo\Infrastructure\Http\PhoenixClient;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class PhoenixClientTest extends TestCase
{
    public function testFetchPhotosReturnsPhotosArray(): void
    {
        $http = $this->createMock(HttpClientInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $http->expects(self::once())
            ->method('request')
            ->with(
                'GET',
                'http://phoenix:4000/api/photos',
                self::callback(function (array $opts): bool {
                    return ($opts['headers']['access-token'] ?? null) === 'token123';
                })
            )
            ->willReturn($response);

        $response->expects(self::once())
            ->method('toArray')
            ->willReturn(['photos' => [['id' => 1, 'photo_url' => 'x']]]);

        $client = new PhoenixClient($http, 'http://phoenix:4000');

        self::assertSame([['id' => 1, 'photo_url' => 'x']], $client->fetchPhotos('token123'));
    }

    public function testFetchPhotosReturnsEmptyWhenNoPhotosKey(): void
    {
        $http = $this->createMock(HttpClientInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $http->method('request')->willReturn($response);
        $response->method('toArray')->willReturn(['foo' => 'bar']);

        $client = new PhoenixClient($http, 'http://phoenix:4000');

        self::assertSame([], $client->fetchPhotos('token123'));
    }

    public function testFetchPhotosWrapsException(): void
    {
        $http = $this->createMock(HttpClientInterface::class);

        $http->method('request')->willThrowException(new RuntimeException('boom'));

        $client = new PhoenixClient($http, 'http://phoenix:4000');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Phoenix fetchPhotos failed');

        $client->fetchPhotos('token123');
    }
}
