<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserFixtures extends Fixture
{
    public const AUTHOR = 'AUTHOR';
    public const USER = 'USER';
    public const SADMIN = 'SADMIN';
    public const YADMIN = 'YADMIN';

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        // Create Yohann Admin
        $yohann = new User();
        $yohann->setEmail('yohanndurand76@gmail.com');
        $yohann->setPassword($this->passwordEncoder->encodePassword($yohann,'dev'));
        $yohann->setRoles(["ROLE_ADMIN"]);
        $yohann->setPseudo('yohann');
        $this->addReference('YADMIN',$yohann);
        $manager->persist($yohann);

        // Create Sacha Admin
        $sacha = new User();
        $sacha->setEmail('sacha6623@gmail.com');
        $sacha->setPassword($this->passwordEncoder->encodePassword($sacha,'000000'));
        $sacha->setRoles(["ROLE_ADMIN"]);
        $sacha->setPseudo('sacha');
        $this->addReference('SADMIN',$sacha);
        $manager->persist($sacha);

        // Create User
        $user = new User();
        $user->setEmail('user@gmail.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user,'dev'));
        $user->setRoles(["ROLE_USER"]);
        $user->setPseudo('user1');
        $this->addReference('USER',$user);
        $manager->persist($user);

        // Create Author
        $author = new User();
        $author->setEmail('author@gmail.com');
        $author->setPassword($this->passwordEncoder->encodePassword($author,'dev'));
        $author->setRoles(["ROLE_AUTHOR"]);
        $author->setPseudo('author1');
        $this->addReference('AUTHOR',$author);
        $manager->persist($author);

        $manager->flush();

    }
}