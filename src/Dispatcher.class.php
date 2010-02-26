<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 *
 * Sonata_Dispatcher
 *
 * @package framework
 **/
 
class Sonata_Dispatcher
{
  protected $container = null;
  
  protected $preFilterChain = null;
  
  protected $postFilterChain = null;
  
  public function __construct(sfServiceContainerInterface $container)
  {
    $this->container = $container;
    
    $this->preFilterChain = $this->initializePreFilterChain();
    $this->postFilterChain = $this->initializePostFilterChain();
  }
  
  public function setControllersDir($directory)
  {
    $this->controllersDir = $directory;
  }
  
  public function getControllersDir()
  {
    return $this->controllersDir;
  }
  
  protected function initializePreFilterChain()
  {
    return new Sonata_FilterChain();
  }
  
  protected function initializePostFilterChain()
  {
    return new Sonata_FilterChain();
  }
  
  public function addPreFilters($filters)
  {
    $this->addFilters($filters, 'pre');
  }
  
  public function addPostFilters($filters)
  {
    $this->addFilters($filters, 'post');
  }
  
  protected function addFilters($filters, $type)
  {
    if (is_null($filters) || empty($filters))
    {
      return;
    }
    
    $filters = is_array($filters) ? $filters : array();
    foreach ($filters as $filter)
    {
      $type = strtolower($type);
      if ($type == 'pre')
      {
        $this->preFilterChain->addFilter($filter);
      }
      elseif ($type == 'post')
      {
        $this->postFilterChain->addFilter($filter);
      }
    }
    
    return;
  } 
  
  public function dispatch()
  {
    $request      = $this->container->getService('request');
    $response     = $this->container->getService('response');
    $routeMap     = $this->container->getService('route_map');
    $templateView = $this->container->getService('template_view');
    
    try
    {
      $routeString = $request->getParameter('route');
      
      if (!$routeMap->resolveRouteString($routeString))
      {
        throw new Sonata_Exception_Dispatcher(sprintf("Could not resolve route '%s'. Please check your routing configuration", $routeString), 500);
      }
      
      $className = $this->getControllerClassName($request);
      if (is_null($className))
      {
        throw new Sonata_Exception_Dispatcher("Could not determine controller name (missing request parameter 'resource')", 500);
      }

      // Load the controller class
      $this->loadControllerClass($className);

      $controller = new $className($request, $response);
      if (!$controller instanceof Sonata_Controller_Action)
      {
        throw new Sonata_Exception_Dispatcher(sprintf("Controller '%s' is not an instance of Sonata_Controller_Action", $className), 500);
      }

      // Retrieve action name. If no action name was given, switch to 'list' action
      $action = $request->getParameter('action', 'list');
      
      // Process all pre filters
      $this->preFilterChain->processFilters($request, $response);

      // Dispatch the action
      $controller->dispatch($action.'Action', $templateView);
      
      // Process all post filters
      $this->postFilterChain->processFilters($request, $response);
    }
    catch (Sonata_Exception $ex)
    {
      $templateView->assign('code', $ex->getCode());
      $templateView->assign('message', $ex->getMessage());
      $rawData = $templateView->render('Error', null, $request->getParameter('format'));
      $response->appendToBody($rawData);
    }
    
    // Flush the output
    $response->flush();
  }
  
  public function getControllerClassName(Sonata_Request $request)
  {
    $resource = $request->getParameter('resource');
    if (!$resource)
    {
      return null;
    }
    
    return ucfirst(Sonata_Utils::camelize($resource)).'Controller';
  }
  
  public function loadControllerClass($className)
  {
    $controllerPath = $this->controllersDir.'/'.$className.'.class.php';
    if (is_readable($controllerPath))
    {
      require_once $controllerPath;
      return;
    }
    else
    {
      throw new Sonata_Exception_Dispatcher(sprintf("Could not load controller class '%s' in directory '%s'", $className, $this->controllersDir), 500);
    }
  }
}