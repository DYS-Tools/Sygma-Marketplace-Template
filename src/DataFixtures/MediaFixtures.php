<?php

namespace App\DataFixtures;

use App\Entity\Media;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;




class MediaFixtures extends Fixture implements DependentFixtureInterface
{
    public const ONECATEGORY = 'ONECATEGORY';

    public function load(ObjectManager $manager)
    {

        $media = new Media();
        $media->setPath("Media6Product6.jpg");
        $media->setText("This picture");
        $media->setThumbnail(0);
        $media->setProduct( $this->getReference('PRODUCT'));
        $manager->persist($media);


        $media1 = new Media();
        $media1->setPath("Media1Product1.jpg");
        $media1->setText("This picture ....");
        $media1->setThumbnail(0);
        $media1->setProduct( $this->getReference('PRODUCT1'));
        $manager->persist($media1);


        $manager->flush();
    }
    // DependentFixtureInterface :  Load ProductFixtures before MediaFixture
    public function getDependencies()
    {
        return array(
            ProductFixtures::class
        );
    }
}