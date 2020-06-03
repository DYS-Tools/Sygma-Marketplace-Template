<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'help' => 'Choose a name for your exceptional product!'
            ])
            ->add('description',TextType::class, [
                'help' => 'Make a description'
            ])
            ->add('content',TextType::class, [
                'help' => 'Make a content'
            ])

            //->add('file', CollectionType::class, [
                //'entry_type' => PictureFormType::class,
                //'allow_add' => true,
                //'required'   => false,

            //])
            
            //->add('published') // Todo : with controller
            // ->add('updated')  // Todo : with controller
            ->add('price')

            ->add('category',EntityType::class, [
                'class' => Category::class,
                'help' => 'select a category',
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
