<?php

/*
 * This file is part of the Lime test framework.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Bernhard Schussek <bernhard.schussek@symfony-project.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Represents the current state of the mock.
 *
 * A mock can have different states during his lifetime. The functionality
 * in these different states is implemented using the State Pattern. Each
 * state should extend this interface.
 *
 * @package    Lime
 * @author     Bernhard Schussek <bernhard.schussek@symfony-project.com>
 * @version    SVN: $Id: LimeMockStateInterface.php 23701 2009-11-08 21:23:40Z bschussek $
 */
interface LimeMockStateInterface
{
  /**
   * Handles an invoked method on the mock.
   *
   * Depending on the state of the mock, invoked methods may be treated
   * differently.
   *
   * @param  string $class
   * @param  string $method
   * @param  array|string $parameters
   * @return mixed
   * @throws LimeMockInvocationException
   * @throws Exception
   */
  public function invoke($class, $method, array $parameters = null);

  /**
   * Tells the state that the mock should not receive any method invocation.
   */
  public function setExpectNothing();

  /**
   * Verifies the mock in the current state.
   */
  public function verify();
}