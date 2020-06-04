<?php

namespace App\DataFixtures;

use App\Entity\Order;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // Create Order for user
        $order = new Order();
        $order->setAmount(50);
        $order->setStatus("In progress");
        $order->setCreated( new \DateTime() );
        $order->setUser($this->getReference('YADMIN'));

        $manager->persist($order);
        $manager->flush();
    }

    // DependentFixtureInterface :  Load UserFixtures before OrderFixture
    public function getDependencies()
    {
        return array(
            UserFixtures::class
        );
    }

}