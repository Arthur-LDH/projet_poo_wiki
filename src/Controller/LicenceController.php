<?php

namespace App\Controller;

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
        // Get Licence list
        $licenceRepository = $doctrine->getRepository(Licence::class);
        $licences = $licenceRepository->findAll();

        return $this->render('front/licences_list.html.twig', [
            'controller_name' => 'LicenceController',
            'licences' => $licences
        ]);
    }

    #[Route('/licences/{name}', name: 'show_licence')]
    public function show(ManagerRegistry $doctrine, Licence $licence, mixed $name): Response
    {
        // Get the console name
        // $console = $doctrine->getRepository(Console::class)->find($name);
        // Get Articles from the Console name
        // $articles = $console->getArticles();

        return $this->render('front/show_licence.html.twig', [
            'licence' => $licence,
            // 'articles' => $articles
        ]);
    }
}
