<?php

namespace MagentoHackathon\PluginVisualization\Model\Scanner;

class Plugin
{
    public function stripNamespacePrefix($className) 
    {
        return preg_replace('/^\\\\/', '', $className);
    }
    
    public function getAllClasses($files)
    {
        $classes = [];
        foreach ($files as $file) {
            $classes = array_merge($classes, $this->scanFile($file));
        }
        $classes = array_map([$this, 'stripNamespacePrefix'], $classes);
        sort($classes);
        $classes = array_unique($classes);
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