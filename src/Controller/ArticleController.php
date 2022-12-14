<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Console;
use App\Entity\Licence;
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

    #[Route('/article/delete/{slug}', name: 'delete_article')]
    public function deleteArticle($slug, Request $request): Response
    {
        $user = $this->getUser();
        $article = $this->entityManager->getRepository(Article::class)->findOneBy(['slug' => $slug]);

        if ($article == null) {
            $this->addFlash('danger', 'Impossible de supprimer l\'article, il n\'existe pas !');
        }
        else if ($user->getId() == $article->getAuthor()->getId() || in_array('ROLE_MODERATEUR', $user->getRoles())) {
            $this->entityManager->remove($article);
            $this->entityManager->flush();
            $this->addFlash('success', 'Article supprimé');
        }
        else {
            $this->addFlash('danger', 'Impossible de supprimer l\'article, vous n\'êtes pas l\'auteur !');
        }

        $referer = $request->headers->get('referer');
        if ($referer == null) {
            return $this->redirectToRoute('show_article', ['slug' => $slug]);
        }
        else {
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
        if($article->isState() === false || $article->isModerated() === false){
            if(!$this->isGranted('ROLE_MODERATOR') && $this->getUser() != $article->getAuthor()){
                throw $this->createNotFoundException("Cet article n'existe pas");
            }
        }

        // Check if the user still exists, if not: change the user_id to the user "Utilisateur supprimé"
        $authorId = $article->getAuthor($articleId);
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
                $this->addFlash('error', 'Votre article n\'a pas été créée !');
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
                $article->setSlug($this->entityManager);
                // persist again with slug
                $this->entityManager->persist($article);
                $this->entityManager->flush($article);

                // get new slug in order to set path to article
                $slug = $article->getSlug();

                $this->addFlash('success', 'Votre article a bien été créée !');

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
    public function edit(Article $article, ManagerRegistry $doctrine, Request $request): Response
    {
        $article->setUpdatedAt(new \DateTimeImmutable());

        $consoleRepository = $doctrine->getRepository(Console::class);
        $consoles = $consoleRepository->findAll();

        // create article form
        $form = $this->createForm(ArticleFormType::class, $article);

        // check is form is valid
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $this->addFlash('error', 'Votre article n\'a pas été mis à jour !');
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
                $article->setSlug($this->entityManager);
                $this->entityManager->persist($article);
                $this->entityManager->flush($article);

                // get new slug in order to set path to article
                $slug = $article->getSlug();

                $this->addFlash('success', 'Votre article a bien été mis à jour !');

                return $this->redirectToRoute('show_article', [
                    'slug' => $slug,
                ]);
            }
        }

        return $this->render('front/edit_article.html.twig', [
            'article_form' => $form->createView()
        ]);
    }
}
