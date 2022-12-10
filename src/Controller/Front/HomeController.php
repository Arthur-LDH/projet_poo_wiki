<?php

namespace App\Controller\Front;

use App\Entity\Article;
use App\Entity\Console;
use App\Entity\Licence;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(ManagerRegistry $doctrine): Response
    {
        // get article repository
        $articleRepository = $doctrine->getRepository(Article::class);
        // get list of articles and randomize results
        $articles = $articleRepository->findAll();
        shuffle($articles);

        // get console repository
        $consoleRepository = $doctrine->getRepository(Console::class);
        // get list of consoles and randomize results
        $consoles = $consoleRepository->findAll();
        shuffle($consoles);

        // get licence repository
        $licenceRepository = $doctrine->getRepository(Licence::class);
        // get list of licences and randomize results
        $licences = $licenceRepository->findAll();
        shuffle($licences);

        return $this->render('front/home.html.twig', [
            'controller_name' => 'HomeController',
            'articles' => $articles,
            'consoles' => $consoles,
            'licences' => $licences,
        ]);
    }
}
