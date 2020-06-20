<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use App\Form\BecomeAuthorFormType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

     /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // \Swift_Mailer $mailer
            $message = (new \Swift_Message('Web-Item-Market'))
                ->setFrom('sacha6623@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'Emails/register.html.twig',
                        []),
                 'text/html');
            $mailer->send($message);

            return $this->redirectToRoute('product_index');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

     /**
     * @Route("/become_author", name="become_author")
     * @Security("is_granted('ROLE_USER')")
     */
    public function becomeAuthor(EntityManagerInterface $em, Request $request)
    {
        //@IsGranted("ROLE_USER")
        //@Security("is_granted('ROLE_USER')")

        $form = $this->createForm(BecomeAuthorFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = $this->getUser();
            $user->setRoles(['ROLE_AUTHOR']);
            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'success',
                'you are author now, congratulation !'
            );

            return $this->redirectToRoute('home');
        }

        return $this->render('security/becomeAuthor.html.twig', [
            'form' => $form->createView(),
        ]);
        
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
