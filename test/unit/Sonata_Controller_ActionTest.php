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

class FooController extends Sonata_Controller_Action
{
  protected $varHolderMock = null;
  
  public function __construct(Sonata_Request $request, Sonata_Response $response, Sonata_ParameterHolder $varHolderMock)
  {
    $this->varHolderMock = $varHolderMock;
    parent::__construct($request, $response);
  }
  
  protected function initializeVarHolder()
  {
    return $this->varHolderMock;
  }
    
  public function fooAction()
  {
  }
  
  public function barAction()
  {
    return self::ACTION_FAILURE;
  }
  
  public function myBar()
  {
  }
}

// @Before

$requestMock = $t->mock('Sonata_Request');
$responseMock = $t->mock('Sonata_Response');
$varHolderMock = $t->mock('Sonata_ParameterHolder');
$templateVewMock = $t->mock('Sonata_TemplateView');

$fooController = new FooController($requestMock, $responseMock, $varHolderMock);

// @Test: ->dispatch() - general

try 
{
  $fooController->dispatch('fail', $templateVewMock);
  $t->fail('No code should be executed after calling non-existing actions');
}
catch (Sonata_Exception_Controller_Action $ex)
{
  $t->pass('An exception is thrown for non-existing actions');
}

try
{
  $fooController->dispatch('myBar', $templateVewMock);
  $t->pass('Existing methods are executed correctly');
}
catch (Sonata_Exception_Controller_Action $ex)
{
  $t->fail('No exception should be thrown for existing methods');
}

try
{
  $fooController->dispatch('myBaz', $templateVewMock);
  $t->fail('No code should be executed after calling non-existing methods');
}
catch (Sonata_Exception_Controller_Action $ex)
{
  $t->pass('An exception is thrown for non-existing methods');
}

// @Test: ->dispatch() - action returning Sonata_Controller_Action::ACTION_SUCCESS

$requestMock->getParameter('resource')->returns('article')->once();
$requestMock->getParameter('format')->returns('xml')->once();
$requestMock->replay();

$varHolderMock->getAll()->returns(null)->once();
$varHolderMock->replay();

$templateVewMock->assign(null)->once();
$templateVewMock->render('foo', 'article', 'xml')->once();
$templateVewMock->replay();

$fooController->dispatch('fooAction', $templateVewMock);

$requestMock->verify();
$varHolderMock->verify();
$templateVewMock->verify();

// @Test: ->dispatch() - action returning Sonata_Controller_Action::ACTION_FAILURE

$requestMock->getParameter('resource')->returns('article')->once();
$requestMock->getParameter('format')->returns('xml')->once();
$requestMock->replay();

$varHolderMock->getAll()->returns(null)->once();
$varHolderMock->replay();

$templateVewMock->assign(null)->once();
$templateVewMock->render('Error', null, 'xml')->once();
$templateVewMock->replay();

$fooController->dispatch('barAction', $templateVewMock);

$requestMock->verify();
$varHolderMock->verify();
$templateVewMock->verify();
 
// @Test: ->setVar()

$varHolderMock->set('foo', '0815')->once();
$varHolderMock->replay();

$fooController->setVar('foo', '0815');

$varHolderMock->verify();
 
// @Test: ->getVar()

$varHolderMock->get('foo')->once();
$varHolderMock->replay();

$fooController->getVar('foo');

$varHolderMock->verify();
 
// @Test: ->__set()

$varHolderMock->setByRef('foo', 42)->once();
$varHolderMock->replay(); 

$fooController->foo = 42;

$varHolderMock->verify();
 
// @Test: ->__get()

$varHolderMock->get('foo')->once();
$varHolderMock->replay();

$foo = $fooController->foo;

$varHolderMock->verify();
 
// @Test: ->__isset()

$varHolderMock->has('foo')->once();
$varHolderMock->replay();

$res = isset($fooController->foo);

$varHolderMock->verify();
 
// @Test: ->__unset()
 
$varHolderMock->remove('bar')->once();
$varHolderMock->replay();

unset($fooController->bar);

$varHolderMock->verify();
