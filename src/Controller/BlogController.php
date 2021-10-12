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
        $form = $this->createForm(CreateArticleFormType::class,$newArticle);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $newArticle
                ->setUser($this->getUser())
                ->setPublicationDate(new DateTime())
            ;
            $em = $this->getDoctrine()->getManager();
            $em->persist($newArticle);
            $em->flush();
            $this->addFlash('success','article ajouter avec succés');
            return $this->redirectToRoute('main_home');

        }
        return $this->render('blog/article_create.html.twig',[
            'form' => $form->createView(),
        ]);
    }
    /**
     * controller de la page de creation d'article
     *
     * @Route("/liste-des-article/", name="article_list")
     */
    public function articleList(Request $request, PaginatorInterface $paginator): Response
    {
        $requestedPage = $request->query->getInt('page',1);

        if ($requestedPage< 1){
            throw new NotFoundHttpException();
        }
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery('SELECT a FROM App\Entity\Article a ORDER BY a.publicationDate DESC ');

        $articleList = $paginator->paginate(
            $query,
            $requestedPage,
            10
        );

        return $this->render('blog/article_list.html.twig',[
            'liste'=> $articleList
        ]);
    }

    /**
     * controller de la page de creation d'article
     *
     * @Route("/article/{slug}", name="article")
     */
    public function article(Article $article,Request $request): Response
    {
        $newComment = new Comment();
        $newComment->setUser($this->getUser());
        $newComment->setPublicationDate(new \DateTime());
        $newComment->setArticle($article);

        $form = $this->createForm(CreateCommentFormType::class,$newComment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newComment);
            $em->flush();
            $this->addFlash('success', 'commentaire ajouter avec succés');
            unset($newComment);
            unset($form);
            $newComment = new Comment();
            $form = $this->createForm(CreateCommentFormType::class,$newComment);
        }

        return $this->render('blog/article.html.twig',[
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }
}
