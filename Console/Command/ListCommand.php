<?php

namespace MagentoHackathon\PluginVisualization\Console\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('dev:plugin:list')
            ->setDescription('List plugins');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $types = $this->getTypes();
        $rows = [];
        foreach ($types as $plugin) {
            $rows[] = [$plugin];
        }
        $tableHelper = new Table($output);
        $tableHelper
            ->setHeaders(['Types'])
            ->setRows($rows)
            ->render();
    }
}
