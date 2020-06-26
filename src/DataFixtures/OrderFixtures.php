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
        $order->setProduct($this->getReference('FPRODUCT'));
        $manager->persist($order);

        $order1 = new Order();
        $order1->setAmount(48);
        $order1->setStatus("finish");
        $order1->setCreated( new \DateTime() );
        $order1->setUser($this->getReference('SADMIN'));
        $order1->setProduct($this->getReference('FPRODUCT'));
        $manager->persist($order1);

        // Create Order for user
        $order2 = new Order();
        $order2->setAmount(12);
        $order2->setStatus("waiting for payment");
        $order2->setCreated( new \DateTime() );
        $order2->setUser($this->getReference('AUTHOR'));
        $order2->setProduct($this->getReference('FPRODUCT'));  
        $manager->persist($order2);

        $manager->flush();
    }

    // DependentFixtureInterface :  Load UserFixtures before OrderFixture
    public function getDependencies()
    {
        return array(
            ProductFixtures::class
        );
    }

}