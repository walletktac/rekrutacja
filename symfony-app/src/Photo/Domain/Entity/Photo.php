<?php

declare(strict_types=1);

namespace App\Photo\Domain\Entity;

use App\Identity\Domain\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Entity]
#[ORM\Table(name: 'photos')]
class Photo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private string $imageUrl;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $location = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $camera = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $takenAt = null;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $likeCounter = 0;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    public function __construct(User $user, string $imageUrl)
    {
        $imageUrl = trim($imageUrl);
        if ($imageUrl === '') {
            throw new InvalidArgumentException('Image URL cannot be empty');
        }

        $this->user = $user;
        $this->imageUrl = $imageUrl;
    }

    public function getId(): ?int { return $this->id; }
    public function getUser(): User { return $this->user; }
    public function getImageUrl(): string { return $this->imageUrl; }
    public function getLocation(): ?string { return $this->location; }
    public function getDescription(): ?string { return $this->description; }
    public function getCamera(): ?string { return $this->camera; }
    public function getTakenAt(): ?DateTimeImmutable { return $this->takenAt; }
    public function getLikeCounter(): int { return $this->likeCounter; }

    public function updateDetails(?string $location, ?string $description, ?string $camera, ?DateTimeImmutable $takenAt): void
    {
        $this->location = $location !== null ? trim($location) : null;
        $this->description = $description !== null ? trim($description) : null;
        $this->camera = $camera !== null ? trim($camera) : null;
        $this->takenAt = $takenAt;
    }

    public function incrementLikes(): void { $this->likeCounter++; }

    public function decrementLikes(): void
    {
        if ($this->likeCounter > 0) {
            $this->likeCounter--;
        }
    }
}
