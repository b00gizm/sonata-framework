<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 *
 * Sonata_FilterChain
 *
 * @package framework
 **/
class Sonata_FilterChain implements ArrayAccess, Countable
{
  /**
   * Array of filters to execute
   *
   * @var array
   */
  protected $filters = array();
  
  /**
   * Constructor
   */
  public function __construct()
  {
    // Actually does nothing ...
  }
  
  /**
   * Adds a filter to the array of filters
   *
   * @param Sonata_Filter $filter
   */
  public function addFilter(Sonata_Filter $filter)
  {
    $this->filters[] = $filter;
  }
  
  /**
   * Returns the array with all filters
   *
   * @return void
   * @author Pascal Cremer
   */
  public function getFilters()
  {
    return $this->filters;
  }
  
  /**
   * Processes all filters
   *
   * @param Sonata_Request $request 
   * @param Sonata_Response $response 
   */
  public function processFilters(Sonata_Request $request, Sonata_Response $response)
  {
    foreach ($this->filters as $filter)
    {
      $filter->execute($request, $response);
    }
  }
  
  public function offsetSet($offset, $value) 
  {
    if ($value instanceof Sonata_Filter)
    {
      if (is_null($offset))
      {
        $this->filters[] = $value;
      }
      else
      {
        $this->filters[$offset] = $value;
      }
    }
    else
    {
      // TODO: Throw exception
    }
  }

  public function offsetExists($offset) 
  {
    return isset($this->filters[$offset]);
  }

  public function offsetUnset($offset) 
  {
    unset($this->filters[$offset]);
  }

  public function offsetGet($offset) 
  {
    return isset($this->filters[$offset]) ? $this->filters[$offset] : null;
  }   

  public function count() 
  {
    return count($this->filters);
  }
}
