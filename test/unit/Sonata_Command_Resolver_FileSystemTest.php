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

$fh = fopen(dirname(__FILE__).'/../fixtures/commands/PassCommand.class.php', 'wb');
fputs($fh, '<?php class PassCommand extends Sonata_Command { public function doExecute(Sonata_Request $request, Sonata_Response $response) {} } ?>');
fclose($fh);

$fh = fopen(dirname(__FILE__).'/../fixtures/commands/FailCommand.class.php', 'wb');
fputs($fh, '<?php class FailCommand { public function doExecute(Sonata_Request $request, Sonata_Response $response) {} } ?>');
fclose($fh);

$fh = fopen(dirname(__FILE__).'/../fixtures/commands/AnotherFail.class.php', 'wb');
fputs($fh, '<?php class AnotherFailCommand extends Sonata_Command { public function doExecute(Sonata_Request $request, Sonata_Response $response) {} } ?>');
fclose($fh);

// @Before:

$request = new Sonata_Request();
$resolver = new Sonata_Command_Resolver_FileSystem();

// @AfterAll

unlink(dirname(__FILE__).'/../fixtures/commands/PassCommand.class.php');
unlink(dirname(__FILE__).'/../fixtures/commands/FailCommand.class.php');
unlink(dirname(__FILE__).'/../fixtures/commands/AnotherFail.class.php');

// @After

unset($request);
unset($resolver);

// @Test: ->getCommand()

$request->addParameter('cmd', 'Pass');

try
{
  $resolver->getCommand($request);
  $t->fail();
}
catch (Sonata_Exception_ConfigNotFound $ex)
{
  $t->pass('An exception is thrown Sonata_Command_Resolver could not determine the path for the commands directory');
}

$parser = new Sonata_Parser_Config(new Sonata_Parser_Driver_Yaml());
Sonata_Config::load(dirname(__FILE__).'/../fixtures/config/sonata.yml', $parser);

$t->isnt($resolver->getCommand($request), null, 'The result for a correct command class is not NULL');
$t->is(get_class($resolver->getCommand($request)), 'PassCommand', 'The result is an instance of PassCommand');

$request->addParameter('cmd', 'Fail');
$t->is($resolver->getCommand($request), null, 'NULL is returned for a class that doesn\'t extend Sonata_Command');

$request->addParameter('cmd', 'AnotherFail');
$t->is($resolver->getCommand($request), null, 'NULL is returned for a class which filename doesn\'t end with *Command');
