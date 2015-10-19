<?php
namespace Piwik\Plugins\GeoIpChain;

/**
 * This is a hacky autoload...since plugin.json does not provide a composer hook until yet!
 */
$classMap = include __DIR__ . '/vendor/composer/autoload_classmap.php';

/* @var $loader \Composer\Autoload\ClassLoader */
$loader = include PIWIK_VENDOR_PATH . '/autoload.php';
$loader->addClassMap($classMap);

foreach (include __DIR__ . '/vendor/composer/autoload_files.php' as $file) {
    if (strpos($file, 'igorw')) {
        include $file;
    }
}

class GeoIpChain extends \Piwik\Plugin
{
}
