<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 *
 * Sonata_Parser_Config
 *
 * @package framework.parser
 **/
class Sonata_Parser_Config extends Sonata_Parser
{
  /**
   * A Sonata_Parser_Driver instance
   *
   * @var string
   */
  protected $driver = null;
  
  /**
   * The constructor
   */
  public function __construct(Sonata_Parser_Driver $driver)
  {
    $this->driver = $driver;
  }
  
  /**
   * Setter parser driver
   *
   * @param Sonata_Parser_Driver $driver 
   */
  public function setDriver(Sonata_Parser_Driver $driver)
  {
    $this->driver = $driver;
  }
  
  /**
   * Getter parser driver
   *
   * @return mixed
   */
  public function getDriver()
  {
    return $this->driver;
  }
  
  public function parse($filename)
  {
    if (!is_readable($filename))
    {
      throw new RuntimeException(sprintf("The config file '%s' does not exist or is not readable", $filename));
    }
    
    try
    {
      $result = $this->driver->doParse($filename);
      return $result;
    }
    catch(Exception $ex)
    {
      // Actually does nothing
      throw $ex;
    }
  }
}

