<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Form\PayoutFormType;
use App\Repository\CategoryRepository;
use App\Service\MakeJsonFormat;
use App\Service\payment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Require ROLE_AUTHOR for *every* controller method in this class.
 * @IsGranted("ROLE_AUTHOR")
 * @Route("/author")
 */
class AuthorDashboardController extends AbstractController
{
    /**
     * @Route("/MySell", name="my_sell")
     */
    public function mySell(MakeJsonFormat $makeJsonFormat, CategoryRepository $categoryRepository)
    {
        $user = $this->getUser() ;

        $orderRepository = $this->getDoctrine()->getRepository(Order::class);
        $productRepository = $this->getDoctrine()->getRepository(Product::class);

        return $this->render('author/mysell.html.twig', [
            'order' => $orderRepository->findBy(['user' => $user]),
            'user' => $this->getUser(),
            'ArrayForGraph' => $makeJsonFormat->get30LastDaysCommandsForAuthor($user),
            'MoneyGenerated' => $orderRepository->getTotalOrderAmountForOneAuthorWithRemoveCommision($user), /* argent généré en tout  ( somme commande * 0.80 ) */ 
            'authorProductNumber' => count($productRepository->findBy(['user' => $user, 'verified' => 1])), /* lenght produit author accepté par la modération */
            'CAAuthorForMonth' => $orderRepository->getTotalAmountGeneratedIn30LastDaysForAuthorWithRemoveCommision($user), 
            'categories' => $categoryRepository->findBy(['active' => 1]),
        ]);
    }

    /**
     * @Route("/authorProduct", name="author_product")
     */
    public function authorProduct(CategoryRepository $categoryRepository)
    {
        $user = $this->getUser();
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $products = $productRepository->findBy(['user' => $user]);

        return $this->render('author/authorProduct.html.twig', [
            'products' => $products,
            'user' => $user,
            'categories' => $categoryRepository->findBy(['active' => 1]),
        ]);
    }

    /**
     * @Route("/money_managment", name="money_managment")
     */
    public function payoutAuthor(Request $request, payment $payment, CategoryRepository $categoryRepository)
    {
        // Finance in nav 

        // https://developer.paypal.com/docs/platforms/checkout/set-up-payments/
        // new code
        // Todo : Payout author with Paypal

        // End new code

        // Old Code
        if(!empty($request)){
            // regarder + query #parameters 'code' = ac_HaOpXENdmpt0lf8IwoTrh10TaXEF2pWq
            dump($request);
            dump($request->getContent() );
        }
        //dd($payment->getConnectAccount($this->getUser()));
        $user = $this->getUser();
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $products = $productRepository->findBy(['user' => $user]);

        $form = $this->createForm(PayoutFormType::class);
        $form->handleRequest($request);
        // if acct_XXX is defined 
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if($form->get('amount')->getData() <= $user->getAvailablePayout()){
                $user->setAvailablePayout($user->getAvailablePayout() - $form->get('amount')->getData());
            }
            else{
                $this->redirectToRoute('money_managment');
                //TODO: FlashMessage
            }
        }
        // Old Code

        
        // TODO : if payout ok  : remove number in available payout variable

        return $this->render('author/money_managment.html.twig', [
            'products' => $products,
            'user' => $user,
            'userPayout' => $user->getAvailablePayout(),
            'form' => $form->createView(),
            'categories' => $categoryRepository->findBy(['active' => 1]),
        ]);
    }
}
