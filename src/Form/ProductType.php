<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
            ->add('description',TextareaType::class, [
                'help' => 'Description Courte',
                'attr' => ['rows' => 2],
                'label' => 'Description courte'
            ])

            ->add('file', FileType::class, [
                'label' => 'Uploader le produit',
                // https://fr.wikipedia.org/wiki/Type_de_m%C3%A9dias ( List media )
                'attr' => array(
                    'accept' => "application/zip",
                    'class' => 'custom-file-label',
                    'placeholder' => 'Déposez ici votre produit (Format ZIP)'
                ),
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => ['application/zip'],
                        'mimeTypesMessage' => 'formats autorisés: ZIP uniquement',
                    ])
                ],
                'data_class'=>null,
                'required'   => true,
                'help' => 'Fichier ZIP uniquement'
            ])
            
            ->add('content',TextareaType::class, [
                'help' => 'Description détaillée',
                'attr' => ['rows' => 10],
                'label' => 'Fiche produit ( texte détaillé )'
            ])

            ->add('img1', FileType::class, [
                'label' => 'ajouter une image ( dimension optimales 1200x900 )',
                'data_class'=>null,
                'required'   => true,
                'attr' => [
                    'class' => 'custom-file-label',
                    'placeholder' => 'Déposez une image (1200x900)'
                ],
                'help' => 'Format non valide, utilisez le format 1200x900'
            ])
            ->add('img2', FileType::class, [
                'label' => 'ajouter une image optionelle ( dimension optimales 1200x900 )',
                'data_class'=>null,
                'required'   => false,
                'attr' => [
                    'class' => 'custom-file-label',
                    'placeholder' => 'Déposez une image (1200x900)'
                ],
                'help' => 'Format non valide, utilisez le format 1200x900'
            ])
            ->add('img3', FileType::class, [
                'label' => 'ajouter une image optionelle ( dimension optimales 1200x900 )',
                'data_class'=>null,
                'required'   => false,
                'attr' => [
                    'class' => 'custom-file-label',
                    'placeholder' => 'Déposez une image (1200x900)'
                ],
                'help' => 'Format non valide, utilisez le format 1200x900'
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
