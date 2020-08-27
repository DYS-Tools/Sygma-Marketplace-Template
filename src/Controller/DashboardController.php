<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Ticket;
use App\Entity\User;
use App\Form\PayoutFormType;
use App\Form\ProductType;
use App\Form\RejectProductFormType;
use App\Form\ResolveTicketType;
use App\Repository\ArticleRepository;
use App\Service\MakeJsonFormat;
use App\Service\payment;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Require ROLE_USER for *every* controller method in this class.
 * @IsGranted("ROLE_USER")
 */
class DashboardController extends AbstractController
{

    /**
     * @Route("/dashboard/ProductVerified", name="product_verified")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function productVerifiedPage(Request $request)
    {
        // get current user
        $user = $this->getUser() ;

        return $this->render('dashboard/productVerified.html.twig', [
            'noVerifiedProduct' => $this->getDoctrine()->getRepository(Product::class)->findAllTProductNoVerified(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/dashboard/ticketHandler", name="ticket_handler")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function ticketHandler()
    {
        // get current user
        $user = $this->getUser() ;

        $ticketInProgressRepo = $this->getDoctrine()->getRepository(Ticket::class);
        $ticketInProgress = $ticketInProgressRepo->findBy(['status' => 0]);

        return $this->render('dashboard/ticketHandler.html.twig', [
            'ticketInProgress' => $ticketInProgress,
            'user' => $user,
        ]);
    }

    /**
     * @Route("/dashboard/ticketHandler/{id}", name="ticket_handler_Single")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function ticketHandlerSingle($id, Request $request, \Swift_Mailer $mailer, EntityManagerInterface $em)
    {
        // get current user
        $user = $this->getUser() ;

        $ticketInProgressRepo = $this->getDoctrine()->getRepository(Ticket::class);
        $ticket = $ticketInProgressRepo->findOneBy(['id' => $id]);

        $mail = $ticket->getEmail();

        $form = $this->createForm(ResolveTicketType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $reponse = $form->get('Reponse')->getData();

            $message = (new \Swift_Message('Web-Item-Market'))
                ->setFrom('sacha6623@gmail.com')
                ->setTo($mail)
                ->setBody(
                    $this->renderView(
                        'Emails/contact.html.twig',
                        [ 'message' => $reponse, ]), 'text/html');
            $mailer->send($message);
            $ticket->setStatus(1);
            $em->persist($ticket);
            $em->flush();
            $this->redirectToRoute('ticket_handler');

        }
        return $this->render('dashboard/ticketHandlerSingle.html.twig', [
            'ticket' => $ticket,
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/dashboard/Blog", name="article_index_admin")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function BlogAdminPage(ArticleRepository $articleRepository)
    {
        // get current user
        $user = $this->getUser() ;

        return $this->render('dashboard/blog.html.twig', [
            'articles' => $articleRepository->findAll(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/dashboard/statistic", name="statistic_admin")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function statisticAdmin(MakeJsonFormat $makeJsonFormat)
    {
        $user = $this->getUser();
        $winWithApplication = $this->getDoctrine()->getRepository(Order::class)->countTotalCommissions() + $this->getDoctrine()->getRepository(Order::class)->countOrderAdminEur();
        
        
        $ArrayForGraph = $makeJsonFormat->get30LastDaysCashflow($user);

        return $this->render('dashboard/statisticAdmin.html.twig', [
            'ProductForSell' => $this->getDoctrine()->getRepository(Product::class)->countAllProductForSell(),
            'ProductForVerified' => $this->getDoctrine()->getRepository(Product::class)->countAllProductForVerified(),
            'countOrder' => $this->getDoctrine()->getRepository(Order::class)->countAllOrder(),
            'countUser' => $this->getDoctrine()->getRepository(User::class)->countAllUser(),
            'countMember' => $this->getDoctrine()->getRepository(User::class)->countAllMember(),
            'countAdmin' => $this->getDoctrine()->getRepository(User::class)->countAllAdmin(),
            'countAuthor' => $this->getDoctrine()->getRepository(User::class)->countAllAuthor(),
            'countArticle' => $this->getDoctrine()->getRepository(Article::class)->countArticle(),
            'countTicketOpen' => $this->getDoctrine()->getRepository(Ticket::class)->countTicketOpen(),
            'countTicketClose' => $this->getDoctrine()->getRepository(Ticket::class)->countTicketClose(),
            'saveDate' => 'xx/xx/xx', // TODO: Information from SaveBot
            'orderAdminEur' => $this->getDoctrine()->getRepository(Order::class)->countOrderAdminEur(),   // TT order avec un user Admin
            'currentAvailablePayout' => $this->getDoctrine()->getRepository(User::class)->countCurrentAvailablePayout(), // TT available payout
            'totalCommissions' => $this->getDoctrine()->getRepository(Order::class)->countTotalCommissions(), // tt order amount * 0.20
            'winWithApplication' => $winWithApplication, // totalComissions + orderAdminEur,
            'ArrayForGraph' => $ArrayForGraph,
        ]);
    }

    /**
     * @Route("/dashboard/product/verified/{id}", name="app_dashboard_product_verified")
     */
    public function productVerifiedAction(Request $request,\Swift_Mailer $mailer, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $ProductNoVerified = $this->getDoctrine()->getRepository(Product::class)->find($id);
        $ProductNoVerified->setVerified(1);
        $entityManager->persist($ProductNoVerified);
        $entityManager->flush();

        $uploaderUser = $ProductNoVerified->getUser();

        $message = (new \Swift_Message('Web-Item-Market'))
                ->setFrom('sacha6623@gmail.com')
                ->setTo($uploaderUser->getEmail())
                ->setBody(
                    $this->renderView(
                        'Emails/acceptedProduct.html.twig',[
                        ]),
                 'text/html');
            $mailer->send($message);

        return $this->redirectToRoute('product_verified');
    }

    /**
     * @Route("/dashboard/Product/rejected/{id}", name="app_dashboard_product_rejected")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function productRejeted(Request $request, \Swift_Mailer $mailer, $id)
    {
        // get current user
        $user = $this->getUser() ;

        $entityManager = $this->getDoctrine()->getManager();
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        $uploaderUser = $product->getUser();

        $form = $this->createForm(RejectProductFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // \Swift_Mailer $mailer
            $message = (new \Swift_Message('Web-Item-Market'))
                ->setFrom('sacha6623@gmail.com')
                ->setTo($uploaderUser->getEmail())
                ->setBody(
                    $this->renderView(
                        'Emails/rejectedProduct.html.twig',[
                            'motif' => $form->get('Motif')->getData(),
                            'message_generique' => 'Votre produit ne correspond pas a nos attente sur la place de marché',
                        ]),
                 'text/html');
            $mailer->send($message);

            $entityManager->remove($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_verified');
        }

        return $this->render('dashboard/productRejected.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            
        ]);
    }

    /**
     * @Route("/dashboard/MySell", name="my_sell")
     * @Security("is_granted('ROLE_AUTHOR')")
     */
    public function mySell(MakeJsonFormat $makeJsonFormat)
    {
        $user = $this->getUser() ;

        $orderRepository = $this->getDoctrine()->getRepository(Order::class);
        $productRepository = $this->getDoctrine()->getRepository(Product::class);

        return $this->render('dashboard/mysell.html.twig', [
            'order' => $orderRepository->findBy(['user' => $user]),
            'user' => $this->getUser(),
            'ArrayForGraph' => $makeJsonFormat->get30LastDaysCommandsForAuthor($user),
            'MoneyGenerated' => $orderRepository->getTotalOrderAmountForOneAuthorWithRemoveCommision($user), /* argent généré en tout  ( somme commande * 0.80 ) */ 
            'authorProductNumber' => count($productRepository->findBy(['user' => $user, 'verified' => 1])), /* lenght produit author accepté par la modération */
            'CAAuthorForMonth' => $orderRepository->getTotalAmountGeneratedIn30LastDaysForAuthorWithRemoveCommision($user), 
        ]);
    }

    /**
     * @Route("/dashboard/MyOrder", name="my_order")
     */
    public function myOrder()
    {
        $user = $this->getUser() ;
        
        $orderRepository = $this->getDoctrine()->getRepository(Order::class);

        return $this->render('dashboard/myOrder.html.twig', [
            'orders' => $orderRepository->findBy(['user' => $user]),
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/dashboard/authorProduct", name="author_product")
     * @Security("is_granted('ROLE_AUTHOR')")
     */
    public function authorProduct()
    {

        $user = $this->getUser();
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $products = $productRepository->findBy(['user' => $user]);

        return $this->render('dashboard/authorProduct.html.twig', [
            'products' => $products,
            'user' => $user,
        ]);
    }


    /**
     * @Route("/dashboard/money_managment", name="money_managment")
     * @Security("is_granted('ROLE_AUTHOR')")
     */
    public function payoutAuthor(Request $request, payment $payment)
    {
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

        return $this->render('dashboard/money_managment.html.twig', [
            'products' => $products,
            'user' => $user,
            'userPayout' => $user->getAvailablePayout(),
            'form' => $form->createView(),
        ]);
    }
}
