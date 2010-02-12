<?php

$paths = array(
  'config'      => "@config_dir@",
  'controllers' => "@controllers_dir@",
  'templates'   => "@templates_dir@",
);

Sonata_Config::set('paths', $paths);
