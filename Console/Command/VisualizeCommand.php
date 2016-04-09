<?php

namespace MagentoHackathon\PluginVisualization\Console\Command;

use Magento\Setup\Module\Di\Code\Scanner\ConfigurationScanner;
use MagentoHackathon\PluginVisualization\Model\Scanner\Plugin;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VisualizeCommand extends Command
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
        $this->setName('dev:plugin:visualize')
            ->setDescription('Visualize sort order of plugins');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files = $this->configurationScanner->scan('di.xml');
        $types = $this->pluginScanner->getAllClasses($files);
        //@todo: continue here
    }
}
