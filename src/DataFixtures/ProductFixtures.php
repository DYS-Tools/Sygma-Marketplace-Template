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
        // Create Product no verified
        $product = new Product();
        $product->setName("Formulaire de contact");
        $product->setDescription("Formulaire de contact...");
        $product->setContent("Formulaire de contact, UX Design ..");
        $product->setFile("name_product.zip");
        $product->setPublished( new \DateTime() );
        $product->setDemoLink("https://demo.projet.com" );
        $product->setNumberSale(47 );
        $product->setPrice(50);
        $product->setUser($this->getReference('YADMIN'));
        $product->setCategory( $this->getReference('ONECATEGORY'));
        $product->setVerified(0);
        $product->setImg1("firstImage.png");
        // add reference for media
        $this->addReference('PRODUCT',$product);
        $manager->persist($product);

        // Create Product no verified
        $product1 = new Product();
        $product1->setName("Footer");
        $product1->setDescription("Footer avec images");
        $product1->setContent("Footer idéal.........");
        $product1->setFile("name_footer.zip");
        $product1->setPublished( new \DateTime() );
        $product1->setDemoLink("https://demo.projet1.com" );
        $product1->setNumberSale(2 );
        $product1->setPrice(1);
        $product1->setUser($this->getReference('SADMIN'));
        $product1->setCategory( $this->getReference('TWOCATEGORY'));
        $product1->setVerified(0);
        $product1->setImg1("secondImage.png");
        // add reference for media
        $this->addReference('PRODUCT1',$product1);
        $manager->persist($product1);

        // Create Product Verified
        $product2 = new Product();
        $product2->setName("Theme WordPress");
        $product2->setDescription("WordPress Simple et facile");
        $product2->setContent("Theme pour entreprise");
        $product2->setFile("WordPress-theme.zip");
        $product2->setPublished( new \DateTime() );
        $product2->setDemoLink("https://demo.projet2.com" );
        $product2->setNumberSale(19 );
        $product2->setPrice(67);
        $product2->setUser($this->getReference('SADMIN'));
        $product2->setCategory( $this->getReference('TWOCATEGORY'));
        $product2->setVerified(1);
        $product2->setImg1("WPimg1.png");
        $product2->setImg2("WPimg2.png");
        $product2->setImg3("WPimg3.png");
        // add reference for media
        $this->addReference('PRODUCT2',$product2);
        $manager->persist($product2);




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