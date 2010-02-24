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

$containerMock = $t->mock('sfServiceContainerInterface');
$containerMock->getService('request')->returns($requestMock);
$containerMock->getService('response')->returns($responseMock);
$containerMock->getService('route_map')->returns($routeMapMock);
$containerMock->getService('template_view')->returns($templateViewMock);
$containerMock->replay();

$dispatcher = new Sonata_Dispatcher($containerMock);

// @AfterAll

unlink(dirname(__FILE__).'/../fixtures/controllers/ArticleController.class.php');
unlink(dirname(__FILE__).'/../fixtures/controllers/InvalidController.class.php');

// @Test: ->getControllerClassName() - non-existing request parameter 'resource'

$requestMock->getParameter('resource')->returns(null);
$requestMock->replay();

$t->is($dispatcher->getControllerClassName($requestMock), null, 'Returns NULL for non-existing request parameter \'resource\'');

// @Test: ->getControllerClassName() - existing request parameter 'resource'

$requestMock->getParameter('resource')->returns('article');
$requestMock->replay();

$t->is($dispatcher->getControllerClassName($requestMock), 'ArticleController', 'Returns the correct controller name for existing request parameter \'resource\'');

$requestMock->verify();

// @Test ->loadControllerClass - non-existing controller class

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

// @Test ->loadControllerClass - existing controller class

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

// @Test: ->dispatch() - simulating a route string that cannot be resolved

$requestMock->getParameter('format')->returns('xml');
$requestMock->getParameter('route')->returns('foo/bar.xml');
$requestMock->replay();

$routeMapMock->resolveRouteString('foo/bar.xml')->returns(false);
$routeMapMock->replay();

$templateViewMock->assign('code', 500)->once();
$templateViewMock->assign('message', 'Could not resolve route \'foo/bar.xml\'. Please check your routing configuration')->once();
$templateViewMock->render('Error', null, 'xml')->once();
$templateViewMock->replay();

$dispatcher->dispatch();

$containerMock->verify();
$routeMapMock->verify();
$routeMapMock->verify();
$templateViewMock->verify();

// @Test: ->dispatch() - simulating non-existing controller class

$dispatcher->setControllersDir(dirname(__FILE__).'/../fixtures/controllers');

$requestMock->getParameter('format')->returns('xml');
$requestMock->getParameter('route')->returns('foo/bar.xml');
$requestMock->getParameter('resource')->returns('non_existing');
$requestMock->replay();

$routeMapMock->resolveRouteString('foo/bar.xml')->returns(true);
$routeMapMock->replay();

$templateViewMock->assign('code', 500)->once();
$templateViewMock->assign('message', sprintf("Could not load controller class '%s' in directory '%s'", 'NonExistingController', (dirname(__FILE__).'/../fixtures/controllers')))->once();
$templateViewMock->render('Error', null, 'xml')->once();
$templateViewMock->replay();

$dispatcher->dispatch();

$containerMock->verify();
$routeMapMock->verify();
$routeMapMock->verify();
$templateViewMock->verify();

// @Test: ->dispatch() - simulating an invalid controller class

$dispatcher->setControllersDir(dirname(__FILE__).'/../fixtures/controllers');

$requestMock->getParameter('format')->returns('xml');
$requestMock->getParameter('route')->returns('foo/bar.xml');
$requestMock->getParameter('resource')->returns('invalid');
$requestMock->replay();

$routeMapMock->resolveRouteString('foo/bar.xml')->returns(true);
$routeMapMock->replay();

$templateViewMock->assign('code', 500)->once();
$templateViewMock->assign('message', sprintf("Controller '%s' is not an instance of Sonata_Controller_Action", 'InvalidController'))->once();
$templateViewMock->render('Error', null, 'xml')->once();
$templateViewMock->replay();

$dispatcher->dispatch();

$containerMock->verify();
$routeMapMock->verify();
$routeMapMock->verify();
$templateViewMock->verify();

// @Test: ->dispatch() - simulating a valid controller class

$dispatcher->setControllersDir(dirname(__FILE__).'/../fixtures/controllers');

$requestMock->getParameter('format')->returns('xml');
$requestMock->getParameter('route')->returns('foo/bar.xml');
$requestMock->getParameter('resource')->returns('invalid');
$requestMock->getParameter('action')->returns('foo')->once();
$requestMock->replay();

$routeMapMock->resolveRouteString('foo/bar.xml')->returns(true);
$routeMapMock->replay();

$dispatcher->dispatch();

$containerMock->verify();
$routeMapMock->verify();
$routeMapMock->verify();

