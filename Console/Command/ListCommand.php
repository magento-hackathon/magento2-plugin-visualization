<?php

namespace MagentoHackathon\PluginVisualization\Console\Command;

use Magento\Framework\Interception\PluginListInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Setup\Module\Di\Code\Scanner\ConfigurationScanner;
use MagentoHackathon\PluginVisualization\Model\Scanner\Plugin;
use PBergman\Console\Helper\TreeHelper;
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
    /**
     * @var TreeHelper
     */
    private $tree;

    public function __construct(
        ConfigurationScanner $configurationScanner,
        Plugin $pluginScanner,
        PluginListInterface $pluginList,
        ObjectManagerInterface $objectManager,
        TreeHelper $tree
    ) {
        $this->configurationScanner = $configurationScanner;
        $this->pluginScanner = $pluginScanner;
        $this->pluginList = $pluginList;
        $this->objectManager = $objectManager;
        $this->tree = $tree;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('dev:plugin:list')
            ->setDescription('List plugins');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $files = $this->configurationScanner->scan('di.xml');
        $typeList = $this->pluginScanner->getAllTypes($files);
        $pluginList = [];
        foreach ($typeList as $area => $types) {
            $areaNode = $this->tree->newNode($area);
            foreach ($types as $type => $classes) {
                try {
                    $reflection = new \ReflectionClass($type);
                    $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
                    $methodsFound = [];
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
                            $methodsFound[$methodName] = $plugins;
                        }
                    }
                    if (!empty($methodsFound)) {
                        foreach($methodsFound as $methodName => $plugins) {
                            $methodNode = $areaNode->newNode($type);
                            foreach($plugins as $plugin) {
                                //$pluginNode = $methodNode->newNode($plugin);
                                if (isset($classes[$plugin])) {
                                    $reflection = new \ReflectionClass($classes[$plugin]);
                                    $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
                                    foreach ($methods as $method) {
                                        foreach (['before', 'after', 'around'] as $prefix) {
                                            if (strpos($method->name, $prefix) === 0) {
                                                $methodNode->newNode($classes[$plugin] . '::' . $method->name);
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    $output->writeln("Cannot analyze $type");
                }
            }
        }
        $this->tree->printTree($output);
    }
}
