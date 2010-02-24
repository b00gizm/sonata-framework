<?php

class SandboxApp extends Sonata_App
{
  public function registerAppDir()
  {
    return dirname(__FILE__).'/..';
  }
  
  public function registerPaths()
  {
    return array(
      'config'      => dirname(__FILE__).'/../config',
      'controllers' => dirname(__FILE__).'/../controllers',
      'templates'   => dirname(__FILE__).'/../templates',
    );
  }
  
  public function registerRoutes(Sonata_RouteMap $map) 
  {
    $map->load(dirname(__FILE__).'/../config/routing.yml');
  }
}
