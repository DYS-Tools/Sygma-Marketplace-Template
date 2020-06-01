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
        // Create Order for user
        $product = new Product();
        $product->setName("Product One");
        $product->setPrice(50);
        $product->setDescription("Quality Product");
        $product->setContent("Quality Product perfect...................");
        $product->setFile("name_product.zip");
        $product->setPublished( new \DateTime() );
        $product->setUpdated( new \DateTime() );

        $product->setUser($this->getReference('YADMIN'));
        $product->setCategory( $this->getReference('ONECATEGORY'));

        // add reference for media
        $this->addReference('PRODUCT',$product);

        $manager->persist($product);
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