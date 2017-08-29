# Magento 2 Plugin Visualization

A tool to visualize which before/after/around methods of which modules will get executed in what order.

## Installation

1. Add the repository to the repositories section of your composer.json file:
```
"repositories": [
    {
     "type": "vcs",
     "url": "git@github.com:magento-hackathon/magento2-plugin-visualization.git"
    }
],
```
2. Require the module & install

```
composer require magento-hackaton/module-plugin-visualization:dev-master
```

## Usage

After installation you will find three new commands via the `bin/magento` command line tool:

- dev:plugin:list - shows a list of the plugins used (recommended)
- dev:plugin:graphviz - in development
- dev:plugin:visualize - not done yet

## Further Information

This module was developed during the Imagine Hackathon 2016.
Pull requests are very welcome!

Original Hackathon Issue Discussion: [magento-hackathon/pre-imagine-2016/issues/20](https://github.com/magento-hackathon/pre-imagine-2016/issues/20)
