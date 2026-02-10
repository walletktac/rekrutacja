<?php

declare(strict_types=1);

namespace App\Identity\Infrastructure\Http\Controller;

use App\Identity\Infrastructure\Security\CurrentUserProvider;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile', methods: ['GET'])]
    public function profile(CurrentUserProvider $currentUser): Response
    {
        $user = $currentUser->get();
        if ($user === null) {
            return $this->redirectToRoute('home');
        }

        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile/phoenix-token', name: 'profile_phoenix_token', methods: ['POST'])]
    public function savePhoenixToken(
        Request $request,
        CurrentUserProvider $currentUser,
        EntityManagerInterface $em,
    ): Response {
        $user = $currentUser->get();
        if ($user === null) {
            return $this->redirectToRoute('home');
        }

        $token = (string) $request->request->get('phoenix_token', '');
        $token = trim($token);

        try {
            if ($token === '') {
                $user->clearPhoenixApiToken();
                $this->addFlash('info', 'Phoenix API token cleared.');
            } else {
                $user->assignPhoenixApiToken($token);
                $this->addFlash('success', 'Phoenix API token saved.');
            }

            $em->flush();
        } catch (InvalidArgumentException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('profile');
    }
}
