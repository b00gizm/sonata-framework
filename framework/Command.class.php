<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 *
 * Sonata_Command
 *
 * @package framework
 **/
abstract class Sonata_Command
{
  const EXEC_SUCCESS = 1;
  const EXEC_FAILURE = 2;
  
  /**
   * Instance of Sonata_ParameterHolder
   *
   * @var Sonata_ParameterHolder
   */
  protected $varHolder = null;
  
  /**
   * Constructor
   */
  public function __construct()
  {
    $this->varHolder = new Sonata_ParameterHolder();
  }
  
  /**
   * Sets a variable for the template view
   *
   * @param string $name The name of the variable 
   * @param string $value The value of the variable
   */
  public function setVar($name, $value)
  {
    $this->varHolder->set($name, $value);
  }
  
  /**
   * Gets a variable for the template view
   *
   * @param string $name The name of the variable
   * @return mixed The value
   */
  public function getVar($name)
  {
    return $this->varHolder->get($name);
  }
  
  /**
   * Gets the var holder
   *
   * @return Sonata_ParameterHolder
   */
  public function getVarHolder()
  {
    return $this->varHolder;
  }
  
  /**
   * Shortcut for
   *
   * <code>$this->setVar('name', 'value')</code>
   *
   * @param string $name The name of the variable
   * @param string $value The value of the variable
   */
  public function __set($name, $value)
  {
    return $this->varHolder->setByRef($name, $value);
  }
  
  /**
   * Shortcut for
   *
   * <code>$this->getVar('name', 'value')</code>
   *
   * @param string $name The name of the variable
   * @return mixed The value
   */
  public function & __get($name)
  {
    return $this->varHolder->get($name);
  }
  
  /**
   * Checks the existance of a template variable
   * This is a shortcut for
   *
   * <code>$this->getVarHolder()->has('name')</code>
   *
   * @param string $name The name of the variable
   * @return boolean The result
   */
  public function __isset($name)
  {
    return $this->varHolder->has($name);
  }
  
  /**
   * Removes a variable for the template
   * This is a shortcut for
   *
   * <code>$this->getVarHolder()->remove('name')</code>
   *
   * @param string $name The name of the variable 
   */
  public function __unset($name)
  {
    $this->varHolder->remove($name);
  }
  
  /**
   * Executes the command
   *
   * @param Sonata_Request $request A request object
   * @param Sonata_Response $response A response object
   */
  public function execute(Sonata_Request $request, Sonata_Response $response)
  {
    $res = $this->doExecute($request, $response);
    if (is_null($res) || empty($res))
    {
      $res = self::EXEC_SUCCESS;
    }
    
    $templateViewName = null;
    switch ($res)
    {
      case self::EXEC_SUCCESS :
        $templateViewName = $request->getParameter('cmd');
        break;
      case self::EXEC_FAILURE :
        $templateViewName = 'Error';
        break;
      default :
        $templateViewName = $res;
        break;
    }
    
    $templateView = new Sonata_TemplateView($templateViewName);
    $this->assignTemplateVars($templateView);
    $templateView->render($request, $response);
  }
  
  /**
   * Takes all set variables from the var holder and assigns them
   * to the template view
   *
   * @param Sonata_TemplateView $templateView 
   */
  protected function assignTemplateVars(Sonata_TemplateView $templateView)
  {
    foreach($this->getVarHolder()->getAll() as $name => $value)
    {
      $templateView->assign($name, $value);
    }
  }
  
  /**
   * Every derived command class stores its business logic in this method
   *
   * @param Sonata_Request $request A request object
   * @param Sonata_Response $response A response object
   */
  abstract protected function doExecute(Sonata_Request $request, Sonata_Response $response);
}