<?php

namespace MagentoHackathon\PluginVisualization\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VisualizeCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('dev:plugin:visualize')
            ->setDescription('Visualize sort order of plugins');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $types = $this->getTypes();
        //@todo: continue here
    }
}
