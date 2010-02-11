<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 */

require_once dirname(__FILE__).'/bootstrap.php';

$t = new LimeTest();

// @BeforeAll

class TestFilter extends Sonata_Filter
{
  public function execute(Sonata_Request $request, Sonata_Response $response)
  {
  }
}

// @Before

$reqMock = $t->mock('Sonata_Request');
$resMock = $t->mock('Sonata_Response');
$fcMock  = $t->mock('Sonata_FilterChain');
$dspMock = $t->mock('Sonata_Dispatcher');
$rmMock  = $t->mock('Sonata_RouteMap');

$frontController = new Sonata_Controller_Front($dspMock, $rmMock);

// @After

unset($frontController);

// @After

unset($frontController);

unset($reqMock);
unset($resMock);
unset($fcMock);
unset($dspMock);
unset($rmMock);

// @Test: ->addPreFilter() (with filter chains unset)

$filter = new TestFilter();

$fcMock->addFilter($filter)->never();
$fcMock->replay();

$frontController->addPreFilter($filter);

$fcMock->verify();

// @Test: ->addPreFilter() (with filter chains set)

$filter = new TestFilter();

$frontController->setFilterChains($fcMock, $fcMock);

$fcMock->addFilter($filter)->once();
$fcMock->replay();

$frontController->addPreFilter($filter);

$fcMock->verify();

// @Test: ->addPostFilter() (with filter chains unset)

$filter = new TestFilter();

$fcMock->addFilter($filter)->never();
$fcMock->replay();

$frontController->addPostFilter($filter);

$fcMock->verify();

// @Test: ->addPostFilter() (with filter chains set)

$filter = new TestFilter();

$frontController->setFilterChains($fcMock, $fcMock);

$fcMock->addFilter($filter)->once();
$fcMock->replay();

$frontController->addPostFilter($filter);

$fcMock->verify();

// @Test: ->handleRequest();

$t->todo('Write test for ->handleRequest()');
