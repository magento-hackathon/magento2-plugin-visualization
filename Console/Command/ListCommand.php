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
use Zend\Server\Reflection\ReflectionClass;

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
                echo get_class($proxy) . "\n";
                $reflection = new \ReflectionClass($proxy);
                $methods = $reflection->getMethods();
                foreach ($methods as $method) {
                    var_dump($this->pluginList->getPlugin($type, $method->name));
                }
                $rows[] = [$type];
            } catch (\Exception $e) {
                echo $type;
                echo "\t";
                echo $e->getMessage();
                echo "\n";
            }
        }
        $tableHelper = new Table($output);
        $tableHelper
            ->setHeaders(['Types'])
            ->setRows($rows)
            ->render();
    }
}
