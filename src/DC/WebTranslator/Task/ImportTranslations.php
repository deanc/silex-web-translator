<?php

namespace DC\WebTranslator\Task;

use Symfony\Component\Console\Command\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Bridge\Twig\Translation\TwigExtractor;

require_once(__DIR__ . '/../../../../vendor/autoload.php');

class FixLatLngs extends Command
{
    protected function configure()
    {
        $this->setName('wt:import')
            ->setDescription('Imports translations');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->writeln("Beginning import");

        $twigExtractor = new TwigExtractor();

        $output->writeln("Import complete - <info>10</info> new translations found");
    }
}


