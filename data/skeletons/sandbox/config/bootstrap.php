<?php

// Register the Sonata autoloader
require_once dirname(__FILE__).'/../lib/vendor/sonata-framework/src/Autoloader.class.php';
Sonata_Autoloader::register();

// Bootstap all vendor libraries
require_once dirname(__FILE__).'/../lib/vendor/sf_yaml/lib/sfYaml.php';
require_once dirname(__FILE__).'/../lib/vendor/sf_container/lib/sfServiceContainerAutoloader.php';
sfServiceContainerAutoloader::register();

// Project configurations
$paths = array(
  'config'      => dirname(__FILE__),
  'controllers' => dirname(__FILE__).'/../controllers',
  'templates'   => dirname(__FILE__).'/../templates',
);

Sonata_Config::set('paths', $paths);


// Custom bootstapping goes here ...
