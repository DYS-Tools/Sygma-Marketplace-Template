<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public const ONECATEGORY = 'ONECATEGORY';

    public function load(ObjectManager $manager)
    {
        // Create Order for user
        $category = new Category();
        $category->setName("Category One");
        $category->setDescription("This category...............................");
        $this->addReference('ONECATEGORY',$category);

        $manager->persist($category);
        $manager->flush();
    }

}