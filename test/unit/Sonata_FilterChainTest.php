<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 */

require_once dirname(__FILE__).'/bootstrap.php';

$t = new LimeTest();

class FooFilter extends Sonata_Filter
{
  public function execute(Sonata_Request $request, Sonata_Response $response)
  {
  }
}

class BarFilter extends Sonata_Filter
{
  public function execute(Sonata_Request $request, Sonata_Response $response)
  {
  }
}

class BazFilter extends Sonata_Filter
{
  public function execute(Sonata_Request $request, Sonata_Response $response)
  {
  }
}

// @Before

$fooFilter = new FooFilter();
$barFilter = new BarFilter();
$bazFilter = new BazFilter();

// @After

unset($fooFilter);
unset($barFilter);
unset($bazFilter);

// @Test: ->getFilters()

$fc = new Sonata_FilterChain();
$t->ok(is_array($fc->getFilters()) && count($fc->getFilters()) == 0, 'The method returns the empty filters array');

// @Test: ->addFilter()

$fc = new Sonata_FilterChain();
$fc->addFilter($fooFilter);
$fc->addFilter($barFilter);
$fc->addFilter($bazFilter);

$t->is($fc->getFilters(), array($fooFilter, $barFilter, $bazFilter), 'All filters were added correctly');

// @Test: ->processFilters()

$request = new Sonata_Request();
$response = new Sonata_Response();

$fooFilter = $t->mock('FooFilter');
$fooFilter->execute($request, $response)->once();
$fooFilter->replay();

$barFilter = $t->mock('BarFilter');
$barFilter->execute($request, $response)->once();
$barFilter->replay();

$bazFilter = $t->mock('BazFilter');
$bazFilter->execute($request, $response)->once();
$bazFilter->replay();

$fc = new Sonata_FilterChain();
$fc->addFilter($fooFilter);
$fc->addFilter($barFilter);
$fc->addFilter($bazFilter);

$fc->processFilters($request, $response);
$fooFilter->verify();

// @Test: Countable Interface

$fc = new Sonata_FilterChain();
$fc->addFilter($fooFilter);
$fc->addFilter($barFilter);
$fc->addFilter($bazFilter);

$t->is(count($fc), 3, 'You can use count() on a filter chain instance');

// @Test: ArrayAccess Interface

$fc = new Sonata_FilterChain();
$fc[] = $fooFilter;
$fc[] = $barFilter;
$fc[0] = $bazFilter;

$t->is($fc->getFilters(), array($bazFilter, $barFilter), 'You can use array operations on a filter chain instance');

unset($fc[1]);

$t->is($fc->getFilters(), array($bazFilter), 'You can unset/remove single filter objects from filter chain in an array-like way');
$t->ok(!isset($fc[1]), 'You can check for the existance of single filter objects in an array-like way');
