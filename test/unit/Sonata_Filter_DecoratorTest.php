<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 */

require_once dirname(__FILE__).'/bootstrap.php';

$t = new LimeTest();

class TestFilter extends Sonata_Filter
{
  public function execute(Sonata_Request $request, Sonata_Response $response)
  {
  }
}

class TestFilterDecorator extends Sonata_Filter_Decorator
{
  public function doExecute(Sonata_Request $request, Sonata_Response $response)
  {
  }
}

// @Before

$request = new Sonata_Request();
$response = new Sonata_Response();

// @After

unset($request);
unset($response);

// @Test: ->execute()

$filter = $t->mock('TestFilter');
$filter->execute($request, $response)->once();
$filter->replay();

$filterDecorator = new TestFilterDecorator($filter);
$filterDecorator->execute($request, $response);

$filter->verify();
