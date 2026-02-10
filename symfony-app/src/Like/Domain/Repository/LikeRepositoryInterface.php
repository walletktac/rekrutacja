<?php

declare(strict_types=1);

namespace App\Like\Domain\Repository;

use App\Identity\Domain\Entity\User;
use App\Photo\Domain\Entity\Photo;

interface LikeRepositoryInterface
{
    public function hasUserLikedPhoto(User $user, Photo $photo): bool;
    public function like(User $user, Photo $photo): void;
    public function unlike(User $user, Photo $photo): void;
}