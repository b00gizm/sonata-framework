<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 *
 * Sonata_Parser_Driver
 *
 * @package framework.parser
 **/
abstract class Sonata_Parser_Driver
{
  /**
   * Parses the file which name is given by filename
   *
   * @param string $filename The file's name
   * @return array
   */
  abstract public function doParse($filename);
}
