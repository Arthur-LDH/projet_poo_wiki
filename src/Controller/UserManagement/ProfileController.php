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

    //update userIdentifier
    public function updateIdentifier(EntityManagerInterface $entityManager, $newIdentifier)
    {
        $user = $this->getUser();
        $user->setUsername($newIdentifier);
        $entityManager->persist($user);
        $entityManager->flush();
    }

    #[Route('/profile', name: 'user_profile')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('UserManagement/profile.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }
}
