<?php
namespace MPM\Console\Commands;

use MPM\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Get the specified versions of all dependencies.')
            ->addArgument('name', InputArgument::REQUIRED, 'The script name to run');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Config::runScript(getcwd(), $input->getArgument('name'));
    }
}