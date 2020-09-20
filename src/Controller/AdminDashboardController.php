<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Ticket;
use App\Entity\User;
use App\Form\RejectProductFormType;
use App\Form\ResolveTicketType;
use App\Repository\ArticleRepository;
use App\Service\MakeJsonFormat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Require ROLE_ADMIN for *every* controller method in this class.
 * @IsGranted("ROLE_ADMIN")
 */
class AdminDashboardController extends AbstractController
{

    /**
     * @Route("/SY_admin/Home", name="admin_home")
     */
    public function AdminHome(Request $request)
    {
        // get current user
        $user = $this->getUser() ;

        return $this->render('admin/home.html.twig', [
            'user' => $user,
        ]);
    }

     /**
     * @Route("/SY_admin/ProductVerification", name="product_verified")
     */
    public function productVerifiedPage(Request $request)
    {
        // get current user
        $user = $this->getUser() ;

        return $this->render('admin/productVerified.html.twig', [
            'noVerifiedProduct' => $this->getDoctrine()->getRepository(Product::class)->findAllTProductNoVerified(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/SY_admin/ticketHandler", name="ticket_handler")
     */
    public function ticketHandler()
    {
        // get current user
        $user = $this->getUser() ;

        $ticketInProgressRepo = $this->getDoctrine()->getRepository(Ticket::class);
        $ticketInProgress = $ticketInProgressRepo->findBy(['status' => 0]);

        return $this->render('admin/ticketHandler.html.twig', [
            'ticketInProgress' => $ticketInProgress,
            'user' => $user,
        ]);
    }

    /**
     * @Route("/SY_admin/ticketHandler/{id}", name="ticket_handler_Single")
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
        return $this->render('admin/ticketHandlerSingle.html.twig', [
            'ticket' => $ticket,
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/SY_admin/Blog", name="article_index_admin")
     */
    public function BlogAdminPage(ArticleRepository $articleRepository)
    {
        // get current user
        $user = $this->getUser() ;

        return $this->render('admin/blog.html.twig', [
            'articles' => $articleRepository->findAll(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/SY_admin/statistic", name="statistic_admin")
     */
    public function statisticAdmin(MakeJsonFormat $makeJsonFormat)
    {
        $user = $this->getUser();
        $winWithApplication = $this->getDoctrine()->getRepository(Order::class)->countTotalCommissions() + $this->getDoctrine()->getRepository(Order::class)->countOrderAdminEur();
        
        
        $ArrayForGraph = $makeJsonFormat->get30LastDaysCashflow($user);

        return $this->render('admin/statisticAdmin.html.twig', [
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
     * @Route("/SY_admin/Product/rejected/{id}", name="app_dashboard_product_rejected")
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

        return $this->render('admin/productRejected.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            
        ]);
    }

    /**
     * @Route("/SY_admin/product/verified/{id}", name="app_dashboard_product_verified")
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
     * @Route("/SY_admin/category_handler", name="category_handler")
     */
    public function category_handler()
    {
        $user = $this->getUser() ;

        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $products = $productRepository->findAll();

        return $this->render('admin/categoryHandler.html.twig', [
            'categories' => $categories,
            'product' => $products
        ]);
    }

    /**
     * @Route("/SY_admin/laboratory", name="laboratory")
     */
    public function laboratory()
    {
        return $this->render('admin/laboratory.html.twig', [
            'user' => $this->getUser()
            
        ]);
    }
}
