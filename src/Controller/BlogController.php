<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CreateArticleFormType;
use App\Form\CreateCommentFormType;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/blog", name="blog_")
 */
class BlogController extends AbstractController
{
    /**
     * controller de la page de creation d'article
     *
     * @Security("is_granted('ROLE_ADMIN')")
     * @Route("/créer-un-article/", name="article_create")
     */
    public function articleCreation(Request $request): Response
    {

        $newArticle = new Article();
        $form = $this->createForm(CreateArticleFormType::class, $newArticle);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newArticle
                ->setUser($this->getUser())
                ->setPublicationDate(new DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($newArticle);
            $em->flush();
            $this->addFlash('success', 'article ajouter avec succés');
            return $this->redirectToRoute('main_home');

        }
        return $this->render('blog/article_create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     *
     * @Route("/liste-des-article/", name="article_list")
     */
    public function articleList(Request $request, PaginatorInterface $paginator): Response
    {
        $requestedPage = $request->query->getInt('page', 1);

        if ($requestedPage < 1) {
            throw new NotFoundHttpException();
        }
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery('SELECT a FROM App\Entity\Article a ORDER BY a.publicationDate DESC ');

        $articleList = $paginator->paginate(
            $query,
            $requestedPage,
            10
        );

        return $this->render('blog/article_list.html.twig', [
            'liste' => $articleList
        ]);
    }

    /**
     * controller de la page de creation d'article
     *
     * @Route("/article/{slug}", name="article")
     */
    public function article(Article $article, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->render('blog/article.html.twig',[
                'article' => $article,
            ]);
        }

        $newComment = new Comment();

        $form = $this->createForm(CreateCommentFormType::class, $newComment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newComment->setUser($this->getUser());
            $newComment->setPublicationDate(new \DateTime());
            $newComment->setArticle($article);
            $em = $this->getDoctrine()->getManager();
            $em->persist($newComment);
            $em->flush();
            $this->addFlash('success', 'commentaire ajouter avec succés');
            unset($newComment);
            unset($form);
            $newComment = new Comment();
            $form = $this->createForm(CreateCommentFormType::class, $newComment);
        }

        return $this->render('blog/article.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    /**
     *
     *
     * @Route("/liste-des-article-recherche/", name="search")
     */
    public function articlesearch(Request $request, PaginatorInterface $paginator): Response
    {
        $requestedPage = $request->query->getInt('page', 1);

        $research = $request->query->get('searcharea');
        if ($requestedPage < 1) {
            throw new NotFoundHttpException();
        }
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery("SELECT a FROM App\Entity\Article a WHERE a.title LIKE :key OR a.content LIKE :key ORDER BY a.publicationDate DESC ")->setParameter('key', '%' . $research . '%');
        dump($query);
        $articleList = $paginator->paginate(
            $query,
            $requestedPage,
            10
        );


        return $this->render('blog/search.html.twig', [
            'liste' => $articleList
        ]);
    }

    /**
     *
     *
     * @Route("/article/delete/{id}", name="articledelete")
     */
    public function articleDelete(Article $article, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('article_delete_' . $article->getId(), $request->query->get('csrf_token'))) {
            $this->addFlash('error', 'token secu invalide reessayer');
        } else {

            $em = $this->getDoctrine()->getManager();

            $em->remove($article);

            $em->flush();

            $this->addFlash('success', 'l\'article a bien etais surpprimé');

        }
        return $this->redirectToRoute('blog_article_list');


    }
    /**
     *
     * @Route("/commentaire/delete/{id}", name="commentdelete")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function commentDelete(Comment $comment, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('comment_delete_' . $comment->getId(), $request->query->get('csrf_token'))) {
            $this->addFlash('error', 'token secu invalide reessayer');
        } else {

            $em = $this->getDoctrine()->getManager();

            $em->remove($comment);

            $em->flush();

            $this->addFlash('success', 'le commentaire a bien etais surpprimé');

        }
        return $this->redirectToRoute('blog_article',[

            'slug' => $comment->getArticle()->getSlug(),

        ]);


    }

    /**
     * @Route("/article/modifier/{id}", name="article_edit")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function articleEdit(Article $article, Request $request): Response
    {
        $form = $this->createForm(CreateArticleFormType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'publication modifiée avec succés');

            return $this->redirectToRoute('blog_article', [
                'slug' => $article->getSlug()
            ]);
        }


        return $this->render('blog/article/modification.html.twig', [
            'form' => $form->createView(),
        ]);


    }
}
