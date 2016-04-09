<?php

namespace MagentoHackathon\PluginVisualization\Console\Command;

use Magento\Setup\Module\Di\Code\Scanner\ConfigurationScanner;
use MagentoHackathon\PluginVisualization\Model\Scanner\Plugin;
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
    /**
     * @var Plugin
     */
    private $pluginScanner;

    public function __construct(
        ConfigurationScanner $configurationScanner,
        Plugin $pluginScanner
    ) {
        $this->configurationScanner = $configurationScanner;
        parent::__construct();
        $this->pluginScanner = $pluginScanner;
    }

    protected function configure()
    {
        $this->setName('dev:plugin:list')
            ->setDescription('List plugins');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files = $this->configurationScanner->scan('di.xml');
        $types = $this->pluginScanner->getAllClasses($files);
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
