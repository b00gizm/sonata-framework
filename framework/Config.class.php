<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 *
 * Sonata_Config
 *
 * @package framework
 **/
class Sonata_Config
{ 
  /**
   * Array with all stored values
   *
   * @var array
   */
  protected static $values = array(); 
  
  /**
   * Constructor
   */
  protected function __construct() { }
  
  /**
   * Sets a key-value pair
   *
   * @param string $key The key 
   * @param string $value The value
   */
  public static function set($key, $value)
  {
    self::$values[$key] = $value;
  }
  
  /**
   * Retrieves a value for a key. If the key cannot be found the fallback
   * is returned instead
   *
   * @param string $key The key
   * @param string $fallback The fallback value
   * @return mixed The result
   */
  public static function get($key, $fallback = null)
  {
    if (!isset(self::$values[$key]))
    {
      return $fallback;
    }
    
    return self::$values[$key];
  }
  
  /**
   * Loads the content of a config file
   *
   * @param string $filename The path of the config file
   * @param Sonata_Parser_Config $configParser A config parser instance
   * @param boolean $clear Clear old data first? 
   * @throws RuntimeException
   */
  public static function load($filename, Sonata_Parser_Config $configParser, $clear = true)
  {
    try 
    {
      $res = $configParser->parse($filename);
      
      if ($clear)
      {
        self::$values = $res;
      }
      else
      {
        self::$values = array_merge(self::$values, $res);
      }
    }
    catch (RuntimeException $ex)
    {
      // TODO: Do something useful ...
      throw $ex;
    }
  }
}

