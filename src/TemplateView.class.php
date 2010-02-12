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
  protected $template;
  
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
   */
  public function __construct($template)
  {
    $this->template = $template;
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
    
    $filename = $paths['templates'].'/'.$this->template.'Success.'.$response->getFormat().'.php';
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

