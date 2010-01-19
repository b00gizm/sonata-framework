<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 *
 * Sonata_FrontController
 *
 * @package framework
 **/
class Sonata_FrontController
{
  /**
   * A command resolver instance
   *
   * @var Sonata_Command_Resolver
   */
  protected $resolver = null;
  
  /**
   * A route map instance
   *
   * @var Sonata_RouteMap
   */
  protected $map = null;
  
  /**
   * A HTTP request instance
   *
   * @var Sonata_Request
   */
  protected $request = null;
  
  /**
   * A HTTP response instance
   *
   * @var Sonata_Response
   */
  protected $response = null;
  
  /**
   * The constructor
   *
   * @param Sonata_Command_Resolver $resolver A command resolver opject
   * @param Sonata_RouteMap $map A route map object
   */
  public function __construct(Sonata_Command_Resolver $resolver, Sonata_RouteMap $map)
  {
    $this->resolver = $resolver;
    $this->map = $map;
  } 
  
  /**
   * Setter command resolver
   *
   * @param Sonata_Command_Resolver $resolver A command resolver object
   */
  public function setResolver(Sonata_Command_Resolver $resolver)
  {
    $this->resolver = $resolver;
  }
  
  /**
   * Getter command resolver
   *
   * @return Sonata_Command_Resolver A command resolver object
   */
  public function getResolver()
  {
    return $this->resolver;
  }
  
  /**
   * Setter route map
   *
   * @param Sonata_RouteMap $map A route map object
   */
  public function setRouteMap(Sonata_RouteMap $map)
  {
    $this->map = $map;
  }
  
  /**
   * Getter route map
   *
   * @return Sonata_RouteMap A route map object
   */
  public function getRouteMap()
  {
    return $this->map;
  }
  
  public function handleRequest(Sonata_Request $request, Sonata_Response $response)
  {
    $this->request = $request;
    $this->response = $response;
    
    // Try to resolve route string
    $routeString = $this->request->getParameter('route');
    $this->redirectErrorUnless($this->map->resolveRouteString($routeString), 404);
    
    // Try to resolve command
    $command = $this->resolver->getCommand($this->request);
    $this->redirectErrorUnless($command, 500);
    
    try
    {
      // Execute the command
      $command->execute($this->request, $this->response);
      $this->response->flush();
    }
    catch(Sonata_Exception_TemplateNotFound $ex)
    {
      $this->redirectErrorUnless($command, 500);
    }
  }
  
  protected function redirectError($code, $message = null)
  {
    $this->response->setStatusCode($code);
    $templateView = new Sonata_TemplateView('Error');
    $templateView->assign('code', $this->response->getStatusCode());
    $message = is_null($message) ? $this->response->getStatusText() : $message;
    $templateView->assign('message', $message);
    
    $templateView->render($this->request, $this->response);
    $this->response->flush();
    exit(0);
  }
  
  protected function redirectErrorIf($condition, $code, $message = null)
  {
    if ($condition)
    {
      $this->redirectError($code, $message);
    }
  }
  
  protected function redirectErrorUnless($condition, $code, $message = null)
  {
    if (!$condition)
    {
      $this->redirectError($code, $message);
    }
  }
}

