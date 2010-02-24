<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 *
 * Sonata_TemplateView
 *
 * @package framework
 **/
class Sonata_TemplateView
{
  /**
   * Templates directory
   *
   * @var string
   */
  protected $dir;
  
  /**
   * Name of the template
   *
   * @var string
   */
  protected $name;
  
  /**
   * Name of the resource the template belongs to
   *
   * @var string
   */
  protected $resource;
  
  /**
   * Array with vars to be used in the template
   *
   * @var array
   */
  protected $templateVars = array();
  
  /**
   * Constructor
   *
   * @param string $template Name of the template to be rendered
   * @param string $resource Name of the resource the template belongs to
   */
  public function __construct()
  {
    // Actually does nothing ...
  }
  
  /**
   * Setter template dir
   *
   * @param string $dir The template directory
   */
  public function setDir($dir)
  {
    $this->dir = $dir;
  }
  
  /**
   * Getter template dir
   *
   * @return string The template directory
   */
  public function getDir()
  {
    return $this->dir;
  }
  
  /**
   * Assigns a key/value pair or an array with key/value pairs to be used as variable in the template
   *
   * @param array $arg An array with key/value pairs
   * @param string $arg The key
   * @param string $value The value
   */
  public function assign($arg)
  {
    $numArgs = func_num_args();    
    $firstArg = func_get_arg(0);
    if ($numArgs == 1)
    {
      if (!is_array($firstArg))
      {
        return;
      }
      
      $this->templateVars = array_merge($this->templateVars, $firstArg);
    }
    else
    {
      $secArg = func_get_arg(1);
      $this->templateVars[$firstArg] = $secArg;
    }
    
    return;
  }
  
  /**
   * Getter template vars
   *
   * @return array The template vars
   */
  public function getTemplateVars()
  {
    return $this->templateVars;
  }
  
  /**
   * Magic method __get to proxy a request for an probably assigned
   * template variable
   *
   * @param string $property The name of the (non-existing) class property
   * @return mixed The result
   */
  public function __get($property)
  {
    if (isset($this->templateVars[$property]))
    {
      return $this->templateVars[$property];
    }
    
    return null;
  }
  
  /**
   * Renders a template and returns the raw data
   *
   * @param string $name The name of the template 
   * @param string $resource The name of the resource | NULL for generic templates
   * @param string $format The response format
   * @return string The rendered data
   */
  public function render($name, $resource = null, $format = 'xml')
  {
    if ($resource)
    {
      $templatePath = $this->dir.'/'.Sonata_Utils::underscore($resource).'/'.$name.'Success.'.$format.'.php';
    }
    else
    {
      $templatePath = dirname(__FILE__).'/../data/templates/'.$name.'Success.'.$format.'.php';
    }
    
    if (!is_readable($templatePath))
    {
      throw new Sonata_Exception_Template(sprintf("The template '%s' does not exist or is not readable.", $name));
    }
    
    ob_start();
    include $templatePath;
    $data = ob_get_clean();
    
    return $data;
  }
}

