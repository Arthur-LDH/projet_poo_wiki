<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Console;
use App\Entity\Licence;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/articles', name: 'articles_list')]
    public function index(ManagerRegistry $doctrine, mixed $id): Response
    {
        // Get article repository
        $artileRepository = $doctrine->getRepository(Article::class);
        $articles = $artileRepository->findBy(['id' => $id]);

        // Get licence repository
        $licenceRepository = $doctrine->getRepository(Licence::class);
        $licences = $licenceRepository->findBy(['id' => $id]);

        // Get console repository
        $consoleRepository = $doctrine->getRepository(Console::class);
        $consoles = $consoleRepository->findBy(['id' => $id]);

        return $this->render('front/articles_list.html.twig', [
            'controller_name' => 'ArticleController',
            'articles' => $articles,
            'licences' => $licences,
            'consoles' => $consoles
        ]);
    }

    #[Route('/article/{name}', name: 'show_article')]
    public function show(ManagerRegistry $doctrine, Article $article, mixed $name): Response
    {
        // // Get article repository
        // $artileRepository = $doctrine->getRepository(Article::class);
        // $articles = $artileRepository->findBy(['id' => $id]);

        // // Get licence repository
        // $licenceRepository = $doctrine->getRepository(Licence::class);
        // $licences = $licenceRepository->findBy(['id' => $id]);

        // // Get console repository
        // $consoleRepository = $doctrine->getRepository(Console::class);
        // $consoles = $consoleRepository->findBy(['id' => $id]);

        return $this->render('front/show_article.html.twig', [
            'article' => $article,
            // 'console' => $console,
            // 'licence' => $licence,
        ]);
    }
}
