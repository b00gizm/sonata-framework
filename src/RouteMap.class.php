<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 *
 * Sonata_RouteMap
 *
 * @package framework
 **/
class Sonata_RouteMap
{  
  /**
   * The HTTP request object
   *
   * @var Sonata_Request
   **/
  protected $request = null;
  
  /**
   * Array with all connected routes
   *
   * @var array
   **/
  protected $routes = array();
  
  /**
   * Array with route map options
   *
   * @var array
   */
  protected $options = array();
  
  /**
   * The (protected) constructor
   *
   * @param Sonata_Request $request The HTTP request object
   */
  public function __construct(Sonata_Request $request)
  {
    $this->request = $request;
  }
  
  /**
   * Tries to load all routes from a config file
   *
   * @param string $routingConfig The path to the config file 
   * @param Sonata_Parser_Config $parser A config parser instance
   * @throws Exception
   */
  public function load($routingConfig, Sonata_Parser_Config $parser = null)
  {  
    try
    {
      if (is_null($parser))
      {
        $parser = new Sonata_Parser_Config(new Sonata_Parser_Driver_Yaml());
      }
      
      $res = $parser->parse($routingConfig);
      $mappings = (isset($res['route_map'])) ? $res['route_map'] : array();
      
      if (!empty($mappings))
      {
        if (is_array($mappings))
        {
          foreach ($mappings as $key => $mapping) 
          {
            if (!is_array($mapping))
            {
              return;
            }

            $keys = array_keys($mapping);
            switch ($keys[0])
            {
              case 'connect' :
                $paramters = $mapping['connect'];
                $keys = array_keys($paramters);
                if (count(array_intersect(array('pattern', 'resource', 'verbs', 'action'), $keys)) >= 4)
                {
                  $this->connect($paramters['pattern'], $paramters['resource'], $paramters['verbs'], $paramters['action']);
                }
                break;
                
              case 'resources' :
                $resources = $mapping['resources'];
                if (is_string($resources))
                {
                  $this->resources($resources);
                }
                elseif (is_array($resources))
                {
                  $keys = array_keys($resources);
                  if (in_array('plurals', $keys, true))
                  {
                    $plural = $resources['plurals'];
                    if (is_string($plural))
                    {
                      // Default resources
                      $singular = null;
                      if (isset($resources['singulars']) && is_string($resources['singulars']))
                      {
                        $singular = $resources['singulars'];
                      }
                      
                      $pathPrefix = '';
                      if (isset($resources['path_prefix']) && is_string($resources['path_prefix']))
                      {
                        $pathPrefix = $resources['path_prefix'];
                      }
                      
                      $this->resources($plural, $singular, $pathPrefix);
                    }
                    else
                    {
                      // Nested resources
                      if (count($resources['plurals']) >= 2)
                      {
                        $nestedResources = $resources['plurals'][0];
                        $parentResources = $resources['plurals'][1];
                        if (is_string($nestedResources) && is_string($parentResources))
                        {
                          $singulars = array();
                          if (isset($resources['singulars']) && is_array($resources['singulars']) && count($resources['singulars']) >= 2)
                          {
                            $singulars = $resources['singulars'];
                          }
                          
                          $this->nestedResources($nestedResources, $parentResources, $singulars);
                        }
                      }
                    }
                  }
                  elseif (count($resources) >= 2)
                  {
                    $nestedResources = $resources[0];
                    $parentResources = $resources[1];
                    if (is_string($nestedResources) && is_string($parentResources))
                    {
                      $this->nestedResources($nestedResources, $parentResources);
                    }
                  }
                }
                break;
            }
          }
        }
      }
    }
    catch (Exception $ex)
    {
      // TODO: exception handling
    }
  }
  
  /**
   * Getter routes array
   *
   * @return array The connected routes
   */
  public function getRoutes()
  {
    return $this->routes;
  }
  
  /**
   * Setter options
   *
   * @param array $options The options 
   */
  public function setOptions(array $options)
  {
    $this->options = $options;
  }  
  
  /**
   * Getter options
   *
   * @return array options
   */
  public function getOptions()
  {
    return $this->options;
  }
  
  /**
   * Setter HTTP request
   *
   * @param Sonata_Request $request The HTTP request object
   */
  public function setRequest(Sonata_Request $request)
  {
    $this->request = $request;
  }
  
  /**
   * Getter HTTP request
   *
   * @return Sonata_Request The HTTP request object
   */
  public function getRequest()
  {
    return $this->request;
  }
  
  /**
   * Connects a route to the mapper
   *
   * @param string $route The route as string, i.e. /articles/:id.:format
   * @param string $resource The resource to use, i.e. article
   * @param string $verbs The allowed HTTP methods. Leave array empty for all methods
   * @param string $action The action to be performed
   */
  public function connect($route, $resource, $verbs = array(), $action = null)
  {
    $matches = array();
    $pattern = '/:([A-Za-z0-9-_]+)/';
    preg_match_all($pattern, $route, $matches);
    
    $routePattern = preg_replace($pattern, '([A-Za-z0-9-_]+)', $route);
    $routePattern = preg_replace('/\//', '\/', $routePattern);
    $routePattern = preg_replace('/\./', '\.', $routePattern);
    
    $route = new Sonata_Route();
    $route->setPattern('/^'.$routePattern.'/');
    $route->setResource($resource);
    $route->setVerbs($verbs);
    $route->setAction($action);
    $route->setParameters((count($matches) < 1) ? array() : $matches[1]);
    
    $this->routes[] = $route;
  }
  
  /**
   * RESTful routes shortcut. Connects the following routes automatically
   * 
   * GET    /resources.:format     - list
   * POST   /resources.:format     - create
   * GET    /resources/:id.:format - show
   * PUT    /resources/:id.:format - update
   * DELETE /resources/:id.:format - destroy 
   *
   * @param string $resources The resource as plural, i.e. articles
   * @param string $singular The resource's singular form if unregular, i.e. teeth => tooth
   * @param string $pathPrefix A prefix to be used for all routes of this resource
   */
  public function resources($resources, $singular = null, $pathPrefix = '')
  {
    $resource = (is_null($singular) || !is_string($singular)) ? substr($resources, 0, strlen($resources)-1) : $singular;
    
    $this->connect($pathPrefix.'/'.$resources.'.:format', $resource, array('GET'), $action = 'list');
    $this->connect($pathPrefix.'/'.$resources.'.:format', $resource, array('POST'), $action = 'create');
    $this->connect($pathPrefix.'/'.$resources.'/:id.:format', $resource, array('GET'), $action = 'show');
    $this->connect($pathPrefix.'/'.$resources.'/:id.:format', $resource, array('PUT'), $action = 'update');
    $this->connect($pathPrefix.'/'.$resources.'/:id.:format', $resource, array('DELETE'), $action = 'destroy');
  }
  
  /**
   * RESTful nested routes shortcut. Connects the following routes automatically
   *
   * GET    /parents/:parent_id/resources.:format     - list
   * POST   /parents/:parent_id/resources.:format     - create
   * GET    /parents/:parent_id/resources/:id.:format - show
   * PUT    /parents/:parent_id/resources/:id.:format - update
   * DELETE /parents/:parent_id/resources/:id.:format - destroy
   *
   * @param string $resources The resource as plural, i.e. comments
   * @param string $parentResources The parent resource as plural, i.e. articles
   * @param array $singulars Array with singulars if unregular
   */
  public function nestedResources($resources, $parentResources, array $singulars = array())
  {
    $resource = (!isset($singulars[0]) || is_null($singulars[0]) || !is_string($singulars[0])) ? substr($resources, 0, strlen($resources)-1) : $singulars[0];
    $parentResource = (!isset($singulars[1]) || is_null($singulars[1]) || !is_string($singulars[1])) ? substr($parentResources, 0, strlen($parentResources)-1) : $singulars[1];
    
    $this->connect('/'.$parentResources.'/:'.$parentResource.'_id/'.$resources.'.:format', $resource, array('GET'), $action = 'list');
    $this->connect('/'.$parentResources.'/:'.$parentResource.'_id/'.$resources.'.:format', $resource, array('POST'), $action = 'create');
    $this->connect('/'.$parentResources.'/:'.$parentResource.'_id/'.$resources.'/:id.:format', $resource, array('GET'), $action = 'show');
    $this->connect('/'.$parentResources.'/:'.$parentResource.'_id/'.$resources.'/:id.:format', $resource, array('PUT'), $action = 'update');
    $this->connect('/'.$parentResources.'/:'.$parentResource.'_id/'.$resources.'/:id.:format', $resource, array('DELETE'), $action = 'destroy');
  }
  
  /**
   * Tries to resolve a route string. If it matches one of the connected routes
   * (the firt one) the request object is updated with all neccessary parameters
   * and the method returns TRUE.
   *
   * If no route can be matched, the method returns FALSE
   *
   * @param string $routeString The requested route as string
   * @return boolean The result
   */
  public function resolveRouteString($routeString)
  {
    if (empty($routeString))
    {
      return false;
    }
    
    foreach($this->routes as $route)
    { 
      $matches = array();

      $res = preg_match_all($route->getPattern(), '/'.$routeString, $matches);
      
      $verbs = $route->getVerbs();
      if ($res !== false && $res > 0 && (empty($verbs) || in_array($this->request->getMethod(), $verbs)))
      {
        $this->request->addParameter('resource', $route->getResource());
        $this->request->addParameter('action', $route->getAction());
        $this->request->addParameter('cmd', $route->getCommandName());
        
        $parameters = $route->getParameters();
        for ($i = 1; $len = count($parameters), $i <= $len; $i++)
        {
          $this->request->addParameter($parameters[$i-1], $matches[$i][0]);
        }
        
        return true;
      }
    }
    
    return false;
  }
}
