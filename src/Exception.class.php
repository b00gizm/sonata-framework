<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 *
 * Sonata_Exception
 *
 * @package framework
 **/
class Sonata_Exception extends Exception
{
  /**
   * Constructor
   *
   * @param string $message The exception message
   * @param string $code The exception code
   */
  public function __construct($message, $code = 0) 
  {
    // Redefine the exception so message is no longer optional
    parent::__construct($message, $code);
  }
  
  /**
   * custom string representation of object 
   *
   * @return string The exception's string representation
   */
  public function __toString() 
  {
    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
  }
}
