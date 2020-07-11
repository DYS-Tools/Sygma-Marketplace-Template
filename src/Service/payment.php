<?php
/**
 * Created by PhpStorm.
 * User: sacha
 * Date: 14/04/2020
 * Time: 22:33
 */

namespace App\Service;


use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Stripe\Stripe;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\ScopingHttpClient;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class payment
{

    private $entityManager;
    private $mailer;
    private $user;
    private $sandbox_account_test;
    private $paypal_client_id_test;
    private $paypal_secret_test;
    private $client;

    public function __construct(EntityManagerInterface $em,$sandbox_account_test, $paypal_client_id_test , $paypal_secret_test, HttpClientInterface $client )
    {
        $this->em = $em;
        $this->client = $client;
        $this->sandbox_account_test = $sandbox_account_test;
        $this->paypal_client_id_test = $paypal_client_id_test;
        $this->paypal_secret_test = $paypal_secret_test;
    }

    /* get env var Paypal*/
    public function getPaypalSandbox()
    {
        return $this->sandbox_account_test;
    }

    public function getPaypalClientIdTest()
    {
        return $this->paypal_client_id_test;
    }

    public function getPaypalSecretTest()
    {
        return $this->paypal_secret_test;
    }
    /* end get env var Paypal */


    public function makePayment(Product $product, User $user )
    {
        // Todo : render token API ( with function connect Paypal )
        dd ( $this->connectPaypal() );

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
        return "function payout service";
    }


    //////////////////
    // Paypal
    //////////////////

    /*
     * get env var and request Paypal API
     * return token
     */
    public function connectPaypal() {
        // https://developer.paypal.com/docs/platforms/get-started/#step-1-get-api-credentials
        $response = $this->client->request('POST', 'https://api.sandbox.paypal.com/v1/oauth2/token', [
            'headers' => [
                'Accept' => 'application/json, application/x-www-form-urlencoded',
                'Accept-Language' => 'en_US',
            ],
            'auth_basic' =>  [$this->getPaypalClientIdTest(),$this->getPaypalSecretTest()],
            'body' => ['grant_type' => 'client_credentials']
        ]);
        $content = $response->getContent(); // get Content
        $contentJson = json_decode($content); // get Json

        $tokenPaypalAcces = $contentJson->access_token; // get value "acces_token"
        dump($tokenPaypalAcces);

        // token
        return $tokenPaypalAcces;
    }
}