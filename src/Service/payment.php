<?php
/**
 * Created by PhpStorm.
 * User: sacha
 * Date: 14/04/2020
 * Time: 22:33
 */

namespace App\Service;


use App\Entity\Email;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Stripe\Stripe;
use Symfony\Component\Security\Core\User\UserInterface;

class payment
{

    private $entityManager;
    private $secretStripeKeyTest;
    private $publicStripeKeyTest;
    private $mailer;
    private $user;


    public function __construct(EntityManagerInterface $em, $secretStripeKeyTest, $publicStripeKeyTest /* \Swift_Mailer $mailer*/)
    {
        $this->em = $em;
        $this->secretStripeKeyTest = $secretStripeKeyTest;
        $this->publicStripeKeyTest = $publicStripeKeyTest;
        /*$this->mailer = $mailer;*/
    }

    public function getStripeSecretCredentials(){
        //return secret key
        return $this->secretStripeKeyTest;
    }

    public function getStripePublicCredentials(){
        //return public key
        return $this->publicStripeKeyTest;
    }

    /*
    public function sendMailAfterOrder($order,$user){
        
        $message = (new \Swift_Message('Votre commande SpeedMailer'))
                ->setFrom('sacha6623@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/order.html.twig',
                        ['order' => $order,
                         'user' => $user ])
                    , 'text/html'
                )
            ;
            $this->mailer->send($message);
    }
    */

    public function makePayment(Product $product, User $user )
    {

        //dump($this->secretStripeKeyTest);
        Stripe::setApiKey($this->secretStripeKeyTest);
        
        //create Order
        $order = new Order;
        $order->setUser($user);
        $order->setAmount($product->getPrice());
        $order->setStatus('waiting for payment');
        $order->setCreated(new \DateTime(date('Y-m-d H:i:s')));

        $this->em->persist($order);
        $this->em->flush();

        //Convert euro in centim
        $price = $product->getPrice() * 100 ;
        
        //TODO: Create success and cancel URL and redirect payment
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
              'name' => $product->getName(),
              'description' => $product->getDescription(),
              //'images' => ['./' . $product->getImg1()],
              'amount' => $price,
              'currency' => 'eur',
              'quantity' => 1,
            ]],
            'success_url' => 'https://WebItemMarket.com/sucesspayment',
            'cancel_url' => 'https://WebItemMarket/cancelURL',
          ]);

        return $session;


    }

    public function redirectToPayment(){
        dd('redirect to payment');
    }



}