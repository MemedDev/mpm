<?php
namespace MPM\Console\Commands;

use MPM\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('install')
            ->setDescription('Get the specified versions of all dependencies.');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Config::install(getcwd());
    }
}