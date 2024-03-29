<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Recaptcha\RecaptchaValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/inscription", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface, RecaptchaValidator $recaptcha): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('main_home');
        }
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $recaptchaResponse = $request->request->get('g-recaptcha-response', null);
            $ip = $request->server->get('REMOTE_ADDR');

            if ($recaptchaResponse == null || !$recaptcha->verify($recaptchaResponse, $ip)) {
                $form->addError(new FormError('captcha non remplis'));
            }
            if ($form->isValid()) {

                // encode the plain password
                $user
                    ->setPassword(
                        $userPasswordHasherInterface->hashPassword(
                            $user,
                            $form->get('plainPassword')->getData()
                        )
                    )
                    ->setCreationDate(new \DateTime());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                // do anything else you need here, like send an email
                $this->addFlash('success', 'bravo');

                return $this->redirectToRoute('app_login');
            }


        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
