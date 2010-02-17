<?php
 
/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 *
 * Sonata_Controller_Front
 *
 * @package framework.controller
 **/
class Sonata_Controller_Front
{
  /**
   * A dispatcher instance
   *
   * @var Sonata_Dispatcher
   */
  protected $dispatcher = null;
  
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
  public function __construct(Sonata_Dispatcher $dispatcher, Sonata_RouteMap $map)
  {
    $this->dispatcher = $dispatcher;
    $this->map = $map;
  } 
  
  /**
   * Setter dispatcher
   *
   * @param Sonata_Dispatcher $dispatcher A dispatcher object
   */
  public function setDispatcher(Sonata_Dispatcher $dispatcher)
  {
    $this->resolver = $dispatcher;
  }
  
  /**
   * Getter dispatcher
   *
   * @return Sonata_Dispatcher $dispatcher A dispatcher object
   */
  public function getDispatcher()
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
    
    try
    {
      // Try to resolve route string
      $routeString = $this->request->getParameter('route');
      if (!$this->map->resolveRouteString($routeString))
      {
        throw new Sonata_Exception_Controller_Front(sprintf("Could not resolve route '%s'. Please check your config.", $routeString), 404);
      }
      
      if ($this->preFilters)
      {
        // Process all pre filters
        $this->preFilters->processFilters($request, $response);
      }
      
      // Begin dispatch
      $paths = Sonata_Config::get('paths');
      if (empty($paths) || !isset($paths['controllers']))
      {
        throw new Sonata_Exception_Config('Could not determine the path for the controllers directory', 500);
      }
      $this->dispatcher->setControllersDir($paths['controllers']);
      $this->dispatcher->dispatch($this->request, $this->response);
      
      if ($this->postFilters)
      {
        // Process all post filters
        $this->postFilters->processFilters($request, $response);
      }
    }
    catch(Sonata_Exception $ex)
    {
      $this->renderError($ex->getMessage(), $ex->getCode());
    }
    
    // Flush the response
    $this->response->flush();
  }
  
  public function setFilterChains(Sonata_FilterChain $preFilterChain, Sonata_FilterChain $postFilterChain)
  {
    $this->preFilters = $preFilterChain;
    $this->postFilters = $postFilterChain;
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
    if ($this->preFilters)
    {
      $this->preFilters->addFilter($filter);
    }
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
    if ($this->postFilters)
    {
      $this->postFilters->addFilter($filter);
    }
  }
  
  protected function renderError($message, $code = 500)
  {
    $this->response->setStatusCode($code);
    $templateView = new Sonata_TemplateView('Error');
    $templateView->assign('code', $code);
    $message = is_null($message) ? $this->response->getStatusText() : $message;
    $templateView->assign('message', $message);
    
    $templateView->render($this->request, $this->response);
    $this->response->flush();
    exit(0);
  }
}