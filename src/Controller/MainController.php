<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\CreateArticleFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * controller de la page d'accueil
     *
     * @Route("/", name="main_home")
     */
    public function home(): Response
    {
        $articlerepo = $this->getDoctrine()->getRepository(Article::class);

        $articles = $articlerepo->findBy([], ['publicationDate' => 'DESC'], $this->getParameter('app.nbr_article'));

        return $this->render('main/home.html.twig',[
            'articles' => $articles,
        ]);
    }
    /**
     * controller de la page de profil
     *
     * @Security("is_granted('ROLE_USER')")
     * @Route("/profil/", name="main_profil")
     */
    public function profile(): Response
    {

        return $this->render('main/profil.html.twig');
    }

}
