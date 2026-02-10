<?php

declare(strict_types=1);

namespace App\Like\Application\Service;

use App\Identity\Domain\Entity\User;
use App\Like\Domain\Repository\LikeRepositoryInterface;
use App\Photo\Domain\Entity\Photo;

class LikeService
{
    public function __construct(
        private readonly LikeRepositoryInterface $likeRepository
    ) {}

   public function toggle(User $user, Photo $photo): bool
   {
       if ($this->likeRepository->hasUserLikedPhoto($user, $photo)) {
           $this->likeRepository->unlike($user, $photo);
           return false;
       }

       $this->likeRepository->like($user, $photo);
       return true;
   }
}
