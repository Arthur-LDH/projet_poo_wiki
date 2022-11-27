<?php

namespace App\Controller\Front;

use App\Entity\Article;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(ManagerRegistry $doctrine): Response
    {
        // get articles repository
        $articleRepository = $doctrine->getRepository(Article::class);
        // get list of articles
        $articles = $articleRepository->findAll();

        return $this->render('front/home.html.twig', [
            'controller_name' => 'HomeController',
            'articles' => $articles,
        ]);
    }
}
