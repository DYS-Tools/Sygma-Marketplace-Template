<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\payment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @Route("/order/{product}", name="order")
     */
    public function initOrder(payment $payment, $product)
    {
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $product = $productRepository->findOneBy(['id' => $product]);

        $session = $payment->makePayment($product, $this->getUser());
        //dd($product);

        

        return $this->render('order/order.html.twig', [
            'stripe_public_key' => $payment->getStripePublicCredentials(),
            'CHECKOUT_SESSION_ID' => $session['id'],
            'product' => $product
        ]);
    }

    /**
     * @Route("/redirectStripe/{product}", name="redirectStripe")
     */
    public function redirectStripe(payment $payment, $product)
    {
        $session = $payment->makePayment($product, $this->getUser());
        //dd($product);
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
     * @Route("/successPayment", name="successPayment")
     */
    public function successPayment(payment $payment, $product)
    {
        //dd('redirection Stripe Here');
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
