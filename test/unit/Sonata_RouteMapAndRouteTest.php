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

$requestStub = $t->stub('Sonata_Request');
$rm = new Sonata_RouteMap($requestStub);

// @After

unset($requestMock);
unset($rm);

// @Test: ->connect()

  // @Test: trying '/foo/bar'

  $rm->connect('/foo/bar', 'foo', array(), 'bar');
  $routes = $rm->getRoutes();
  $t->is(count($routes), 1, 'The route was connected (added) to the routes array');
  $route = array_pop($routes);
  $t->ok($route instanceof Sonata_Route, 'The added route is a PSRoute object');
  $t->is($route->getPattern(), '/^\/foo\/bar/', 'The pattern has the right form further regex operations');
  $t->is($route->getResource(), 'foo', 'The resource was set correctly');
  $t->is($route->getVerbs(), array(), 'There were no verbs to set');
  $t->is($route->getAction(), 'bar', 'The action was set correctly');
  $t->is($route->getParameters(), array(), 'There were no parameters to set');
  $t->is($route->getCommandName(), 'BarFoo', 'The camelized command name is generated correctly');
  
  // @Test: trying '/my_foo/my_bar'

  $rm->connect('/my_foo/my_bar', 'my_foo', array(), 'my_bar');
  $routes = $rm->getRoutes();
  $route = array_pop($routes);
  $t->ok($route instanceof Sonata_Route, 'The added route is a PSRoute object');
  $t->is($route->getPattern(), '/^\/my_foo\/my_bar/', 'The pattern has the right form further regex operations');
  $t->is($route->getResource(), 'my_foo', 'The resource was set correctly');
  $t->is($route->getVerbs(), array(), 'There were no verbs to set');
  $t->is($route->getAction(), 'my_bar', 'The action was set correctly');
  $t->is($route->getParameters(), array(), 'There were no parameters to set');
  $t->diag('NOTICE: Due to limitations of the lime2 testing framework, it\'s not possible to mock static methods.');
  $t->diag('Sonata_Route::getCommand() uses the static method Sanata_Route::camelize() internally, so make sure to run the Sonata_Utils test suite first.');
  $t->is($route->getCommandName(), 'MyBarMyFoo', 'The camelized command name is generated correctly, even for underscored resources and/or actions');
  
  // @Test: trying '/articles/:id.:format'

  $rm->connect('/articles/:id.:format', 'article', array('GET'), 'show');
  $routes = $rm->getRoutes();
  $route = array_pop($routes);
  $t->is($route->getPattern(), '/^\/articles\/([A-Za-z0-9-_]+)\.([A-Za-z0-9-_]+)/', 'The pattern has the right form further regex operations');
  $t->is($route->getResource(), 'article', 'The resource was set correctly');
  $t->is($route->getVerbs(), array('GET'), 'There verbs were set correctly');
  $t->is($route->getAction(), 'show', 'The action was set correctly');
  $t->is($route->getParameters(), array('id', 'format'), 'The parameters were set correctly');
  $t->is($route->getCommandName(), 'ShowArticle', 'The camelized command name is generated correctly');

// @Test: ->resources()

  // @Test: trying 'GET /albums.:format'

  $rm->resources('albums');
  $t->is(count($rm->getRoutes()), 5, 'All 5 routes were connected (added) to the routes array');

  $routes = $rm->getRoutes();
  $route = $routes[0];
  $t->is($route->getPattern(), '/^\/albums\.([A-Za-z0-9-_]+)/', 'The pattern has the right form further regex operations');
  $t->is($route->getResource(), 'album', 'The resource was set correctly');
  $t->is($route->getVerbs(), array('GET'), 'There verbs were set correctly');
  $t->is($route->getAction(), 'list', 'The action was set correctly');
  $t->is($route->getParameters(), array('format'), 'The parameters were set correctly');
  $t->is($route->getCommandName(), 'ListAlbum', 'The camelized command name is generated correctly');
  
  // @Test: trying 'PUT /albums.:format'

  $rm->resources('albums');

  $routes = $rm->getRoutes();
  $route = $routes[1];
  $t->is($route->getPattern(), '/^\/albums\.([A-Za-z0-9-_]+)/', 'The pattern has the right form further regex operations');
  $t->is($route->getResource(), 'album', 'The resource was set correctly');
  $t->is($route->getVerbs(), array('POST'), 'There verbs were set correctly');
  $t->is($route->getAction(), 'create', 'The action was set correctly');
  $t->is($route->getParameters(), array('format'), 'The parameters were set correctly');
  $t->is($route->getCommandName(), 'CreateAlbum', 'The camelized command name is generated correctly');
  
  // @Test: trying 'GET /albums/:id.:format'

  $rm->resources('albums');

  $routes = $rm->getRoutes();
  $route = $routes[2];
  $t->is($route->getPattern(), '/^\/albums\/([A-Za-z0-9-_]+)\.([A-Za-z0-9-_]+)/', 'The pattern has the right form further regex operations');
  $t->is($route->getResource(), 'album', 'The resource was set correctly');
  $t->is($route->getVerbs(), array('GET'), 'There verbs were set correctly');
  $t->is($route->getAction(), 'show', 'The action was set correctly');
  $t->is($route->getParameters(), array('id', 'format'), 'The parameters were set correctly');
  $t->is($route->getCommandName(), 'ShowAlbum', 'The camelized command name is generated correctly');
  
  // @Test: trying 'POST /albums/:id.:format'

  $rm->resources('albums');

  $routes = $rm->getRoutes();
  $route = $routes[3];
  $t->is($route->getPattern(), '/^\/albums\/([A-Za-z0-9-_]+)\.([A-Za-z0-9-_]+)/', 'The pattern has the right form further regex operations');
  $t->is($route->getResource(), 'album', 'The resource was set correctly');
  $t->is($route->getVerbs(), array('PUT'), 'There verbs were set correctly');
  $t->is($route->getAction(), 'update', 'The action was set correctly');
  $t->is($route->getParameters(), array('id', 'format'), 'The parameters were set correctly');
  $t->is($route->getCommandName(), 'UpdateAlbum', 'The camelized command name is generated correctly');
  
  // @Test: trying 'DELETE /albums/:id.:format'

  $rm->resources('albums');

  $routes = $rm->getRoutes();
  $route = $routes[4];
  $t->is($route->getPattern(), '/^\/albums\/([A-Za-z0-9-_]+)\.([A-Za-z0-9-_]+)/', 'The pattern has the right form further regex operations');
  $t->is($route->getResource(), 'album', 'The resource was set correctly');
  $t->is($route->getVerbs(), array('DELETE'), 'There verbs were set correctly');
  $t->is($route->getAction(), 'destroy', 'The action was set correctly');
  $t->is($route->getParameters(), array('id', 'format'), 'The parameters were set correctly');
  $t->is($route->getCommandName(), 'DestroyAlbum', 'The camelized command name is generated correctly');

// @Test: ->nestedResources()

  // @Test: trying 'GET /albums/:album_id/images.:format'

  $rm->nestedResources('images', 'albums');
  $t->is(count($rm->getRoutes()), 5, 'All 5 routes were connected (added) to the routes array');

  $routes = $rm->getRoutes();
  $route = $routes[0];
  $t->is($route->getPattern(), '/^\/albums\/([A-Za-z0-9-_]+)\/images\.([A-Za-z0-9-_]+)/', 'The pattern has the right form further regex operations');
  $t->is($route->getResource(), 'image', 'The resource was set correctly');
  $t->is($route->getVerbs(), array('GET'), 'There verbs were set correctly');
  $t->is($route->getAction(), 'list', 'The action was set correctly');
  $t->is($route->getParameters(), array('album_id', 'format'), 'The parameters were set correctly');
  $t->is($route->getCommandName(), 'ListImage', 'The camelized command name is generated correctly');
  
  // @Test: trying 'POST /albums/:album_id/images.:format'

  $rm->nestedResources('images', 'albums');
  
  $routes = $rm->getRoutes();
  $route = $routes[1];
  $t->is($route->getPattern(), '/^\/albums\/([A-Za-z0-9-_]+)\/images\.([A-Za-z0-9-_]+)/', 'The pattern has the right form further regex operations');
  $t->is($route->getResource(), 'image', 'The resource was set correctly');
  $t->is($route->getVerbs(), array('POST'), 'There verbs were set correctly');
  $t->is($route->getAction(), 'create', 'The action was set correctly');
  $t->is($route->getParameters(), array('album_id', 'format'), 'The parameters were set correctly');
  $t->is($route->getCommandName(), 'CreateImage', 'The camelized command name is generated correctly');
  
  // @Test: trying 'GET /albums/:album_id/images/:id.:format'
  
  $rm->nestedResources('images', 'albums');

  $routes = $rm->getRoutes();
  $route = $routes[2];
  $t->is($route->getPattern(), '/^\/albums\/([A-Za-z0-9-_]+)\/images\/([A-Za-z0-9-_]+)\.([A-Za-z0-9-_]+)/', 'The pattern has the right form further regex operations');
  $t->is($route->getResource(), 'image', 'The resource was set correctly');
  $t->is($route->getVerbs(), array('GET'), 'There verbs were set correctly');
  $t->is($route->getAction(), 'show', 'The action was set correctly');
  $t->is($route->getParameters(), array('album_id', 'id', 'format'), 'The parameters were set correctly');
  $t->is($route->getCommandName(), 'ShowImage', 'The camelized command name is generated correctly');
  
  // @Test: trying 'PUT /albums/:album_id/images/:id.:format
  
  $rm->nestedResources('images', 'albums');

  $routes = $rm->getRoutes();
  $route = $routes[3];
  $t->is($route->getPattern(), '/^\/albums\/([A-Za-z0-9-_]+)\/images\/([A-Za-z0-9-_]+)\.([A-Za-z0-9-_]+)/', 'The pattern has the right form further regex operations');
  $t->is($route->getResource(), 'image', 'The resource was set correctly');
  $t->is($route->getVerbs(), array('PUT'), 'There verbs were set correctly');
  $t->is($route->getAction(), 'update', 'The action was set correctly');
  $t->is($route->getParameters(), array('album_id', 'id', 'format'), 'The parameters were set correctly');
  $t->is($route->getCommandName(), 'UpdateImage', 'The camelized command name is generated correctly');
  
  // @Test: trying 'DELETE /albums/:album_id/images/:id.:format
  
  $rm->nestedResources('images', 'albums');

  $routes = $rm->getRoutes();
  $route = $routes[4];
  $t->is($route->getPattern(), '/^\/albums\/([A-Za-z0-9-_]+)\/images\/([A-Za-z0-9-_]+)\.([A-Za-z0-9-_]+)/', 'The pattern has the right form further regex operations');
  $t->is($route->getResource(), 'image', 'The resource was set correctly');
  $t->is($route->getVerbs(), array('DELETE'), 'There verbs were set correctly');
  $t->is($route->getAction(), 'destroy', 'The action was set correctly');
  $t->is($route->getParameters(), array('album_id', 'id', 'format'), 'The parameters were set correctly');
  $t->is($route->getCommandName(), 'DestroyImage', 'The camelized command name is generated correctly');

// @Test: ->resolveRouteString()

  // @Test: trying a GET request

  // fixtures
  $rm->connect('/foo/bar', 'foo', array(), 'bar');
  $rm->connect('/articles/:id.:format', 'article', array('GET'), 'show');
  $rm->resources('albums');
  $rm->nestedResources('images', 'albums');

  $requestStub->getMethod()->returns('GET');
  $requestStub->replay();

  // test
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
  
  $requestStub->verify();
  
  // @Test: trying a POST request
  
  // fixtures
  $rm->connect('/foo/bar', 'foo', array(), 'bar');
  $rm->connect('/articles/:id.:format', 'article', array('GET'), 'show');
  $rm->resources('albums');
  $rm->nestedResources('images', 'albums');

  $requestStub->getMethod()->returns('PUT');
  $requestStub->replay();

  $t->is($rm->resolveRouteString('albums/123/images/4711.xml'), true, '\'albums/123/images/4711.xml\' is resolved correctly');
  $t->is($rm->resolveRouteString('albums/123/images.xml'), false, '\'albums/123/images.xml\' cannot be resolved');
  
  $requestStub->verify();

// @Test: ->load() - Load from config

  $configPath = '/path/to/config.yml';

  $configParserStub = $t->stub('Sonata_Parser_Config');
  $configParserStub->parse($configPath)->returns(array(
    'route_map' => array(
      'resources_example' => array(
        'resources'  => 'articles',
      ),
    
      'nested_resources_example' => array(
        'resources'  => array('comments', 'articles'),
      ),
    
      'connect_example' => array(
        'connect' => array(
          'pattern'  => '/articles/recent.:format',
          'resource' => 'article',
          'verbs'    => array('GET'),
          'action'   => 'listRecent',
        )
      )
    )
  ));
  $configParserStub->replay();

  $rm->load($configPath, $configParserStub);
  $t->is(count($rm->getRoutes()), 11, 'All 11 routes were connected (added) to the routes array');
