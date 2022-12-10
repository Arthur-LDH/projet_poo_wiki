<?php

namespace App\Controller\UserManagement;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LogoutController extends AbstractController
{
    #[Route('/logout', name: 'logout', methods: ['GET'])]
    public function logout(): void
    {
        // controller can be blank: it will never be called!
    }
}