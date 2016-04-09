<?php

namespace MagentoHackathon\PluginVisualization\Model\Scanner;

class Plugin
{
    public function getAllClasses($files)
    {
        $classes = [];
        foreach ($files as $file) {
            $classes += $this->scanFile($file);
        }
        return $classes;
    }

    public function scanFile($file)
    {
        $types = [];
        $dom = new \DOMDocument();
        $dom->load($file);
        $xpath = new \DOMXPath($dom);
        $results = $xpath->query('//plugin/..');
        foreach ($results as $result) {
            $types[] = $result->getAttribute('name');
        }
        return $types;
    }
}