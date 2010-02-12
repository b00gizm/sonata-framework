<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 *
 * Sonata_Autoloader
 * Responsible for autoloading all service classes
 *
 * @package framework
 **/
class Sonata_Autoloader
{
  /**
   * Registers Sonata_Autoloader as an SPL autoloader
   *
   * @return void
   */
  public static function register()
  {
    ini_set('unserialize_callback_func', 'spl_autoload_call');
    spl_autoload_register(array(new self, 'autoload'));
  }
  
  /**
   * Loads a class specified by its class name.
   * Due to non-existing namespaces in PHP < 5.3 class names are mapped to
   * class files as following:
   *
   * Sonata_Foo     => /path/to/sonata/src/Foo.class.php
   * Sonata_Foo_Bar => /path/to/sonata/src/foo/Bar.class.php
   *
   * @param string $class The class name
   * @return boolean Returns true if the class could be loaded, FALSE otherwise
   */
  public function autoload($class)
  {
    $parts = explode('_', $class);
    array_shift($parts);
    
    $path = dirname(__FILE__).'/'.implode('/', $parts).'.class.php';
    if (strpos($class, 'Sonata') !== 0 || !is_readable($path))
    {
      return false;
    }
    
    require_once $path;
    
    return true;
  }
}
