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
    public function index(ManagerRegistry $doctrine): Response
    {
        // get licence repository
        $licenceRepository = $doctrine->getRepository(Licence::class);
        // get licence list
        $licences = $licenceRepository->findAll();

        return $this->render('front/licences_list.html.twig', [
            'controller_name' => 'LicenceController',
            'licences' => $licences
        ]);
    }

    #[Route('/licences/{slug}', name: 'show_licence')]
    public function show(ManagerRegistry $doctrine, string $slug): Response
    {
        $licenceRepository = $doctrine->getRepository(Licence::class);
        $licence = $licenceRepository->findOneBy(["slug" => $slug]);
        $articles = $licence->getArticles($licence);
        
        return $this->render('front/show_licence.html.twig', [
            'licence' => $licence,
            'articles' => $articles
        ]);
    }
}
