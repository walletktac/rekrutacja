<?php

declare(strict_types=1);

namespace App\Identity\Infrastructure\Security;

use App\Identity\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class CurrentUserProvider
{
    public function __construct(
        private RequestStack $requestStack,
        private EntityManagerInterface $em,
    ) {}

    public function get(): ?User
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request === null) {
            return null;
        }

        $session = $request->getSession();
        $userId = $session->get('user_id');
        if ($userId === null) {
            return null;
        }

        $user = $this->em->getRepository(User::class)->find((int) $userId);
        if ($user === null) {
            $session->remove('user_id');
        }

        return $user;
    }
}
