<?php

namespace MagentoHackathon\PluginVisualization\Console\Command;

use Magento\Setup\Module\Di\Code\Scanner\ConfigurationScanner;
use MagentoHackathon\PluginVisualization\Model\Scanner\Plugin;
use Symfony\Component\Console\Command\Command;

class AbstractCommand extends Command
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
        $this->pluginScanner        = $pluginScanner;
        parent::__construct();
    }

    /**
     * Get plugin types
     *
     * @return array
     */
    protected function getTypes()
    {
        $files = $this->configurationScanner->scan('di.xml');
        return $this->pluginScanner->getAllTypes($files);
    }
}
