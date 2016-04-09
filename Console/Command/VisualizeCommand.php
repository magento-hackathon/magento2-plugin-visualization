<?php

namespace MagentoHackathon\PluginVisualization\Console\Command;

use Magento\Framework\Module\ModuleListInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VisualizeCommand extends Command
{
    private $_moduleList;

    public function __construct(
        ModuleListInterface $moduleList
    ) {
        $this->_moduleList = $moduleList;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('dev:plugin:visualize')
            ->setDescription('Visualize sort order of plugins');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}
