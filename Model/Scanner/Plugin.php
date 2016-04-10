<?php

namespace MagentoHackathon\PluginVisualization\Model\Scanner;

class Plugin
{
    public function trimInstanceStartingBackslash($typeName) 
    {
        return ltrim($typeName, '\\');
    }
    
    public function getAllTypes($files)
    {
        $classes = [];
        foreach ($files as $file) {
            $classes = array_merge_recursive($classes, $this->scanFile($file));
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
            $class = $this->trimInstanceStartingBackslash($result->getAttribute('name'));
            $disabled = $result->getAttribute('disabled');

            if(isset($disabled) && $disabled=="false") {
                continue;
            }
            
            if (!isset($types[$class])) {
                $types[$class] = [];
            }
            foreach ($result->childNodes as $plugin) {
                if (!($plugin instanceof \DOMElement) || $plugin->tagName !== 'plugin') {
                    continue;
                }
                $types[$class][] = [
                    'plugin' => $plugin->getAttribute('type'),
                    'sort_order' => $plugin->getAttribute('sortOrder'),
                ];
            }
        }
        return $types;
    }
}