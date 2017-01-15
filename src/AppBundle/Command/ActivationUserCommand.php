<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Question\Question;

class ActivationUserCommand extends  ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:user_activate')
            ->setDescription('Activated user')
            ->setHelp('Activation user according ID. For example: app:user_activate 42')
            ->addArgument('id_user', InputArgument::REQUIRED, 'Input user ID: ');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $result = $this->getContainer()->get('user_manager')->userActivation($input->getArgument('id_user'));

        $output->writeln($result);
    }
}