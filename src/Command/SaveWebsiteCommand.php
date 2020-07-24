<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Tester\CommandTester;

class SaveWebsiteCommand extends Command
{
    protected static $defaultName = 'save:website';

    protected function configure()
    {
        $this
            ->setDescription('Save a website ( database + product file ) and commit this in a github repository')
            /*->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')*/
            /*->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')*/
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        

        $command = 'ansible-playbook save/save_playbook.yml -i ansible/hosts.ini --ask-pass'; 
        // $command must be a command ( app:xxxx ) instance
        $command->execute();

        $io->success('Your website has been saved');

        return 0;



        /* Make a database export */ 

        /* Lancer un playbook de sauvegarde ( ansible + copy product + commit )
        ansible-playbook save_playbook/.yml
        ansible-playbook save/save_playbook.yml -i ansible/hosts.ini --ask-pass




        /*
        connect SSH // Voir Avec ansible
        apt-get install mysqldump
        command export : mysqldump -u loginbdd -p nomdelabdd > /var/www/vhosts/votresite.com/httpdocs/save_wim00.sql
        // command export sans mdp : mysqldump -u loginbdd -pmotdepassedelabdd nomdelabdd > /var/www/vhosts/votresite.com/httpdocs/save_wim00.sql
        */

        /* Save Product folder*/    
        /* Commit this file */    






        /*
        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }
        

        if ($input->getOption('option1')) {
            // ...
        }
        */
    }
}
