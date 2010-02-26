<?

/**
 * This file is part of the Sonata RESTful  framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 */

require_once dirname(__FILE__).'/bootstrap.php';

$t = new LimeTest();

// @BeforeAll

  class DispatcherHelper extends Sonata_Dispatcher
  {
    protected $preFilterChainMock = null;
  
    protected $postFilterChainMock = null;
    
    public function __construct(sfServiceContainerInterface $container, Sonata_FilterChain $preFilterChainMock, Sonata_FilterChain $postFilterChainMock)
    {
      $this->preFilterChainMock = $preFilterChainMock;
      $this->postFilterChainMock = $postFilterChainMock;
      parent::__construct($container);
    }
    
    protected function initializePreFilterChain()
    {
      return $this->preFilterChainMock;
    }
  
    protected function initializePostFilterChain()
    {
      return $this->postFilterChainMock;
    }
  }

  $fh = fopen(dirname(__FILE__).'/../fixtures/controllers/ArticleController.class.php', 'wb');
  fputs($fh, '<?php class ArticleController extends Sonata_Controller_Action {} ?>');
  fclose($fh);

  $fh = fopen(dirname(__FILE__).'/../fixtures/controllers/InvalidController.class.php', 'wb');
  fputs($fh, '<?php class InvalidController {} ?>');
  fclose($fh);

// @Before

  $requestMock = $t->mock('Sonata_Request');
  $responseMock = $t->mock('Sonata_Response');
  $routeMapMock = $t->mock('Sonata_RouteMap');
  $templateViewMock = $t->mock('Sonata_TemplateView');
  
  $preFilterChainMock = $t->mock('Sonata_FilterChain');
  $postFilterChainMock = $t->mock('Sonata_FilterChain');

  $containerMock = $t->mock('sfServiceContainerInterface');
  $containerMock->getService('request')->returns($requestMock);
  $containerMock->getService('response')->returns($responseMock);
  $containerMock->getService('route_map')->returns($routeMapMock);
  $containerMock->getService('template_view')->returns($templateViewMock);
  $containerMock->replay();

  $dispatcher = new DispatcherHelper($containerMock, $preFilterChainMock, $postFilterChainMock);
  
// @After

  unset($requestMock);
  unset($responseMock);
  unset($routeMapMock);
  unset($templateViewMock);
  unset($containerMock);

// @AfterAll

  unlink(dirname(__FILE__).'/../fixtures/controllers/ArticleController.class.php');
  unlink(dirname(__FILE__).'/../fixtures/controllers/InvalidController.class.php');
  
// @Test: ->addPreFilters()

  // @Test: argument: null
  
  $preFilterChainMock->addFilters()->never();
  $preFilterChainMock->replay();
  
  $dispatcher->addPreFilters(null);
  
  $preFilterChainMock->verify();
  
  // @Test: argument: array()
  $preFilterChainMock->addFilters()->never();
  $preFilterChainMock->replay();
  
  $dispatcher->addPreFilters(array());
  
  $preFilterChainMock->verify();
  
  // @Test: argument: invalid input
  
  $preFilterChainMock->addFilters()->never();
  $preFilterChainMock->replay();
  
  $dispatcher->addPreFilters("This won't do");
  
  $preFilterChainMock->verify();
  
  // @Test: argument: array of (mocked) filters
  $filterMock = $t->mock('Sonata_Filter');
  $arg = array($filterMock, $filterMock, $filterMock);
  
  $preFilterChainMock->addFilter($filterMock)->times(3);
  $preFilterChainMock->replay();
  
  $dispatcher->addPreFilters($arg);
  
  $preFilterChainMock->verify();
  
// @Test: ->addPostFilters()
    
  // @Test: argument: null
  
  $postFilterChainMock->addFilters()->never();
  $postFilterChainMock->replay();
  
  $dispatcher->addPostFilters(null);
  
  $postFilterChainMock->verify();
  
  // @Test: argument: array()
  
  $postFilterChainMock->addFilters()->never();
  $postFilterChainMock->replay();
  
  $dispatcher->addPostFilters(array());
  
  $postFilterChainMock->verify();
  
  // @Test: argument: invalid input
  
  $postFilterChainMock->addFilters()->never();
  $postFilterChainMock->replay();
  
  $dispatcher->addPostFilters("This won't do");
  
  $postFilterChainMock->verify();
  
  // @Test: argument: array of (mocked) filters
  
  $filterMock = $t->mock('Sonata_Filter');
  $arg = array($filterMock, $filterMock, $filterMock);
  
  $postFilterChainMock->addFilter($filterMock)->times(3);
  $postFilterChainMock->replay();
  
  $dispatcher->addPostFilters($arg);
  
  $postFilterChainMock->verify();

// @Test: ->getControllerClassName() - non-existing request parameter 'resource'

  $requestMock->getParameter('resource')->returns(null);
  $requestMock->replay();

  $t->is($dispatcher->getControllerClassName($requestMock), null, 'Returns NULL for non-existing request parameter \'resource\'');

// @Test: ->getControllerClassName() - existing request parameter 'resource'

  $requestMock->getParameter('resource')->returns('article');
  $requestMock->replay();

  $t->is($dispatcher->getControllerClassName($requestMock), 'ArticleController', 'Returns the correct controller name for existing request parameter \'resource\'');

  $requestMock->verify();

// @Test: ->loadControllerClass

  // @Test: non-existing controller class

  $dispatcher->setControllersDir(dirname(__FILE__).'/../fixtures/controllers');

  try
  {
    $dispatcher->loadControllerClass('NonExistingController');
    $t->fail('No code should be executed after this');  
  }
  catch (Sonata_Exception_Dispatcher $ex)
  {
    $t->pass('Throws an exception for non-existing controller classes');
  }

  // @Test: existing controller class

  $dispatcher->setControllersDir(dirname(__FILE__).'/../fixtures/controllers');

  try
  {
    $dispatcher->loadControllerClass('ArticleController');
    $t->is(class_exists('ArticleController'), true, 'The controller class was loaded correctly');  
  }
  catch (Sonata_Exception_Dispatcher $ex)
  {
    $t->pass('Throws an exception for non-existing controller classes');
  }
  
// @Test: ->dispatch()

  // @Test: simulating a route string that cannot be resolved

  $requestMock->getParameter('format')->returns('xml');
  $requestMock->getParameter('route')->returns('foo/bar.xml');
  $requestMock->replay();

  $routeMapMock->resolveRouteString('foo/bar.xml')->returns(false);
  $routeMapMock->replay();

  $templateViewMock->assign('code', 500)->once();
  $templateViewMock->assign('message', 'Could not resolve route \'foo/bar.xml\'. Please check your routing configuration')->once();
  $templateViewMock->render('Error', null, 'xml')->returns('<?xml version="1.0" encoding="utf-8"><foo></foo>')->once();
  $templateViewMock->replay();
  
  $responseMock->appendToBody('<?xml version="1.0" encoding="utf-8"><foo></foo>')->once();
  $responseMock->flush()->once();
  $responseMock->replay();

  $dispatcher->dispatch();

  $containerMock->verify();
  $requestMock->verify();
  $routeMapMock->verify();
  $templateViewMock->verify();
  $responseMock->verify();

  // @Test: simulating non-existing controller class

  $dispatcher->setControllersDir(dirname(__FILE__).'/../fixtures/controllers');

  $requestMock->getParameter('format')->returns('xml');
  $requestMock->getParameter('route')->returns('foo/bar.xml');
  $requestMock->getParameter('resource')->returns('non_existing');
  $requestMock->replay();

  $routeMapMock->resolveRouteString('foo/bar.xml')->returns(true);
  $routeMapMock->replay();

  $templateViewMock->assign('code', 500)->once();
  $templateViewMock->assign('message', sprintf("Could not load controller class '%s' in directory '%s'", 'NonExistingController', (dirname(__FILE__).'/../fixtures/controllers')))->once();
  $templateViewMock->render('Error', null, 'xml')->returns('<?xml version="1.0" encoding="utf-8"><foo></foo>')->once();
  $templateViewMock->replay();
  
  $responseMock->appendToBody('<?xml version="1.0" encoding="utf-8"><foo></foo>')->once();
  $responseMock->flush()->once();
  $responseMock->replay();

  $dispatcher->dispatch();

  $containerMock->verify();
  $routeMapMock->verify();
  $routeMapMock->verify();
  $templateViewMock->verify();
  $responseMock->verify();

  // @Test: simulating an invalid controller class

  $dispatcher->setControllersDir(dirname(__FILE__).'/../fixtures/controllers');

  $requestMock->getParameter('format')->returns('xml');
  $requestMock->getParameter('route')->returns('foo/bar.xml');
  $requestMock->getParameter('resource')->returns('invalid');
  $requestMock->replay();

  $routeMapMock->resolveRouteString('foo/bar.xml')->returns(true);
  $routeMapMock->replay();

  $templateViewMock->assign('code', 500)->once();
  $templateViewMock->assign('message', sprintf("Controller '%s' is not an instance of Sonata_Controller_Action", 'InvalidController'))->once();
  $templateViewMock->render('Error', null, 'xml')->returns('<?xml version="1.0" encoding="utf-8"><foo></foo>')->once();
  $templateViewMock->replay();
  
  $responseMock->appendToBody('<?xml version="1.0" encoding="utf-8"><foo></foo>')->once();
  $responseMock->flush()->once();
  $responseMock->replay();

  $dispatcher->dispatch();

  $containerMock->verify();
  $routeMapMock->verify();
  $routeMapMock->verify();
  $templateViewMock->verify();
  $responseMock->verify();

  // @Test: simulating a valid controller class

  $dispatcher->setControllersDir(dirname(__FILE__).'/../fixtures/controllers');

  $requestMock->getParameter('format')->returns('xml');
  $requestMock->getParameter('route')->returns('foo/bar.xml');
  $requestMock->getParameter('resource')->returns('mock');
  $requestMock->getParameter('action', 'list')->returns('foo')->once();
  $requestMock->replay();

  $routeMapMock->resolveRouteString('foo/bar.xml')->returns(true);
  $routeMapMock->replay();
  
  $preFilterChainMock->processFilters($requestMock, $responseMock)->once();
  $preFilterChainMock->replay();
  
  $postFilterChainMock->processFilters($requestMock, $responseMock)->once();
  $postFilterChainMock->replay();

  $responseMock->appendToBody()->never();
  $responseMock->flush()->once();
  $responseMock->replay();

  $dispatcher->dispatch();

  $containerMock->verify();
  $routeMapMock->verify();
  $routeMapMock->verify();
  $preFilterChainMock->verify();
  $postFilterChainMock->verify();
  $responseMock->verify();
