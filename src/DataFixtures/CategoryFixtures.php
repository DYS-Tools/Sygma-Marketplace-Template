<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public const ONECATEGORY = 'ONECATEGORY';
    public const TWOCATEGORY = 'ONECATEGORY';
    public const THREECATEGORY = 'ONECATEGORY';

    public function load(ObjectManager $manager)
    {
        // Create Order for user
        $category = new Category();
        $category->setName("CSS template");
        $category->setDescription("This category...............................");
        $this->addReference('ONECATEGORY',$category);


        $manager->persist($category);

        $category2 = new Category();
        $category2->setName("Wordpress theme");
        $category2->setDescription("This category...............................");
        $this->addReference('TWOCATEGORY',$category2);

        $manager->persist($category2);

        /*
        $category3 = new Category();
        $category3->setName("Plugin");
        $category3->setDescription("This category...............................");
        $this->addReference('THREECATEGORY',$category3);

        $manager->persist($category3);
        */

        $manager->flush();
    }

}