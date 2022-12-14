<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Console;
use App\Entity\Comments;
use App\Form\ArticleFormType;
use App\Form\CommentFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getByAuthor($authorId): array
    {
        return $this->entityManager->getRepository(Article::class)->findBy(['author' => $authorId]);
    }

    #[Route('/setPublished/{id}/{state}', name: 'article_setState')]
    public function setStateArticle($id, $state): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $response = [
            'success' => false,
        ];
        $article = $this->entityManager->getRepository(Article::class)->find($id);
        if (!$article) {
            $response['message'] = 'Article not found';
        } else if ($user = $this->getUser()) {
            if ($user->getId() === $article->getAuthor()->getId() || $user.$this->isGranted('ROLE_MODERATEUR')) {
                $article->setState($state);
                $this->entityManager->flush();
                $response['success'] = true;
                $response['message'] = 'Article updated';
                $response['state'] = $state;
            } else {
                $response['message'] = 'You are not allowed to do this';
            }
        }
        return $this->json($response);
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

    #[Route('/article/delete/{slug}', name: 'delete_article')]
    public function deleteArticle($slug, Request $request): Response
    {
        $user = $this->getUser();
        $article = $this->entityManager->getRepository(Article::class)->findOneBy(['slug' => $slug]);

        if ($article == null) {
            $this->addFlash('danger', 'Impossible de supprimer l\'article, il n\'existe pas !');
        } else if ($user == $article->getAuthor() || in_array('ROLE_MODERATEUR', $user->getRoles())) {
            $this->entityManager->remove($article);
            $this->entityManager->flush();
            $this->addFlash('success', 'Article supprim??');
        } else {
            $this->addFlash('danger', 'Impossible de supprimer l\'article, vous n\'??tes pas l\'auteur !');
        }

        $referer = $request->headers->get('referer');
        if ($referer == null) {
            return $this->redirectToRoute('show_article', ['slug' => $slug]);
        } else {
            return new RedirectResponse($referer);
        }
    }

    #[Route('/articles/{slug}', name: 'show_article')]
    public function show(ManagerRegistry $doctrine,  Request $request, string $slug): Response
    {
        $userRepository = $doctrine->getRepository(User::class);
        $articleRepository = $doctrine->getRepository(Article::class);
        $article = $articleRepository->findOneBy(["slug" => $slug]);
        if ($article == null) {
            throw $this->createNotFoundException("Cet article n'existe pas");
        }
        $articleId = $article->getId($article);

        // Error 404 if the article is not published and not moderated and if the current user is not the owner or at least Moderator of the article
        if ($article->isState() === false || $article->isModerated() === false) {
            if (!$this->isGranted('ROLE_MODERATOR') && $this->getUser() != $article->getAuthor()) {
                throw $this->createNotFoundException("Cet article n'existe pas");
            }
        }

        // get comment repository
        $commentRepository = $doctrine->getRepository(Comments::class);
        // get all comments
        $comments = $commentRepository->findAll();
        $currentUser = $this->getUser();
        // comment form creation
        $comment = new Comments();
        // init some datas into the form
        $comment->setDate(new \DateTime())->setCreatedAt(new \DateTimeImmutable())->setUpdatedAt(new \DateTimeImmutable())->setAuthor($currentUser);
        $form = $this->createForm(CommentFormType::class, $comment);

        // check if form is valid
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
            'comments' => $comments,
            'comment_form' => $form->createView(),
        ]);
    }

    #[Route('/create', name: 'create_article')]
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        $article = new Article($this->entityManager);
        $article->setCreatedAt(new \DateTimeImmutable());

        $consoleRepository = $doctrine->getRepository(Console::class);
        $consoles = $consoleRepository->findAll();

        // create article form
        $form = $this->createForm(ArticleFormType::class);

        // check is form is valid
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $this->addFlash('error', 'Votre article n\'a pas ??t?? cr????e !');
            } else {
                // set current user
                $article->setAuthor($this->getUser());
                $article->setName($form->get('name')->getData());
                $article->setDescription($form->get('description')->getData());
                $article->setContent($form->get('content')->getData());

                $consoles = $form->get('console')->getData();
                foreach ($consoles as $console) {
                    $article->addConsole($console);
                }
                $article->setLicence($form->get('licence')->getData());
                $article->setArticleImgFile($form->get('articleImgFile')->getData());


                // persist article
                $this->entityManager->persist($article);
                $this->entityManager->flush($article);
                // after creation slugify
                $article->generateSlug($this->entityManager);
                // persist again with slug
                $this->entityManager->persist($article);
                $this->entityManager->flush($article);

                // get new slug in order to set path to article
                $slug = $article->getSlug();

                $this->addFlash('success', 'Votre article a bien ??t?? cr????e !');

                return $this->redirectToRoute('show_article', [
                    'slug' => $slug,
                ]);
            }
        }

        return $this->render('front/new_article.html.twig', [
            'article_form' => $form->createView(),
        ]);
    }

    #[Route('/articles/{slug}/edit', name: 'edit_article')]
    public function edit(Article $article, ManagerRegistry $doctrine, Request $request, string $slug): Response
    {
        $article = $this->entityManager->getRepository(Article::class)->findOneBy(['slug' => $slug]);
        $article->setUpdatedAt(new \DateTimeImmutable());

        $consoleRepository = $doctrine->getRepository(Console::class);
        $consoles = $consoleRepository->findAll();

        // create article form
        $form = $this->createForm(ArticleFormType::class, $article);

        // check is form is valid
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $this->addFlash('error', 'Votre article n\'a pas ??t?? mis ?? jour !');
            } else {
                $article->setName($form->get('name')->getData());
                $article->setDescription($form->get('description')->getData());
                $article->setContent($form->get('content')->getData());

                $consoles = $form->get('console')->getData();
                foreach ($consoles as $console) {
                    $article->addConsole($console);
                }
                $article->setLicence($form->get('licence')->getData());
                $article->setArticleImgFile($form->get('articleImgFile')->getData());

                // remove previous slug in order to avoid duplicate article during edit
                $article->removeSlug();

                // persist article
                $this->entityManager->persist($article);
                $this->entityManager->flush($article);
                $article->generateSlug($this->entityManager);
                $this->entityManager->persist($article);
                $this->entityManager->flush($article);

                // get new slug in order to set path to article
                $slug = $article->getSlug();

                $this->addFlash('success', 'Votre article a bien ??t?? mis ?? jour !');

                return $this->redirectToRoute('show_article', [
                    'slug' => $slug,
                ]);
            }
        }

        return $this->render('front/edit_article.html.twig', [
            'article' => $article,
            'article_form' => $form->createView()
        ]);
    }
}
