<?php

declare(strict_types=1);

namespace App\Like\Infrastructure\Doctrine;

use App\Identity\Domain\Entity\User;
use App\Like\Domain\Entity\Like;
use App\Like\Domain\Repository\LikeRepositoryInterface;
use App\Photo\Domain\Entity\Photo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class LikeRepository extends ServiceEntityRepository implements LikeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }

    public function hasUserLikedPhoto(User $user, Photo $photo): bool
    {
        $id = $this->createQueryBuilder('l')
            ->select('l.id')
            ->where('l.user = :user')
            ->andWhere('l.photo = :photo')
            ->setParameter('user', $user)
            ->setParameter('photo', $photo)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $id !== null;
    }

    public function like(User $user, Photo $photo): void
    {
        $em = $this->getEntityManager();

        if ($this->hasUserLikedPhoto($user, $photo)) {
            return;
        }

        $em->persist(new Like($user, $photo));
        $photo->incrementLikes();
        $em->flush();
    }

    public function unlike(User $user, Photo $photo): void
    {
        $em = $this->getEntityManager();

        $like = $this->createQueryBuilder('l')
            ->where('l.user = :user')
            ->andWhere('l.photo = :photo')
            ->setParameter('user', $user)
            ->setParameter('photo', $photo)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($like === null) {
            return;
        }

        $em->remove($like);
        $photo->decrementLikes();
        $em->flush();
    }
}
