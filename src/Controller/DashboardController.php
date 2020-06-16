<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use App\Form\ProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

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
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function productVerifiedPage()
    {
        // get current user
        $user = $this->getUser() ;

        return $this->render('dashboard/productVerified.html.twig', [
            'noVerifiedProduct' => $this->getDoctrine()->getRepository(Product::class)->findAllTProductNoVerified(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/dashboard/statistic", name="statistic_admin")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function statisticAdmin()
    {
        $user = $this->getUser() ;

        return $this->render('dashboard/statisticAdmin.html.twig', [
            'ProductForSell' => $this->getDoctrine()->getRepository(Product::class)->countAllProductForSell(),
            'ProductForVerified' => $this->getDoctrine()->getRepository(Product::class)->countAllProductForVerified(),
            'countOrder' => $this->getDoctrine()->getRepository(Order::class)->countAllOrder(),
            'countUser' => $this->getDoctrine()->getRepository(User::class)->countAllUser(),
            'countMember' => $this->getDoctrine()->getRepository(User::class)->countAllMember(),
            'countAdmin' => $this->getDoctrine()->getRepository(User::class)->countAllAdmin(),
            'countAuthor' => $this->getDoctrine()->getRepository(User::class)->countAllAuthor(),
        ]);
    }

    /**
     * @Route("/dashboard/product/verified/{id}", name="app_dashboard_product_verified")
     */
    public function productVerifiedAction(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $ProductNoVerified = $this->getDoctrine()->getRepository(Product::class)->find($id);
        $ProductNoVerified->setVerified(1);
        $entityManager->persist($ProductNoVerified);
        $entityManager->flush();

        return $this->redirectToRoute('product_verified');
    }

    /**
     * @Route("/dashboard/MySell", name="my_sell")
     * @Security("is_granted('ROLE_AUTHOR')")
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
     * @Route("/dashboard/MyOrder", name="my_order")
     */
    public function myOrder()
    {
        // get current user
        $user = $this->getUser();
        $orderRepository = $this->getDoctrine()->getRepository(Order::class);
        $ordered = $orderRepository->findBy(['user' => $user]);

        return $this->render('dashboard/myOrder.html.twig', [
            'orders' => $ordered,
            'user' => $user,
        ]);
    }

    /**
     * @Route("/dashboard/MyProduct", name="my_product")
     */
    public function myProduct()
    {   // my product for client
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
     * @Route("/dashboard/authorProduct", name="author_product")
     * @Security("is_granted('ROLE_AUTHOR')")
     */
    public function authorProduct()
    {   // product galery for author
        // get current user
        $user = $this->getUser();
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $products = $productRepository->findBy(['user' => $user]);

        return $this->render('dashboard/authorProduct.html.twig', [
            'products' => $products,
            'user' => $user,
        ]);
    }

    /**
     * @Route("/dashboard/payout_author", name="payout_author")
     * @Security("is_granted('ROLE_AUTHOR')")
     */
    public function payoutAuthor()
    {   // payout function

        // get current user
        $user = $this->getUser();
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $products = $productRepository->findBy(['user' => $user]);
        
        // if payout : déduire la somme sur available_payout

        return $this->render('dashboard/authorPayout.html.twig', [
            'products' => $products,
            'user' => $user,
            'userPayout' => $user->getAvailablePayout(),
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

    /**
     * @Route("/dashboard/basket", name="app_basket")
     */
    public function basket()
    {
        // get current user
        $user = $this->getUser();

        return $this->render('dashboard/basket.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/dashboard/download/{fichier}", name="downloadProduct")
     */
    public function download($fichier)
    {
        // get current user
        header('Content-Type: application/octet-stream');
        //header('Content-Length: '. $poids);
        header('Content-disposition: attachment; filename='. $fichier);
        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        //readfile($situation);
        exit();

        /*
        return $this->render('dashboard/basket.html.twig', [
            'user' => $user,
        ]);
        */
    }
}
