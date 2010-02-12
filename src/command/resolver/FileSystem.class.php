<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) Pascal Cremer
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 *
 * Sonata_Command_Resolver_FileSystem
 *
 * @package framework.command.resolver
 **/
class Sonata_Command_Resolver_FileSystem extends Sonata_Command_Resolver
{
  public function __construct()
  {
    // Actually does nothing ...
  }
  
  public function getCommand(Sonata_Request $request)
  {
    if ($request->hasParameter('cmd'))
    {
      $cmdName = $request->getParameter('cmd');
      
      return $command = $this->loadCommand($cmdName);
    }

    return null;
  }
  
  protected function loadCommand($cmdName)
  {
    $paths = Sonata_Config::get('paths');
    if (empty($paths) || !isset($paths['commands']))
    {
      throw new Sonata_Exception_ConfigNotFound('Could not determine the path for the commands directory');
    }
    
    $className = $cmdName.'Command';
    $commandPath = $paths['commands'].'/'.$className.'.class.php';
    if (!is_readable($commandPath))
    {
      return null;
    }
    
    require_once $commandPath;
    
    $commandObject = new $className();
    if ($commandObject instanceof Sonata_Command)
    {
        return new $commandObject;
    }
    
    return null;
  }
}
