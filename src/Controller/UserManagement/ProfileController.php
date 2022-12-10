<?php

namespace App\Controller\UserManagement;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var User $user */
        $user = $this->getUser();
        /*return $this->render('profile/login.html.twig', [
            'controller_name' => 'ProfileController',
        ]);*/
        return new Response('Bonjour '.$user->getUserIdentifier());
    }
}
