<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2004-2006 Sean Kerr <sean@code-box.org>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 *
 * Sonata_ParameterHolder
 *
 * @package framework
 **/
class Sonata_ParameterHolder implements Serializable
{
  /**
   * Array with all parameters
   *
   * @var string
   */
  protected $parameters = array();
  
  /**
   * Constructor
   */
  public function __construct()
  {
    
  }
  
  /**
   * Retrieves a parameter
   *
   * @param string $name The name of a parameter
   * @param mixed $default The default parameter value
   * @return mixed A parameter value or NULL/default if it doesn't exist
   */
  public function & get($name, $default = null)
  {
    if (array_key_exists($name, $this->parameters))
    {
      $value = & $this->parameters[$name];
    }
    else
    {
      $value = $default;
    }
    
    return $value;
  }
  
  /**
   * Retrieves an array of parameter names
   *
   * @return array An array of parameter names
   */
  public function getNames()
  {
    return array_keys($this->parameters);
  }
  
  /**
   * Retrieves an array of parameters
   *
   * @return array An array of parameters
   * @author Pascal Cremer
   */
  public function & getAll()
  {
    $retval = & $this->parameters;
    return $retval;
  }
  
  /**
   * Checks if a parameter exists (by its name)
   *
   * @param string $name The parameter's name
   * @return boolean The result
   */
  public function has($name)
  {
    return array_key_exists($name, $this->parameters);
  }
  
  /**
   * Removes a parameter
   *
   * @param string $name The name of a parameter
   * @param mixed $default The default value of a parameter
   * @return mixed The value of the parameter that was removed of NULL/default if it doesn't exist
   */
  public function remove($name, $default = null)
  {
    $retval = $default;
    
    if ($this->has($name))
    {
      $retval = $this->parameters[$name];
      unset($this->parameters[$name]);
    }
    
    return $retval;
  }
  
  /**
   * Sets a parameter
   *
   * @param string $name The name of a parameter 
   * @param mixed $value The value of a parameter
   */
  public function set($name, $value)
  {
    $this->parameters[$name] = $value;
  }
  
  /**
   * Sets a paramter by reference
   *
   * @param string $name The name of a parameter
   * @param mixed $value The reference to the value of a parameter
   */
  public function setByRef($name, &$value)
  {
    $this->parameters[$name] = &$value;
  }
  
  /**
   * Adds an array of parameters
   *
   * @param array $parameters An array of parameters 
   */
  public function add($parameters)
  {
    if (is_null($parameters) || empty($parameters) || !is_array($parameters))
    {
      return;
    }
    
    foreach ($parameters as $name => $value)
    {
      $this->set($name, $value);
    }
  }
  
  /**
   * Adds an array of parameters by reference
   *
   * @param array $parameters An array of parameters with references to their value
   */
  public function addByRef(&$parameters)
  {
    if (is_null($parameters) || empty($parameters) || !is_array($parameters))
    {
      return;
    }
    
    foreach ($parameters as $name => &$value)
    {
      $this->setByRef($name, &$value);
    }
  }
  
  /**
   * Serializes the current instance
   *
   * @return array Objects instance
   */
  public function serialize()
  {
    return serialize($this->parameters);
  }
  
  /**
   * Unserializes a Sonata_ParameterHolder instance
   *
   * @param string $serialized  A serialized Sonata_ParameterHolder instance
   */
  public function unserialize($serialized)
  {
    $this->parameters = unserialize($serialized);
  }
}

