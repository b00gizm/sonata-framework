<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 */

require_once dirname(__FILE__).'/bootstrap.php';

$t = new LimeTest(11);

// @BeforeAll

  class SonataAppHelper extends Sonata_App
  {
    protected $containerMock = null;
  
    protected $dispatcherMock = null;
  
    protected $preFilterChainMock = null;
  
    protected $postFilterChainMock = null;
  
    public function __construct(sfContainerInterface $containerMock, $environment = 'prod', $isDebug = false)
    {
      $this->containerMock = $containerMock;
      parent::__construct($environment, $isDebug);
    }
  
    public function setDispatcherMock(Sonata_Dispatcher $dispatcherMock)
    {
      $this->dispatcherMock = $dispatcherMock;
    }
  
    public function setPreFilterChainMock(Sonata_FilterChain $filterChainMock)
    {
      $this->preFilterChainMock = $filterChainMock;
    }
  
    public function setPostFilterChainMock(Sonata_FilterChain $filterChainMock)
    {
      $this->postFilterChainMock = $filterChainMock;
    }
  
    protected function initializeContainer()
    {
      return $this->containerMock;
    }
  
    protected function initializePreFilterChain()
    {
      return $this->preFilterChainMock;
    }
  
    protected function createDispatcher()
    {
      return $this->dispatcherMock;
    }
  
    protected function initializePostFilterChain()
    {
      return $this->postFilterChainMock;
    }
    
    public function registerAppDir() 
    {
      return dirname(__FILE__);
    }

    // fixtures

    public function registerPaths() 
    {
      return array(
        'config'      => dirname(__FILE__).'/../config',
        'controllers' =>  dirname(__FILE__).'/../controllers',
        'templates'   =>  dirname(__FILE__).'/../templates',
      );
    }

    public function registerRoutes(Sonata_RouteMap $map) 
    {
    }

    public function registerPreFilters() 
    {
    }

    public function registerPostFilters() 
    {
    }
  }

// @Before

  $containerMock = $t->mock('sfContainerInterface');
  $dispatcherMock = $t->mock('Sonata_Dispatcher');
  $preFilterChainMock = $t->mock('Sonata_FilterChain');
  $postFilterChainMock = $t->mock('Sonata_FilterChain');

  $app = new SonataAppHelper($containerMock);
  $app->setDispatcherMock($dispatcherMock);
  $app->setPreFilterChainMock($preFilterChainMock);
  $app->setPostFilterChainMock($postFilterChainMock);

// @After

  unset($containerMock);
  unset($dispatcherMock);
  unset($preFilterChainMock);
  unset($postFilterChainMock);
  unset($app);

// @Test: ->run()

  $requestMock = $t->mock('Sonata_Reqeust');
  $responseMock = $t->mock('Sonata_Response');
  $routeMapMock = $t->mock('Sonata_RouteMap');
  $templateViewMock = $t->mock('Sonata_TemplateView');
  
  $containerMock->getService('request')->returns($requestMock)->once();
  $containerMock->getService('response')->returns($responseMock)->once();
  $containerMock->getService('route_map')->returns($routeMapMock)->once();
  $containerMock->getService('template_view')->returns($templateViewMock)->once();
  $containerMock->replay();
  
  $preFilterChainMock->processFilters($requestMock, $responseMock)->once();
  $preFilterChainMock->replay();
  
  $dispatcherMock->setControllersDir(dirname(__FILE__).'/../controllers')->once();
  $dispatcherMock->dispatch()->once();
  $dispatcherMock->replay();
  
  $postFilterChainMock->processFilters($requestMock, $responseMock)->once();
  $postFilterChainMock->replay();
  
  $app->run();
  
  $t->is($app->getEnvironment(), 'prod', 'The default environment was set correctly');
  $t->is($app->getIsDebug(), false, 'The default \'isDebug\' flag was set correctly');
  $t->is($app->getPaths(), array(
    'config'      => dirname(__FILE__).'/../config',
    'controllers' =>  dirname(__FILE__).'/../controllers',
    'templates'   =>  dirname(__FILE__).'/../templates',
  ), 'The paths were set correctly');
  
  $containerMock->verify();
  $preFilterChainMock->verify();
  $dispatcherMock->verify();
  $postFilterChainMock->verify();
  