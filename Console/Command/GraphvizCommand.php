<?php

namespace MagentoHackathon\PluginVisualization\Console\Command;

use Magento\Framework\Module\ModuleListInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GraphvizCommand extends Command
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
        $this->setName('dev:plugin:graphviz')
            ->setDescription('Output GraphViz format');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $plugins = $this->_moduleList->getAll();
        $rows = [];
        foreach ($plugins as $plugin) {
            $rows[$plugin['name']] = [$plugin['name'], $plugin['setup_version']];
        }
        ksort($rows);
        //$tableHelper = new Table($output);
        //$tableHelper
        //    ->setHeaders(['Name', 'Setup Version'])
        //    ->setRows($rows)
        //    ->render();

        $outputString = '';
	$outputString .= $this->defaultHeader();
        $outputString .= $this->getContent($rows);
	$outputString .= $this->defaultFooter();
	$output->writeln($outputString);

    }

    protected function defaultHeader()
    {
	$header = <<<EOT
digraph magento2plugins {
	              bgcolor="#2e3e56"
	              pad="0.5" /* add padding round the edge of the graph */
	/*
		rankdir="LR"  make graph layout left->right rather than top->bottom 
		graph [fontname="Helvetica Neue", fontcolor="#fcfcfc"]
		labelloc="t" label at top 
		label="Test Setup"

		dark blue (background): #2e3e56
		white (text/lines): #fcfcfc
		dark line (hidden lines): #445773

		red: #ea555b - crashes
		yellow: #edad56 - nodes in target
		gold: #AB6D16 - static libs
		dark green: #29b89d 
		purple: #9362a8
		pink: #f2688d - buckets
		green: #a5cf80 - 3rd party library
		blue: #8eabd9 - start

		0.1pt == 3.25px
	*/

	node [shape="circle", width="0.6", style="filled", fillcolor="#edad56", color="#edad56", penwidth="3"]
	edge [color="#fcfcfc", penwidth="2", fontname="helvetica Neue Ultra Light"]

EOT;
       return $header;
   }


   protected function getContent($plugins)
   {
       $content = '';
       
       foreach ($plugins as $name => $pluginVersion) {
           $content .= '"' . $name . '" -> "' . $pluginVersion[1]  . '"'. PHP_EOL;
       }

       return $content;
   }

   protected function defaultFooter()
   {
      $footer = <<<EOT

}
EOT;
      return $footer;
   }

}
