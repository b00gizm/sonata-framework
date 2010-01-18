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

$parser = new Sonata_Parser_Config(new Sonata_Parser_Driver_Yaml());
Sonata_Config::load(dirname(__FILE__).'/../fixtures/config/sonata.yml', $parser);

$request = new Sonata_Request();
$rm = new Sonata_RouteMap($request);

// @After

unset($parser);
unset($request);
unset($rm);

// @Test: ->connect()

$t->diag('...trying \'/foo/bar\'');
$rm->connect('/foo/bar', 'foo', array(), 'bar');
$routes = $rm->getRoutes();
$t->is(count($routes), 1, 'The route was connected (added) to the routes array');
$route1 = array_pop($routes);
$t->ok($route1 instanceof Sonata_Route, 'The added route is a PSRoute object');
$t->is($route1->getPattern(), '/^\/foo\/bar/', 'The pattern has the right form further regex operations');
$t->is($route1->getResource(), 'foo', 'The resource was set correctly');
$t->is($route1->getVerbs(), array(), 'There were no verbs to set');
$t->is($route1->getAction(), 'bar', 'The action was set correctly');
$t->is($route1->getParameters(), array(), 'There were no parameters to set');

$t->diag('...trying \'/articles/:id.:format\'');
$rm->connect('/articles/:id.:format', 'article', array('GET'), 'show');
$routes = $rm->getRoutes();
$route2 = array_pop($routes);
$t->is($route2->getPattern(), '/^\/articles\/([A-Za-z0-9-_]+)\.([A-Za-z0-9-_]+)/', 'The pattern has the right form further regex operations');
$t->is($route2->getResource(), 'article', 'The resource was set correctly');
$t->is($route2->getVerbs(), array('GET'), 'There verbs were set correctly');
$t->is($route2->getAction(), 'show', 'The action was set correctly');
$t->is($route2->getParameters(), array('id', 'format'), 'The parameters were set correctly');

// @Test: ->resources()

$rm->resources('albums');
$t->is(count($rm->getRoutes()), 5, 'All 5 routes were connected (added) to the routes array');

$t->diag('...trying first RESTful route GET \'/albums.:format\'');
$resRoutes = $rm->getRoutes();
$route3 = $resRoutes[0];
$t->is($route3->getPattern(), '/^\/albums\.([A-Za-z0-9-_]+)/', 'The pattern has the right form further regex operations');
$t->is($route3->getResource(), 'album', 'The resource was set correctly');
$t->is($route3->getVerbs(), array('GET'), 'There verbs were set correctly');
$t->is($route3->getAction(), 'list', 'The action was set correctly');
$t->is($route3->getParameters(), array('format'), 'The parameters were set correctly');

$t->diag('...trying second RESTful route PUT \'/albums.:format\'');
$route3 = $resRoutes[1];
$t->is($route3->getPattern(), '/^\/albums\.([A-Za-z0-9-_]+)/', 'The pattern has the right form further regex operations');
$t->is($route3->getResource(), 'album', 'The resource was set correctly');
$t->is($route3->getVerbs(), array('POST'), 'There verbs were set correctly');
$t->is($route3->getAction(), 'create', 'The action was set correctly');
$t->is($route3->getParameters(), array('format'), 'The parameters were set correctly');

$t->diag('...trying third RESTful route GET \'/albums/:id.:format\'');
$route3 = $resRoutes[2];
$t->is($route3->getPattern(), '/^\/albums\/([A-Za-z0-9-_]+)\.([A-Za-z0-9-_]+)/', 'The pattern has the right form further regex operations');
$t->is($route3->getResource(), 'album', 'The resource was set correctly');
$t->is($route3->getVerbs(), array('GET'), 'There verbs were set correctly');
$t->is($route3->getAction(), 'show', 'The action was set correctly');
$t->is($route3->getParameters(), array('id', 'format'), 'The parameters were set correctly');

$t->diag('...trying fourth RESTful route POST \'/albums/:id.:format\'');
$route3 = $resRoutes[3];
$t->is($route3->getPattern(), '/^\/albums\/([A-Za-z0-9-_]+)\.([A-Za-z0-9-_]+)/', 'The pattern has the right form further regex operations');
$t->is($route3->getResource(), 'album', 'The resource was set correctly');
$t->is($route3->getVerbs(), array('PUT'), 'There verbs were set correctly');
$t->is($route3->getAction(), 'update', 'The action was set correctly');
$t->is($route3->getParameters(), array('id', 'format'), 'The parameters were set correctly');

$t->diag('...trying fifth RESTful route DELETE \'/albums/:id.:format\'');
$route3 = $resRoutes[4];
$t->is($route3->getPattern(), '/^\/albums\/([A-Za-z0-9-_]+)\.([A-Za-z0-9-_]+)/', 'The pattern has the right form further regex operations');
$t->is($route3->getResource(), 'album', 'The resource was set correctly');
$t->is($route3->getVerbs(), array('DELETE'), 'There verbs were set correctly');
$t->is($route3->getAction(), 'destroy', 'The action was set correctly');
$t->is($route3->getParameters(), array('id', 'format'), 'The parameters were set correctly');

// @Test: ->nestedResources()

$rm->nestedResources('images', 'albums');
$t->is(count($rm->getRoutes()), 5, 'All 5 routes were connected (added) to the routes array');

$t->diag('...trying first RESTful route GET \'/albums/:album_id/images.:format\'');
$resRoutes = $rm->getRoutes();
$route4 = $resRoutes[0];
$t->is($route4->getPattern(), '/^\/albums\/([A-Za-z0-9-_]+)\/images\.([A-Za-z0-9-_]+)/', 'The pattern has the right form further regex operations');
$t->is($route4->getResource(), 'image', 'The resource was set correctly');
$t->is($route4->getVerbs(), array('GET'), 'There verbs were set correctly');
$t->is($route4->getAction(), 'list', 'The action was set correctly');
$t->is($route4->getParameters(), array('album_id', 'format'), 'The parameters were set correctly');

$t->diag('...trying second RESTful route POST \'/albums/:album_id/images.:format\'');
$route4 = $resRoutes[1];
$t->is($route4->getPattern(), '/^\/albums\/([A-Za-z0-9-_]+)\/images\.([A-Za-z0-9-_]+)/', 'The pattern has the right form further regex operations');
$t->is($route4->getResource(), 'image', 'The resource was set correctly');
$t->is($route4->getVerbs(), array('POST'), 'There verbs were set correctly');
$t->is($route4->getAction(), 'create', 'The action was set correctly');
$t->is($route4->getParameters(), array('album_id', 'format'), 'The parameters were set correctly');

$t->diag('...trying third RESTful route GET \'/albums/:album_id/images/:id.:format\'');
$route4 = $resRoutes[2];
$t->is($route4->getPattern(), '/^\/albums\/([A-Za-z0-9-_]+)\/images\/([A-Za-z0-9-_]+)\.([A-Za-z0-9-_]+)/', 'The pattern has the right form further regex operations');
$t->is($route4->getResource(), 'image', 'The resource was set correctly');
$t->is($route4->getVerbs(), array('GET'), 'There verbs were set correctly');
$t->is($route4->getAction(), 'show', 'The action was set correctly');
$t->is($route4->getParameters(), array('album_id', 'id', 'format'), 'The parameters were set correctly');

$t->diag('...trying fourth RESTful route PUT \'/albums/:album_id/images/:id.:format\'');
$route4 = $resRoutes[3];
$t->is($route4->getPattern(), '/^\/albums\/([A-Za-z0-9-_]+)\/images\/([A-Za-z0-9-_]+)\.([A-Za-z0-9-_]+)/', 'The pattern has the right form further regex operations');
$t->is($route4->getResource(), 'image', 'The resource was set correctly');
$t->is($route4->getVerbs(), array('PUT'), 'There verbs were set correctly');
$t->is($route4->getAction(), 'update', 'The action was set correctly');
$t->is($route4->getParameters(), array('album_id', 'id', 'format'), 'The parameters were set correctly');

$t->diag('...trying fourth RESTful route DELETE \'/albums/:album_id/images/:id.:format\'');
$route4 = $resRoutes[4];
$t->is($route4->getPattern(), '/^\/albums\/([A-Za-z0-9-_]+)\/images\/([A-Za-z0-9-_]+)\.([A-Za-z0-9-_]+)/', 'The pattern has the right form further regex operations');
$t->is($route4->getResource(), 'image', 'The resource was set correctly');
$t->is($route4->getVerbs(), array('DELETE'), 'There verbs were set correctly');
$t->is($route4->getAction(), 'destroy', 'The action was set correctly');
$t->is($route4->getParameters(), array('album_id', 'id', 'format'), 'The parameters were set correctly');

// @Test: ->resolveRouteString()

$rm->connect('/foo/bar', 'foo', array(), 'bar');
$rm->connect('/articles/:id.:format', 'article', array('GET'), 'show');
$rm->resources('albums');
$rm->nestedResources('images', 'albums');

// Create request stub to simulate HTTP GET request
$request = $t->stub('Sonata_Request');
$request->getMethod()->returns('GET');
$request->replay();
$rm->setRequest($request);

$t->diag('...trying a GET request');
$t->is($rm->resolveRouteString(null), false, 'Returns false for an empty / NULL route string');
$t->is($rm->resolveRouteString('i/can/haz/cheezburgers.lol'), false, '\'i/can/haz/cheezburgers.lol\' cannot be resolved');
$t->is($rm->resolveRouteString('foo/bar'), true, '\'foo/bar\' is resolved correctly');
$t->is($rm->resolveRouteString('articles/123.xml'), true, '\'articles/123.xml\' is resolved correctly');
$t->is($rm->resolveRouteString('albums.xml'), true, '\'albums.xml\' is resolved correctly');
$t->is($rm->resolveRouteString('albums/456.xml'), true, '\'albums/456.xml\' is resolved correctly');
$t->is($rm->resolveRouteString('albums/456_xml'), false, '\'albums/456_xml\' cannot be resolved');
$t->is($rm->resolveRouteString('albums/456/not_existing.xml'), false, '\'albums/456/not_existing.xml\' cannot be resolved');
$t->is($rm->resolveRouteString('albums/123/images/4711.xml'), true, '\'albums/123/image/4711.xml\' is resolved correctly');
$t->is($rm->resolveRouteString('albums/123/images/pedobear-at-little-girls-birthday.xml'), true, '\'albums/123/images/pedobear-at-little-girls-birthday.xml\' is resolved correctly');

// Create request stub to simulate HTTP PUT request
$request = $t->stub('Sonata_Request');
$request->getMethod()->returns('PUT');
$request->replay();
$rm->setRequest($request);

$t->diag('...trying a PUT request');
$t->is($rm->resolveRouteString('albums/123/images/4711.xml'), true, '\'albums/123/images/4711.xml\' is resolved correctly');
$t->is($rm->resolveRouteString('albums/123/images.xml'), false, '\'albums/123/images.xml\' cannot be resolved');

// Use of config file

$t->diag('Mapping routes from a config file');

$rm = new Sonata_RouteMap($request, array('use_config' => true));
$t->is(count($rm->getRoutes()), 11, 'All routes from the config file were mapped correctly');