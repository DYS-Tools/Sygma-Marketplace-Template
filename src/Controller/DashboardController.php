<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Require ROLE_USER for *every* controller method in this class.
 * @IsGranted("ROLE_USER")
 */
class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index()
    {
        // get current user
        $user = $this->getUser() ;

        return $this->render('dashboard/dashboard.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/dashboard/ProductVerified", name="product_verified")
     */
    public function productVerified()
    {
        // get current user
        $user = $this->getUser() ;

        return $this->render('dashboard/productVerified.html.twig', [
            'noVerifiedProduct' => $this->getDoctrine()->getRepository(Product::class)->findAllTProductNoVerified(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/dashboard/MySell", name="my_sell")
     */
    public function mySell()
    {
        // get current user
        $user = $this->getUser();
        $orderRepository = $this->getDoctrine()->getRepository(Order::class);
        $ordered = $orderRepository->findBy(['user' => $user]);

        return $this->render('dashboard/mysell.html.twig', [
            'order' => $ordered,
            'user' => $user,
        ]);
    }

    /**
     * @Route("/dashboard/MyProduct", name="my_product")
     */
    public function myProduct()
    {
        // get current user
        $user = $this->getUser();
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $products = $productRepository->findBy(['user' => $user]);

        return $this->render('dashboard/myProduct.html.twig', [
            'products' => $products,
            'user' => $user,
        ]);
    }

    /**
     * @Route("/dashboard/MyWallet", name="my_wallet")
     */
    public function myWallet()
    {
        // get current user
        $user = $this->getUser();
        //$productRepository = $this->getDoctrine()->getRepository(Product::class);
        //$products = $productRepository->findBy(['user' => $user]);

        return $this->render('dashboard/myWallet.html.twig', [
            //'products' => $products,
            'user' => $user,
        ]);
    }
}
