<?php

declare(strict_types=1);

namespace App\Photo\Application\Service;

use App\Identity\Domain\Entity\User;
use App\Like\Domain\Repository\LikeRepositoryInterface;
use App\Photo\Infrastructure\Doctrine\PhotoRepository;

final class GalleryService
{
    public function __construct(
        private PhotoRepository $photoRepository,
        private LikeRepositoryInterface $likeRepository,
    ) {}

    /**
     * @return array{photos: array, userLikes: array<int,bool>}
     */
    public function getGallery(?User $user): array
    {
        $photos = $this->photoRepository->findAllWithUsers();

        $userLikes = [];
        if ($user !== null) {
            foreach ($photos as $photo) {
                $userLikes[$photo->getId()] = $this->likeRepository->hasUserLikedPhoto($user, $photo);
            }
        }

        return ['photos' => $photos, 'userLikes' => $userLikes];
    }
}
