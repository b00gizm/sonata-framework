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
abstract class Sonata_App
{
  protected $environment = 'prod';
  
  protected $isDebug = false;
  
  protected $paths = array();
  
  protected $container = null;
  
  protected function initializeContainer()
  {
    $name = 'SonataProject_'.md5($this->appDir.$this->isDebug.$this->environment).'ServiceContainer';
    $file = sys_get_temp_dir().'/'.$name.'.php';
    
    if (!$isDebug && is_readable($file))
    {
      require_once $file;
      $container = new $name;
    }
    else
    {      
      $serviceConfig = $this->paths['config'].'/services.yml';
      if (!is_readable($serviceConfig))
      {
        throw new RuntimeException(sprintf("The service config file in '%s' does not exist or is not readable", $serviceConfig));
      }
      
      $container = new sfServiceContainerBuilder();
      $loader = new sfServiceContainerLoaderFileYaml($container);
      $loader->load($serviceConfig);
      
      if (!$isDebug)
      {
        $dumper = new sfServiceContainerDumperPhp($container);
        file_put_contents($file, $dumper->dump(array('class' => $name)));
      }
    }
    
    return $container;
  }
  
  protected function createDispatcher()
  {
    return new Sonata_Dispatcher($this->container);
  }
  
  public function __construct($environment = 'prod', $isDebug = false)
  {
    $this->environment = $environment;
    $this->isDebug = (bool)$isDebug;
    
    $this->paths = $this->registerPaths();
    $this->container = $this->initializeContainer();
  }
  
  public function registerAppDir() {}
  
  public function registerPaths() {}
  
  public function registerRoutes(Sonata_RouteMap $map) {}
  
  public function registerPreFilters() 
  {
    return array();
  }
  
  public function registerPostFilters() 
  {
    return array();
  }
  
  public function run()
  {
    $start = microtime();
    
    $request      = $this->container->getService('request');
    $response     = $this->container->getService('response');
    $routeMap     = $this->container->getService('route_map');
    $templateView = $this->container->getService('template_view'); 
    
    $appDir = $this->registerAppDir();
    
    $this->registerRoutes($routeMap);
    
    $templateView->setDir(isset($this->paths['templates']) ? $this->paths['templates'] : '');
    
    $dispatcher = $this->createDispatcher();
    $dispatcher->setControllersDir(isset($this->paths['controllers']) ? $this->paths['controllers'] : '');
    $dispatcher->addPreFilters($this->registerPreFilters());
    $dispatcher->addPostFilters($this->registerPostFilters());
    $dispatcher->dispatch();
    
    $end = microtime();
    $duration = ($this->getMicrotime($end) - $this->getMicrotime($start));
    
    // TODO: So something useful with the duration info
  }
  
  protected function getMicrotime($t)
  {
    list($usec, $sec) = explode(" ", $t);
    return ((float)$usec + (float)$sec);
  }
  
  public function getEnvironment()
  {
    return $this->environment;
  }
  
  public function getIsDebug()
  {
    return $this->isDebug;
  }
  
  public function getPaths()
  {
    return $this->paths;
  }
}
