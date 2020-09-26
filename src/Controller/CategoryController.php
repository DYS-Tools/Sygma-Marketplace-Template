<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\NewCategoryFormType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
//use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends AbstractController
{

    /**
     * @Route("/dashboard/category/new", name="category_new")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function category_new(Request $request, EntityManagerInterface $em)
    {
        //$form->get('Reponse')->getData();

        $form = $this->createForm(NewCategoryFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = new Category;
            $category->setName($form->get('name')->getData());
            $category->setDescription($form->get('description')->getData());
            $em->persist($category);
            $em-> flush();

            return $this->redirectToRoute('category_handler');
        }

        return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("dashboard/category/swap/{id}", name="category_swap")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function swap_category(Request $request, Category $category, CategoryRepository $categoryRepo, $id, ProductRepository $productRepo)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $products = $productRepository->findAll();

        $category = $categoryRepo->find($id);

        // swap the category
        if($category->getActive() == 1){
            $category->setActive(0);
        }
        elseif($category->getActive() == 0){
            $category->setActive(1);
        }
        
        //$entityManager->remove($category);
        $entityManager->persist($category);
        $entityManager->flush();

        return $this->redirectToRoute('category_handler');
    }

    public function GetAllCategory(CategoryRepository $categoryRepo)
    {
        /*
        $categories = $categoryRepo->findAll();
        //return new Response($categories);    App\\Controller\\CategoryController::GetallCategory
        //return $this->render($categories);

        //$categories = implode($categories);
        // (et l'action de controller que tu appelle peux éventuellement te renvoyer du HTML avec un renderView)
        //dd(implode($categories));
        $nav = '';
        foreach ($categories as $category){
            return new Response(
                "<li><a href="{{ path('contact') }}">" . $category . "</a></li>"
            );
        }
        */

    }
}
