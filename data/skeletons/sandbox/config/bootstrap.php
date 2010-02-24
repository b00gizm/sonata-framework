<?php

// Register the Sonata autoloader
require_once dirname(__FILE__).'/../lib/vendor/sonata-framework/src/Autoloader.class.php';
Sonata_Autoloader::register();

// Register extra directories to search for files for autoloading
$autoloader = new Sonata_Autoloader();
$autoloader->registerExtraDirs(array(
  dirname(__FILE__).'/../lib',
));

// Bootstap all vendor libraries
require_once dirname(__FILE__).'/../lib/vendor/sf_yaml/lib/sfYaml.php';
require_once dirname(__FILE__).'/../lib/vendor/sf_container/lib/sfServiceContainerAutoloader.php';
sfServiceContainerAutoloader::register();

// Custom bootstapping goes here ...
