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
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\ScopingHttpClient;
use Symfony\Component\Security\Core\User\UserInterface;


class payment
{

    private $entityManager;
    private $mailer;
    private $user;

    public function __construct(EntityManagerInterface $em )
    {
        $this->em = $em;
    }

    public function makePayment(Product $product, User $user )
    {
        //create Order
        $order = new Order;
        $order->setUser($user);
        $order->setAmount($product->getPrice());
        $order->setStatus('waiting for payment');
        $order->setCreated(new \DateTime(date('Y-m-d H:i:s')));
        $order->setProduct($product);

        $this->em->persist($order);
        $this->em->flush();

        

          // TODO: $order->setStatus('Finished');
        return ;
    }

    

    public function payout(){
        // transfet funds in acct_XXXXX account
        dd();
    }



}