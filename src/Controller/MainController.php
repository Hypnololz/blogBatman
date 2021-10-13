<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\CreateArticleFormType;
use App\Form\EditPhotoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    /**
     * controller de la page de profil
     *
     * @Security("is_granted('ROLE_USER')")
     * @Route("/edit-photo/", name="edit_photo")
     */
    public function photoEdit(Request $request): Response
    {

        $form = $this->createForm(EditPhotoType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $photo = $form->get('photo')->getData();

            if ($this->getUser()->getPhoto() != null && file_exists($this->getParameter('app.user.photo.directory'). $this->getUser()->getPhoto())){

                unlink( $this->getParameter('app.user.photo.directory') . $this->getUser()->getPhoto() );
            }

            do{
                $newFileName = md5(random_bytes(100)).'.'. $photo->guessExtension();

            }while(file_exists($this->getParameter('app.user.photo.directory'). $newFileName));


            $this->getUser()->setPhoto($newFileName);

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $photo->move(
                $this->getParameter('app.user.photo.directory'),
                $newFileName
            );
            $this->addFlash('success', 'Batphoto modifier avec succés');

            return $this->redirectToRoute('main_profil');
        }




        return $this->render('main/photo.html.twig',[
            'form'=>$form->createView()
        ]);
    }

}
