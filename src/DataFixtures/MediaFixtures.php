<?php

namespace App\DataFixtures;

use App\Entity\Media;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;




class MediaFixtures extends Fixture
{
    public const ONECATEGORY = 'ONECATEGORY';

    public function load(ObjectManager $manager)
    {
        // Create Order for user
        $media = new Media();
        $media->setPath("Media6Product6.jpg");
        $media->setText("This picture");
        $media->setThumbnail(0);

        //$this->addReference('ONECATEGORY',$media);
        $manager->persist($media);
        $manager->flush();
    }

}