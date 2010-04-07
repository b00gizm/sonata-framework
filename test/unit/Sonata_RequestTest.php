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

  $_REQUEST = array(
    'foo' => 123,
    'bar' => array('quux', 'quuux'),
  );

  $req = new Sonata_Request();

// @After

  unset($req);

// @Test: ->hasParameter()

  $t->is($req->hasParameter('foo'), true, 'Returns true if a parameter exists');
  $t->is($req->hasParameter('baz'), false, 'Returns false if a parameter does not exist');

// @Test: ->getParameter()

  $t->is($req->getParameter('foo'), 123, 'Returns the correct value for an existing parameter');
  $t->is($req->getParameter('bar'), array('quux', 'quuux'), 'This even works for arrays');
  $t->is($req->getParameter('baz'), null, 'Returns NULL for non-existing parameters if no fallback given');
  $t->is($req->getParameter('baz', 'fail'), 'fail', 'Returns the correct fallback for non-existing parameters');

// @Test: ->addParameter()

  $req->addParameter('fido', 456);
  $t->is($req->getParameter('fido'), 456, 'Additional parameters are added correctly');

// @Test: ->getMethod()

  $_SERVER['REQUEST_METHOD'] = 'GET';
  $t->is($req->getMethod(), 'GET', 'Returns the correct HTTP method');

// @Test: ->isMethod()

  $_SERVER['REQUEST_METHOD'] = 'GET';
  $t->is($req->isMethod('GET'), true, 'Returns true for the correctly requested HTTP method');

// @Test: ->getHttpHeader()

  $_SERVER['HTTP_USER_AGENT'] = 'MyTestUserAgent/1.0';
  $t->is($req->getHttpHeader('User-Agent'), 'MyTestUserAgent/1.0', 'The user agent header field\'s value was retrieved correctly');
  
  $_SERVER['HTTP_ACCEPT_CHARSET'] = 'utf-8';
  $t->is($req->getHttpHeader('Accept-Charset'), 'utf-8', 'The accepted charset header field\'s value was retrieved correctly');
