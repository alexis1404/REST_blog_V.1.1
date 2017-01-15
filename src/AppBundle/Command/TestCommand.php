<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Question\Question;

class TestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:test-comm')

            ->setDescription('Test command')

            ->setHelp('Simple test console command')

            ->addArgument('data', InputArgument::REQUIRED, 'Input your data: ')
            ->addArgument('name', InputArgument::OPTIONAL, 'Second data?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Test exec',
            '============',
            '',
        ]);

        $output->writeln(
            '<info>Hello! '
        );

        $helper = $this->getHelper('question');

        $question = new Question('What is the database password? ');

        $password = $helper->ask($input, $output, $question);

        $question = new Question('Your email: ');

        $email = $helper->ask($input, $output, $question);

        $output->writeln('Its simple test command : ' . $input->getArgument('data'));
        $output->writeln('Second data: ' . $input->getArgument('name'));
        $output->writeln('PASSWORD: ' . $password);
        $output->writeln('EMAIL: ' . $email . '</info>');
    }
}