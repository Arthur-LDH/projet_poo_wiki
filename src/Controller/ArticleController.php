<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Console;
use App\Entity\Licence;
use App\Entity\Comments;
use App\Entity\User;
use App\Form\CommentFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getByAuthor($authorId): array
    {
        return $this->entityManager->getRepository(Article::class)->findBy(['author' => $authorId]);
    }

    #[Route('/articles', name: 'articles_list')]
    public function index(ManagerRegistry $doctrine): Response
    {
        // get article repository
        $articleRepository = $doctrine->getRepository(Article::class);

        // get all articles
        $articles = $articleRepository->findAll();

        return $this->render('front/articles_list.html.twig', [
            'controller_name' => 'ArticleController',
            'articles' => $articles,
        ]);
    }

    #[Route('/articles/{id}', name: 'show_article')]
    public function show(ManagerRegistry $doctrine, Article $article, Licence $licence, Request $request, int $id): Response
    {
        // Check if the user still exists, if not: change the user_id to the user "Utilisateur supprimé"
        $authorId = $article->getAuthor($id);
        $userRepository = $doctrine->getRepository(User::class);
        $user = $userRepository->findOneBy(['id' => $authorId]);
        if ($user == null) {
            // L'ID 10 correspond au User "Utilisateur Supprimé"
            $article = $article->setAuthor($userRepository->findOneBy(['id' => 10]));
            $this->entityManager->persist($article);
            $this->entityManager->flush();
        }

        // get comment repository
        $commentRepository = $doctrine->getRepository(Comments::class);
        // get all comments
        $comments = $commentRepository->findAll();

        // comment form creation
        $comment = new Comments();
        // init some datas into the form
        $comment->setDate(new \DateTime())->setCreatedAt(new \DateTimeImmutable())->setUpdatedAt(new \DateTimeImmutable());
        // create comments form
        $form = $this->createForm(CommentFormType::class, $comment);

        // check is form is valid
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setArticle($article);
            // set current user
            $comment->setAuthor($this->getUser());

            // persist comment
            $this->entityManager->persist($comment);
            $this->entityManager->flush();
        }

        return $this->render('front/show_article.html.twig', [
            'article' => $article,
            'comment_form' => $form->createView(),
        ]);
    }

    #[Route('/articles/create', name: 'create_article')]
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        $article = new Article();

        // create article form
        // $form = $this->createForm(ArticleFormType::class, $article);

        // check is form is valid
        // $form->handleRequest($request);
        // if ($form->isSubmitted() && $form->isValid()) {
        //     $article->setArticle($article);
        //     // set current user
        //     $article->setAuthor($this->getUser());

        //     // persist article
        //     $this->entityManager->persist($article);
        //     $this->entityManager->flush();
        // }

        return $this->render('front/new_article.html.twig');
    }
}
