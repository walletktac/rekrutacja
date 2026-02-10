<?php

declare(strict_types=1);

namespace App\Controller;

use App\Identity\Infrastructure\Security\CurrentUserProvider;
use App\Photo\Application\Service\GalleryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
        GalleryService $gallery,
        CurrentUserProvider $currentUserProvider,
    ): Response {
        $currentUser = $currentUserProvider->get();
        $data = $gallery->getGallery($currentUser);

        return $this->render('home/index.html.twig', [
            'photos' => $data['photos'],
            'currentUser' => $currentUser,
            'userLikes' => $data['userLikes'],
        ]);
    }
}
