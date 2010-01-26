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
   * Filter chain with filters to be executes before the controller handles the actual request
   *
   * @var array
   */
  protected $preFilters = null;
  
  /**
   * Filter chain with filters to be executes after the controller handles the actual request
   *
   * @var array
   */
  protected $postFilters = null;
  
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
    
    $this->preFilters = new Sonata_FilterChain();
    $this->postFilters = new Sonata_FilterChain();
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
      // Process all pre filters
      $this->preFilters->processFilters($request, $response);
      
      // Execute the command
      $command->execute($this->request, $this->response);
      
      // Process all post filters
      $this->preFilters->processFilters($request, $response);
    }
    catch(Sonata_Exception $ex)
    {
      $this->redirectErrorUnless($command, $ex->getCode());
    }
    
    // Flush the response
    $this->response->flush();
  }
  
  /**
   * Adds a filter to the filter chain that contains pre filters
   * by delegating the it to Sonata_Filter::addFilter()
   *
   * @param Sonata_Filter $filter 
   * @see Sonata_Filter::addFilter()
   */
  public function addPreFilter(Sonata_Filter $filter)
  {
    $this->preFilters->addFilter($filter);
  }
  
  /**
   * Adds a filter to the filter chain that contains post filters
   * by delegating the it to Sonata_Filter::addFilter()
   *
   * @param Sonata_Filter $filter 
   * @see Sonata_Filter::addFilter()
   */
  public function addPostFilter(Sonata_Filter $filter)
  {
    $this->postFilters->addFilter($filter);
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

