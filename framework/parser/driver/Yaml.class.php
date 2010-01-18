<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 *
 * Sonata_Parser_Driver_Yaml
 *
 * @package framework.parser.driver
 **/
class Sonata_Parser_Driver_Yaml extends Sonata_Parser_Driver
{
  public function doParse($filename)
  {
    include_once dirname(__FILE__).'/../../../lib/sf_yaml/sfYaml.php';
    
    return sfYaml::load($filename);
  }
}
