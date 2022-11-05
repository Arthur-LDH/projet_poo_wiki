<?php

namespace App\Controller;

use App\Entity\Console;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConsoleController extends AbstractController
{
    #[Route('/consoles', name: 'consoles_list')]
    public function index(ManagerRegistry $doctrine, mixed $id): Response
    {
        // Get Console list
        $consoleRepository = $doctrine->getRepository(Console::class);
        $consoles = $consoleRepository->findAll();

        return $this->render('front/consoles_list.html.twig', [
            'controller_name' => 'ConsoleController',
            'consoles' => $consoles
        ]);
    }
}
