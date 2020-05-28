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
        // Create Order for user
        $media = new Media();
        $media->setPath("Media6Product6.jpg");
        $media->setText("This picture");
        $media->setThumbnail(0);

        $media->setProduct( $this->getReference('PRODUCT'));
        $manager->persist($media);
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