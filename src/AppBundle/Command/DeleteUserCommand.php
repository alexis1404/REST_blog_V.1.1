<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Question\Question;

class DeleteUserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:delete_user')
            ->setDescription('Delete user')
            ->setHelp('Delete user. Format: app:delete_user 10 (10 - user ID)')
            ->addArgument('id_user', InputArgument::REQUIRED, 'Input ID user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = $this->getContainer()->get('user_manager')->getOneUser($input->getArgument('id_user'));

        if(!$user){
            $output->writeln('<error>User not found! (Unknown ID)</error>');
        }else{
            $this->getContainer()->get('user_manager')->deleteUser($input->getArgument('id_user'));
            $output->writeln('<info>User successfully removed</info>');
        }
    }

}