<?php

if (!defined('ABSPATH')) {
    exit();
}

/**
 * @param $class string
 */
function GDGalleryAutoload($class)
{
    // project-specific namespace prefix
    $prefix = 'GDGallery\\';
    // base directory for the namespace prefix
    $baseDir = __DIR__ . '/';
    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }
    // get the relative class name
    $relativeClass = substr($class, $len);
    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = rtrim($baseDir, '/') . '/' . str_replace('\\', '/', $relativeClass) . '.php';
    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
}

if (version_compare(phpversion(), '5.4', '<')) {
    throw new Exception('Your current version of PHP ' . phpversion() . ' is outdated. Please, update it to 5.4 and higher in order to use GrandWP Gallery.');
}

spl_autoload_register('GDGalleryAutoload');