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
        // get console repository
        $consoleRepository = $doctrine->getRepository(Console::class);
        // get consoles list
        $consoles = $consoleRepository->findAll();

        return $this->render('front/consoles_list.html.twig', [
            'controller_name' => 'ConsoleController',
            'consoles' => $consoles
        ]);
    }

    #[Route('/consoles/{slug}', name: 'show_console')]
    public function show(ManagerRegistry $doctrine, string $slug): Response
    {
        $consoleRepository = $doctrine->getRepository(Console::class);
        $console = $consoleRepository->findOneBy(["slug" => $slug]);
        if ($console == null) {
            throw $this->createNotFoundException("Cette console n'existe pas");
        }
        $articles = $console->getArticles($console);
        
        return $this->render('front/show_console.html.twig', [
            'console' => $console,
            'articles' => $articles
        ]);
    }
}
