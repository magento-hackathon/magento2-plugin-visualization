<?php

namespace MagentoHackathon\PluginVisualization\Console\Command;

use Magento\Framework\Module\ModuleListInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends Command
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
        $this->setName('dev:plugin:list')
            ->setDescription('List plugins');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $plugins = $this->_moduleList->getNames();
        sort($plugins);
        $rows = [];
        foreach ($plugins as $plugin) {
            $rows[] = [$plugin];
        }
        $tableHelper = new Table($output);
        $tableHelper
            ->setHeaders(['Name'])
            ->setRows($rows)
            ->render();
    }
}
