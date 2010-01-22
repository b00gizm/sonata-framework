<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 *
 * Sonata_Utils
 *
 * @package framework
 **/
class Sonata_Utils
{
  /**
   * 'Camelizes' a string, e.g. transforms 'foo_bar' into 'FooBar'
   *
   * @param string $string The string to be camelized
   * @param boolean $startLower Indicates if the camelized string should begin lower-case
   * @return string The result
   */
  public static function camelize($string, $startLower = false)
  {
    if (empty($string))
    {
      return $string;
    }
    
    $parts = explode('_', $string);
    
    $ret = '';
    for ($i = 0; $i < count($parts); $i++)
    {
      $part = trim(strtolower($parts[$i]));
      if ($startLower == false || $i > 0)
      {
        $part = ucfirst($part);
      }
      $ret .= $part;
    }
    
    return $ret;
  }
  
  /**
   * 'Underscores' a string, e.g. transforms 'FooBar' into 'foo_bar'
   *
   * @param string $string The string to be underscored
   * @return string The result
   */
  public static function underscore($string)
  {
    if (empty($string))
    {
      return $string;
    }
    
    // Remove all non-digit characters
    $string = preg_replace('~[^\\pL\d]+~u', '', $string);
    
    $matches = array();
    if (preg_match_all('/[A-Z][a-z0-9]*/', $string, &$matches))
    {
      $parts = $matches[0];
      $cnt = 0;
      for ($i = 0; $i < count($parts); $i++)
      {
        $cnt += strlen($parts[$i]);
        $parts[$i] = strtolower($parts[$i]);
      }
      
      $offset = strlen($string) - $cnt;
      if ($offset > 0)
      {
        array_unshift(&$parts, substr($string, 0, $offset));
      }
      
      return implode('_', $parts);
    }
    else
    {
      return $string;
    }
  }
  
  /**
   * 'Slugifies' a string, e.g. transforms 'Here be dragons!' into 'here-be-dragons'
   *
   * @param string $string The string to be slugified
   * @return string The result
   *
   * @author Miguel Santirso <miguel.santirso+scb@gmail.com>
   */
  public static function slugify($string)
  {
    // Replace non letter or digits by -
    $string = preg_replace('~[^\\pL\d]+~u', '-', $string);
    
    // Trim string
    $string = trim($string, '-');
    
    // Transliterate
    if (function_exists('iconv'))
    {
      $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);
    }
    
    // Lowercase
    $string = strtolower($string);
    
    // Remove unwanted characters
    $string = preg_replace('~[^-\w]+~', '', $string);
    
    if (empty($string))
    {
      return 'n-a';
    }
    
    return $string;
  }
}
