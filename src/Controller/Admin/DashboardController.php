<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Console;
use App\Entity\Licence;
use App\Entity\User;
use App\Entity\Comments;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function show(ManagerRegistry $doctrine): Response{

        $articleRepository = $doctrine->getRepository(Article::class);
        $articles = $articleRepository->findAll();

        $commentRepository = $doctrine->getRepository(Comments::class);
        $comments = $commentRepository->findAll();

        return $this->render('admin/index.html.twig',[
            'articles' => $articles,
            'comments' => $comments,
        ]);
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {   
        $userImg = $user->getImg();
        $pathImg = "build/images/user_img/".$userImg;
        return parent::configureUserMenu($user)
        ->setAvatarUrl($pathImg)

        ;
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<img src="build/images/wikitendo_logo.png" style="width: 150px; height: auto;">')
            ->setFaviconPath('build/images/favicon.ico')
            ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa-solid fa-gauge-high');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class)->setPermission('ROLE_ADMIN');//Hide the button if not ADMIN
        yield MenuItem::linkToCrud('Consoles', 'fa-solid fa-gamepad', Console::class);
        yield MenuItem::linkToCrud('Licence', 'fa-solid fa-dungeon', Licence::class);
        yield MenuItem::linkToCrud('Articles', 'fa-solid fa-scroll', Article::class);
        yield MenuItem::linkToRoute('Retour au site', 'fa fa-home', 'home');
    }
}
