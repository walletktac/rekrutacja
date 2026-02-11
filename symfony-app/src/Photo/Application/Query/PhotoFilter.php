<?php

declare(strict_types=1);

namespace App\Photo\Application\Query;

use DateTimeImmutable;

final class PhotoFilter
{
    public function __construct(
        public readonly ?string $location = null,
        public readonly ?string $camera = null,
        public readonly ?string $description = null,
        public readonly ?DateTimeImmutable $takenAt = null,
        public readonly ?string $username = null,
    ) {}
}
