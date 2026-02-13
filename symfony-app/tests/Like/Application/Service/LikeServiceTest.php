<?php

namespace Like\Application\Service;

use App\Identity\Domain\Entity\User;
use App\Like\Application\Service\LikeService;
use App\Like\Domain\Repository\LikeRepositoryInterface;
use App\Photo\Domain\Entity\Photo;
use PHPUnit\Framework\TestCase;

class LikeServiceTest extends TestCase
{
    public function testToggleUnlikeWhenLiked(): void
    {
        $repo = $this->createMock(LikeRepositoryInterface::class);
        $service = new LikeService($repo);

        $user = $this->createMock(User::class);
        $photo = $this->createMock(Photo::class);

        $repo->expects(self::once())
            ->method('hasUserLikedPhoto')
            ->with($user, $photo)
            ->willReturn(true);

        $repo->expects(self::once())
            ->method('unlike')
            ->with($user, $photo);

        $repo->expects(self::never())->method('like');

        self::assertFalse($service->toggle($user, $photo));
    }

    public function testToggleLikesWhenNotLikedYet(): void
    {
        $repo = $this->createMock(LikeRepositoryInterface::class);
        $service = new LikeService($repo);

        $user = $this->createMock(User::class);
        $photo = $this->createMock(Photo::class);

        $repo->expects(self::once())
            ->method('hasUserLikedPhoto')
            ->with($user, $photo)
            ->willReturn(false);

        $repo->expects(self::once())
            ->method('like')
            ->with($user, $photo);

        $repo->expects(self::never())->method('unlike');

        self::assertTrue($service->toggle($user, $photo));
    }
}
