<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 *
 * Sonata_Route
 *
 * @package framework
 **/
class Sonata_Route
{
  protected 
    $pattern = null,
    $resource = null,
    $verbs = array(),
    $action = null,
    $parameters = array();
    
  public function setPattern($pattern)
  {
    $this->pattern = $pattern;
  }
  
  public function getPattern()
  {
    return $this->pattern;
  }
  
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  
  public function getResource()
  {
    return $this->resource;
  }
  
  public function setVerbs(array $verbs)
  {
    $this->verbs = $verbs;
  }
  
  public function getVerbs()
  {
    return $this->verbs;
  }
  
  public function setAction($action)
  {
    $this->action = $action;
  }
  
  public function getAction()
  {
    return $this->action;
  }
  
  public function setParameters(array $parameters)
  {
    $this->parameters = $parameters;
  }
  
  public function getParameters()
  {
    return $this->parameters;
  }
  
  public function getCommandName()
  {
    if (is_null($this->action) || is_null($this->resource))
    {
      return null;
    }
    
    return ucfirst($this->action).ucfirst($this->resource);
  }
}