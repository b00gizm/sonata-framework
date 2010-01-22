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

$res = new Sonata_Response();

// @After

unset($res);

// @Test: ->getStatusCode()

$t->is($res->getStatusCode(), 200, 'Returns HTTP status code 200 per default');

// @Test: ->getStatusText()

$t->is($res->getStatusText(), 'OK', 'Returns HTTP status text \'OK\' per default');

// @Test: ->setStatusCode()

$res->setStatusCode(404);
$t->is($res->getStatusCode(), 404, 'Sets HTTP status code correctly');
$t->is($res->getStatusText(), 'Not Found', 'If no status text is given the default text is returned');

$res->setStatusCode(666, 'Some random shit');
$t->is($res->getStatusText(), 'Some random shit', 'If status text is given it will be returned correctly');

// @Test: ->setFormat()

$res->setFormat('json');
$t->is($res->getFormat(), 'json', 'The format was set correctly');
$t->is($res->getMimeType(), 'application/json', 'The mime type was set correctly, too');

$res->setFormat('foo');
$t->isnt($res->getFormat(), 'foo', 'The format is not set if no matching format/mime type can be found');

// @Test: ::registerMimeType()

Sonata_Response::registerMimeType('foo', 'application/foo');
$res->setFormat('foo');
$t->is($res->getFormat(), 'foo', 'After registering a new format/mime type, the format can be set to it');
$t->is($res->getMimeType(), 'application/foo', 'The mime type was set correctly, too');

Sonata_Response::registerMimeType('html', 'application/bar');
$res->setFormat('html');
$t->isnt($res->getMimeType(), 'application/bar', 'Existing mime type cannot be altered');

// @Test: ->addHeader()

$res->addHeader('Content-type', 'text/xml');
$t->is($res->getHeaders(), array('Content-type' => 'text/xml'), 'The header was added correctly to the headers array');
$res->addHeader('Content-encoding', 'utf-8');
$t->is($res->getHeaders(), array('Content-type' => 'text/xml', 'Content-encoding' => 'utf-8'), 'A second one, too');

// @Test: ->appendToBody()

$t->diag('->appendToBody()');
$res->appendToBody('Hello World.');
$t->is($res->getBody(), 'Hello World.', 'The body data was append correctly');
