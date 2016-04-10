<?php

namespace MagentoHackathon\PluginVisualization\Console\Command;

use Magento\Framework\Interception\PluginListInterface;
use Magento\Framework\ObjectManagerInterface;
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
    /**
     * @var PluginListInterface
     */
    private $pluginList;
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        ConfigurationScanner $configurationScanner,
        Plugin $pluginScanner,
        PluginListInterface $pluginList,
        ObjectManagerInterface $objectManager
    ) {
        $this->configurationScanner = $configurationScanner;
        $this->pluginScanner = $pluginScanner;
        $this->pluginList = $pluginList;
        $this->objectManager = $objectManager;
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
        $types = $this->pluginScanner->getAllTypes($files);
        $rows = [];
        foreach ($types as $type) {
            try {
                $proxy = $this->objectManager->get($type . '\\Proxy');
                $reflection = new \ReflectionClass($proxy);
                $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
                foreach ($methods as $method) {
                    $methodName = $method->name;
                    $plugin = $this->pluginList->getNext($type, $methodName);
                    $plugins = [];
                    if (!empty($plugin)) {
                        foreach ($plugin as $plug) {
                            if (is_array($plug)) {
                                foreach ($plug as $something) {
                                    $plugins[] = $something;
                                }
                            } else {
                                $plugins[] = $plug;
                            }
                        }
                        $rows[] = [
                            $type,
                            $methodName,
                            implode(', ', $plugins)
                        ];
                    }
                }
            } catch (\Exception $e) {
                $output->writeln("Cannot analyze $type");
            }
        }
        $tableHelper = new Table($output);
        $tableHelper
            ->setHeaders(['Type', 'Method', 'Plugins'])
            ->setRows($rows)
            ->render();
    }
}
