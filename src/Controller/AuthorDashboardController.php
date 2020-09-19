<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Form\PayoutFormType;
use App\Service\MakeJsonFormat;
use App\Service\payment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AuthorDashboardController extends AbstractController
{
    /**
     * @Route("/author/MySell", name="my_sell")
     * @Security("is_granted('ROLE_AUTHOR')")
     */
    public function mySell(MakeJsonFormat $makeJsonFormat)
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
        ]);
    }

    /**
     * @Route("/author/authorProduct", name="author_product")
     * @Security("is_granted('ROLE_AUTHOR')")
     */
    public function authorProduct()
    {
        $user = $this->getUser();
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $products = $productRepository->findBy(['user' => $user]);

        return $this->render('author/authorProduct.html.twig', [
            'products' => $products,
            'user' => $user,
        ]);
    }

    /**
     * @Route("/author/money_managment", name="money_managment")
     * @Security("is_granted('ROLE_AUTHOR')")
     */
    public function payoutAuthor(Request $request, payment $payment)
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
        ]);
    }
}
