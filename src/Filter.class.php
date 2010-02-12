<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 *
 * Sonata_Filter
 *
 * @package framework
 **/
abstract class Sonata_Filter
{
  /**
   * Executes the filter
   *
   * @param Sonata_Request $request 
   * @param Sonata_Response $response 
   */
  public abstract function execute(Sonata_Request $request, Sonata_Response $response);
}
