<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Service\payment;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OrderController extends AbstractController
{

    private $client;

    public function __construct(HttpClientInterface $client, EntityManagerInterface $em )
    {
        $this->client = $client;
        $this->em = $em;
    }

    /**
     * @Route("/order/{product}", name="order")
     * @Security("is_granted('ROLE_USER')")
     * get id product and render order page
     */
    public function createOrderWithPaypal(payment $payment, $product)
    {
        // get product
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $product = $productRepository->findOneBy(['id' => $product]);

        // get current user
        $user = $this->getUser();

        // Create Order
        $order = new Order;
        $order->setUser($user);
        $order->setAmount($product->getPrice());
        $order->setStatus('waiting for payment');
        $order->setCreated( new \DateTime() );
        $order->setProduct($product);

        $this->em->persist($order);
        $this->em->flush();

        return $this->render('order/choosePayment.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/successPayment", name="successPayment")
     * @Security("is_granted('ROLE_USER')")
     * if payement succes
     */
    public function successPayment(payment $payment, \Swift_Mailer $mailer)
    {
        // get current user
        $user = $this->getUser();

        // edit order for status = finished
        $orderRepository = $this->getDoctrine()->getRepository(Order::class);
        $order = $orderRepository->findLastOrderOfUser($user);
        $order[0]->setStatus("finish");

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($order[0]);
        $entityManager->flush();

        // update available payout Author
        // Todo : Security , if user is author , if admin...
        $orderAmount = $order[0]->getAmount() ; // get Amount in order
        $creditAuthor = $orderAmount * 0.80; // 20% taxe

        $orderAuthor = $order[0]->getUser(); // price order
        $orderAuthor->setAvailablePayout($creditAuthor); // Todo : Not float ?

        $entityManager->persist($orderAuthor); // persist
        $entityManager->flush();

        // update NumberSale + 1
        $numberSaleProduct = $order[0]->getProduct()->getNumberSale(); // get NumberSale in product
        $orderProduct = $order[0]->getProduct() ; // get product in Order
        $orderProduct->setNumberSale($numberSaleProduct + 1); // + 1 // Todo : Loop if reload

        $entityManager->persist($orderProduct); // persist
        $entityManager->flush();


        // send mail
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

        // Add flash message -> Todo : SweetAlert
        $this->addFlash('success', "Email has been send");

        return $this->render('order/successPayment.html.twig', [
        ]);
    }


    /**
     * @Route("/errorPayment", name="errorPayment")
     */
    public function errorPayment(payment $payment, $product)
    {
        // return view error
        return $this->render('order/errorPayment.html.twig', [
        ]);
    }

    /**
     * @Route("/createOrder", name="create_order" , methods={"POST"} )
     * @Security("is_granted('ROLE_USER')")
     * get id order and create Order with amount...
     */
    public function create_order(HttpClientInterface $client,payment $payment)
    {
        // test function : http://localhost/perso/Web-Item-Market/public/createOrder
        // https://developer.paypal.com/docs/platforms/checkout/set-up-payments/

        // sendbox paypal
        // sb-lz8752580455@personal.example.com
        // d]s^zA<3

        // get Token Paypal API
        $BearerAccessToken = $payment->connectPaypal();
        $accessToken= $BearerAccessToken;
        dump($accessToken);

        $ch = curl_init('https://api.sandbox.paypal.com/v2/checkout/orders');
        curl_setopt($ch, CURLOPT_POST, 1);
        // HEADER
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $accessToken,
            'Accept: application/json',
            'Content-Type: application/json'
        ));
        // SSL certificat
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        // get current user
        $user = $this->getUser();

        // get Amount Order for payment Paypal
        $orderRepository = $this->getDoctrine()->getRepository(Order::class);
        $order = $orderRepository->findLastOrderOfUser($user);

        $priceOrder = $order[0]->getAmount() ; // price order

        $payloadData = '
        {
          "intent" : "CAPTURE",
          "purchase_units" :[
            {
              "amount" :{
                "currency_code" : "USD",
                "value" : "'.$priceOrder.'"
              }
            }
          ]
        }';

        curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadData); // send JSON
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return caracter
        $result = curl_exec($ch);
        curl_close($ch);
        $resultJson = json_decode($result);  // return complete json
        dump($resultJson);
        $orderId = $resultJson->id ;  // return just OrderId
        dump($orderId);
        //return json Response
        return new JsonResponse(
            $resultJson
        );

        // curl debug
        // $err = curl_error($ch); // dump($err) ;
        //$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // dd($httpStatusCode);
    }

    /**
     * @Route("/captureOrder/{orderId}", name="capture_order")
     * @Security("is_granted('ROLE_USER')")
     * if payout approuved
     */
    public function capture_order(payment $payment, $orderId)
    {
        dump($orderId);
        // test function : http://localhost/perso/Web-Item-Market/public/captureOrder
        // https://developer.paypal.com/docs/platforms/checkout/set-up-payments/

        $BearerAccessToken = $payment->connectPaypal();
        $accessToken = $BearerAccessToken;
        dump($accessToken);

        //$ch = curl_init('https://api.paypal.com/v2/checkout/orders/'.$orderId.'/capture'); // docs
        $ch = curl_init('https://api.sandbox.paypal.com/v2/checkout/orders/'.$orderId.'/capture');  // sendbox api
        curl_setopt($ch, CURLOPT_POST, 1);
        // HEADER
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $accessToken,
            'Accept: application/json',
            'Content-Type: application/json',
            //'PayPal-Request-Id : 7b92603e-77ed-4896-8e78-5dea2050476a'
            //'PayPal-Partner-Attribution-Id:  BN-Code'
        ));
        // SSL certificat
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $payloadData = '
        {
        }';

        curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadData); // send JSON
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return caracter

        $result = curl_exec($ch);
        //dump($result);  // no json

        // get error
        // $err = curl_error($ch); // dump($err) ;
        curl_close($ch);

        $resultJson = json_decode($result);
        dd($resultJson);  // json   Error ?

        return $resultJson ;
    }
}
