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

class MyParserDriver extends Sonata_Parser_Driver
{ 
  public function doParse($filename) 
  {
  }
}

class MyOtherParserDriver extends Sonata_Parser_Driver
{ 
  public function doParse($filename) 
  {
  }
}

// @Before

$configFile = dirname(__FILE__).'/../fixtures/config/some_random_config.yml';
$fh = fopen($configFile, 'wb');
fclose($fh);

$cp = new Sonata_Parser_Config(new MyParserDriver());


// @After

unlink($configFile);
unset($cp);

// @Test: ->getDriver()

$t->is($cp->getDriver() instanceof MyParserDriver, true, 'The driver was returned correctly');

// @Test: ->setDriver()

$cp->setDriver(new MyOtherParserDriver());
$t->is($cp->getDriver() instanceof MyOtherParserDriver, true, 'The new driver was set correctly');

// @Test: ->parse()

$t->diag('->parse()');
try
{
  $cp->parse('some_non_existing_file.yml');
  $t->fail('No code should be executed after this');
}
catch(RuntimeException $ex)
{
  $t->pass('An exception is thrown if the config file cannot be found');
}

$driverStub = $t->stub('MyParserDriver');
$driverStub->doParse($configFile)->throws('InvalidArgumentException');
$driverStub->replay();

$cp->setDriver($driverStub);
try
{
  $cp->parse($configFile);
  $t->fail('No code should be executed after this');
}
catch (InvalidArgumentException $ex)
{
  $t->pass('An exception thrown by the driver will also be thrown by the config parser');
}

$driverStub = $t->stub('MyParserDriver');
$driverStub->doParse($configFile)->returns(array("foo" => array("bar" => 42, "baz" => 4711)));
$driverStub->replay();

$cp->setDriver($driverStub);
$t->is($cp->parse($configFile), array("foo" => array("bar" => 42, "baz" => 4711)), 'Returns an array with all parsed values');
