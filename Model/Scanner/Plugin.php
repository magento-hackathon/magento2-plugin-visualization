<?php

namespace MagentoHackathon\PluginVisualization\Model\Scanner;

class Plugin
{
    /**
     * Remove begining backslash on class names
     *
     * @param  string $typeName
     * @return string
     */
    public function trimInstanceStartingBackslash($typeName) 
    {
        return ltrim($typeName, '\\');
    }

    /**
     * Get all types and their plugins
     *
     * @param  string[] $files
     * @return array
     */
    public function getAllTypes($files)
    {
        $classes = [];
        foreach ($files as $file) {
            $classes = array_merge_recursive($classes, $this->scanFile($file));
        }
        return $classes;
    }

    /**
     * Get plugins in file
     *
     * @param  string $file
     * @return array
     */
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
                if (!$this->isValidPlugin($plugin)) {
                    continue;
                }
                $methods = $this->getMethods($plugin->getAttribute('type'));
                if (!count($methods)) {
                    continue;
                }
                $types[$class][] = [
                    'plugin'     => $this->trimInstanceStartingBackslash($plugin->getAttribute('type')),
                    'sort_order' => $plugin->getAttribute('sortOrder'),
                    'methods'    => implode(', ', $methods),
                ];
            }
        }
        return $types;
    }

    /**
     * Determine if node name is plugin and if it's type exists
     *
     * @param  DOMElement|DOMText $plugin
     * @return boolean
     */
    protected function isValidPlugin($plugin)
    {
        return ($plugin instanceof \DOMElement)
         && $plugin->tagName === 'plugin'
         && $plugin->getAttribute('type')
         && class_exists($plugin->getAttribute('type'));
    }

    /**
     * Get valid methods for class
     *
     * @param  string $class
     * @return string[]
     */
    protected function getMethods($class)
    {
        $reflection = new \ReflectionClass($class);
        $methods = [];
        foreach ($reflection->getMethods() as $method) {
            foreach (['before', 'after', 'around'] as $prefix) {
                if (strpos($method->name, $prefix) === 0) {
                    $methods[] = $method->name;
                    break;
                }
            }
        }
        return $methods;
    }
}