<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 *
 * Sonata_Command_Resolver
 *
 * @package framework.command
 **/
abstract class Sonata_Command_Resolver
{
  /**
   * Retrieves a Sonata_Command object
   *
   * @param Sonata_Request $request The HTTP request object
   * @return Sonata_Command The retrieved command
   */
  abstract public function getCommand(Sonata_Request $request);
}
