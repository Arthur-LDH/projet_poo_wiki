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
    public function index(ManagerRegistry $doctrine): Response
    {
        // get article repository
        $artileRepository = $doctrine->getRepository(Article::class);
        // get all articles
        $articles = $artileRepository->findAll();

        return $this->render('front/articles_list.html.twig', [
            'controller_name' => 'ArticleController',
            'articles' => $articles,
        ]);
    }

    #[Route('/articles/{id}', name: 'show_article')]
    public function show(Article $article): Response
    {
        return $this->render('front/show_article.html.twig', [
            'article' => $article,
        ]);
    }
}
