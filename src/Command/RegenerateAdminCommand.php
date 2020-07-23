<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegenerateAdminCommand extends Command
{
    protected static $defaultName = 'regenerate:admin';
    private $em;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct();
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function configure()
    {
        $this
            ->setDescription('Regenerate 2 administrator : Sacha and yohann (prod) ')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Password for no admin found usage ')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Password for no admin found usage ')
            
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $io = new SymfonyStyle($input, $output);

        //$em = $this->container->get('doctrine')->getManager();

        $arg1 = $input->getArgument('arg1');


        // find sacha6623@gmail.com // verifier l'existance, verifier le role
        $adminSacha = $this->userRepository->findOneBy(['email' => 'sacha6623@gmail.com']);
        $adminYohann = $this->userRepository->findOneBy(['email' => 'yohanndurand76@gmail.com']);

        if(empty($adminSacha)){
            if($arg1){
                $admin = new User;
                $admin->setEmail('sacha6623@gmail.com');
                $admin->setRoles(["ROLE_ADMIN"]);
                $admin->setPassword($arg1);
                $admin->setPassword($this->passwordEncoder->encodePassword($admin,$arg1));
                $admin->setAvailablePayout(0);
                $admin->setPseudo('sacha');

                $this->em->persist($admin);
                $this->em->flush();
                $output->write('New user created for Sacha' );
            }
            else{
                $output->write('Admin sacha not found, No argument given' );
            }
        }
        elseif(empty($adminYohann)){
            if($arg1){
                $admin = new User;
                $admin->setEmail('yohanndurand76@gmail.com');
                $admin->setRoles(["ROLE_ADMIN"]);
                $admin->setPassword($arg1);
                $admin->setPassword($this->passwordEncoder->encodePassword($admin,$arg1));
                $admin->setAvailablePayout(0);
                $admin->setPseudo('Yohann');

                $this->em->persist($admin);
                $this->em->flush();
                $output->write('New user created for Yohann' );
            }
            else{
                $output->write('Admin Yohann not found, No argument given' );
            }
        }

        elseif($adminSacha->getRoles() != ["ROLE_ADMIN"] ){
            $adminSacha->setRoles(["ROLE_ADMIN"]);
            $this->em->persist($adminSacha);
            $this->em->flush();
            $output->write('Sacha : Role has been changed' );

        }

        elseif($adminYohann->getRoles() != ["ROLE_ADMIN"] ){
            $adminYohann->setRoles(["ROLE_ADMIN"]);
            $this->em->persist($adminYohann);
            $this->em->flush();
            $output->write('Yohann : Role has been changed' );

        }

        /*
        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }
        */

        $io->success('Administrator has been regenerated');

        return 0;
        // return Command::SUCCESS;
    }
}
