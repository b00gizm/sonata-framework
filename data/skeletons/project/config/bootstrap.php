<?php

// Register the Sonata autoloader
require_once "@project_root@/lib/sonata/Autoloader.class.php";
Sonata_Autoloader::register();

// Bootstap all vendor libraries
require_once "@project_root@/lib/vendor/sf_yaml/lib/sfYaml.php";
require_once "@project_root@/lib/vendor/sf_container/lib/sfServiceContainerAutoloader.php";
sfServiceContainerAutoloader::register();

// Project configurations
$paths = array(
  'config'      => "@project_root@/@config_dir@",
  'controllers' => "@project_root@/@controllers_dir@",
  'templates'   => "@project_root@/@templates_dir@",
);

Sonata_Config::set('paths', $paths);


// Custom bootstapping goes here ...
