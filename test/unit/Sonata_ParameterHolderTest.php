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

  class Foo 
  {
    private $foo = null;
  
    public function __construct($foo = null)
    {
      if (is_null($foo))
      {
        $foo = 'Here be dragons';
      }
    
      $this->foo = $foo;
    }
  
    public function set($foo)
    {
      $this->foo = $foo;
    }
  
    public function get()
    {
      return $this->foo;
    }
  }

// @Before

  $paramHolder = new Sonata_ParameterHolder();
  $paramHolder->add($params = array(
    'foo' => 42,
    'bar' => 4711,
    'baz' => new Foo(),
  ));

// @After

  unset($paramHolder);
  unset($params);

// @Test: ->add()

  $t->is($paramHolder->getAll(), $params, 'All parameters were added correctly');
  $paramHolder->add(null);
  $t->isnt($paramHolder->getAll(), null, 'Nothing is added if NULL was given');
  $paramHolder->add(array());
  $t->isntSame($paramHolder->getAll(), array(), 'Nothing is added if an empty array was given');
  $paramHolder->add('this fails');
  $t->isnt($paramHolder->getAll(), 'this fails', 'Nothing is added if parameter isn\'t an array');

// @Test: ->get()

  $foo = $paramHolder->get('foo');
  $t->is($foo, 42, 'The value is retrieved correctly');
  $rex = $paramHolder->get('rex');
  $t->is($rex, null, 'NULL is returned if the parameter could not be found');
  $rex = $paramHolder->get('rex', 'fido');
  $t->is($rex, 'fido', 'If a default value is given for a non-existing parameter, the default value is returned');

// @Test: ->getNames()

  $t->same($paramHolder->getNames(), array('foo', 'bar', 'baz'), 'All parameter names are retrieved correctly');

// @Test: ->has()

  $t->is($paramHolder->has('foo'), true, 'TRUE is returned if the parameter exists');
  $t->is($paramHolder->has('rex'), false, 'FALSE is returned if the parameter does not exist');

// @Test: ->remove()

  $ret = $paramHolder->remove('bar');
  $t->is($paramHolder->has('bar'), false, 'The parameter was removed correctly');
  $t->is($ret, 4711, 'When removing a paramter, its value is returned correctly');
  $ret = $paramHolder->remove('rex');
  $t->is($ret, null, 'NULL is returned for non-existing parameters');
  $ret = $paramHolder->remove('rex', 'fido');
  $t->is($ret, 'fido', 'If a default value is given for a non-existing parameter, the default value is returned');

// @Test: ->set()

  $paramHolder->set('rex', 'fido');
  $t->is($paramHolder->get('rex'), 'fido', 'New parameters are set correctly');

// @Test: ->setByRef()

  $rex = 'fido';
  $paramHolder->setByRef('rex', $rex);
  $rex = 'odif';
  $t->is($paramHolder->get('rex'), 'odif', 'Parameters can also be set by reference');

// @Test: ->addByRef()

  $paramHolder->addByRef($params);
  $params['foo'] = 17;
  $t->is($paramHolder->get('foo'), 17, 'Parameters can also be added as array by their values references');
