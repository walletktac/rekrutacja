<?php

declare(strict_types=1);

namespace App\Photo\Infrastructure\Doctrine;

use App\Photo\Application\Query\PhotoFilter;
use App\Photo\Domain\Entity\Photo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PhotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Photo::class);
    }

    public function findAllWithUsers(): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.user', 'u')
            ->addSelect('u')
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function search(PhotoFilter $filter): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.user', 'u')
            ->addSelect('u');

        if ($filter->location) {
            $qb->andWhere('LOWER(p.location) LIKE LOWER(:location)')
                ->setParameter('location', '%' . $filter->location . '%');
        }

        if ($filter->camera) {
            $qb->andWhere('LOWER(p.camera) LIKE LOWER(:camera)')
                ->setParameter('camera', '%' . $filter->camera . '%');
        }

        if ($filter->description) {
            $qb->andWhere('LOWER(p.description) LIKE LOWER(:description)')
                ->setParameter('description', '%' . $filter->description . '%');
        }

        if ($filter->takenAt) {
            $qb->andWhere('DATE(p.takenAt) = :takenAt')
                ->setParameter('takenAt', $filter->takenAt->format('Y-m-d'));
        }

        if ($filter->username) {
            $qb->andWhere('LOWER(u.username) LIKE LOWER(:username)')
                ->setParameter('username', '%' . $filter->username . '%');
        }

        return $qb
            ->orderBy('p.takenAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
