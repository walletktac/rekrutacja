<?php

declare(strict_types=1);

namespace App\Photo\Infrastructure\Http\Controller;

use App\Identity\Infrastructure\Security\CurrentUserProvider;
use App\Like\Application\Service\LikeService;
use App\Like\Infrastructure\Doctrine\LikeRepository;
use App\Photo\Domain\Entity\Photo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PhotoController extends AbstractController
{
    #[Route('/photo/{id}/like', name: 'photo_like')]
    public function like(
        int $id,
        CurrentUserProvider $currentUserProvider,
        EntityManagerInterface $em,
        LikeService $likeService,
    ): Response {
        $user = $currentUserProvider->get();
        if ($user === null) {
            $this->addFlash('error', 'You must be logged in to like photos.');
            return $this->redirectToRoute('home');
        }

        $photo = $em->getRepository(Photo::class)->find($id);
        if ($photo === null) {
            throw $this->createNotFoundException('Photo not found');
        }

        $liked = $likeService->toggle($user, $photo);
        $this->addFlash($liked ? 'success' : 'info', $liked ? 'Photo liked!' : 'Photo unliked!');

        return $this->redirectToRoute('home');
    }
}
