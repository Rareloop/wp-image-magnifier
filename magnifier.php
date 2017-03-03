<?php
/*
Plugin Name: Image Magnifier
Description: Allows for images to be zoomed
Version: 1.0.0
Author: Rareloop
*/

// If we haven't loaded this plugin from Composer we need to add our own autoloader
if (!class_exists('Rareloop\Magnifier\Magnifier')) {
    // Get a reference to our PSR-4 Autoloader function that we can use to add our
    // Rareloop namespace
    $autoloader = require_once('autoload.php');

    // Use the autoload function to setup our class mapping
    $autoloader('Rareloop\\Magnifier\\', __DIR__ . '/src/Rareloop/Magnifier/');
}

// We are now able to autoload classes under the Acme namespace so we
// can implement what ever functionality this plugin is supposed to have
\Rareloop\Magnifier\Magnifier::init(plugin_dir_url(__FILE__));