<?php

namespace MagentoHackathon\PluginVisualization\Console\Command;

use Magento\Setup\Module\Di\Code\Scanner\ConfigurationScanner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends Command
{
    /**
     * @var ConfigurationScanner
     */
    private $configurationScanner;

    public function __construct(
        ConfigurationScanner $configurationScanner
    ) {
        $this->configurationScanner = $configurationScanner;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('dev:plugin:list')
            ->setDescription('List plugins');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files = $this->configurationScanner->scan('di.xml');
        $rows = [];
        foreach ($files as $plugin) {
            $rows[] = [$plugin];
        }
        $tableHelper = new Table($output);
        $tableHelper
            ->setHeaders(['di.xml files'])
            ->setRows($rows)
            ->render();
    }
}
