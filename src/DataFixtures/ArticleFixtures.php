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
        $article->setContent( "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Dignissim enim sit amet venenatis urna cursus. Scelerisque in dictum non consectetur a erat. Accumsan lacus vel facilisis volutpat est. Eget nulla facilisi etiam dignissim diam quis enim. Turpis cursus in hac habitasse platea. Enim nulla aliquet porttitor lacus. Augue lacus viverra vitae congue. Mi proin sed libero enim sed faucibus turpis in eu. Neque laoreet suspendisse interdum consectetur libero id faucibus. Orci nulla pellentesque dignissim enim. Odio ut enim blandit volutpat maecenas volutpat blandit.
                                       Pharetra magna ac placerat vestibulum. Risus sed vulputate odio ut enim blandit volutpat maecenas. Velit scelerisque in dictum non consectetur. Etiam sit amet nisl purus in mollis. Nunc mattis enim ut tellus elementum. Urna duis convallis convallis tellus id. Ultrices sagittis orci a scelerisque purus semper eget duis at. Ac tincidunt vitae semper quis lectus nulla at volutpat. Cras fermentum odio eu feugiat pretium. Facilisis volutpat est velit egestas. Sed blandit libero volutpat sed cras. Sed sed risus pretium quam vulputate dignissim suspendisse. Lacus vel facilisis volutpat est velit. Morbi tempus iaculis urna id volutpat. Ultricies mi quis hendrerit dolor magna eget est lorem.
                                       Id diam vel quam elementum pulvinar. Id consectetur purus ut faucibus pulvinar elementum integer enim neque. Ipsum suspendisse ultrices gravida dictum fusce ut. Odio ut enim blandit volutpat maecenas volutpat blandit aliquam. Quis blandit turpis cursus in hac habitasse platea. Consectetur a erat nam at lectus urna. Etiam sit amet nisl purus in mollis nunc. Laoreet id donec ultrices tincidunt arcu. Eget aliquet nibh praesent tristique. Nunc consequat interdum varius sit amet mattis. Quisque egestas diam in arcu. Tempor orci dapibus ultrices in."
        );
        $article->setImg1("Qui-sommes-nous.jpeg");
        $article->setCreatedAt(new \DateTime());
        $manager->persist($article);

        // Create Article
        $article1 = new Article();
        $article1->setTitle("Nos meilleurs produits");
        $article1->setDescription("Le top 10 de nos produits");
        $article1->setContent( "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Dignissim enim sit amet venenatis urna cursus. Scelerisque in dictum non consectetur a erat. Accumsan lacus vel facilisis volutpat est. Eget nulla facilisi etiam dignissim diam quis enim. Turpis cursus in hac habitasse platea. Enim nulla aliquet porttitor lacus. Augue lacus viverra vitae congue. Mi proin sed libero enim sed faucibus turpis in eu. Neque laoreet suspendisse interdum consectetur libero id faucibus. Orci nulla pellentesque dignissim enim. Odio ut enim blandit volutpat maecenas volutpat blandit.
                                       Pharetra magna ac placerat vestibulum. Risus sed vulputate odio ut enim blandit volutpat maecenas. Velit scelerisque in dictum non consectetur. Etiam sit amet nisl purus in mollis. Nunc mattis enim ut tellus elementum. Urna duis convallis convallis tellus id. Ultrices sagittis orci a scelerisque purus semper eget duis at. Ac tincidunt vitae semper quis lectus nulla at volutpat. Cras fermentum odio eu feugiat pretium. Facilisis volutpat est velit egestas. Sed blandit libero volutpat sed cras. Sed sed risus pretium quam vulputate dignissim suspendisse. Lacus vel facilisis volutpat est velit. Morbi tempus iaculis urna id volutpat. Ultricies mi quis hendrerit dolor magna eget est lorem.
                                       Id diam vel quam elementum pulvinar. Id consectetur purus ut faucibus pulvinar elementum integer enim neque. Ipsum suspendisse ultrices gravida dictum fusce ut. Odio ut enim blandit volutpat maecenas volutpat blandit aliquam. Quis blandit turpis cursus in hac habitasse platea. Consectetur a erat nam at lectus urna. Etiam sit amet nisl purus in mollis nunc. Laoreet id donec ultrices tincidunt arcu. Eget aliquet nibh praesent tristique. Nunc consequat interdum varius sit amet mattis. Quisque egestas diam in arcu. Tempor orci dapibus ultrices in."
        );
        $article1->setImg1("Top10.jpeg");
        $article1->setCreatedAt(new \DateTime());
        $manager->persist($article1);

        $manager->flush();
    }
}
