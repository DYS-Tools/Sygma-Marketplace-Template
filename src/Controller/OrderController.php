<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Service\payment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OrderController extends AbstractController
{

    private $client;

    public function __construct(HttpClientInterface $client )
    {
        $this->client = $client;
    }

    //////////////////////////////////////////////////////
    /* Sacha Dev test */
    //////////////////////////////////////////////////////

    /**
     * @Route("/order/{product}", name="order_test")
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

    //////////////////////////////////////////////////////
    /* Yohann Dev test */
    //////////////////////////////////////////////////////

    /**
     * @Route("/order/{product}", name="order")
     * @Security("is_granted('ROLE_USER')")
     * get id product and render order page
     */
    public function createOrderWithPaypal(payment $payment, $product)
    {
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $product = $productRepository->findOneBy(['id' => $product]);
        $user = $this->getUser();

        $session = $payment->makePayment($product, $user);

        return $this->render('order/order.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/createOrder", name="create_order")
     * @Security("is_granted('ROLE_USER')")
     * get id product and render order page
     */
    public function create_order(HttpClientInterface $client,payment $payment)
    {
        // test function : http://localhost/perso/Web-Item-Market/public/createOrder
        // https://developer.paypal.com/docs/platforms/checkout/set-up-payments/

        $BearerAccessToken = $payment->connectPaypal();
        $accessToken= $BearerAccessToken;

        $ch = curl_init('https://api.sandbox.paypal.com/v2/checkout/orders');

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $accessToken,
            'Accept: application/json',
            'Content-Type: application/json'
        ));

        // SSL
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $payloadData = '
        {
          "intent" : "CAPTURE",
          "purchase_units" :[
            {
              "amount" :{
                "currency_code" : "EUR",
                "value" : "7.47"
              }
            }
          ]
        }';

        curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadData);

        // brut
        //$result = curl_exec($ch);
        //dump($result);

        $response = json_decode(curl_exec($ch), true);
        dd($response);
        // debug

        $err = curl_error($ch); // dump($err) ;
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // dd($httpStatusCode);

        curl_close($ch);

        // return id Order ?
    }

    /**
     * @Route("/captureOrder", name="capture_order")
     * @Security("is_granted('ROLE_USER')")
     * payout approuved
     */
    public function capture_order()
    {
        // https://developer.paypal.com/docs/platforms/checkout/set-up-payments/
        // Todo : a la finalisation du paiement (  défini dans le js dans Order/order.html.twig )
        /*
        curl -v -k -X POST https://api.paypal.com/v2/checkout/orders/5O190127TN364715T/capture \
         -H 'PayPal-Request-Id: 7b92603e-77ed-4896-8e78-5dea2050476a' \
         -H 'PayPal-Partner-Attribution-Id:  BN-Code' \
         -H 'Authorization: Bearer Access-Token' \
         -H 'Content-Type: application/json' \
         -d '{}'
        */
    }





}
