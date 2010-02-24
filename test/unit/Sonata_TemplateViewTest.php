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

$templateView = new Sonata_TemplateView();

// @After

unset($templateView);

// @Test: ->assign() - with single arguments

$templateView->assign('foo', 42);
$templateView->assign('bar', 4711);

$t->is($templateView->getTemplateVars(), array('foo' => 42, 'bar' => 4711), 'Template vars are assigned correctly (single)');

// @Test: ->assign() - with array as argument

$templateView->assign(array('foo' => 42, 'bar' => 4711));

$t->is($templateView->getTemplateVars(), array('foo' => 42, 'bar' => 4711), 'Template vars are assigned correctly (array)');

// @Test: ->__get()

$templateView->assign('foo', 42);

$t->is($templateView->foo, 42, 'Template vars retrieved correctly via PHP\'s magic \'__get\' method');

// @Test: ->render() - non-existing templates

$templateView->setDir(dirname(__FILE__).'/../fixtures/templates');

try
{
  $templateView->render('foo', 'article', 'xml');
  $t->fail('No code should be executed after this');
}
catch (Sonata_Exception_Template $ex)
{
  $t->pass('Throws an exception for non-existing templates');
}

// @Test: ->render() - existing templates

$templateView->setDir(dirname(__FILE__).'/../fixtures/templates');

$expectedData = <<<EOF
<?xml version="1.0" encoding="utf-8" ?>
<rsp stat="ok">
  <article id="123">
    <title>Example</title>
    <body>My article example</body>
    <author>John Doe</author>
  </article>
</rsp>

EOF;

$t->is($templateView->render('list', 'article', 'xml'), $expectedData, 'The template is rendered correctly');

// @Test: ->render() - error templates

$templateView->setDir(dirname(__FILE__).'/../fixtures/templates');
$templateView->assign(array('code' => 123, 'message' => 'My error message example'));

$expectedData = <<<EOF
<?xml version="1.0" encoding="utf-8" ?>
<rsp stat="error">
  <code>123</code>
  <message>My error message example</message>
</rsp>

EOF;

$t->is($templateView->render('Error', NULL, 'xml'), $expectedData, 'The error template is rendered correctly');

