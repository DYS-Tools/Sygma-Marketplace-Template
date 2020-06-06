<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;



class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public const PRODUCT = 'PRODUCT';

    public function load(ObjectManager $manager)
    {
        // Create Product
        $product = new Product();
        $product->setName("Formulaire de contact");
        $product->setDescription("Formulaire de contact...");
        $product->setContent("Formulaire de contact, UX Design ..");
        $product->setFile("name_product.zip");
        $product->setPublished( new \DateTime() );
        $product->setUpdated( new \DateTime() );
        $product->setDemoLink("https://demo.projet.com" );
        $product->setNumberSale(47 );
        $product->setPrice(50);
        $product->setUser($this->getReference('YADMIN'));
        $product->setCategory( $this->getReference('ONECATEGORY'));
        // add reference for media
        $this->addReference('PRODUCT',$product);
        $manager->persist($product);

        // Create Order for user
        $product1 = new Product();
        $product1->setName("Footer");
        $product1->setDescription("Footer avec images");
        $product1->setContent("Footer idéal.........");
        $product1->setFile("name_footer.zip");
        $product1->setPublished( new \DateTime() );
        $product1->setUpdated( new \DateTime() );
        $product1->setDemoLink("https://demo.projet1.com" );
        $product1->setNumberSale(2 );
        $product1->setPrice(1);
        $product1->setUser($this->getReference('SADMIN'));
        $product1->setCategory( $this->getReference('TWOCATEGORY'));
        // add reference for media
        $this->addReference('PRODUCT1',$product1);
        $manager->persist($product1);




        $manager->flush();
    }

    // DependentFixtureInterface :  Load UserFixtures before ProductFixture
    public function getDependencies()
    {
        return array(
            UserFixtures::class
        );
    }
}