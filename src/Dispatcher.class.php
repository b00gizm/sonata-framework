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
  protected $controllersDir = null;
  
  public function __construct()
  {
    // Actually does nothing ...
  }
  
  public function setControllersDir($directory)
  {
    $this->controllersDir = $directory;
  }
  
  public function getControllersDir()
  {
    return $this->controllersDir;
  }
  
  public function dispatch(Sonata_Request $request, Sonata_Response $response)
  {
    $className = $this->getControllerClassName($request);
    if (is_null($className))
    {
      throw new Sonata_Exception_Dispatcher("Could not determine controller name (missing request parameter 'resource')");
    }
    
    // Load the controller class
    $this->loadControllerClass($className);
    
    $controller = new $className($request, $response);
    if (!$controller instanceof Sonata_Controller_Action)
    {
      throw new Sonata_Exception_Dispatcher(sprintf("Controller '%s' is not an instance of Sonata_Controller_Action", $className));
    }
    
    // Retrieve action name. If no action name was given, switch to 'list' action
    $action = $request->getParameter('action', 'list');
    
    // Dispatch the action
    $controller->dispatch($action.'Action');
  }
  
  public function getControllerClassName(Sonata_Request $request)
  {
    $resource = $request->getParameter('resource');
    if (!$resource)
    {
      return null;
    }
    
    return ucfirst($resource).'Controller';
  }
  
  public function loadControllerClass($className)
  {
    $controllerPath = $this->controllersDir.DIRECTORY_SEPARATOR.$className.'.class.php';
    if (is_readable($controllerPath))
    {
      require_once $controllerPath;
      return;
    }
    else
    {
      throw new Sonata_Exception_Dispatcher(sprintf("Could not load controller class '%s' in directory '%s'", $className, $this->controllerDir));
    }
  }
}