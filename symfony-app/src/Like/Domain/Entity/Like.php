<?php

declare(strict_types=1);

namespace App\Like\Domain\Entity;

use App\Identity\Domain\Entity\User;
use App\Photo\Domain\Entity\Photo;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'likes')]
#[ORM\UniqueConstraint(name: 'uniq_like_user_photo', columns: ['user_id', 'photo_id'])]
final class Like
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Photo::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Photo $photo;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(User $user, Photo $photo)
    {
        $this->user = $user;
        $this->photo = $photo;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getUser(): User { return $this->user; }
    public function getPhoto(): Photo { return $this->photo; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
}
