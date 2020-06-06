<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'help' => 'Nom du produit'
            ])
            ->add('description',TextType::class, [
                'help' => 'Description'
            ])
            ->add('content',TextType::class, [
                'help' => 'Contenu'
            ])

            ->add('img1', FileType::class, [
                'label' => 'ajouter une image',
                'required'   => true,
            ])
            ->add('img2', FileType::class, [
                'label' => 'ajouter une image ( option )',
                'required'   => false,
            ])
            ->add('img3', FileType::class, [
                'label' => 'ajouter une image ( option )',
                'required'   => false,
            ])

            ->add('price')

            ->add('demo_link')

            ->add('category',EntityType::class, [
                'class' => Category::class,
                'help' => 'Choisir une categorie',
                'placeholder' => 'different category',
                'choice_label' => function(Category $category) {
                    return sprintf('%s', $category->getName());
                },
            ])
            //->add('orders')  // Todo : with controller
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
