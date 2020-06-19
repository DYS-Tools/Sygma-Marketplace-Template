<?php

namespace App\DataFixtures;


use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Create Article
        $article = new Article();
        $article->setTitle("Qui sommes nous ?");
        $article->setDescription("Présentation de l'équipe.");
        $article->setContent( ".............................................................." );
        $article->setImg1("Qui-sommes-nous.jpeg");
        $article->setCreatedAt(new \DateTime());
        $manager->persist($article);

        // Create Article
        $article1 = new Article();
        $article1->setTitle("Nos meilleurs produits");
        $article1->setDescription("Le top 10 de nos produits");
        $article1->setContent( ".............................................................." );
        $article1->setImg1("Top10.jpeg");
        $article1->setCreatedAt(new \DateTime());
        $manager->persist($article1);

        $manager->flush();
    }
}
