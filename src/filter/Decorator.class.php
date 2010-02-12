<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 *
 * Sonata_Filter_Decorator
 *
 * @package framework.filter
 **/
abstract class Sonata_Filter_Decorator
{
  /**
   * nested filter to be executed within
   *
   * @var Sonata_Filter
   */
  protected $nestedFilter = null;
  
  /**
   * Constructor
   *
   * @param Sonata_Filter $nestedFilter Injected nested filter
   */
  public function __construct(Sonata_Filter $nestedFilter)
  {
    $this->nestedFilter = $nestedFilter;
  }
  
  /**
   * Executes the injected nested filter first and calls doExecute()
   * afterwards
   *
   * @param Sonata_Request $request 
   * @param Sonata_Response $response 
   * @see Sonata_Filter_Decorator::doExecute()
   */
  public function execute(Sonata_Request $request, Sonata_Response $response)
  {
    $this->nestedFilter->execute($request, $response);
    $this->doExecute($request, $response);
  }
  
  /**
   * Contains any executable code for this filter
   *
   * @param Sonata_Request $request 
   * @param Sonata_Response $response 
   */
  public abstract function doExecute(Sonata_Request $request, Sonata_Response $response);
}
