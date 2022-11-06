<?php

namespace App\Controller;

use App\Entity\Article;
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

    #[Route('/consoles/{name}', name: 'show_console')]
    public function show(ManagerRegistry $doctrine, Console $console, mixed $name): Response
    {
        // Get the console name
        $consoleRepository = $doctrine->getRepository(Console::class);
        // $console = $consoleRepository->findBy(['console' => $name]);
        // Get Articles from the Console name
        // $articles = $console->getArticles();

        return $this->render('front/show_console.html.twig', [
            'console' => $console,
            // 'articles' => $articles
        ]);
    }
}
