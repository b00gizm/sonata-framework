<?php

/**
 * This file is part of the Sonata RESTful PHP framework
 * (c) 2009-2010 Pascal Cremer <b00giZm@gmail.com>
 *
 * @author Pascal Cremer <b00giZm@gmail.com>
 */

require_once dirname(__FILE__).'/bootstrap.php';

$t = new LimeTest(7);

// @BeforeAll

  $fh = fopen(dirname(__FILE__).'/../../src/Foo.class.php', 'wb');
  fputs($fh, '<?php class Foo {} ?>');
  fclose($fh);

  $fh = fopen(dirname(__FILE__).'/../../src/Pass.class.php', 'wb');
  fputs($fh, '<?php class Sonata_Pass {} ?>');
  fclose($fh);

  mkdir(dirname(__FILE__).'/../../src/Pass');
  $fh = fopen(dirname(__FILE__).'/../../src/Pass/Nested.class.php', 'wb');
  fputs($fh, '<?php class Sonata_Pass_Nested {} ?>');
  fclose($fh);

  $fh = fopen(dirname(__FILE__).'/../../src/Fail.php', 'wb');
  fputs($fh, '<?php class Sonata_Fail {} ?>');
  fclose($fh);

  $fh = fopen(dirname(__FILE__).'/../fixtures/Extra.class.php', 'wb');
  fputs($fh, '<?php class Extra {} ?>');
  fclose($fh);
  
// @Before

  $autoloader = new Sonata_Autoloader();

// @AfterAll

  unlink(dirname(__FILE__).'/../../src/Foo.class.php');
  unlink(dirname(__FILE__).'/../../src/Pass.class.php');
  unlink(dirname(__FILE__).'/../../src/Pass/Nested.class.php');
  rmdir(dirname(__FILE__).'/../../src/Pass');
  unlink(dirname(__FILE__).'/../../src/Fail.php');
  unlink(dirname(__FILE__).'/../fixtures/Extra.class.php');

// @Test: ->autoload() loads class files by class name

  $t->is($autoloader->autoload('Sonata_Pass'), true, 'Returns true if a class can be loaded');
  $t->is($autoloader->autoload('Sonata_Pass_Nested'), true, 'Still works for more nested classes');
  $t->is($autoloader->autoload('Sonata_Fail'), false, 'Returns false if the class filename does not end with \'.class.php\'');
  $t->is($autoloader->autoload('Foo'), false, 'Returns false if the class name does not start with \'Sonata\'');
  $t->is($autoloader->autoload('Sonata_Not_Here'), false, 'Returns false for non-existing classes');

// @Test: ::extend() registers extra directories for autoloading

  $t->is($autoloader->autoload('Extra'), false, 'The \'Extra\' class cannot be autoloaded because its path isn\'t registered for autoloading');
  
  Sonata_Autoloader::extend(array(dirname(__FILE__).'/../fixtures'));
  $t->is($autoloader->autoload('Extra'), true, 'The \'Extra\' class is now autoloaded correctly');
