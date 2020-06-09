<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    /**
     * @Route("/review", name="review_client")
     */
    public function reviewClient()
    {
        return $this->render('front/ReviewClient.html.twig', [
        ]);
    }

    /**
     * @Route("/legal", name="app_legal")
     */
    public function legal()
    {
        return $this->render('front/legal.html.twig', [
        ]);
    }

    /**
     * @Route("/faq", name="app_faq")
     */
    public function faq()
    {
        return $this->render('front/faq.html.twig', [
        ]);
    }
}
