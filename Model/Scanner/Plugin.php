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
            $area    = $this->getAreaFromFileName($file);
            if (!isset($classes[$area])) {
                $classes[$area] = [];
            }
            $classes[$area] = array_merge_recursive($classes[$area], $this->scanFile($file));
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
            $typeClass = $this->trimInstanceStartingBackslash($result->getAttribute('name'));
            $disabled = $result->getAttribute('disabled');
            if(isset($disabled) && $disabled=="false") {
                continue;
            }
            if (!isset($types[$typeClass])) {
                $types[$typeClass] = [];
            }
            foreach ($result->childNodes as $plugin) {
                if (!$this->isValidPlugin($plugin)) {
                    continue;
                }
                $pluginClass = $this->trimInstanceStartingBackslash($plugin->getAttribute('type'));
                $types[$typeClass][$plugin->getAttribute('name')] = $pluginClass;
            }
        }
        return $types;
    }

    /**
     * Determine configuration are by file name
     *
     * @param  string $name
     * @return string
     */
    protected function getAreaFromFileName($name)
    {
        preg_match('/\/([a-z_]+)\/di\.xml$/', $name, $matches);
        if (isset($matches[1]) && $matches[1] !== 'etc') {
            return $matches[1];
        }
        return 'default';
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
}