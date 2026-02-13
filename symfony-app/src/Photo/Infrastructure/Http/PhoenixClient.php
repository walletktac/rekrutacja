<?php

declare(strict_types=1);

namespace App\Photo\Infrastructure\Http;

use RuntimeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

class PhoenixClient
{
    public function __construct(
        private readonly HttpClientInterface $http,
        private readonly string $baseUrl,
    ) {}

    /** @return array<int, array{ id:int, photo_url:string }> */
    public function fetchPhotos(string $token): array
    {
        $url = rtrim($this->baseUrl, '/') . '/api/photos';

        try {
            $data = $this->http->request('GET', $url, [
                'headers' => ['access-token' => trim($token)],
            ])->toArray(false);

            $photos = $data['photos'] ?? [];
            return is_array($photos) ? $photos : [];
        } catch (Throwable $e) {
            throw new RuntimeException('Phoenix fetchPhotos failed: ' . $e->getMessage(), 0, $e);
        }
    }
}
