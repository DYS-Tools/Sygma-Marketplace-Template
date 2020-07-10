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
            'product' => $product
        ]);
    }

    /**
     * @Route("/redirectPayment/{product}", name="redirectPayment")
     * @Security("is_granted('ROLE_USER')")
     */
    public function redirectPayment(payment $payment, $product)
    {
        $user = $this->getUser();

        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $product = $productRepository->findOneBy(['id' => $product]);

        $session = $payment->makePayment($product, $user);
        
        dd($session);
        
        return $this->render('order/order.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/successPayment/{orderId}", name="successPayment")
     * @Security("is_granted('ROLE_USER')")
     */
    public function successPayment(payment $payment, $orderId, \Swift_Mailer $mailer)
    {
        $orderRepository = $this->getDoctrine()->getRepository(Order::class);
        $order = $orderRepository->findOneBy(['id' => $orderId]);
        $order->setStatus('Finished');
        


        
            // \Swift_Mailer $mailer
            $message = (new \Swift_Message('Web-Item-Market'))
                ->setFrom('sacha6623@gmail.com')
                ->setTo($this->getUser()->getEmail())
                ->setBody(
                    $this->renderView(
                        'Emails/successPayment.html.twig',
                        []),
                 'text/html');
            $mailer->send($message);

            $this->addFlash('success', "Email has been send");
            

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
