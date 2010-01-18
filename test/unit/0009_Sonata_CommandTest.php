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

class TestCommand1 extends Sonata_Command 
{
  public function doExecute(Sonata_Request $request, Sonata_Response $response) {}
}

class TestCommand2 extends Sonata_Command 
{
  public function doExecute(Sonata_Request $request, Sonata_Response $response) 
  {
    return self::EXEC_SUCCESS;
  }
}

class TestCommand3 extends Sonata_Command
{
  public function doExecute(Sonata_Request $request, Sonata_Response $response) 
  {
    return 'Foo';
  }
}

class TestCommand4 extends Sonata_Command
{
  public function doExecute(Sonata_Request $request, Sonata_Response $response) 
  {
    return 'Bar';
  }
}

class TestCommand5 extends Sonata_Command
{
  public function doExecute(Sonata_Request $request, Sonata_Response $response) 
  {
    return self::EXEC_FAILURE;
  }
}

$fh = fopen(dirname(__FILE__).'/../fixtures/templates/TestSuccess.xml.php', 'wb');
fputs($fh, '<rsp>It works</rsp>');
fclose($fh);

$fh = fopen(dirname(__FILE__).'/../fixtures/templates/FooSuccess.xml.php', 'wb');
fputs($fh, '<rsp>This works, too</rsp>');
fclose($fh);

$parser = new Sonata_Parser_Config(new Sonata_Parser_Driver_Yaml());
Sonata_Config::load(dirname(__FILE__).'/../fixtures/config/sonata.yml', $parser);

// @Before

$request = new Sonata_Request();
$response = new Sonata_Response();
$testCommand = new TestCommand1();

$reqStub = $t->stub('Sonata_Request');
$reqStub->getParameter('format')->returns('xml');
$reqStub->getParameter('cmd')->returns('Test');
$reqStub->replay();

// @AfterAll

unlink(dirname(__FILE__).'/../fixtures/templates/TestSuccess.xml.php');
unlink(dirname(__FILE__).'/../fixtures/templates/FooSuccess.xml.php');

// @After

unset($request);
unset($response);
unset($testCommand);
unset($reqStub);

// @Test: ->execute() / ->doExecute()

$testCommand = new TestCommand1();
$testCommand->execute($reqStub, $response);
$t->is($response->getBody(), '<rsp>It works</rsp>', 'Loads the template <CommandName>Success.*.php if doExecute() returns nothing');

$testCommand = new TestCommand2();
$testCommand->execute($reqStub, $response);
$t->is($response->getBody(), '<rsp>It works</rsp>', 'Loads the template <CommandName>Success.*.php if doExecute() returns PSCommand::EXEC_SUCCESS');

$testCommand = new TestCommand3();
$testCommand->execute($reqStub, $response);
$t->is($response->getBody(), '<rsp>This works, too</rsp>', 'Loads the template <ReturnString>Success.*.php if doExecute() returns <ReturnString>');

$testCommand = new TestCommand4();
try
{
  $testCommand->execute($reqStub, $response);
  $t->fail('No code should be executed here!');
}
catch(Sonata_Exception_TemplateNotFound $ex)
{
  $t->pass('An exeception is thrown if no corresponding template could be found');
}

$testCommand = new TestCommand5();
$testCommand->execute($reqStub, $response);
$t->is($response->getBody(), 
'<?xml version="1.0" encoding="utf-8" ?>
<rsp stat="error">
  <code></code>
  <message></message>
</rsp>', 
'Loads the default error template if doExecute() returns Sonata_Command::EXEC_FAILURE');

// @Test: ->getVarHolder()

$t->is(get_class($testCommand->getVarHolder()), 'Sonata_ParameterHolder', 'The var holder has the right type');

// @Test: ->setVar()

$t->is($testCommand->getVarHolder()->get('foo'), '0815', 'Template variables are set correcty');

// @Test: ->getVar()

$t->is($testCommand->getVar('foo'), '0815', 'Template variables are retrieved correctly');

// @Test: ->__set()

$testCommand->foo = 42;
$testCommand->bar = 4711;
$t->same($testCommand->getVarHolder()->getAll(), array('foo' => 42, 'bar' => 4711), 'Template variables are set correctly');

// @Test: ->__get()

$foo = $testCommand->foo;
$t->is($foo, 42, 'Template variables are retrieved correctly');
$baz = $testCommand->baz;
$t->is($baz, null, 'NULL is returned for non-existing template variables');

// @Test: ->__isset()

$t->is(isset($testCommand->foo), true, 'TRUE is returned for existing template variables');
$t->is(isset($testCommand->baz), false, 'FALSE is returned for non-existing template variables');

// @Test: ->__unset()

unset($testCommand->bar);
$t->is(isset($testCommand->bar), false, 'The template variable was unset correctly');
