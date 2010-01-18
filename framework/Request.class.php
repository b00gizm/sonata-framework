<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 *
 * Sonata_Request
 *
 * @package framework
 **/
class Sonata_Request
{
  /**
   * parameters array
   *
   * @var array
   **/
  protected $parameters = array();
  
  /**
   * Constructor
   */
  public function __construct()
  {
    $this->parameters = $_REQUEST;
  }
  
  /**
   * Checks if parameter exists
   *
   * @param string $key The parameter's key
   * @return boolean The result
   */
  public function hasParameter($key)
  {
    if (array_key_exists($key, $this->parameters))
    {
      return true;
    }
    
    return false;
  }
  
  /**
   * Returns the parameter or, if it does not exist, 
   * the given fallback
   *
   * @param string $key The parameter's key
   * @param mixed $fallback A fallback value if the key does not exist
   * @return mixed The result
   */
  public function getParameter($key, $fallback = null)
  {
    if ($this->hasParameter($key))
    {
      return $this->parameters[$key];
    }
    
    return $fallback;
  }
  
  /**
   * Adds a parameter for a given key/value
   *
   * @param string $key The parameter's key
   * @param string $value The parameter's value
   */
  public function addParameter($key, $value)
  {
    $this->parameters[$key] = $value;
  }
  
  /**
   * Checks the HTTP method of the request
   *
   * @param string The HTTP method
   * @return boolean The result
   */
  public function isMethod($method)
  {
    return $this->getMethod() === $method;
  }
  
  /**
   * Returns the HTTP method
   *
   * @return string The HTTP method
   **/
  public function getMethod()
  {
    return $_SERVER['REQUEST_METHOD'];
  }
}
