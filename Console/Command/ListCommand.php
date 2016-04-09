<?php

namespace MagentoHackathon\PluginVisualization\Console\Command;

use Symfony\Component\Console\Command\Command;

class ListCommand extends Command
{
    protected function configure()
    {
        $this->setName('dev:plugin:list');
        $this->setDescription('List plugins');
    }
}
