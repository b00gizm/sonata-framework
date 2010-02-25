<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 */

require_once dirname(__FILE__).'/bootstrap.php';

$t = new LimeTest();

// @Test: ::camelize()

  $t->is(Sonata_Utils::camelize('foo_bar_baz'), 'FooBarBaz', 'The string was camelized correctly');
  $t->is(Sonata_Utils::camelize('foo_bar_baz', true), 'fooBarBaz', 'The string was camelized correctly, starting lower-case');
  $t->is(Sonata_Utils::camelize('foo _ bar _baz'), 'FooBarBaz', 'Whitespaces are ignored');
  $t->is(Sonata_Utils::camelize(''), '', 'Empty strings are retuned untouched');

// @Test: ::underscore()

  $t->is(Sonata_Utils::underscore('FooBar'), 'foo_bar', 'The string was underscored correctly');
  $t->is(Sonata_Utils::underscore('myFooBar'), 'my_foo_bar', 'It even works for camelized strings that start lower-case');
  $t->is(Sonata_Utils::underscore('Foo Bar-!/#Baz'), 'foo_bar_baz', 'Whitespaces and non-digit characters are ignored');
  $t->is(Sonata_Utils::underscore(''), '', 'Empty strings are retuned untouched');

// @Test: ::slugify()

  $t->is(Sonata_Utils::slugify('Here be dragons!'), 'here-be-dragons', 'The string was slugified correctly');
  $t->is(Sonata_Utils::slugify('Hérè be drägons!'), 'here-be-dragons', 'Transforms UTF-8 characters correctly');
  $t->is(Sonata_Utils::slugify(''), 'n-a', '\'n-a\' is returned for empty strings');
