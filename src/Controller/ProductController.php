<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Form\SearchProductFormType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\Upload;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Knp\Component\Pager\PaginatorInterface;


class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product_index")
     */
    public function index(ProductRepository $productRepository, PaginatorInterface $paginator, CategoryRepository $categoryRepository, Request $request): Response
    {
        $product = $productRepository->findAllTProductVerified();

        $searchForm = $this->createForm(SearchProductFormType::class);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $keyword = $searchForm->get('search')->getData();
            $product = $productRepository->findLike($keyword);

            if (empty($searchForm->get('search')->getData()))
            {
                $productRepository->findAllTProductVerified();

            }

            return $this->redirectToRoute('product_with_search', array(
                'keyword' => $keyword,
            ));
        }

        $pagination = $paginator->paginate(
            $product, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAllTProductVerified(),
            'categories' => $categoryRepository->findAll(),
            'pagination' => $pagination,
            'searchForm' => $searchForm->createView()
        ]);
    }

    /**
     * @Route("/search/{keyword}", name="product_with_search", methods={"GET"})
     */
    public function productWithSearch(ProductRepository $productRepository, PaginatorInterface $paginator, Request $request, CategoryRepository $categoryRepository , $keyword): Response
    {
        $products = $productRepository->findLike($keyword);

        $searchForm = $this->createForm(SearchProductFormType::class);
        $searchForm->handleRequest($request);


        $pagination = $paginator->paginate(
            $products, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('product/searchProduct.html.twig', [
            'products' => $products,
            'pagination' => $pagination,
            'searchForm' => $searchForm->createView()
        ]);
    }


    /**
     * @Route("/category/{id}", name="product_with_category", methods={"GET"})
     */
    public function productWithCategory(ProductRepository $productRepository, PaginatorInterface $paginator, Request $request, CategoryRepository $categoryRepository, $id): Response
    {
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $products = $productRepository->findBy(['category' => $id]);

        $searchForm = $this->createForm(SearchProductFormType::class);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $keyword = $searchForm->get('search')->getData();
            $products = $productRepository->findLike($keyword);

            if (empty($searchForm->get('search')->getData()))
            {
                $productRepository->findAllTProductVerified();
            }
        }

        $pagination = $paginator->paginate(
            $products, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        $category = $categoryRepository->find($id);

        return $this->render('product/categoryProduct.html.twig', [
            'products' => $products,
            'categories' => $category,
            'pagination' => $pagination,
            'searchForm' => $searchForm->createView()
        ]);
    }

    /**
     * @Route("product/new", name="product_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_AUTHOR')")
     * 
     */
    public function new(Request $request, Upload $upload, \Swift_Mailer $mailer): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $product->setUser($this->getUser());


            $fileName1 = $upload->upload($form->get('img1')->getData());
            $product->setImg1($fileName1);

            if($form->get('img2')->getData()){
                $fileName2 = $upload->upload($form->get('img2')->getData());
                $product->setImg2($fileName2);
            }
            if($form->get('img3')->getData()){
                $fileName3 = $upload->upload($form->get('img3')->getData());
                $product->setImg3($fileName3);
            }

            if($form->get('file')->getData()){
                $fileName3 = $upload->uploadFile($form->get('file')->getData());
                $product->setFile($fileName3);
            }
            //upload des images
            
            $product->setPublished(new \Datetime('now'));   //2020-06-06 14:52:49
            $entityManager->persist($product);
            $entityManager->flush();

             // \Swift_Mailer $mailer
             $message = (new \Swift_Message('Web-Item-Market'))
             ->setFrom('sacha6623@gmail.com')
             ->setTo($this->getUser()->getEmail())
             ->setBody(
                 $this->renderView(
                     'Emails/upload.html.twig',
                     []),
              'text/html');
            $mailer->send($message);

         $this->addFlash('success', "Email has been send");
            
            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("product/{id}", name="product_show", methods={"GET"})
     */
    public function show(Product $product, $id): Response
    {
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $products = $productRepository->findBy(['id' => $id]);
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("product/{id}/edit", name="product_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_AUTHOR')")
     */
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("product/{id}", name="product_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_AUTHOR')")
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index');
    }
}
