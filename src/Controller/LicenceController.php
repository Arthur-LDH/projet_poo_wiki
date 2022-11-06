<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Licence;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LicenceController extends AbstractController
{
    #[Route('/licences', name: 'licences_list')]
    public function index(ManagerRegistry $doctrine, mixed $id): Response
    {
        // Get licence repository
        $licenceRepository = $doctrine->getRepository(Licence::class);
        // Get licence list
        $licences = $licenceRepository->findAll();

        return $this->render('front/licences_list.html.twig', [
            'controller_name' => 'LicenceController',
            'licences' => $licences
        ]);
    }

    #[Route('/licences/{id}', name: 'show_licence')]
    public function show(ManagerRegistry $doctrine, Licence $licence, mixed $id): Response
    {
        // Get licence repository
        $articleRepository = $doctrine->getRepository(Article::class);
        // Get articles from current licence
        $articles = $articleRepository->findBy(['licence' => $id]);

        return $this->render('front/show_licence.html.twig', [
            'licence' => $licence,
            'articles' => $articles
        ]);
    }
}
