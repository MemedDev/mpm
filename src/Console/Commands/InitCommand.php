<?php
namespace MPM\Console\Commands;

use MPM\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Create a new config file for the project.');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Config::init();
    }
}