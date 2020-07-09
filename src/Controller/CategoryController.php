<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\NewCategoryFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class CategoryController extends AbstractController
{
     /**
     * @Route("/dashboard/category_handler", name="category_handler")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function category_handler()
    {
        $user = $this->getUser() ;

        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        return $this->render('dashboard/categoryHandler.html.twig', [
            'categories' => $categories
        ]);
    }

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
}
