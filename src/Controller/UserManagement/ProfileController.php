<?php

namespace App\Controller\UserManagement;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\ArticleController;

class ProfileController extends AbstractController
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/profile', name: 'user_profile')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var User $user */
        $user = $this->getUser();
        $articlesController = new ArticleController($this->entityManager);
        $articles = $articlesController->getByAuthor($user->getId());

        return $this->render('UserManagement/profile.html.twig', [
            'controller_name' => 'ProfileController',
            'articles' => $articles,
        ]);
    }
}
