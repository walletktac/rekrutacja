<?php

declare(strict_types=1);

namespace App\Photo\Application\Service;

use App\Identity\Domain\Entity\User;
use App\Like\Domain\Repository\LikeRepositoryInterface;
use App\Photo\Application\Query\PhotoFilter;
use App\Photo\Infrastructure\Doctrine\PhotoRepository;
use DateTimeImmutable;

final class GalleryService
{
    public function __construct(
        private PhotoRepository $photoRepository,
        private LikeRepositoryInterface $likeRepository,
    ) {}

    /** @param array<string,mixed> $query */
    public function getGallery(?User $currentUser, array $query = []): array
    {
        $filter = new PhotoFilter(
            location: $this->s($query['location'] ?? null),
            camera: $this->s($query['camera'] ?? null),
            description: $this->s($query['description'] ?? null),
            takenAt: !empty($query['taken_at']) ? new DateTimeImmutable((string)$query['taken_at']) : null,
            username: $this->s($query['username'] ?? null),
        );

        $photos = $this->photoRepository->search($filter);

        $userLikes = [];
        if ($currentUser !== null) {
            foreach ($photos as $photo) {
                $userLikes[$photo->getId()] = $this->likeRepository->hasUserLikedPhoto($currentUser, $photo);
            }
        }

        return [
            'photos' => $photos,
            'userLikes' => $userLikes,
            'filters' => $filter,
        ];
    }

    private function s(mixed $v): ?string
    {
        $v = is_string($v) ? trim($v) : null;
        return $v === '' ? null : $v;
    }

}
