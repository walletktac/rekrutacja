<?php

declare(strict_types=1);

namespace App\Identity\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $username;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $email;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $age = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $phoenixApiToken = null;

    public function __construct(
        string $username,
        string $email,
        ?string $name = null,
        ?string $lastName = null,
        ?int $age = null,
        ?string $bio = null
    )
    {
        $username = trim($username);
        $email = trim($email);

        if ($username === '') {
            throw new InvalidArgumentException('Username cannot be empty');
        }

        if ($email === '') {
            throw new InvalidArgumentException('Email cannot be empty');
        }

        $this->username = $username;
        $this->email = $email;
        $this->name = $name;
        $this->lastName = $lastName;
        $this->age = $age;
        $this->bio = $bio;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function getPhoenixApiToken(): ?string
    {
        return $this->phoenixApiToken;
    }

    public function assignPhoenixApiToken(string $token): void
    {
        $token = trim($token);

        if ($token === '') {
            throw new InvalidArgumentException('Phoenix API token cannot be empty');
        }

        $this->phoenixApiToken = $token;
    }

    public function clearPhoenixApiToken(): void
    {
        $this->phoenixApiToken = null;
    }

    public function updateProfile(?string $name, ?string $lastName, ?int $age, ?string $bio): void
    {
        if ($age !== null && $age < 0) {
            throw new InvalidArgumentException('Age cannot be negative');
        }

        $this->name = $name;
        $this->lastName = $lastName;
        $this->age = $age;
        $this->bio = $bio;
    }
}
