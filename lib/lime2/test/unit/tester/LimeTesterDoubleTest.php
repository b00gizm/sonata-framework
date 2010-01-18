<?php

/*
 * This file is part of the Lime framework.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Bernhard Schussek <bernhard.schussek@symfony-project.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

include dirname(__FILE__).'/../../bootstrap/unit.php';

LimeAnnotationSupport::enable();

$t = new LimeTest(2);


// @Test: __toString() returns the value as float

  $actual = new LimeTesterDouble(1);
  $t->ok($actual->__toString() === '1.0', 'The value is correct');


// @Test: is() throws no exception if the difference between the doubles is very small

  // fixtures
  $actual = new LimeTesterDouble(1/3);
  $expected = new LimeTesterDouble(1 - 2/3);
  // test
  $actual->is($expected);


// @Test: isnt() throws an exception if the difference between the doubles is very small

  // fixtures
  $actual = new LimeTesterDouble(1/3);
  $expected = new LimeTesterDouble(1 - 2/3);
  // test
  $t->expect('LimeAssertionFailedException');
  $actual->isnt($expected);

