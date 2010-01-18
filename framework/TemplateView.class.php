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
   * Name of the template
   *
   * @var string
   */
  protected $__template;
  
  /**
   * Array with vars to be used in the template
   *
   * @var array
   */
  protected $__templateVars = array();
  
  /**
   * Constructor
   *
   * @param string $template Name of the template to be rendered
   */
  public function __construct($template)
  {
    $this->__template = $template;
  }
  
  /**
   * Assigns a key/value pair to be used as variable in the template
   *
   * @param string $name The name
   * @param string $value The value
   */
  public function assign($name, $value)
  {
    $this->__templateVars[$name] = $value;
  }
  
  /**
   * Getter template vars
   *
   * @return array The template vars
   */
  public function getTemplateVars()
  {
    return $this->__templateVars;
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
    if (isset($this->__templateVars[$property]))
    {
      return $this->__templateVars[$property];
    }
    
    return null;
  }
  
  /**
   * Renders a template
   *
   * @param Sonata_Request $request The HTTP request object
   * @param Sonata_Response $response The HTTP response object
   * @throws Sonata_Exception_Config
   * @throws Sonata_Exception_TemplateNotFound
   */
  public function render(Sonata_Request $request, Sonata_Response $response)
  {
    $format = $request->getParameter('format');
    if (!is_null($format) && !empty($format))
    {
      $response->setFormat($format);
    }
    
    $paths = Sonata_Config::get('paths');
    if (empty($paths) || !isset($paths['templates']))
    {
      throw new Sonata_Exception_ConfigNotFound('Could not determine the path for the templates directory');
    }
    
    $filename = $paths['templates'].'/'.$this->__template.'Success.'.$response->getFormat().'.php';
    if (!is_readable($filename))
    {
      throw new Sonata_Exception_TemplateNotFound(sprintf("The template '%s' does not exist or is not readable.", $filename));
    }
    
    ob_start();
    include $filename;
    $data = ob_get_clean();
    $response->appendToBody($data);
  }
}

