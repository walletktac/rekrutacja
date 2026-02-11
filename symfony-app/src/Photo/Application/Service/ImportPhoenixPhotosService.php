<?php

declare(strict_types=1);

namespace App\Photo\Application\Service;

use App\Identity\Domain\Entity\User;
use App\Photo\Domain\Entity\Photo;
use App\Photo\Infrastructure\Http\PhoenixClient;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;

final class ImportPhoenixPhotosService
{
    public function __construct(
        private readonly PhoenixClient $phoenixClient,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function import(User $user): int
    {
        $token = $user->getPhoenixApiToken();
        if ($token === null || trim($token) === '') {
            throw new RuntimeException('Phoenix API token is not set.');
        }

        $phoenixPhotos = $this->phoenixClient->fetchPhotos($token);

        $imported = 0;

        foreach ($phoenixPhotos as $row) {
            $url = $row['photo_url'] ?? null;
            if (!is_string($url) || trim($url) === '') {
                continue;
            }

            $url = trim($url);

            $exists = $this->entityManager->getRepository(Photo::class)->findOneBy([
                'user' => $user,
                'imageUrl' => $url,
            ]);

            if ($exists !== null) {
                continue;
            }

            $this->entityManager->persist(new Photo($user, $url));
            $imported++;
        }

        $this->entityManager->flush();

        return $imported;
    }
}
