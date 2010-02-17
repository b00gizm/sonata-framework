<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 *
 * Sonata_Controller_Action
 *
 * @package framework.controller
 **/
 
abstract class Sonata_Controller_Action
{
  const ACTION_SUCCESS = 1;
  const ACTION_FAILURE = 2;
  
  protected $request = null;
  
  protected $response = null;
  
  /**
   * Instance of Sonata_ParameterHolder
   *
   * @var Sonata_ParameterHolder
   */
  protected $varHolder = null;
  
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
   * Constructor
   *
   * @param Sonata_Request $request A HTTP request object
   * @param Sonata_Response $response A HTTP response object 
   */
  public function __construct(Sonata_Request $request, Sonata_Response $response)
  {
    $this->request = $request;
    $this->response = $response;
    
    $this->varHolder = new Sonata_ParameterHolder();
  }
  
  public function setRequest(Sonata_Request $request)
  {
    $this->request = $request;
  }
  
  public function getRequest()
  {
    return $this->request;
  }
  
  public function setResponse(Sonata_Response $response)
  {
    $this->response = $response;
  }
  
  public function getResponse()
  {
    return $this->response;
  }
  
  public function preDispatch()
  { 
  }
  
  public function postDispatch()
  {
  }
  
  public function __call($methodName, $args)
  {
    if (substr($methodName, -6) == 'Action')
    {
      $action = substr($methodName, 0, strlen($methodName) - 6);
      throw new Sonata_Exception_Controller_Action(sprintf("Action '%s' does not exist", $action), 404);
    }
    
    throw new Sonata_Exception_Controller_Action(sprintf("Method '%s' does not exist", $methodName), 500);
  }
  
  public function dispatch($action)
  {
    $this->preDispatch();
    
    if (in_array($action, get_class_methods($this)))
    {
      $res = $this->$action($this->request, $this->response);
      if (substr($action, -6) == 'Action')
      {
        if (is_null($res) || empty($res))
        {
          $res = self::ACTION_SUCCESS;
        }

        $templateViewName = null;
        switch ($res)
        {
          case self::ACTION_SUCCESS :
            $templateViewName = substr($action, 0, strlen($action) - 6);
            break;
          case self::ACTION_FAILURE :
            $templateViewName = 'Error';
            break;
          default :
            $templateViewName = $res;
            break;
        }
        
        $templateView = new Sonata_TemplateView($templateViewName, $this->request->getParameter('resource'));
        $templateView->assign($this->getVarHolder()->getAll());
        $templateView->render($this->request, $this->response);
      }
    }
    else
    {
      $this->__call($action, array());
    }
    
    $this->postDispatch();
  }
}
