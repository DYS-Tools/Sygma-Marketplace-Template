<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Service\payment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class OrderController extends AbstractController
{
    /**
     * @Route("/order/{product}", name="order")
     * @Security("is_granted('ROLE_USER')")
     */
    public function initOrder(payment $payment, $product)
    {
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $product = $productRepository->findOneBy(['id' => $product]);
        $user = $this->getUser();
        //dd($user);
        $session = $payment->makePayment($product, $user);
        //dd($product);
        dump($session);
        

        return $this->render('order/order.html.twig', [
            'stripe_public_key' => $payment->getStripePublicCredentials(),
            'CHECKOUT_SESSION_ID' => $session['id'],
            'product' => $product
        ]);
    }

    /**
     * @Route("/redirectStripe/{product}", name="redirectStripe")
     * @Security("is_granted('ROLE_USER')")
     */
    public function redirectStripe(payment $payment, $product)
    {
        $user = $this->getUser();
        $session = $payment->makePayment($product, $user);
        dd($session);
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $product = $productRepository->findOneBy(['id' => $product]);

        //dd('redirection Stripe Here');
        return $this->render('order/order.html.twig', [
            'stripe_public_key' => $payment->getStripePublicCredentials(),
            'CHECKOUT_SESSION_ID' => $session['id'],
            'product' => $product
        ]);
    }

    /**
     * @Route("/successPayment/{orderId}", name="successPayment")
     * @Security("is_granted('ROLE_USER')")
     */
    public function successPayment(payment $payment, $orderId)
    {
        $orderRepository = $this->getDoctrine()->getRepository(Order::class);
        $order = $orderRepository->findOneBy(['id' => $orderId]);
        $order->setStatus('Finished');

        // TODO: Validation de la commande 
        // Incrementer la vente dans l'objet product
        // incrementer $order->getAmount() * 0.80;      // 80 % a availablePayout

        return $this->render('order/success.html.twig', [
        ]);
    }

    /**
     * @Route("/cancelPayment", name="cancelPayment")
     */
    public function cancelPayment(payment $payment, $product)
    {
        //dd('redirection Stripe Here');
        return $this->render('order/cancel.html.twig', [
        ]);
    }
}
