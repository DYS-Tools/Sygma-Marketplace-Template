<?php

namespace App\DataFixtures;


use App\Entity\Ticket;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TicketFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Create Ticket
        $ticket = new Ticket();
        $ticket->setName("Client4");
        $ticket->setSubject("Demande de catégorie");
        $ticket->setEmail("Yohanndurand76@gmail.com");
        $ticket->setMessage( "Lorem ipsum dolor liquam. amet mattis. Quisque egestas diam in arcu. Tempor orci dapibus ultrices in.");
        $ticket->setStatus("0");
        $manager->persist($ticket);

        // Create Ticket1
        $ticket1 = new Ticket();
        $ticket1->setName("Client4");
        $ticket1->setSubject("Demande de catégorie");
        $ticket1->setEmail("Yohanndurand76@gmail.com");
        $ticket1->setMessage( "Lorem ipsum dolor liquam. amet mattis. Quisque egestas diam in arcu. Tempor orci dapibus ultrices in.");
        $ticket1->setStatus("1");
        $manager->persist($ticket1);


        $manager->flush();
    }
}
