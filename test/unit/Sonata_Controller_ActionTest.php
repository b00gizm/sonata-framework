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
  public function fooAction()
  {
  }
  
  public function myBar()
  {
  }
}
 
$paths = array(
  'templates' => dirname(__FILE__).'/../templates',
  'commands'  => dirname(__FILE__).'/../commands',
  'routes'    => dirname(__FILE__).'/../routing/routing.yml',
);

Sonata_Config::set('paths', $paths);
 
// @Before
 
$request = $t->mock('Sonata_Request');
$response = $t->mock('Sonata_Response');
 
$reqStub = $t->stub('Sonata_Request');

$controller = new FooController($request, $response);
 
// @After
 
unset($request);
unset($response);
unset($controller);
unset($reqStub);

// @Test: ->dispatch()

$t->todo('Write test for ->dispatch()');
 
// @Test: ->getVarHolder()
 
$t->is(get_class($controller->getVarHolder()), 'Sonata_ParameterHolder', 'The var holder has the right type');
 
// @Test: ->setVar()

$controller->setVar('foo', '0815');
$t->is($controller->getVarHolder()->get('foo'), '0815', 'Template variables are set correcty');
 
// @Test: ->getVar()

$controller->setVar('foo', '0815');
$t->is($controller->getVar('foo'), '0815', 'Template variables are retrieved correctly');
 
// @Test: ->__set()
 
$controller->foo = 42;
$controller->bar = 4711;
$t->same($controller->getVarHolder()->getAll(), array('foo' => 42, 'bar' => 4711), 'Template variables are set correctly');
 
// @Test: ->__get()

$controller->foo = 42;

$foo = $controller->foo;
$t->is($foo, 42, 'Template variables are retrieved correctly');
$baz = $controller->baz;
$t->is($baz, null, 'NULL is returned for non-existing template variables');
 
// @Test: ->__isset()

$controller->foo = 42;

$t->is(isset($controller->foo), true, 'TRUE is returned for existing template variables');
$t->is(isset($controller->baz), false, 'FALSE is returned for non-existing template variables');
 
// @Test: ->__unset()
 
unset($controller->bar);
$t->is(isset($controller->bar), false, 'The template variable was unset correctly');
