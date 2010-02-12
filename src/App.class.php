<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 *
 * Sonata_App
 *
 * @package framework
 **/
class Sonata_App
{
  public static function run()
  {
    $paths = sfConfig::get('paths');
    if (empty($paths) || !isset($paths['config']))
    {
      throw new Sonata_Exception_Config('Could not determine the path for the config directory');
    }
    
    $serviceConfig = $paths['config'].'services.yml';
    if (!is_readable())
    {
      throw new RuntimeException(sprintf("The service config file in '%s' does not exist or is not readable", $serviceConfig));
    }
    
    $serviceContainer = new sfServiceContainerBuilder();
    $loader = new sfServiceContainerLoaderFileYaml($serviceContainer);
    $loader->load($serviceConfig);
    
    // Retrieve services
    $request    = $serviceContainer->getService('request');
    $response   = $serviceContainer->getService('response');
    
    $dispatcher = $serviceContainer->getService('dispatcher');
    $routeMap   = $serviceContainer->getService('route_map');
    
    // Create front controller
    $frontController = new Sonata_Controller_Front($dispatcher, $routeMap);
    $frontController->setFilterChains(new Sonata_Filter_Chain(), new Sonata_Filter_Chain());
    
    // Add filters
    // TODO
    
    // Handle request
    $frontController->handleRequest($request, $response);
  }
}
