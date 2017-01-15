<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Question\Question;

class CreateSuperUserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:create_superuser')
            ->setDescription('Create superuser')
            ->setHelp('It creates a user with the specified parameters');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Start create superuser:',
            '============',
            '',
        ]);

        $helper = $this->getHelper('question');

        $question = new Question('User name: ');

        $name = $helper->ask($input, $output, $question);

        $question = new Question('User email: ');

        $email = $helper->ask($input, $output, $question);

        $question = new Question('User active (1 or 0): ');

        $active = $helper->ask($input, $output, $question);

        $question = new Question('User role (ROLE_USER or ROLE_ADMIN): ');

        $role = $helper->ask($input, $output, $question);

        $question = new Question('User password (no more 30 symbol and no less 5): ');

        $password = $helper->ask($input, $output, $question);

        $result = $this->getContainer()->get('user_manager')->createSuperUser(
            $name,
            $email,
            $role,
            $active,
            $password
        );

        if($result){

            $output->writeln('<info>User successfully created. ApiKey:  </info>' . $result);
        }else{
            $output->writeln('<error>User not created! Check your entries!</error>');
        }
    }
}