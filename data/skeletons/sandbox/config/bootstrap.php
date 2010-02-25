<?php

// Register the Sonata autoloader
// Add additional directories for autoloading via 'extend'
require_once dirname(__FILE__).'/../lib/vendor/sonata-framework/src/Autoloader.class.php';
Sonata_Autoloader::register();
Sonata_Autoloader::extend(array(
  dirname(__FILE__).'/../lib',
));

// Bootstap all vendor libraries
require_once dirname(__FILE__).'/../lib/vendor/sf_yaml/lib/sfYaml.php';
require_once dirname(__FILE__).'/../lib/vendor/sf_container/lib/sfServiceContainerAutoloader.php';
sfServiceContainerAutoloader::register();

// Custom bootstapping goes here ...
