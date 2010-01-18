<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 */

require_once dirname(__FILE__).'/bootstrap.php';

$t = new LimeTest();

// @Before

$fh = fopen(dirname(__FILE__).'/../fixtures/templates/Foo.php', 'wb');
fputs($fh, 'This will fail');
fclose($fh);

$fh = fopen(dirname(__FILE__).'/../fixtures/templates/MyCommandSuccess.xml.php', 'wb');
fputs($fh, '<h1>This will pass</h1>');
fclose($fh);

$request = new Sonata_Request();
$response = new Sonata_Response();

// @After

unlink(dirname(__FILE__).'/../fixtures/templates/Foo.php');
unlink(dirname(__FILE__).'/../fixtures/templates/MyCommandSuccess.xml.php');

unset($request);
unset($response);

// @Test: ->assign()

$tv = new Sonata_TemplateView('Bar');
$tv->assign('foo', 42);
$tv->assign('bar', 4711);
$t->is($tv->getTemplateVars(), array('foo' => 42, 'bar' => 4711), 'The template vars were assigned correctly');

// @Test: ->__get()

$tv = new Sonata_TemplateView('Bar');
$tv->assign('foo', 42);
$tv->assign('bar', 4711);
$t->is($tv->foo, 42, 'Assigned template vars are retrieved correctly via __get');
$t->is($tv->baz, null, 'Non-existing template vars are NULL per default');

// @Test: ->render()

$tv = new Sonata_TemplateView('Foo');

try
{
  $tv->render($request, $response);
  $t->fail();
}
catch (Sonata_Exception_ConfigNotFound $ex)
{
  $t->pass('An exception is thrown Sonata_Template_View could not determine the path for the templates directory');
}

$parser = new Sonata_Parser_Config(new Sonata_Parser_Driver_Yaml());
Sonata_Config::load(dirname(__FILE__).'/../fixtures/config/sonata.yml', $parser);

try
{
  $tv->render($request, $response);
  $t->fail();
}
catch (Sonata_Exception_TemplateNotFound $ex)
{
  $t->pass('An exception is thrown if you try to access a non-existing template');
}

$tv = new Sonata_TemplateView('MyCommand');
try
{
  $tv->render($request, $response);
  $t->pass('No exception will be thrown for existing template files');
}
catch (Sonata_Exception_TemplateNotFound $ex)
{
  $t->fail();
}

$t->is($response->getBody(), '<h1>This will pass</h1>', 'The template\'s content was append to the response\'s body correctly');
